<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\BatchConversion;
use App\Models\BatchProduction;
use App\Classes\Manufacturing\ManufacturingTransaction;
use App\Classes\Manufacturing\ManufacturingCostPrice;
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

            // Validate produced_qty doesn't exceed remaining
            $remainingQty = $batch->remaining_qty;
            if ($request->produced_qty > $remainingQty) {
                throw new \Exception("Produced quantity ({$request->produced_qty}) exceeds remaining quantity ({$remainingQty})");
            }

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

            // Calculate WIP cost to deduct (proportional to qty being converted)
            $totalExpectedOutput = $batch->quantity * $bom->actual_output;
            $wipCostPerUnit = $totalExpectedOutput > 0 ? $batch->wip_value / $totalExpectedOutput : 0;
            $wipCostDeducted = $request->produced_qty * $wipCostPerUnit;

            $model->wip_cost_deducted = $wipCostDeducted;
            $model->total_cost = $wipCostDeducted;
            $model->unit_cost = $request->produced_qty > 0 ? $wipCostDeducted / $request->produced_qty : 0;

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

        $conversion->load(['batchProduction.bom.finishProduct', 'batchProduction.bom.outputStore', 'createdBy', 'postedBy']);

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
            ManufacturingTransaction::batchConversion($conversion->id);

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
        $batch->load('requisition.schedule.items.productionOrderItem');
        $requisition = $batch->requisition;
        if (!$requisition) {
            return;
        }

        $orderItem = null;

        if ($requisition->schedule_id) {
            // Path 1: Schedule-based requisition
            $schedule = $requisition->schedule;
            if ($schedule) {
                $scheduleItem = $schedule->items()
                    ->whereHas('productionOrderItem', function ($q) use ($batch) {
                        $q->where('bom_id', $batch->bom_id);
                    })
                    ->first();

                if ($scheduleItem && $scheduleItem->productionOrderItem) {
                    $orderItem = $scheduleItem->productionOrderItem;
                }
            }
        }

        if (!$orderItem) {
            // Path 2: Fallback - find matching ProductionOrderItem directly by bom_id
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
            \Log::warning("Could not find ProductionOrderItem to update produced_qty for batch: {$batch->reference}, bom_id: {$batch->bom_id}");
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

        $batch = BatchProduction::with(['bom.finishProduct', 'bom.outputStore'])->find($request->batch_production_id);
        $bom = $batch->bom;

        $totalExpectedOutput = $batch->quantity * $bom->actual_output;
        $wipCostPerUnit = $totalExpectedOutput > 0 ? $batch->wip_value / $totalExpectedOutput : 0;

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
                'wip_cost_per_unit' => $wipCostPerUnit
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

        $batch = BatchProduction::with('bom')->find($request->batch_production_id);
        $bom = $batch->bom;

        $totalExpectedOutput = $batch->quantity * $bom->actual_output;
        $wipCostPerUnit = $totalExpectedOutput > 0 ? $batch->wip_value / $totalExpectedOutput : 0;
        $wipCostDeducted = $request->produced_qty * $wipCostPerUnit;

        return response()->json([
            'status' => true,
            'data' => [
                'produced_qty' => $request->produced_qty,
                'wip_cost_per_unit' => $wipCostPerUnit,
                'wip_cost_deducted' => $wipCostDeducted,
                'total_cost' => $wipCostDeducted,
                'unit_cost' => $wipCostPerUnit
            ]
        ]);
    }
}
