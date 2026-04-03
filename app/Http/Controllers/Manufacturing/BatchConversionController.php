<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\BatchConversion;
use App\Models\BatchProduction;
use App\Classes\Manufacturing\ManufacturingTransaction;
use App\Classes\Manufacturing\ManufacturingCostPrice;
use App\Classes\Manufacturing\ProductionCalculator;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BatchConversionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manufacturing.batch_conversion.index');

        $user = Auth::user();
        $records = BatchConversion::with(['batchProduction.bom.finishProduct', 'createdBy'])
            ->forBranch($user->branch_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('pages.manufacturing.processing.batch_conversion.index', [
            'records' => $records
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('manufacturing.batch_conversion.create');

        $model = new BatchConversion;
        $model->reference = BatchConversion::generateNewNumber();
        $model->conversion_date = date('Y-m-d');

        $user = Auth::user();

        // Get posted batch productions that have remaining quantity for conversion
        $batches = BatchProduction::availableForConversion()
            ->forBranch($user->branch_id)
            ->with('bom.finishProduct')
            ->get();

        return view('pages.manufacturing.processing.batch_conversion.create', [
            'model' => $model,
            'batches' => $batches
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.batch_conversion.create');

        $request->validate([
            'reference' => 'required|unique:batch_conversions,reference',
            'batch_production_id' => 'required|exists:batch_productions,id',
            'produced_qty' => 'required|numeric|min:0.0001'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $batch = BatchProduction::with('bom')->find($request->batch_production_id);
            $bom = $batch->bom;

            $model = new BatchConversion;
            $model->reference = $request->reference;
            $model->conversion_date = $request->conversion_date;
            $model->batch_production_id = $request->batch_production_id;
            $model->produced_qty = $request->produced_qty;
            $model->finish_product_id = $bom->finish_product_id;
            $model->output_store_id = $bom->output_store_id;
            $model->branch_id = $user->branch_id;
            $model->status = BatchConversion::STATUS_PENDING;
            $model->created_by = Auth::id();

            // Full WIP cost attributed to actual produced qty (one conversion per batch)
            $wipCostDeducted = $batch->wip_value;

            $model->wip_cost_deducted = $wipCostDeducted;

            // Packaging material cost (main_raw_material from BOM)
            $materialCost = 0;
            if ($bom->main_raw_material_id) {
                $materialStoreId = $bom->materials()->first()?->source_store_id;
                $materialUnitCost = ProductionCalculator::getCurrentCostPrice($bom->main_raw_material_id, $user->branch_id);

                $model->material_product_id = $bom->main_raw_material_id;
                $model->material_store_id   = $materialStoreId;
                $model->material_qty        = $request->produced_qty;
                $model->material_unit_cost  = $materialUnitCost;
                $model->material_cost       = round($request->produced_qty * $materialUnitCost, 2);
                $materialCost = $model->material_cost;
            }

            $totalCost = $wipCostDeducted + $materialCost;
            $model->total_cost = $totalCost;
            $model->unit_cost  = $request->produced_qty > 0 ? $totalCost / $request->produced_qty : 0;

            $model->save();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created batch conversion: " . $model->reference);
            session()->flash('app_message', 'Batch Conversion created successfully');
            return redirect()->route('manufacturing.batch_conversion.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(BatchConversion $conversion)
    {
        $this->authorize('manufacturing.batch_conversion.show');

        $conversion->load(['batchProduction.bom.finishProduct', 'batchProduction.bom.outputStore', 'materialProduct', 'materialStore', 'createdBy', 'postedBy']);

        return view('pages.manufacturing.processing.batch_conversion.show', [
            'record' => $conversion
        ]);
    }

    public function post(BatchConversion $conversion)
    {
        $this->authorize('manufacturing.batch_conversion.post');

        if (!$conversion->isPending()) {
            session()->flash('app_error', 'Only pending conversions can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            $batch = $conversion->batchProduction;
            $bom = $batch->bom;

            // Deduct packaging material from inventory (if applicable)
            if ($conversion->material_product_id && $conversion->material_qty > 0) {
                $materials = [[
                    'product_id' => $conversion->material_product_id,
                    'store_id'   => $conversion->material_store_id,
                    'quantity'   => $conversion->material_qty,
                    'unit_cost'  => $conversion->material_unit_cost,
                ]];
                $deductResult = ManufacturingCostPrice::deductRawMaterials(
                    $materials,
                    $batch->batch_number,
                    $conversion->conversion_date,
                    $conversion->branch_id
                );
                if (!$deductResult['status']) {
                    throw new \Exception('Packaging material deduction failed: ' . $deductResult['message']);
                }
            }

            // Add margin to conversion cost
            $marginPerPiece = (float) ($bom->margin_per_piece ?? 0);
            $totalMargin = $marginPerPiece * $conversion->produced_qty;
            if ($totalMargin > 0) {
                $conversion->total_cost += $totalMargin;
                $conversion->save();
            }

            // Add finished goods to inventory
            $result = ManufacturingCostPrice::addFinishedGoods(
                $conversion->finish_product_id,
                $conversion->output_store_id,
                $conversion->produced_qty,
                $conversion->total_cost,
                $conversion->branch_id,
                $batch->batch_number,
                $conversion->conversion_date
            );

            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            // Post to ledger (debit finish goods, credit WIP)
            $ledgerResult = ManufacturingTransaction::batchConversion($conversion->id);
            if (!$ledgerResult['status']) {
                throw new \Exception('Ledger posting failed: ' . $ledgerResult['message']);
            }

            // Update batch production converted quantity
            $batch->addConvertedQty($conversion->produced_qty, $conversion->wip_cost_deducted);

            // Update conversion status
            $conversion->post();

            // Update production order item produced qty
            $this->updateProductionOrderProducedQty($batch, $conversion->produced_qty);

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Posted batch conversion: " . $conversion->reference);
            session()->flash('app_message', 'Batch Conversion posted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(BatchConversion $conversion)
    {
        $this->authorize('manufacturing.batch_conversion.delete');

        if (!$conversion->isPending()) {
            session()->flash('app_error', 'Only pending conversions can be deleted.');
            return redirect()->back();
        }

        $reference = $conversion->reference;
        $conversion->delete();

        AuditLog::auditLog(Auth::id(), "Deleted batch conversion: " . $reference);
        session()->flash('app_message', 'Batch Conversion deleted successfully');

        return redirect()->route('manufacturing.batch_conversion.index');
    }

    /**
     * Update production order item's produced_qty when batch is converted
     * Traces: BatchProduction -> Requisition -> Schedule -> ScheduleItem -> ProductionOrderItem
     * Also handles BOM-only requisitions (no schedule) by matching bom_id directly.
     */
    private function updateProductionOrderProducedQty(BatchProduction $batch, $producedQty)
    {
        $batch->load('requisition.schedule');
        $requisition = $batch->requisition;
        if (!$requisition) {
            \Log::warning("updateProductionOrderProducedQty: No requisition for batch {$batch->reference}");
            return;
        }

        $orderItem = null;

        if ($requisition->schedule_id) {
            // Path 1: Schedule-based - use schedule's production_order_id directly
            $schedule = $requisition->schedule;
            if ($schedule && $schedule->production_order_id) {
                $orderItem = \App\Models\ProductionOrderItem::where('production_order_id', $schedule->production_order_id)
                    ->where('bom_id', $batch->bom_id)
                    ->first();
            }
        }

        if (!$orderItem) {
            // Path 2: Fallback - find any open production order with matching bom_id
            $orderItem = \App\Models\ProductionOrderItem::where('bom_id', $batch->bom_id)
                ->whereHas('productionOrder', function ($q) use ($batch) {
                    $q->where('branch_id', $batch->branch_id)
                      ->whereIn('status', ['approved', 'pending']);
                })
                ->first();
        }

        if ($orderItem) {
            $orderItem->addProducedQty($producedQty);

            // Update parent order's processed_qty
            $order = $orderItem->productionOrder;
            if ($order) {
                $order->processed_qty = $order->items()->sum('produced_qty');
                $order->save();
            }
        } else {
            \Log::warning("updateProductionOrderProducedQty: No matching ProductionOrderItem for batch {$batch->reference}, bom_id: {$batch->bom_id}, requisition: {$requisition->reference}, schedule_id: {$requisition->schedule_id}");
        }
    }

    /**
     * AJAX: Get batch production details for conversion
     */
    public function getBatchDetails(Request $request)
    {
        $request->validate([
            'batch_production_id' => 'required|exists:batch_productions,id'
        ]);

        $batch = BatchProduction::with(['bom.finishProduct', 'bom.outputStore', 'bom.mainRawMaterial'])->find($request->batch_production_id);
        $bom = $batch->bom;

        $totalExpectedOutput = $batch->quantity * $bom->actual_output;
        $maxQty = ($totalExpectedOutput * (1 + ($bom->accepted_excess / 100))) - $batch->converted_qty;
        $minQty = max(0.0001, ($totalExpectedOutput * (1 - ($bom->accepted_shortage / 100))) - $batch->converted_qty);

        return response()->json([
            'status' => true,
            'data' => [
                'batch_number' => $batch->batch_number,
                'bom_reference' => $bom->reference,
                'finish_product_id' => $bom->finish_product_id,
                'finish_product_name' => $bom->finishProduct->name ?? '',
                'output_store_id' => $bom->output_store_id,
                'output_store_name' => $bom->outputStore->name ?? '',
                'total_expected_output' => $totalExpectedOutput,
                'converted_qty' => $batch->converted_qty,
                'remaining_qty' => $batch->remaining_qty,
                'wip_value' => $batch->wip_value,
                'accepted_excess' => $bom->accepted_excess,
                'accepted_shortage' => $bom->accepted_shortage,
                'min_qty' => $minQty,
                'max_qty' => $maxQty,
                'main_raw_material_id' => $bom->main_raw_material_id,
                'main_raw_material_name' => $bom->mainRawMaterial->name ?? null,
            ]
        ]);
    }

    /**
     * AJAX: Calculate conversion costs based on batch and quantity
     */
    public function calculateCosts(Request $request)
    {
        $request->validate([
            'batch_production_id' => 'required|exists:batch_productions,id',
            'produced_qty' => 'required|numeric|min:0.0001'
        ]);

        $batch = BatchProduction::with(['bom.mainRawMaterial'])->find($request->batch_production_id);
        $bom = $batch->bom;
        $user = Auth::user();

        // Full WIP attributed to actual produced qty (one conversion per batch)
        $wipCostDeducted = $batch->wip_value;
        $wipCostPerUnit  = $request->produced_qty > 0 ? $wipCostDeducted / $request->produced_qty : 0;

        // Packaging material cost
        $materialCost = 0;
        $materialUnitCost = 0;
        $materialProductName = null;
        if ($bom->main_raw_material_id) {
            $materialUnitCost = ProductionCalculator::getCurrentCostPrice($bom->main_raw_material_id, $user->branch_id);
            $materialCost = round($request->produced_qty * $materialUnitCost, 2);
            $materialProductName = $bom->mainRawMaterial->name ?? null;
        }

        $totalCost = $wipCostDeducted + $materialCost;

        $totalExpectedOutput = $batch->quantity * $bom->actual_output;
        $maxQty = ($totalExpectedOutput * (1 + ($bom->accepted_excess / 100))) - $batch->converted_qty;
        $minQty = max(0.0001, ($totalExpectedOutput * (1 - ($bom->accepted_shortage / 100))) - $batch->converted_qty);

        return response()->json([
            'status' => true,
            'data' => [
                'produced_qty'           => $request->produced_qty,
                'wip_cost_per_unit'      => $wipCostPerUnit,
                'wip_cost_deducted'      => $wipCostDeducted,
                'material_product_name'  => $materialProductName,
                'material_qty'           => $request->produced_qty,
                'material_unit_cost'     => $materialUnitCost,
                'material_cost'          => $materialCost,
                'total_cost'             => $totalCost,
                'unit_cost'              => $request->produced_qty > 0 ? $totalCost / $request->produced_qty : 0,
                'min_qty'                => $minQty,
                'max_qty'                => $maxQty
            ]
        ]);
    }
}
