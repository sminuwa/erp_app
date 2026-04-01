<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacturing\DailySchedules\Index;
use App\Http\Requests\Manufacturing\DailySchedules\Create;
use App\Http\Requests\Manufacturing\DailySchedules\Store;
use App\Http\Requests\Manufacturing\DailySchedules\Show;
use App\Http\Requests\Manufacturing\DailySchedules\Approve;
use App\Http\Requests\Manufacturing\DailySchedules\Receive;
use App\Http\Requests\Manufacturing\DailySchedules\Destroy;
use App\Models\DailyManufacturingSchedule;
use App\Models\DailyManufacturingScheduleItem;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use App\Classes\Manufacturing\InventoryReservationService;
use App\Classes\Manufacturing\ProductionCalculator;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DailyScheduleController extends Controller
{
    public function index(Index $request)
    {
        $user = Auth::user();
        $records = DailyManufacturingSchedule::with(['productionOrder', 'branch', 'createdBy'])
            ->forBranch($user->branch_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('pages.manufacturing.processing.schedules.index', [
            'records' => $records
        ]);
    }

    public function create(Create $request)
    {
        $model = new DailyManufacturingSchedule;
        $model->reference = DailyManufacturingSchedule::generateNewNumber();
        $model->schedule_date = date('Y-m-d');

        $user = Auth::user();

        // Get approved orders that have at least one item with unscheduled qty
        $orders = ProductionOrder::approved()
            ->forBranch($user->branch_id)
            ->with(['items.bom.finishProduct'])
            ->whereHas('items', function ($q) {
                $q->whereRaw('quantity_to_produce > scheduled_qty');
            })
            ->orderBy('reference')
            ->get();

        return view('pages.manufacturing.processing.schedules.create', [
            'model' => $model,
            'productionOrders' => $orders,
        ]);
    }

    public function store(Store $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Validate: no duplicate order items
            $orderItemIds = array_column($request->items, 'order_item_id');
            if (count($orderItemIds) !== count(array_unique($orderItemIds))) {
                throw new \Exception('Duplicate order items are not allowed. Each BOM line can only appear once.');
            }

            // Validate: qty does not exceed remaining for each item
            // Also aggregate materials for availability check
            $allMaterials = [];
            foreach ($request->items as $item) {
                if (empty($item['order_item_id']) || empty($item['quantity'])) {
                    continue;
                }

                $orderItem = ProductionOrderItem::with('bom')->find($item['order_item_id']);
                if (!$orderItem) {
                    throw new \Exception('Invalid order item selected.');
                }

                $remaining = $orderItem->quantity_to_produce - $orderItem->scheduled_qty;
                if ($item['quantity'] > $remaining) {
                    $bomRef = $orderItem->bom->reference ?? 'Unknown';
                    throw new \Exception("Schedule quantity ({$item['quantity']}) for BOM {$bomRef} exceeds remaining quantity ({$remaining}). Already scheduled: {$orderItem->scheduled_qty}");
                }

                // Calculate required materials for availability check
                $materialsData = ProductionCalculator::calculateMaterials(
                    $orderItem->bom_id,
                    $item['quantity'],
                    $user->branch_id
                );

                foreach ($materialsData['materials'] as $material) {
                    $key = $material['product_id'] . '_' . $material['store_id'];
                    if (!isset($allMaterials[$key])) {
                        $allMaterials[$key] = [
                            'product_id' => $material['product_id'],
                            'store_id' => $material['store_id'],
                            'qty' => $material['quantity'],
                        ];
                    } else {
                        $allMaterials[$key]['qty'] += $material['quantity'];
                    }
                }
            }

            // Check raw material availability before allowing creation
            if (!empty($allMaterials)) {
                $availability = InventoryReservationService::validateAvailability(array_values($allMaterials));
                if (!$availability['status']) {
                    $shortageMsg = "Insufficient raw materials:\n";
                    if (isset($availability['insufficient']) && is_array($availability['insufficient'])) {
                        foreach ($availability['insufficient'] as $shortage) {
                            $product = \App\Models\Product::find($shortage['product_id']);
                            $productName = $product ? $product->name : "Product #{$shortage['product_id']}";
                            $shortageMsg .= "- {$productName}: Required {$shortage['required']}, Available {$shortage['available']}\n";
                        }
                    } else {
                        $shortageMsg .= $availability['message'] ?? 'One or more materials are insufficient.';
                    }
                    throw new \Exception($shortageMsg);
                }
            }

            $model = new DailyManufacturingSchedule;
            $model->reference = $request->reference;
            $model->schedule_date = $request->schedule_date;
            $model->production_order_id = $request->order_id;
            $model->notes = $request->notes;
            $model->branch_id = $user->branch_id;
            $model->status = DailyManufacturingSchedule::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            // Save schedule items
            foreach ($request->items as $item) {
                if (!empty($item['order_item_id']) && !empty($item['quantity'])) {
                    DailyManufacturingScheduleItem::create([
                        'schedule_id' => $model->id,
                        'production_order_item_id' => $item['order_item_id'],
                        'scheduled_qty' => $item['quantity']
                    ]);
                }
            }

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created daily schedule: " . $model->reference);
            session()->flash('app_message', 'Daily Schedule created successfully');
            return redirect()->route('manufacturing.schedules.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(Show $request, DailyManufacturingSchedule $schedule)
    {
        $schedule->load([
            'productionOrder',
            'items.productionOrderItem.bom.finishProduct',
            'branch', 'createdBy', 'approvedBy', 'receivedBy'
        ]);

        return view('pages.manufacturing.processing.schedules.show', [
            'record' => $schedule
        ]);
    }

    public function edit(DailyManufacturingSchedule $schedule)
    {
        $this->authorize('manufacturing.schedules.edit');

        if (!$schedule->isPending()) {
            session()->flash('app_error', 'Only pending schedules can be edited.');
            return redirect()->back();
        }

        $schedule->load(['items.productionOrderItem.bom.finishProduct']);

        $user = Auth::user();

        // Get approved orders that have unscheduled items OR the current order
        $orders = ProductionOrder::approved()
            ->forBranch($user->branch_id)
            ->with(['items.bom.finishProduct'])
            ->where(function ($q) use ($schedule) {
                $q->where('id', $schedule->production_order_id)
                  ->orWhereHas('items', function ($q2) {
                      $q2->whereRaw('quantity_to_produce > scheduled_qty');
                  });
            })
            ->orderBy('reference')
            ->get();

        return view('pages.manufacturing.processing.schedules.edit', [
            'model' => $schedule,
            'productionOrders' => $orders,
        ]);
    }

    public function update(Request $request, DailyManufacturingSchedule $schedule)
    {
        $this->authorize('manufacturing.schedules.edit');

        if (!$schedule->isPending()) {
            session()->flash('app_error', 'Only pending schedules can be edited.');
            return redirect()->back();
        }

        $request->validate([
            'schedule_date' => 'required|date',
            'order_id' => 'required|exists:production_orders,id',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:production_order_items,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Validate: no duplicate order items
            $orderItemIds = array_column($request->items, 'order_item_id');
            if (count($orderItemIds) !== count(array_unique($orderItemIds))) {
                throw new \Exception('Duplicate order items are not allowed. Each BOM line can only appear once.');
            }

            // Get existing schedule items to calculate remaining correctly
            $existingItems = $schedule->items->keyBy('production_order_item_id');

            // Validate qty and aggregate materials for availability check
            $allMaterials = [];
            foreach ($request->items as $item) {
                if (empty($item['order_item_id']) || empty($item['quantity'])) {
                    continue;
                }

                $orderItem = ProductionOrderItem::with('bom')->find($item['order_item_id']);
                if (!$orderItem) {
                    throw new \Exception('Invalid order item selected.');
                }

                // Add back the qty from existing schedule item (since we're replacing it)
                $existingQty = isset($existingItems[$item['order_item_id']])
                    ? $existingItems[$item['order_item_id']]->scheduled_qty
                    : 0;
                $remaining = ($orderItem->quantity_to_produce - $orderItem->scheduled_qty) + $existingQty;

                if ($item['quantity'] > $remaining) {
                    $bomRef = $orderItem->bom->reference ?? 'Unknown';
                    throw new \Exception("Schedule quantity ({$item['quantity']}) for BOM {$bomRef} exceeds remaining quantity ({$remaining}).");
                }

                // Calculate required materials for availability check
                $materialsData = ProductionCalculator::calculateMaterials(
                    $orderItem->bom_id,
                    $item['quantity'],
                    $user->branch_id
                );

                foreach ($materialsData['materials'] as $material) {
                    $key = $material['product_id'] . '_' . $material['store_id'];
                    if (!isset($allMaterials[$key])) {
                        $allMaterials[$key] = [
                            'product_id' => $material['product_id'],
                            'store_id' => $material['store_id'],
                            'qty' => $material['quantity'],
                        ];
                    } else {
                        $allMaterials[$key]['qty'] += $material['quantity'];
                    }
                }
            }

            // Check raw material availability
            if (!empty($allMaterials)) {
                $availability = InventoryReservationService::validateAvailability(array_values($allMaterials));
                if (!$availability['status']) {
                    $shortageMsg = "Insufficient raw materials:\n";
                    if (isset($availability['insufficient']) && is_array($availability['insufficient'])) {
                        foreach ($availability['insufficient'] as $shortage) {
                            $product = \App\Models\Product::find($shortage['product_id']);
                            $productName = $product ? $product->name : "Product #{$shortage['product_id']}";
                            $shortageMsg .= "- {$productName}: Required {$shortage['required']}, Available {$shortage['available']}\n";
                        }
                    } else {
                        $shortageMsg .= $availability['message'] ?? 'One or more materials are insufficient.';
                    }
                    throw new \Exception($shortageMsg);
                }
            }

            // Update schedule header
            $schedule->schedule_date = $request->schedule_date;
            $schedule->production_order_id = $request->order_id;
            $schedule->notes = $request->notes;
            $schedule->save();

            // Delete old items and create new ones
            DailyManufacturingScheduleItem::where('schedule_id', $schedule->id)->delete();

            foreach ($request->items as $item) {
                if (!empty($item['order_item_id']) && !empty($item['quantity'])) {
                    DailyManufacturingScheduleItem::create([
                        'schedule_id' => $schedule->id,
                        'production_order_item_id' => $item['order_item_id'],
                        'scheduled_qty' => $item['quantity']
                    ]);
                }
            }

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Updated daily schedule: " . $schedule->reference);
            session()->flash('app_message', 'Daily Schedule updated successfully');
            return redirect()->route('manufacturing.schedules.show', $schedule->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function approve(Approve $request, DailyManufacturingSchedule $schedule)
    {
        if (!$schedule->isPending()) {
            session()->flash('app_error', 'Only pending schedules can be approved.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Validate inventory availability and make reservations
            $schedule->load(['items.productionOrderItem.bom']);

            foreach ($schedule->items as $item) {
                $materialsData = ProductionCalculator::calculateMaterials(
                    $item->productionOrderItem->bom_id,
                    $item->scheduled_qty,
                    $schedule->branch_id
                );

                // Reserve inventory
                $reservationItems = [];
                foreach ($materialsData['materials'] as $material) {
                    $reservationItems[] = [
                        'product_id' => $material['product_id'],
                        'store_id' => $material['store_id'],
                        'qty' => $material['quantity']
                    ];
                }

                $result = InventoryReservationService::reserveMultiple(
                    'daily_schedule',
                    $schedule->id,
                    $reservationItems
                );

                if (!$result['status']) {
                    throw new \Exception($result['message']);
                }

                // Update order item scheduled quantity
                $item->productionOrderItem->scheduled_qty += $item->scheduled_qty;
                $item->productionOrderItem->save();
            }

            $schedule->approve();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Approved daily schedule: " . $schedule->reference);
            session()->flash('app_message', 'Daily Schedule approved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Receive the schedule (marks it as ready for work orders).
     */
    public function receive(Receive $request, DailyManufacturingSchedule $schedule)
    {
        if (!$schedule->canBeReceived()) {
            session()->flash('app_error', 'Only approved schedules can be received.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            $schedule->receive();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Received daily schedule: " . $schedule->reference);
            session()->flash('app_message', 'Daily Schedule received successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(Destroy $request, DailyManufacturingSchedule $schedule)
    {
        if (!$schedule->isPending()) {
            session()->flash('app_error', 'Only pending schedules can be deleted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            DailyManufacturingScheduleItem::where('schedule_id', $schedule->id)->delete();
            $reference = $schedule->reference;
            $schedule->delete();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Deleted daily schedule: " . $reference);
            session()->flash('app_message', 'Daily Schedule deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('manufacturing.schedules.index');
    }

    /**
     * AJAX: Get order items with scheduling data and material requirements.
     */
    public function getOrderItems(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:production_orders,id',
            'schedule_id' => 'nullable|exists:daily_manufacturing_schedules,id'
        ]);

        $order = ProductionOrder::with(['items.bom.finishProduct'])->find($request->order_id);
        $branchId = Auth::user()->branch_id;

        $items = [];
        $allMaterials = [];

        foreach ($order->items as $item) {
            $scheduledQty = (float) $item->scheduled_qty;

            // If editing, add back the qty from the current schedule so it shows as available
            if ($request->schedule_id) {
                $currentScheduleQty = DailyManufacturingScheduleItem::where('schedule_id', $request->schedule_id)
                    ->where('production_order_item_id', $item->id)
                    ->sum('scheduled_qty');
                $scheduledQty -= (float) $currentScheduleQty;
            }

            $remaining = (float) $item->quantity_to_produce - $scheduledQty;

            $items[] = [
                'id' => $item->id,
                'bom_id' => $item->bom_id,
                'bom_ref' => $item->bom->reference ?? '',
                'product' => $item->bom->finishProduct->name ?? '',
                'bom_type' => $item->bom->bom_type ?? '',
                'qty_to_produce' => (float) $item->quantity_to_produce,
                'scheduled_qty' => $scheduledQty,
                'remaining' => $remaining,
                'output' => (float) $item->quantity_to_produce * ($item->bom->actual_output ?? 1),
            ];

            // Calculate materials for the full remaining qty (used to display at bottom)
            if ($remaining > 0) {
                $materialsData = ProductionCalculator::calculateMaterials($item->bom_id, $remaining, $branchId);
                foreach ($materialsData['materials'] as $material) {
                    $key = $material['product_id'] . '_' . $material['store_id'];
                    if (!isset($allMaterials[$key])) {
                        $allMaterials[$key] = $material;
                        // Add product name for display
                        $product = \App\Models\Product::find($material['product_id']);
                        $store = \App\Models\Store::find($material['store_id']);
                        $allMaterials[$key]['product_name'] = $product ? $product->name : '';
                        $allMaterials[$key]['store_name'] = $store ? $store->name : '';
                    } else {
                        $allMaterials[$key]['quantity'] += $material['quantity'];
                        $allMaterials[$key]['total_cost'] += $material['total_cost'];
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
            'data' => $items,
            'materials' => array_values($allMaterials),
        ]);
    }
}
