<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingBom;
use App\Models\ManufacturingBomMaterial;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\Branch;
use App\Models\BranchProductPrice;
use App\Models\SingleProductManufacturing;
use App\Models\BatchProduction;
use App\Models\MaterialsRequisition;
use App\Http\Requests\Manufacturing\Boms\Index as BomIndexRequest;
use App\Http\Requests\Manufacturing\Boms\Create as BomCreateRequest;
use App\Http\Requests\Manufacturing\Boms\Store as BomStoreRequest;
use App\Http\Requests\Manufacturing\Boms\Show as BomShowRequest;
use App\Http\Requests\Manufacturing\Boms\Edit as BomEditRequest;
use App\Http\Requests\Manufacturing\Boms\Update as BomUpdateRequest;
use App\Http\Requests\Manufacturing\Boms\Destroy as BomDestroyRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManufacturingBomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param BomIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(BomIndexRequest $request)
    {
        $user = Auth::user();
        $records = ManufacturingBom::with(['finishProduct', 'outputStore', 'branch'])
            ->forBranch($user->branch_id)
            ->orderBy('reference', 'desc')
            ->paginate(50);

        return view('pages.manufacturing.setup.boms.index', [
            'records' => $records
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param BomCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(BomCreateRequest $request)
    {
        $model = new ManufacturingBom;
        $model->reference = ManufacturingBom::generateNewNumber();
        $model->bom_type = ManufacturingBom::TYPE_BATCH;
        $model->actual_output = 1;
        $model->status = 1;

        $user = Auth::user();

        return view('pages.manufacturing.setup.boms.create', [
            'model' => $model,
            'branches' => Branch::orderBy('name')->get(),
            'stores' => Store::where('branch_id', $user->branch_id)->orderBy('name')->get(),
            'products' => Product::select('id', 'code', 'name')->orderBy('code', 'asc')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BomStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BomStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $model = new ManufacturingBom;
            $model->reference = $request->reference;
            $model->approval_document_no = $request->approval_document_no;
            $model->category_id = $request->category_id;
            $model->description = $request->description;
            $model->bom_type = $request->bom_type;
            $model->finish_product_id = $request->finish_product_id;
            $model->output_store_id = $request->output_store_id;
            $model->actual_output = $request->actual_output ?? 1;
            $model->accepted_excess = $request->accepted_excess ?? 0;
            $model->accepted_shortage = $request->accepted_shortage ?? 0;
            $model->main_raw_material_id = $request->main_raw_material_id;
            $model->labor_cost = $request->labor_cost ?? 0;
            $model->power_cost = $request->power_cost ?? 0;
            $model->other_cost = $request->other_cost ?? 0;
            $model->branch_id = Auth::user()->branch_id;
            $model->status = $request->status;
            $model->created_by = Auth::id();
            $model->save();

            // Save materials
            $totalMaterialCost = 0;
            if ($request->has('materials') && is_array($request->materials)) {
                foreach ($request->materials as $material) {
                    if (!empty($material['product_id']) && !empty($material['quantity'])) {
                        $unitCost = $this->getProductCost($material['product_id'], $model->branch_id);
                        $totalCost = $material['quantity'] * $unitCost;
                        $totalMaterialCost += $totalCost;

                        ManufacturingBomMaterial::create([
                            'bom_id' => $model->id,
                            'product_id' => $material['product_id'],
                            'quantity' => $material['quantity'],
                            'source_store_id' => $material['source_store_id'] ?? null,
                            'unit_cost' => $unitCost,
                            'total_cost' => $totalCost
                        ]);
                    }
                }
            }

            // Calculate totals
            $model->total_material_cost = $totalMaterialCost;
            $model->total_other_cost = ($model->labor_cost ?? 0) + ($model->power_cost ?? 0) + ($model->other_cost ?? 0);
            $totalCost = $model->total_material_cost + $model->total_other_cost;
            $model->total_cost_per_unit = $model->actual_output > 0 ? $totalCost / $model->actual_output : 0;
            $model->save();

            DB::commit();

            $action = "Added a new manufacturing BOM: " . $model->reference;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Bill of Materials saved successfully');
            return redirect()->route('manufacturing.boms.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Something went wrong: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param BomShowRequest $request
     * @param ManufacturingBom $bom
     * @return \Illuminate\Http\Response
     */
    public function show(BomShowRequest $request, ManufacturingBom $bom)
    {
        $bom->load(['finishProduct', 'outputStore', 'mainRawMaterial', 'branch', 'category', 'materials.product', 'materials.sourceStore']);

        return view('pages.manufacturing.setup.boms.show', [
            'record' => $bom
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param BomEditRequest $request
     * @param ManufacturingBom $bom
     * @return \Illuminate\Http\Response
     */
    public function edit(BomEditRequest $request, ManufacturingBom $bom)
    {
        $bom->load(['materials.product', 'materials.sourceStore']);

        // Refresh material costs to current average cost prices
        foreach ($bom->materials as $material) {
            $currentCost = $this->getProductCost($material->product_id, $bom->branch_id);
            if ($currentCost != $material->unit_cost) {
                $material->unit_cost = $currentCost;
                $material->total_cost = $material->quantity * $currentCost;
                $material->save();
            }
        }

        // Recalculate BOM totals with updated costs
        $bom->recalculateCosts();

        return view('pages.manufacturing.setup.boms.edit', [
            'model' => $bom,
            'branches' => Branch::orderBy('name')->get(),
            'stores' => Store::where('branch_id', $bom->branch_id)->orderBy('name')->get(),
            'products' => Product::select('id', 'code', 'name')->orderBy('code', 'asc')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BomUpdateRequest $request
     * @param ManufacturingBom $bom
     * @return \Illuminate\Http\Response
     */
    public function update(BomUpdateRequest $request, ManufacturingBom $bom)
    {
        DB::beginTransaction();

        try {
            $bom->approval_document_no = $request->approval_document_no;
            $bom->category_id = $request->category_id;
            $bom->description = $request->description;
            $bom->bom_type = $request->bom_type;
            $bom->finish_product_id = $request->finish_product_id;
            $bom->output_store_id = $request->output_store_id;
            $bom->actual_output = $request->actual_output ?? 1;
            $bom->accepted_excess = $request->accepted_excess ?? 0;
            $bom->accepted_shortage = $request->accepted_shortage ?? 0;
            $bom->main_raw_material_id = $request->main_raw_material_id;
            $bom->labor_cost = $request->labor_cost ?? 0;
            $bom->power_cost = $request->power_cost ?? 0;
            $bom->other_cost = $request->other_cost ?? 0;
            $bom->status = $request->status;
            $bom->updated_by = Auth::id();
            $bom->save();

            // Delete existing materials
            ManufacturingBomMaterial::where('bom_id', $bom->id)->delete();

            // Save materials
            $totalMaterialCost = 0;
            if ($request->has('materials') && is_array($request->materials)) {
                foreach ($request->materials as $material) {
                    if (!empty($material['product_id']) && !empty($material['quantity'])) {
                        $unitCost = $this->getProductCost($material['product_id'], $bom->branch_id);
                        $totalCost = $material['quantity'] * $unitCost;
                        $totalMaterialCost += $totalCost;

                        ManufacturingBomMaterial::create([
                            'bom_id' => $bom->id,
                            'product_id' => $material['product_id'],
                            'quantity' => $material['quantity'],
                            'source_store_id' => $material['source_store_id'] ?? null,
                            'unit_cost' => $unitCost,
                            'total_cost' => $totalCost
                        ]);
                    }
                }
            }

            // Calculate totals
            $bom->total_material_cost = $totalMaterialCost;
            $bom->total_other_cost = ($bom->labor_cost ?? 0) + ($bom->power_cost ?? 0) + ($bom->other_cost ?? 0);
            $totalCost = $bom->total_material_cost + $bom->total_other_cost;
            $bom->total_cost_per_unit = $bom->actual_output > 0 ? $totalCost / $bom->actual_output : 0;
            $bom->save();

            DB::commit();

            $action = "Updated manufacturing BOM: " . $bom->reference;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Bill of Materials updated successfully');
            return redirect()->route('manufacturing.boms.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Something went wrong: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BomDestroyRequest $request
     * @param ManufacturingBom $bom
     * @return \Illuminate\Http\Response
     */
    public function destroy(BomDestroyRequest $request, ManufacturingBom $bom)
    {
        DB::beginTransaction();

        try {
            // Check if BOM is used in production orders
            $usageCount = DB::table('production_order_items')
                ->where('bom_id', $bom->id)
                ->count();

            if ($usageCount > 0) {
                session()->flash('app_error', 'Cannot delete BOM. It is referenced by ' . $usageCount . ' production order item(s).');
                return redirect()->back();
            }

            // Check materials requisitions
            $requisitionCount = MaterialsRequisition::where('bom_id', $bom->id)->count();
            if ($requisitionCount > 0) {
                session()->flash('app_error', 'Cannot delete BOM. It is referenced by ' . $requisitionCount . ' materials requisition(s).');
                return redirect()->back();
            }

            // Check single product manufacturing
            $singleMfgCount = SingleProductManufacturing::where('bom_id', $bom->id)->count();
            if ($singleMfgCount > 0) {
                session()->flash('app_error', 'Cannot delete BOM. It is referenced by ' . $singleMfgCount . ' single product manufacturing record(s).');
                return redirect()->back();
            }

            // Check batch productions
            $batchCount = BatchProduction::where('bom_id', $bom->id)->count();
            if ($batchCount > 0) {
                session()->flash('app_error', 'Cannot delete BOM. It is referenced by ' . $batchCount . ' batch production record(s).');
                return redirect()->back();
            }

            // If no references found, proceed with deletion
            ManufacturingBomMaterial::where('bom_id', $bom->id)->delete();
            $reference = $bom->reference;
            $bom->delete();

            DB::commit();

            $action = "Deleted manufacturing BOM: " . $reference;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Bill of Materials deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error occurred while deleting Bill of Materials: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Print BOM details
     *
     * @param ManufacturingBom $bom
     * @return \Illuminate\Http\Response
     */
    public function print(ManufacturingBom $bom)
    {
        $this->authorize('manufacturing.boms.print');

        $bom->load(['finishProduct', 'outputStore', 'mainRawMaterial', 'branch', 'category', 'materials.product', 'materials.sourceStore']);

        return view('pages.manufacturing.setup.boms.print', [
            'record' => $bom
        ]);
    }

    /**
     * Get product cost price
     *
     * @param int $productId
     * @param int $branchId
     * @return float
     */
    private function getProductCost($productId, $branchId)
    {
        // First try to get from branch product price
        $branchPrice = BranchProductPrice::where([
            'product_id' => $productId,
            'branch_id' => $branchId
        ])->first();

        if ($branchPrice && $branchPrice->cost_price > 0) {
            return (float) $branchPrice->cost_price;
        }

        // Fallback to product's purchase price
        $product = Product::find($productId);
        if ($product && $product->purchase_price > 0) {
            return (float) $product->purchase_price;
        }

        // Log warning for monitoring
        \Log::warning('Cost price not found or zero', [
            'product_id' => $productId,
            'branch_id' => $branchId,
            'context' => 'ManufacturingBomController.getProductCost'
        ]);

        return 0;
    }

    /**
     * AJAX: Get product cost
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductCostAjax(Request $request)
    {
        $productId = $request->product_id;
        $branchId = $request->branch_id ?? Auth::user()->branch_id;

        $cost = $this->getProductCost($productId, $branchId);

        return response()->json([
            'status' => true,
            'cost' => $cost
        ]);
    }

    /**
     * AJAX: Get stores by branch
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStoresByBranch(Request $request)
    {
        $branchId = $request->branch_id;

        $stores = Store::where('branch_id', $branchId)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json([
            'status' => true,
            'stores' => $stores
        ]);
    }
}
