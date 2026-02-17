<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingReturn;
use App\Models\ManufacturingReturnMaterial;
use App\Models\SingleProductManufacturing;
use App\Models\BatchConversion;
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

        $user = Auth::user();
        $records = ManufacturingReturn::with(['createdBy'])
            ->forBranch($user->branch_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

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

        // Get posted productions that have remaining quantity to return (partial returns allowed)
        $singleManufacturing = SingleProductManufacturing::posted()
            ->forBranch($user->branch_id)
            ->with(['bom.finishProduct', 'materials.product', 'materials.store'])
            ->get()
            ->filter(function ($sm) {
                return $sm->getRemainingReturnableQty() > 0;
            });

        $batchConversions = BatchConversion::posted()
            ->forBranch($user->branch_id)
            ->with(['batchProduction.bom.finishProduct', 'batchProduction.materials.product', 'batchProduction.materials.store', 'finishProduct'])
            ->get()
            ->filter(function ($bc) {
                return $bc->getRemainingReturnableQty() > 0;
            });

        return view('pages.manufacturing.processing.returns.create', [
            'model' => $model,
            'singleManufacturing' => $singleManufacturing,
            'batchConversions' => $batchConversions
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.returns.create');

        $request->validate([
            'reference' => 'required|unique:manufacturing_returns,reference',
            'return_date' => 'required|date',
            'return_qty' => 'required|numeric|min:0.0001',
            'reason' => 'required|max:500'
        ]);

        // Must select one production type
        if (!$request->single_manufacturing_id && !$request->batch_conversion_id) {
            return redirect()->back()->withInput()->with('app_error', 'Please select a production to return.');
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Determine production type and ID (polymorphic)
            $productionType = null;
            $productionId = null;
            $totalCostReturned = 0;

            if ($request->single_manufacturing_id) {
                $productionType = ManufacturingReturn::PRODUCTION_TYPE_SINGLE;
                $productionId = $request->single_manufacturing_id;

                // Validate return qty doesn't exceed remaining
                $production = SingleProductManufacturing::find($productionId);
                if ($production) {
                    $remainingQty = $production->getRemainingReturnableQty();
                    if ($request->return_qty > $remainingQty) {
                        throw new \Exception("Return quantity ({$request->return_qty}) exceeds remaining quantity ({$remainingQty})");
                    }
                    $totalCostReturned = $request->return_qty * $production->getUnitCost();
                }
            } elseif ($request->batch_conversion_id) {
                $productionType = ManufacturingReturn::PRODUCTION_TYPE_BATCH;
                $productionId = $request->batch_conversion_id;

                // Validate return qty doesn't exceed remaining
                $conversion = BatchConversion::find($productionId);
                if ($conversion) {
                    $remainingQty = $conversion->getRemainingReturnableQty();
                    if ($request->return_qty > $remainingQty) {
                        throw new \Exception("Return quantity ({$request->return_qty}) exceeds remaining quantity ({$remainingQty})");
                    }
                    $totalCostReturned = $request->return_qty * $conversion->getUnitCost();
                }
            }

            $model = new ManufacturingReturn;
            $model->reference = $request->reference;
            $model->return_date = $request->return_date;
            $model->production_type = $productionType;
            $model->production_id = $productionId;
            $model->return_qty = $request->return_qty;
            $model->total_cost_returned = $totalCostReturned;
            $model->reason = $request->reason;
            $model->branch_id = $user->branch_id;
            $model->status = ManufacturingReturn::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            // Calculate proportional raw materials for this return
            $this->calculateReturnMaterials($model, $productionType, $productionId, $request->return_qty);

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

        // Load relationships based on production type
        $relations = ['createdBy', 'postedBy', 'materials.product', 'materials.store'];
        if ($return->production_type === ManufacturingReturn::PRODUCTION_TYPE_SINGLE) {
            $relations[] = 'singleManufacturing.bom.finishProduct';
        } else {
            $relations[] = 'batchConversion.finishProduct';
            $relations[] = 'batchConversion.batchProduction.bom.finishProduct';
        }
        $return->load($relations);

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

            // Deduct finished goods from inventory
            $production = $return->getProduction();
            if ($production) {
                $finishProductId = $production->finish_product_id
                    ?? ($production->bom->finish_product_id ?? null);
                $outputStoreId = $production->output_store_id ?? null;
                $unitCost = $production->getUnitCost();

                if ($finishProductId && $outputStoreId) {
                    $result = ManufacturingCostPrice::returnFinishedGoods(
                        $finishProductId,
                        $outputStoreId,
                        $return->return_qty,
                        $return->return_qty * $unitCost,
                        $return->branch_id,
                        $return->reference,
                        $return->return_date
                    );

                    if (!$result['status']) {
                        throw new \Exception($result['message']);
                    }
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

    /**
     * Calculate proportional raw materials for a return based on the original production materials.
     * For partial returns, materials are proportionally reduced based on (return_qty / production_qty).
     */
    private function calculateReturnMaterials(ManufacturingReturn $return, $productionType, $productionId, $returnQty)
    {
        $productionMaterials = collect();
        $productionQty = 0;

        if ($productionType === ManufacturingReturn::PRODUCTION_TYPE_SINGLE) {
            $production = SingleProductManufacturing::with('materials')->find($productionId);
            if ($production) {
                $productionQty = $production->quantity;
                $productionMaterials = $production->materials;
            }
        } else {
            $conversion = BatchConversion::with('batchProduction.materials')->find($productionId);
            if ($conversion && $conversion->batchProduction) {
                $productionQty = $conversion->batchProduction->quantity;
                $productionMaterials = $conversion->batchProduction->materials;
            }
        }

        if ($productionQty <= 0 || $productionMaterials->isEmpty()) {
            return;
        }

        $ratio = $returnQty / $productionQty;

        foreach ($productionMaterials as $material) {
            ManufacturingReturnMaterial::create([
                'return_id' => $return->id,
                'product_id' => $material->product_id,
                'store_id' => $material->store_id,
                'quantity' => round($material->quantity * $ratio, 4),
                'unit_cost' => $material->unit_cost,
                'total_cost' => round($material->quantity * $ratio * $material->unit_cost, 2),
            ]);
        }
    }
}
