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

        // Get requisitions with batch BOMs only (via direct BOM or via schedule), eager load schedule for team/machine
        $requisitions = MaterialsRequisition::where(function($query) {
            $query->whereHas('bom', function($q) {
                $q->where('bom_type', 'batch');
            })->orWhereHas('schedule.items.productionOrderItem.bom', function($q) {
                $q->where('bom_type', 'batch');
            })->orWhereHas('workOrder.items.scheduleItem.productionOrderItem.bom', function($q) {
                $q->where('bom_type', 'batch');
            });
        })->availableForManufacturing()
          ->forBranch($user->branch_id)
          ->with(['bom.finishProduct', 'schedule.items.productionOrderItem.bom.finishProduct', 'workOrder.items.scheduleItem.productionOrderItem.bom.finishProduct', 'workOrder.team', 'workOrder.machine'])
          ->get();

        // Calculate already-manufactured qty per requisition
        $manufacturedQtyByRequisition = BatchProduction::where('branch_id', $user->branch_id)
            ->whereIn('requisition_id', $requisitions->pluck('id'))
            ->groupBy('requisition_id')
            ->selectRaw('requisition_id, SUM(quantity) as total_manufactured')
            ->pluck('total_manufactured', 'requisition_id')
            ->toArray();

        // Calculate manufactured qty per requisition AND per BOM (for per-BOM quantity tracking)
        $manufacturedQtyByReqAndBom = BatchProduction::where('branch_id', $user->branch_id)
            ->whereIn('requisition_id', $requisitions->pluck('id'))
            ->groupBy('requisition_id', 'bom_id')
            ->selectRaw('requisition_id, bom_id, SUM(quantity) as total_manufactured')
            ->get()
            ->groupBy('requisition_id')
            ->map(function($items) {
                return $items->pluck('total_manufactured', 'bom_id')->toArray();
            })
            ->toArray();

        // Filter out exhausted requisitions (remaining qty <= 0)
        $requisitions = $requisitions->filter(function($req) use ($manufacturedQtyByRequisition, $manufacturedQtyByReqAndBom) {
            if ($req->bom_id) {
                $reqQty = $req->quantity ?? 0;
                if ($reqQty <= 0) return true;
                $remaining = $reqQty - ($manufacturedQtyByRequisition[$req->id] ?? 0);
                return $remaining > 0;
            }

            if ($req->workOrder) {
                $totalPlanned = 0;
                foreach ($req->workOrder->items as $woItem) {
                    $bom = $woItem->scheduleItem->productionOrderItem->bom ?? null;
                    if ($bom && $bom->bom_type === 'batch') {
                        $totalPlanned += $woItem->planned_qty;
                    }
                }
                if ($totalPlanned <= 0) return true;
                $totalManufactured = array_sum($manufacturedQtyByReqAndBom[$req->id] ?? []);
                return $totalPlanned > $totalManufactured;
            }

            if ($req->schedule) {
                $totalScheduled = 0;
                foreach ($req->schedule->items as $item) {
                    $bom = $item->productionOrderItem->bom ?? null;
                    if ($bom && $bom->bom_type === 'batch') {
                        $totalScheduled += $item->scheduled_qty;
                    }
                }
                if ($totalScheduled <= 0) return true;
                $totalManufactured = array_sum($manufacturedQtyByReqAndBom[$req->id] ?? []);
                return $totalScheduled > $totalManufactured;
            }

            return true;
        });

        // Collect only BOM IDs referenced by the available requisitions
        $allBomIds = collect();
        foreach ($requisitions as $req) {
            if ($req->bom_id) {
                $allBomIds->push($req->bom_id);
            }
            if ($req->workOrder) {
                foreach ($req->workOrder->items as $woItem) {
                    $bom = $woItem->scheduleItem->productionOrderItem->bom ?? null;
                    if ($bom && $bom->bom_type === 'batch') {
                        $allBomIds->push($bom->id);
                    }
                }
            } elseif ($req->schedule) {
                foreach ($req->schedule->items as $item) {
                    $bom = $item->productionOrderItem->bom ?? null;
                    if ($bom && $bom->bom_type === 'batch') {
                        $allBomIds->push($bom->id);
                    }
                }
            }
        }
        $allBomIds = $allBomIds->unique()->values();

        return view('pages.manufacturing.processing.batch_production.create', [
            'model' => $model,
            'requisitions' => $requisitions,
            'manufacturedQtyByRequisition' => $manufacturedQtyByRequisition,
            'manufacturedQtyByReqAndBom' => $manufacturedQtyByReqAndBom,
            'boms' => \App\Models\ManufacturingBom::whereIn('id', $allBomIds)->with('finishProduct')->get(),
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
            'quantity' => 'required|numeric|min:0.0001'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Validate quantity against requisition remaining (per-BOM for schedule-based)
            $requisition = MaterialsRequisition::with(['schedule.items.productionOrderItem.bom'])->find($request->requisition_id);
            if ($requisition) {
                $bomId = $request->bom_id;
                $maxQty = null;

                if ($requisition->bom_id) {
                    // Direct BOM requisition: use requisition quantity
                    $maxQty = $requisition->quantity;
                    $alreadyManufactured = BatchProduction::where('requisition_id', $request->requisition_id)
                        ->sum('quantity');
                } elseif ($requisition->work_order_id) {
                    // Work-order-based: get per-BOM planned qty from work order items
                    $maxQty = 0;
                    $requisition->load('workOrder.items.scheduleItem.productionOrderItem.bom');
                    foreach ($requisition->workOrder->items as $woItem) {
                        $itemBom = $woItem->scheduleItem->productionOrderItem->bom ?? null;
                        if ($itemBom && $itemBom->id == $bomId) {
                            $maxQty += $woItem->planned_qty;
                        }
                    }
                    $alreadyManufactured = BatchProduction::where('requisition_id', $request->requisition_id)
                        ->where('bom_id', $bomId)
                        ->sum('quantity');
                } elseif ($requisition->schedule) {
                    // Schedule-based: get per-BOM scheduled qty
                    $maxQty = 0;
                    foreach ($requisition->schedule->items as $schedItem) {
                        $itemBom = $schedItem->productionOrderItem->bom ?? null;
                        if ($itemBom && $itemBom->id == $bomId) {
                            $maxQty += $schedItem->scheduled_qty;
                        }
                    }
                    $alreadyManufactured = BatchProduction::where('requisition_id', $request->requisition_id)
                        ->where('bom_id', $bomId)
                        ->sum('quantity');
                } else {
                    $maxQty = $requisition->quantity;
                    $alreadyManufactured = BatchProduction::where('requisition_id', $request->requisition_id)
                        ->sum('quantity');
                }

                if ($maxQty > 0) {
                    $remaining = $maxQty - $alreadyManufactured;
                    if ($request->quantity > $remaining) {
                        throw new \Exception("Quantity ({$request->quantity}) exceeds remaining quantity ({$remaining}). Already manufactured: {$alreadyManufactured}");
                    }
                }
            }

            // Validate BOM matches requisition's BOM (only for direct BOM requisitions)
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

            // Save materials (quantities only — costs fetched live from branch_product_prices)
            foreach ($costData['materials'] as $m) {
                BatchProductionMaterial::create([
                    'batch_production_id' => $model->id,
                    'product_id' => $m['product_id'],
                    'store_id' => $m['store_id'],
                    'quantity' => $m['quantity'],
                ]);
            }

            // Save all costs at creation time (individual + totals)
            $model->total_material_cost = $costData['material_cost'];
            $model->labor_cost = $costData['labor_cost'];
            $model->power_cost = $costData['power_cost'];
            $model->other_cost = $costData['other_cost'];
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

        $batch->load(['requisition', 'bom.finishProduct', 'team', 'machine', 'materials.product', 'materials.store', 'createdBy', 'postedBy']);

        foreach ($batch->materials as $material) {
            $unitCost = ProductionCalculator::getCurrentCostPrice($material->product_id, $batch->branch_id);
            $material->current_unit_cost  = $unitCost;
            $material->current_total_cost = $material->quantity * $unitCost;
        }

        // Compute aggregate costs dynamically from live material prices + BOM overhead
        $bom = $batch->bom;
        $liveMaterialCost = $batch->materials->sum('current_total_cost');
        $laborCost = ($bom->labor_cost ?? 0) * $batch->quantity;
        $powerCost = ($bom->power_cost ?? 0) * $batch->quantity;
        $otherCost = ($bom->other_cost ?? 0) * $batch->quantity;
        $wipValue  = $liveMaterialCost + $laborCost + $powerCost + $otherCost;

        $batch->computed_material_cost = $liveMaterialCost;
        $batch->computed_labor_cost    = $laborCost;
        $batch->computed_power_cost    = $powerCost;
        $batch->computed_other_cost    = $otherCost;
        $batch->computed_wip_value     = $wipValue;

        return view('pages.manufacturing.processing.batch_production.show', [
            'record' => $batch
        ]);
    }

    public function qcVerify(BatchProduction $batch)
    {
        $this->authorize('manufacturing.batch_production.qc_verify');

        if (!$batch->canBeQcVerified()) {
            session()->flash('app_error', 'Only pending records can be QC verified.');
            return redirect()->back();
        }

        $batch->qcVerify();

        AuditLog::auditLog(Auth::id(), "QC verified batch production: " . $batch->reference);
        session()->flash('app_message', 'Batch Production QC verified successfully');

        return redirect()->back();
    }

    public function post(BatchProduction $batch)
    {
        $this->authorize('manufacturing.batch_production.post');

        if (!$batch->canBePosted()) {
            session()->flash('app_error', 'Only QC verified records can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Build materials array with LIVE prices from branch_product_prices
            $materials = $batch->materials->map(function ($m) use ($batch) {
                $unitCost = ProductionCalculator::getCurrentCostPrice($m->product_id, $batch->branch_id);
                return [
                    'product_id' => $m->product_id,
                    'store_id'   => $m->store_id,
                    'quantity'   => $m->quantity,
                    'unit_cost'  => $unitCost,
                ];
            })->toArray();

            // Validate all materials have a cost price before proceeding.
            // A zero cost price would silently post $0 to GL while still deducting stock qty.
            foreach ($materials as $mat) {
                if ($mat['unit_cost'] <= 0) {
                    $productName = \App\Models\Product::find($mat['product_id'])?->name ?? "ID {$mat['product_id']}";
                    throw new \Exception("Product \"{$productName}\" has no cost price configured for this branch. Please set a cost price in Branch Product Prices before posting.");
                }
            }

            // Recompute and FREEZE aggregate costs at posting time using live prices
            $liveMaterialCost = collect($materials)->sum(fn ($m) => $m['quantity'] * $m['unit_cost']);
            $batch->total_material_cost = $liveMaterialCost;
            $batch->wip_value = $liveMaterialCost + $batch->labor_cost + $batch->power_cost + $batch->other_cost;
            $batch->save();

            // Deduct raw materials and credit to WIP account
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

            $batch->loadMissing(['requisition', 'requisition.workOrder']);
            $scheduleId = $batch->requisition?->schedule_id
                ?? $batch->requisition?->workOrder?->daily_schedule_id;
            if ($scheduleId) {
                InventoryReservationService::consumeAllForReference('daily_schedule', $scheduleId);
            }

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

        foreach ($batch->materials as $material) {
            $unitCost = ProductionCalculator::getCurrentCostPrice($material->product_id, $batch->branch_id);
            $material->current_unit_cost  = $unitCost;
            $material->current_total_cost = $material->quantity * $unitCost;
        }

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

        // Add WIP value calculation (material + other costs)
        $costData['wip_value'] = $costData['material_cost'] + $costData['total_other_cost'];

        return response()->json([
            'status' => true,
            'data' => $costData
        ]);
    }
}
