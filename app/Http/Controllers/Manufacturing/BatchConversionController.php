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

        $records = BatchConversion::with(['batchProduction.bom.finishProduct', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

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

        // Get posted batch productions that haven't been fully converted
        $batches = BatchProduction::posted()
            ->forBranch($user->branch_id)
            ->whereColumn('converted_qty', '<', 'quantity')
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
            'batch_id' => 'required|exists:batch_productions,id',
            'output_qty' => 'required|numeric|min:0.0001'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $batch = BatchProduction::with('bom')->find($request->batch_id);
            $bom = $batch->bom;

            $model = new BatchConversion;
            $model->reference = $request->reference;
            $model->conversion_date = $request->conversion_date;
            $model->batch_production_id = $request->batch_id;
            $model->produced_qty = $request->output_qty;
            $model->finish_product_id = $bom->finish_product_id;
            $model->output_store_id = $bom->output_store_id;
            $model->branch_id = $user->branch_id;
            $model->status = BatchConversion::STATUS_PENDING;
            $model->created_by = Auth::id();

            // Calculate costs
            $costPerUnit = $batch->wip_value / ($batch->quantity * $bom->actual_output);
            $wipCostDeducted = $request->output_qty * $costPerUnit;
            
            $model->wip_cost_deducted = $wipCostDeducted;
            $model->labor_cost = $bom->labor_cost * ($request->output_qty / $bom->actual_output);
            $model->power_cost = $bom->power_cost * ($request->output_qty / $bom->actual_output);
            $model->other_cost = $bom->other_cost * ($request->output_qty / $bom->actual_output);
            $model->total_cost = $wipCostDeducted + $model->labor_cost + $model->power_cost + $model->other_cost;
            $model->unit_cost = $model->total_cost / $request->output_qty;

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
}
