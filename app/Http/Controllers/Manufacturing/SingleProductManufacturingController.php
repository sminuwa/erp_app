<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\SingleProductManufacturing;
use App\Models\SingleProductManufacturingMaterial;
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

class SingleProductManufacturingController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manufacturing.single_manufacturing.index');

        $records = SingleProductManufacturing::with(['requisition', 'team', 'machine', 'bom.finishProduct'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.manufacturing.processing.single_manufacturing.index', [
            'records' => $records
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('manufacturing.single_manufacturing.create');

        $model = new SingleProductManufacturing;
        $model->reference = SingleProductManufacturing::generateNewNumber();
        $model->batch_number = SingleProductManufacturing::generateBatchNumber();
        $model->manufacturing_date = date('Y-m-d');

        $user = Auth::user();

        // Get requisitions with single BOMs only
        $requisitions = MaterialsRequisition::whereHas('bom', function($q) {
            $q->where('bom_type', 'single');
        })->orWhereHas('schedule.items.productionOrderItem.bom', function($q) {
            $q->where('bom_type', 'single');
        })->received()->forBranch($user->branch_id)->get();

        return view('pages.manufacturing.processing.single_manufacturing.create', [
            'model' => $model,
            'requisitions' => $requisitions,
            'boms' => \App\Models\ManufacturingBom::where('bom_type', 'single')->active()->forBranch($user->branch_id)->get(),
            'teams' => ManufacturingTeam::active()->forBranch($user->branch_id)->get(),
            'machines' => ManufacturingMachine::active()->forBranch($user->branch_id)->get(),
            'products' => \App\Models\Product::orderBy('name')->get(),
            'stores' => \App\Models\Store::where('branch_id', $user->branch_id)->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.single_manufacturing.create');

        $request->validate([
            'reference' => 'required|unique:single_product_manufacturing,reference',
            'batch_number' => 'required',
            'bom_id' => 'required|exists:manufacturing_boms,id',
            'requisition_id' => 'required|exists:materials_requisitions,id',
            'team_id' => 'required|exists:manufacturing_teams,id',
            'machine_id' => 'nullable|exists:manufacturing_machines,id',
            'quantity' => 'required|numeric|min:0.0001'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $bom = \App\Models\ManufacturingBom::find($request->bom_id);

            $model = new SingleProductManufacturing;
            $model->reference = $request->reference;
            $model->batch_number = $request->batch_number;
            $model->manufacturing_date = $request->manufacturing_date;
            $model->requisition_id = $request->requisition_id;
            $model->bom_id = $request->bom_id;
            $model->finish_product_id = $bom->finish_product_id;
            $model->output_store_id = $bom->output_store_id;
            $model->team_id = $request->team_id;
            $model->machine_id = $request->machine_id;
            $model->quantity = $request->quantity;
            $model->notes = $request->notes;
            $model->branch_id = $user->branch_id;
            $model->status = SingleProductManufacturing::STATUS_PENDING;
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
                SingleProductManufacturingMaterial::create([
                    'manufacturing_id' => $model->id,
                    'product_id' => $m['product_id'],
                    'store_id' => $m['store_id'],
                    'quantity' => $m['quantity'],
                    'unit_cost' => $m['unit_cost'],
                    'total_cost' => $m['total_cost']
                ]);
            }

            // Save costs from BOM calculation
            $model->total_material_cost = $costData['material_cost'];
            $model->total_other_cost = $costData['total_other_cost'];
            $model->total_cost = $costData['total_cost'];
            $model->unit_cost = $costData['unit_cost'];
            $model->save();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created single product manufacturing: " . $model->reference);
            session()->flash('app_message', 'Single Product Manufacturing created successfully');
            return redirect()->route('manufacturing.single_manufacturing.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(SingleProductManufacturing $spm)
    {
        $this->authorize('manufacturing.single_manufacturing.show');

        $spm->load(['requisition', 'bom.finishProduct', 'team', 'machine', 'materials.product', 'materials.store']);

        return view('pages.manufacturing.processing.single_manufacturing.show', [
            'record' => $spm
        ]);
    }

    public function post(SingleProductManufacturing $spm)
    {
        $this->authorize('manufacturing.single_manufacturing.post');

        if (!$spm->isPending()) {
            session()->flash('app_error', 'Only pending records can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Deduct raw materials
            $materials = $spm->materials->map(function($m) {
                return [
                    'product_id' => $m->product_id,
                    'store_id' => $m->store_id,
                    'quantity' => $m->quantity,
                    'unit_cost' => $m->unit_cost
                ];
            })->toArray();

            $result = ManufacturingCostPrice::deductRawMaterials(
                $materials,
                $spm->batch_number,
                $spm->manufacturing_date,
                $spm->branch_id
            );

            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            // Add finished goods
            $bom = $spm->bom;
            $outputQty = $spm->quantity * $bom->actual_output;
            $totalCost = $spm->total_material_cost + ($bom->labor_cost + $bom->power_cost + $bom->other_cost) * $spm->quantity;

            $result = ManufacturingCostPrice::addFinishedGoods(
                $bom->finish_product_id,
                $bom->output_store_id,
                $outputQty,
                $totalCost,
                $spm->branch_id,
                $spm->batch_number,
                $spm->manufacturing_date
            );

            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            // Post to ledger
            ManufacturingTransaction::singleProductManufacturing($spm->id);

            // Consume reservations
            InventoryReservationService::consumeAllForReference('requisition', $spm->requisition_id);

            // Update status and costs
            $spm->total_cost = $totalCost;
            $spm->unit_cost = $spm->quantity > 0 ? $totalCost / $spm->quantity : 0;
            $spm->post();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Posted single product manufacturing: " . $spm->reference);
            session()->flash('app_message', 'Single Product Manufacturing posted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(SingleProductManufacturing $spm)
    {
        $this->authorize('manufacturing.single_manufacturing.delete');

        if (!$spm->isPending()) {
            session()->flash('app_error', 'Only pending records can be deleted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            SingleProductManufacturingMaterial::where('manufacturing_id', $spm->id)->delete();
            $reference = $spm->reference;
            $spm->delete();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Deleted single product manufacturing: " . $reference);
            session()->flash('app_message', 'Single Product Manufacturing deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('manufacturing.single_manufacturing.index');
    }

    public function print(SingleProductManufacturing $spm)
    {
        $spm->load(['requisition', 'bom.finishProduct', 'team', 'machine', 'materials.product']);

        return view('pages.manufacturing.processing.single_manufacturing.print', [
            'record' => $spm
        ]);
    }

    /**
     * AJAX: Calculate production costs based on BOM and quantity
     */
    public function calculateCosts(Request $request)
    {
        $request->validate([
            'bom_id' => 'required|exists:manufacturing_boms,id',
            'quantity' => 'required|numeric|min:0.0001'
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

        return response()->json([
            'status' => true,
            'data' => $costData
        ]);
    }
}
