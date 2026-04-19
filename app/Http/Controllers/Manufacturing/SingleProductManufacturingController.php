<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\SingleProductManufacturing;
use App\Models\SingleProductManufacturingMaterial;
use App\Models\MaterialsRequisition;
use App\Models\ManufacturingTeam;
use App\Models\ManufacturingMachine;
use App\Models\ManufacturingBom;
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

        $user = Auth::user();
        $records = SingleProductManufacturing::with(['requisition', 'team', 'machine', 'bom.finishProduct'])
            ->forBranch($user->branch_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

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

        // Get requisitions with single BOMs only, eager load schedule items for BOM extraction
        $requisitions = MaterialsRequisition::where(function($query) {
            $query->whereHas('bom', function($q) {
                $q->where('bom_type', 'single');
            })->orWhereHas('schedule.items.productionOrderItem.bom', function($q) {
                $q->where('bom_type', 'single');
            })->orWhereHas('workOrder.items.scheduleItem.productionOrderItem.bom', function($q) {
                $q->where('bom_type', 'single');
            });
        })->availableForManufacturing()
          ->forBranch($user->branch_id)
          ->with(['bom.finishProduct', 'schedule.items.productionOrderItem.bom.finishProduct', 'workOrder.items.scheduleItem.productionOrderItem.bom.finishProduct', 'workOrder.team', 'workOrder.machine'])
          ->get();

        // Calculate already-manufactured qty per requisition
        $manufacturedQtyByRequisition = SingleProductManufacturing::where('branch_id', $user->branch_id)
            ->whereIn('requisition_id', $requisitions->pluck('id'))
            ->groupBy('requisition_id')
            ->selectRaw('requisition_id, SUM(quantity) as total_manufactured')
            ->pluck('total_manufactured', 'requisition_id')
            ->toArray();

        // Calculate manufactured qty per requisition AND per BOM (for per-BOM quantity tracking)
        $manufacturedQtyByReqAndBom = SingleProductManufacturing::where('branch_id', $user->branch_id)
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
                // Direct BOM: simple quantity check
                $reqQty = $req->quantity ?? 0;
                if ($reqQty <= 0) return true;
                $remaining = $reqQty - ($manufacturedQtyByRequisition[$req->id] ?? 0);
                return $remaining > 0;
            }

            if ($req->schedule) {
                // Schedule-based: sum per-BOM scheduled quantities and compare to manufactured
                $totalScheduled = 0;
                foreach ($req->schedule->items as $item) {
                    $bom = $item->productionOrderItem->bom ?? null;
                    if ($bom && $bom->bom_type === 'single') {
                        $totalScheduled += $item->scheduled_qty;
                    }
                }
                if ($totalScheduled <= 0) return true;
                $totalManufactured = array_sum($manufacturedQtyByReqAndBom[$req->id] ?? []);
                return $totalScheduled > $totalManufactured;
            }

            return true; // Keep if we can't determine
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
                    if ($bom && $bom->bom_type === 'single') {
                        $allBomIds->push($bom->id);
                    }
                }
            } elseif ($req->schedule) {
                foreach ($req->schedule->items as $item) {
                    $bom = $item->productionOrderItem->bom ?? null;
                    if ($bom && $bom->bom_type === 'single') {
                        $allBomIds->push($bom->id);
                    }
                }
            }
        }
        $allBomIds = $allBomIds->unique()->values();

        return view('pages.manufacturing.processing.single_manufacturing.create', [
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

            // Validate quantity against requisition remaining (per-BOM for schedule-based)
            $requisition = MaterialsRequisition::with(['schedule.items.productionOrderItem.bom'])->find($request->requisition_id);
            if ($requisition) {
                $bomId = $request->bom_id;
                $maxQty = null;

                if ($requisition->bom_id) {
                    // Direct BOM requisition: use requisition quantity
                    $maxQty = $requisition->quantity;
                    $alreadyManufactured = SingleProductManufacturing::where('requisition_id', $request->requisition_id)
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
                    $alreadyManufactured = SingleProductManufacturing::where('requisition_id', $request->requisition_id)
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
                    $alreadyManufactured = SingleProductManufacturing::where('requisition_id', $request->requisition_id)
                        ->where('bom_id', $bomId)
                        ->sum('quantity');
                } else {
                    $maxQty = $requisition->quantity;
                    $alreadyManufactured = SingleProductManufacturing::where('requisition_id', $request->requisition_id)
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

            // Save costs from BOM calculation (individual + totals)
            $model->total_material_cost = $costData['material_cost'];
            $model->labor_cost = $costData['labor_cost'];
            $model->power_cost = $costData['power_cost'];
            $model->other_cost = $costData['other_cost'];
            $model->total_other_cost = $costData['total_other_cost'];

            // Include margin in total cost
            $bom = ManufacturingBom::find($model->bom_id);
            $marginPerPiece = (float) ($bom->margin_per_piece ?? 0);
            $outputQty = $model->quantity * ($bom->actual_output ?? 1);
            $totalMargin = $marginPerPiece * $outputQty;

            $model->total_cost = $costData['total_cost'] + $totalMargin;
            $model->unit_cost = $costData['expected_output'] > 0 ? $model->total_cost / $costData['expected_output'] : 0;
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

    public function qcVerify(SingleProductManufacturing $spm)
    {
        $this->authorize('manufacturing.single_manufacturing.qc_verify');

        if (!$spm->canBeQcVerified()) {
            session()->flash('app_error', 'Only pending records can be QC verified.');
            return redirect()->back();
        }

        $spm->qcVerify();

        AuditLog::auditLog(Auth::id(), "QC verified single product manufacturing: " . $spm->reference);
        session()->flash('app_message', 'Single Product Manufacturing QC verified successfully');

        return redirect()->back();
    }

    public function post(SingleProductManufacturing $spm)
    {
        $this->authorize('manufacturing.single_manufacturing.post');

        if (!$spm->canBePosted()) {
            session()->flash('app_error', 'Only QC verified records can be posted.');
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

            // Add margin to total cost
            $marginPerPiece = (float) ($bom->margin_per_piece ?? 0);
            $totalMargin = $marginPerPiece * $outputQty;
            $totalCost += $totalMargin;

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

            // Update production order item produced qty
            $this->updateProductionOrderProducedQty($spm);

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

    /**
     * Update production order item's produced_qty and order's processed_qty
     * Traces: Manufacturing -> Requisition -> Schedule -> ScheduleItem -> ProductionOrderItem
     * Also handles BOM-only requisitions (no schedule) by matching bom_id directly.
     */
    private function updateProductionOrderProducedQty(SingleProductManufacturing $spm)
    {
        $spm->load('requisition.schedule');
        $requisition = $spm->requisition;
        if (!$requisition) {
            \Log::warning("updateProductionOrderProducedQty: No requisition for manufacturing {$spm->reference}");
            return;
        }

        $orderItem = null;

        if ($requisition->schedule_id) {
            // Path 1: Schedule-based - use schedule's production_order_id directly
            $schedule = $requisition->schedule;
            if ($schedule && $schedule->production_order_id) {
                $orderItem = \App\Models\ProductionOrderItem::where('production_order_id', $schedule->production_order_id)
                    ->where('bom_id', $spm->bom_id)
                    ->first();
            }
        }

        if (!$orderItem) {
            // Path 2: Fallback - find any open production order with matching bom_id
            $orderItem = \App\Models\ProductionOrderItem::where('bom_id', $spm->bom_id)
                ->whereHas('productionOrder', function ($q) use ($spm) {
                    $q->where('branch_id', $spm->branch_id)
                      ->whereIn('status', ['approved', 'pending']);
                })
                ->first();
        }

        if ($orderItem) {
            $orderItem->addProducedQty($spm->quantity);

            // Update parent order's processed_qty
            $order = $orderItem->productionOrder;
            if ($order) {
                $order->processed_qty = $order->items()->sum('produced_qty');
                $order->save();
            }
        } else {
            \Log::warning("updateProductionOrderProducedQty: No matching ProductionOrderItem for manufacturing {$spm->reference}, bom_id: {$spm->bom_id}, requisition: {$requisition->reference}, schedule_id: {$requisition->schedule_id}");
        }
    }
}
