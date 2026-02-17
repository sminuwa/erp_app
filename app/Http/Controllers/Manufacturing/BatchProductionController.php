<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\BatchProduction;
use App\Models\BatchProductionMaterial;
use App\Models\MaterialsRequisition;
use App\Models\ManufacturingTeam;
use App\Models\ManufacturingMachine;
use App\Classes\Manufacturing\ManufacturingTransaction;
use App\Classes\Manufacturing\ManufacturingCostPrice;
use App\Classes\Manufacturing\ProductionCalculator;
use App\Classes\Manufacturing\InventoryReservationService;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BatchProductionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manufacturing.batch_production.index');

        $user = Auth::user();
        $records = BatchProduction::with(['requisition', 'team', 'machine', 'bom.finishProduct'])
            ->forBranch($user->branch_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('pages.manufacturing.processing.batch_production.index', [
            'records' => $records
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('manufacturing.batch_production.create');

        $model = new BatchProduction;
        $model->reference = BatchProduction::generateNewNumber();
        $model->batch_number = BatchProduction::generateBatchNumber();
        $model->production_date = date('Y-m-d');
        $model->quantity = 1; // Default quantity is always 1 for batch

        $user = Auth::user();

        // Get requisitions with batch BOMs only, eager load schedule for team/machine
        $requisitions = MaterialsRequisition::whereHas('bom', function($q) {
            $q->where('bom_type', 'batch');
        })->received()
          ->forBranch($user->branch_id)
          ->with(['bom.finishProduct', 'schedule'])
          ->get();

        // Calculate already-manufactured qty per requisition
        $manufacturedQtyByRequisition = BatchProduction::where('branch_id', $user->branch_id)
            ->whereIn('requisition_id', $requisitions->pluck('id'))
            ->groupBy('requisition_id')
            ->selectRaw('requisition_id, SUM(quantity) as total_manufactured')
            ->pluck('total_manufactured', 'requisition_id')
            ->toArray();

        return view('pages.manufacturing.processing.batch_production.create', [
            'model' => $model,
            'requisitions' => $requisitions,
            'manufacturedQtyByRequisition' => $manufacturedQtyByRequisition,
            'boms' => \App\Models\ManufacturingBom::where('bom_type', 'batch')->active()->forBranch($user->branch_id)->with('finishProduct')->get(),
            'teams' => ManufacturingTeam::active()->forBranch($user->branch_id)->get(),
            'machines' => ManufacturingMachine::active()->forBranch($user->branch_id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.batch_production.create');

        $request->validate([
            'reference' => 'required|unique:batch_productions,reference',
            'batch_number' => 'required',
            'bom_id' => 'required|exists:manufacturing_boms,id',
            'requisition_id' => 'required|exists:materials_requisitions,id',
            'team_id' => 'required|exists:manufacturing_teams,id',
            'machine_id' => 'nullable|exists:manufacturing_machines,id',
            'quantity' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Validate quantity against requisition remaining
            $requisition = MaterialsRequisition::find($request->requisition_id);
            if ($requisition && $requisition->quantity) {
                $alreadyManufactured = BatchProduction::where('requisition_id', $request->requisition_id)
                    ->sum('quantity');
                $remaining = $requisition->quantity - $alreadyManufactured;
                if ($request->quantity > $remaining) {
                    throw new \Exception("Quantity ({$request->quantity}) exceeds remaining requisition quantity ({$remaining}). Already manufactured: {$alreadyManufactured}");
                }
            }

            // Validate BOM matches requisition's BOM
            if ($requisition && $requisition->bom_id && $request->bom_id != $requisition->bom_id) {
                throw new \Exception("Selected BOM does not match the requisition's BOM.");
            }

            $bom = \App\Models\ManufacturingBom::find($request->bom_id);

            $model = new BatchProduction;
            $model->reference = $request->reference;
            $model->batch_number = $request->batch_number;
            $model->production_date = $request->production_date;
            $model->requisition_id = $request->requisition_id;
            $model->bom_id = $request->bom_id;
            $model->team_id = $request->team_id;
            $model->machine_id = $request->machine_id;
            $model->quantity = $request->quantity;
            $model->notes = $request->notes;
            $model->branch_id = $user->branch_id;
            $model->status = BatchProduction::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            // Calculate production cost from BOM (materials + labor/power/other costs)
            $costData = ProductionCalculator::calculateProductionCost(
                $model->bom_id,
                $model->quantity,
                $model->branch_id
            );

            // Save materials
            foreach ($costData['materials'] as $m) {
                BatchProductionMaterial::create([
                    'batch_production_id' => $model->id,
                    'product_id' => $m['product_id'],
                    'store_id' => $m['store_id'],
                    'quantity' => $m['quantity'],
                    'unit_cost' => $m['unit_cost'],
                    'total_cost' => $m['total_cost']
                ]);
            }

            // Save all costs at creation time
            $model->total_material_cost = $costData['material_cost'];
            $model->wip_value = $costData['material_cost'] + $costData['total_other_cost'];
            $model->save();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created batch production: " . $model->reference);
            session()->flash('app_message', 'Batch Production created successfully');
            return redirect()->route('manufacturing.batch_production.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(BatchProduction $batch)
    {
        $this->authorize('manufacturing.batch_production.show');

        $batch->load(['requisition', 'bom.finishProduct', 'team', 'machine', 'materials.product', 'materials.store']);

        return view('pages.manufacturing.processing.batch_production.show', [
            'record' => $batch
        ]);
    }

    public function post(BatchProduction $batch)
    {
        $this->authorize('manufacturing.batch_production.post');

        if (!$batch->isPending()) {
            session()->flash('app_error', 'Only pending records can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Deduct raw materials and credit to WIP account
            $materials = $batch->materials->map(function($m) {
                return [
                    'product_id' => $m->product_id,
                    'store_id' => $m->store_id,
                    'quantity' => $m->quantity,
                    'unit_cost' => $m->unit_cost
                ];
            })->toArray();

            $result = ManufacturingCostPrice::deductRawMaterials(
                $materials,
                $batch->batch_number,
                $batch->production_date,
                $batch->branch_id
            );

            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            // Post to WIP ledger
            ManufacturingTransaction::batchProduction($batch->id);

            // Consume reservations
            InventoryReservationService::consumeAllForReference('requisition', $batch->requisition_id);

            // Update status (WIP value is calculated in post() method)
            $batch->post();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Posted batch production: " . $batch->reference);
            session()->flash('app_message', 'Batch Production posted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(BatchProduction $batch)
    {
        $this->authorize('manufacturing.batch_production.delete');

        if (!$batch->isPending()) {
            session()->flash('app_error', 'Only pending records can be deleted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            BatchProductionMaterial::where('batch_production_id', $batch->id)->delete();
            $reference = $batch->reference;
            $batch->delete();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Deleted batch production: " . $reference);
            session()->flash('app_message', 'Batch Production deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('manufacturing.batch_production.index');
    }

    public function print(BatchProduction $batch)
    {
        $this->authorize('manufacturing.batch_production.print');

        $batch->load(['requisition', 'bom.finishProduct', 'team', 'machine', 'materials.product']);

        return view('pages.manufacturing.processing.batch_production.print', [
            'record' => $batch
        ]);
    }

    /**
     * AJAX: Calculate production costs based on BOM and quantity
     */
    public function calculateCosts(Request $request)
    {
        $request->validate([
            'bom_id' => 'required|exists:manufacturing_boms,id',
            'quantity' => 'required|numeric|min:1'
        ]);

        $user = Auth::user();
        $costData = ProductionCalculator::calculateProductionCost(
            $request->bom_id,
            $request->quantity,
            $user->branch_id
        );

        // Add store names to materials
        if (!empty($costData['materials'])) {
            $storeIds = array_column($costData['materials'], 'store_id');
            $stores = \App\Models\Store::whereIn('id', $storeIds)->pluck('name', 'id');

            foreach ($costData['materials'] as &$material) {
                $material['store_name'] = $stores[$material['store_id']] ?? '-';
            }
        }

        // Add WIP value calculation (material + other costs)
        $costData['wip_value'] = $costData['material_cost'] + $costData['total_other_cost'];

        return response()->json([
            'status' => true,
            'data' => $costData
        ]);
    }
}
