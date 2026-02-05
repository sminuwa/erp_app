<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingRework;
use App\Models\ManufacturingReworkMaterial;
use App\Models\SingleProductManufacturing;
use App\Models\BatchProduction;
use App\Models\Product;
use App\Models\Store;
use App\Classes\Manufacturing\ManufacturingTransaction;
use App\Classes\Manufacturing\ManufacturingCostPrice;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManufacturingReworkController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manufacturing.reworks.index');

        $records = ManufacturingRework::with(['singleManufacturing', 'batchProduction', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.manufacturing.processing.reworks.index', [
            'records' => $records
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('manufacturing.reworks.create');

        $model = new ManufacturingRework;
        $model->reference = ManufacturingRework::generateNewNumber();
        $model->rework_date = date('Y-m-d');

        $user = Auth::user();

        return view('pages.manufacturing.processing.reworks.create', [
            'model' => $model,
            'singleManufacturing' => SingleProductManufacturing::posted()->forBranch($user->branch_id)->get(),
            'batchProductions' => BatchProduction::posted()->forBranch($user->branch_id)->get(),
            'products' => Product::orderBy('name')->get(),
            'stores' => Store::where('branch_id', $user->branch_id)->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.reworks.create');

        $request->validate([
            'reference' => 'required|unique:manufacturing_reworks,reference',
            'reason' => 'required|max:500'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Determine production type and ID (polymorphic)
            $productionType = null;
            $productionId = null;
            if ($request->single_manufacturing_id) {
                $productionType = ManufacturingRework::PRODUCTION_TYPE_SINGLE;
                $productionId = $request->single_manufacturing_id;
            } elseif ($request->batch_production_id) {
                $productionType = ManufacturingRework::PRODUCTION_TYPE_BATCH;
                $productionId = $request->batch_production_id;
            }

            $model = new ManufacturingRework;
            $model->reference = $request->reference;
            $model->rework_date = $request->rework_date;
            $model->production_type = $productionType;
            $model->production_id = $productionId;
            $model->labor_cost = $request->additional_labor_cost ?? 0;
            $model->power_cost = $request->additional_power_cost ?? 0;
            $model->other_cost = $request->additional_other_cost ?? 0;
            $model->reason = $request->reason;
            $model->branch_id = $user->branch_id;
            $model->status = ManufacturingRework::STATUS_PENDING;
            $model->created_by = Auth::id();

            // Save rework materials
            $totalMaterialCost = 0;
            if ($request->has('materials') && is_array($request->materials)) {
                foreach ($request->materials as $material) {
                    if (!empty($material['product_id']) && !empty($material['quantity'])) {
                        $unitCost = $material['unit_cost'] ?? 0;
                        $totalCost = $material['quantity'] * $unitCost;
                        $totalMaterialCost += $totalCost;

                        ManufacturingReworkMaterial::create([
                            'rework_id' => $model->id,
                            'product_id' => $material['product_id'],
                            'store_id' => $material['store_id'],
                            'quantity' => $material['quantity'],
                            'unit_cost' => $unitCost,
                            'total_cost' => $totalCost
                        ]);
                    }
                }
            }

            $model->total_material_cost = $totalMaterialCost;
            $model->total_cost = $totalMaterialCost + $model->labor_cost + $model->power_cost + $model->other_cost;
            $model->save();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created rework: " . $model->reference);
            session()->flash('app_message', 'Rework created successfully');
            return redirect()->route('manufacturing.reworks.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(ManufacturingRework $rework)
    {
        $this->authorize('manufacturing.reworks.show');

        $rework->load(['singleManufacturing', 'batchProduction', 'materials.product', 'materials.store', 'createdBy', 'postedBy']);

        return view('pages.manufacturing.processing.reworks.show', [
            'record' => $rework
        ]);
    }

    public function post(ManufacturingRework $rework)
    {
        $this->authorize('manufacturing.reworks.post');

        if (!$rework->isPending()) {
            session()->flash('app_error', 'Only pending reworks can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Deduct additional materials
            if ($rework->materials->count() > 0) {
                $materials = $rework->materials->map(function($m) {
                    return [
                        'product_id' => $m->product_id,
                        'store_id' => $m->store_id,
                        'quantity' => $m->quantity,
                        'unit_cost' => $m->unit_cost
                    ];
                })->toArray();

                ManufacturingCostPrice::deductRawMaterials(
                    $materials,
                    $rework->reference,
                    $rework->rework_date,
                    $rework->branch_id
                );
            }

            // Add cost to finished product
            if ($rework->production_type === ManufacturingRework::PRODUCTION_TYPE_SINGLE) {
                $production = $rework->getProduction();
                if ($production) {
                    ManufacturingCostPrice::addCostToExistingProduct(
                        $production->finish_product_id,
                        $rework->total_cost,
                        $rework->branch_id
                    );
                }
            }

            // Post to ledger
            ManufacturingTransaction::rework($rework->id);

            $rework->post();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Posted rework: " . $rework->reference);
            session()->flash('app_message', 'Rework posted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(ManufacturingRework $rework)
    {
        $this->authorize('manufacturing.reworks.delete');

        if (!$rework->isPending()) {
            session()->flash('app_error', 'Only pending reworks can be deleted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            ManufacturingReworkMaterial::where('rework_id', $rework->id)->delete();
            $reference = $rework->reference;
            $rework->delete();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Deleted rework: " . $reference);
            session()->flash('app_message', 'Rework deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('manufacturing.reworks.index');
    }
}
