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

        // Get requisitions with batch BOMs only
        $requisitions = MaterialsRequisition::whereHas('bom', function($q) {
            $q->where('bom_type', 'batch');
        })->received()->forBranch($user->branch_id)->get();

        return view('pages.manufacturing.processing.batch_production.create', [
            'model' => $model,
            'requisitions' => $requisitions,
            'boms' => \App\Models\ManufacturingBom::where('bom_type', 'batch')->active()->forBranch($user->branch_id)->get(),
            'teams' => ManufacturingTeam::active()->forBranch($user->branch_id)->get(),
            'machines' => ManufacturingMachine::active()->forBranch($user->branch_id)->get(),
            'products' => \App\Models\Product::orderBy('name')->get(),
            'stores' => \App\Models\Store::where('branch_id', $user->branch_id)->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.batch_production.create');

        $request->validate([
            'reference' => 'required|unique:batch_productions,reference',
            'requisition_id' => 'required|exists:materials_requisitions,id',
            'team_id' => 'required|exists:manufacturing_teams,id',
            'quantity' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $requisition = MaterialsRequisition::with('bom')->find($request->requisition_id);

            $model = new BatchProduction;
            $model->reference = $request->reference;
            $model->batch_number = $request->batch_number;
            $model->production_date = $request->production_date;
            $model->requisition_id = $request->requisition_id;
            $model->bom_id = $requisition->bom_id;
            $model->team_id = $request->team_id;
            $model->machine_id = $request->machine_id;
            $model->quantity = $request->quantity;
            $model->notes = $request->notes;
            $model->branch_id = $user->branch_id;
            $model->status = BatchProduction::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            // Calculate and save materials
            $materialsData = ProductionCalculator::calculateMaterials(
                $model->bom_id,
                $model->quantity,
                $model->branch_id
            );

            $totalMaterialCost = 0;
            foreach ($materialsData['materials'] as $m) {
                BatchProductionMaterial::create([
                    'batch_production_id' => $model->id,
                    'product_id' => $m['product_id'],
                    'store_id' => $m['store_id'],
                    'quantity' => $m['quantity'],
                    'unit_cost' => $m['unit_cost'],
                    'total_cost' => $m['total_cost']
                ]);
                $totalMaterialCost += $m['total_cost'];
            }

            $model->total_material_cost = $totalMaterialCost;
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
}
