<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingReturn;
use App\Models\ManufacturingReturnMaterial;
use App\Models\SingleProductManufacturing;
use App\Models\BatchProduction;
use App\Classes\Manufacturing\ManufacturingTransaction;
use App\Classes\Manufacturing\ManufacturingCostPrice;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManufacturingReturnController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manufacturing.returns.index');

        $records = ManufacturingReturn::with(['singleManufacturing', 'batchProduction', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.manufacturing.processing.returns.index', [
            'records' => $records
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('manufacturing.returns.create');

        $model = new ManufacturingReturn;
        $model->reference = ManufacturingReturn::generateNewNumber();
        $model->return_date = date('Y-m-d');

        $user = Auth::user();

        return view('pages.manufacturing.processing.returns.create', [
            'model' => $model,
            'singleManufacturing' => SingleProductManufacturing::posted()->forBranch($user->branch_id)->get(),
            'batchProductions' => BatchProduction::posted()->forBranch($user->branch_id)->get(),
            'products' => \App\Models\Product::orderBy('name')->get(),
            'stores' => \App\Models\Store::where('branch_id', $user->branch_id)->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.returns.create');

        $request->validate([
            'reference' => 'required|unique:manufacturing_returns,reference',
            'reason' => 'required|max:500'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Determine production type and ID (polymorphic)
            $productionType = null;
            $productionId = null;
            if ($request->single_manufacturing_id) {
                $productionType = ManufacturingReturn::PRODUCTION_TYPE_SINGLE;
                $productionId = $request->single_manufacturing_id;
            } elseif ($request->batch_production_id) {
                $productionType = ManufacturingReturn::PRODUCTION_TYPE_BATCH;
                $productionId = $request->batch_production_id;
            }

            $model = new ManufacturingReturn;
            $model->reference = $request->reference;
            $model->return_date = $request->return_date;
            $model->production_type = $productionType;
            $model->production_id = $productionId;
            $model->return_qty = $request->return_qty ?? 0;
            $model->reason = $request->reason;
            $model->branch_id = $user->branch_id;
            $model->status = ManufacturingReturn::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            // Save return materials if provided
            if ($request->has('materials') && is_array($request->materials)) {
                foreach ($request->materials as $material) {
                    if (!empty($material['product_id']) && !empty($material['quantity'])) {
                        ManufacturingReturnMaterial::create([
                            'return_id' => $model->id,
                            'product_id' => $material['product_id'],
                            'store_id' => $material['store_id'],
                            'quantity' => $material['quantity'],
                            'unit_cost' => $material['unit_cost'] ?? 0,
                            'total_cost' => ($material['quantity'] ?? 0) * ($material['unit_cost'] ?? 0)
                        ]);
                    }
                }
            }

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created manufacturing return: " . $model->reference);
            session()->flash('app_message', 'Manufacturing Return created successfully');
            return redirect()->route('manufacturing.returns.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(ManufacturingReturn $return)
    {
        $this->authorize('manufacturing.returns.show');

        $return->load(['singleManufacturing', 'batchProduction', 'materials.product', 'createdBy', 'postedBy']);

        return view('pages.manufacturing.processing.returns.show', [
            'record' => $return
        ]);
    }

    public function post(ManufacturingReturn $return)
    {
        $this->authorize('manufacturing.returns.post');

        if (!$return->isPending()) {
            session()->flash('app_error', 'Only pending returns can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Credit back raw materials
            if ($return->materials->count() > 0) {
                $materials = $return->materials->map(function($m) {
                    return [
                        'product_id' => $m->product_id,
                        'store_id' => $m->store_id,
                        'quantity' => $m->quantity,
                        'unit_cost' => $m->unit_cost
                    ];
                })->toArray();

                ManufacturingCostPrice::creditRawMaterials(
                    $materials,
                    $return->reference,
                    $return->return_date,
                    $return->branch_id
                );
            }

            // Deduct finished goods if applicable
            if ($return->production_type === ManufacturingReturn::PRODUCTION_TYPE_SINGLE) {
                $production = $return->getProduction();
                if ($production) {
                    ManufacturingCostPrice::returnFinishedGoods(
                        $production->finish_product_id,
                        $production->output_store_id,
                        $return->return_qty,
                        $return->return_qty * $production->unit_cost,
                        $return->branch_id,
                        $return->reference,
                        $return->return_date
                    );
                }
            }

            // Post to ledger
            ManufacturingTransaction::manufacturingReturn($return->id);

            $return->post();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Posted manufacturing return: " . $return->reference);
            session()->flash('app_message', 'Manufacturing Return posted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(ManufacturingReturn $return)
    {
        $this->authorize('manufacturing.returns.delete');

        if (!$return->isPending()) {
            session()->flash('app_error', 'Only pending returns can be deleted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            ManufacturingReturnMaterial::where('return_id', $return->id)->delete();
            $reference = $return->reference;
            $return->delete();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Deleted manufacturing return: " . $reference);
            session()->flash('app_message', 'Manufacturing Return deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('manufacturing.returns.index');
    }
}
