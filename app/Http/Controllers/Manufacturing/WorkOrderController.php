<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacturing\WorkOrders\Index;
use App\Http\Requests\Manufacturing\WorkOrders\Create;
use App\Http\Requests\Manufacturing\WorkOrders\Store;
use App\Http\Requests\Manufacturing\WorkOrders\Show;
use App\Http\Requests\Manufacturing\WorkOrders\Post;
use App\Http\Requests\Manufacturing\WorkOrders\Destroy;
use App\Models\ManufacturingWorkOrder;
use App\Models\ManufacturingWorkOrderItem;
use App\Models\DailyManufacturingSchedule;
use App\Models\DailyManufacturingScheduleItem;
use App\Models\ManufacturingTeam;
use App\Models\ManufacturingMachine;
use App\Models\Product;
use App\Models\Store as StoreModel;
use App\Classes\Manufacturing\ProductionCalculator;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index(Index $request)
    {
        $user = Auth::user();
        $records = ManufacturingWorkOrder::with(['schedule', 'team', 'machine', 'branch', 'createdBy'])
            ->forBranch($user->branch_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('pages.manufacturing.processing.work_orders.index', [
            'records' => $records
        ]);
    }

    public function create(Create $request)
    {
        $model = new ManufacturingWorkOrder;
        $model->reference = ManufacturingWorkOrder::generateNewNumber();
        $model->work_order_date = date('Y-m-d');

        $user = Auth::user();

        // Only show RECEIVED daily schedules that still have unallocated items
        $schedules = DailyManufacturingSchedule::received()
            ->forBranch($user->branch_id)
            ->whereHas('items', function ($query) {
                $query->whereRaw('scheduled_qty > COALESCE((SELECT SUM(planned_qty) FROM manufacturing_work_order_items WHERE manufacturing_work_order_items.schedule_item_id = daily_manufacturing_schedule_items.id), 0)');
            })
            ->with('productionOrder')
            ->orderBy('reference')
            ->get();

        return view('pages.manufacturing.processing.work_orders.create', [
            'model' => $model,
            'schedules' => $schedules,
            'teams' => ManufacturingTeam::active()->forBranch($user->branch_id)->get(),
            'machines' => ManufacturingMachine::active()->forBranch($user->branch_id)->get(),
        ]);
    }

    public function store(Store $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            $request->validate([
                'reference' => 'required|unique:manufacturing_work_orders,reference',
                'work_order_date' => 'required|date',
                'daily_schedule_id' => 'required|exists:daily_manufacturing_schedules,id',
                'team_id' => 'nullable|exists:manufacturing_teams,id',
                'machine_id' => 'nullable|exists:manufacturing_machines,id',
                'items' => 'required|array|min:1',
            ]);

            // Filter to only selected items
            $selectedItems = collect($request->items)->filter(function ($item) {
                return !empty($item['selected']) && !empty($item['schedule_item_id']);
            })->values()->all();

            if (empty($selectedItems)) {
                throw new \Exception('Please select at least one item.');
            }

            // Validate schedule is received
            $schedule = DailyManufacturingSchedule::findOrFail($request->daily_schedule_id);
            if (!$schedule->isReceived()) {
                throw new \Exception('Selected schedule has not been received yet.');
            }

            // Validate planned qty does not exceed remaining qty for each item
            foreach ($selectedItems as $item) {
                $scheduleItem = DailyManufacturingScheduleItem::findOrFail($item['schedule_item_id']);
                if ($scheduleItem->schedule_id != $schedule->id) {
                    throw new \Exception('Schedule item does not belong to the selected schedule.');
                }

                // Calculate already allocated qty from existing work orders
                $allocatedQty = ManufacturingWorkOrderItem::where('schedule_item_id', $scheduleItem->id)
                    ->sum('planned_qty');
                $remainingQty = (float) $scheduleItem->scheduled_qty - (float) $allocatedQty;

                if ($item['planned_qty'] > $remainingQty) {
                    throw new \Exception("Planned quantity ({$item['planned_qty']}) exceeds remaining quantity ({$remainingQty}).");
                }
            }

            $model = new ManufacturingWorkOrder;
            $model->reference = $request->reference;
            $model->work_order_date = $request->work_order_date;
            $model->daily_schedule_id = $request->daily_schedule_id;
            $model->machine_id = $request->machine_id;
            $model->team_id = $request->team_id;
            $model->notes = $request->notes;
            $model->branch_id = $user->branch_id;
            $model->status = ManufacturingWorkOrder::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            // Save work order items
            foreach ($selectedItems as $item) {
                ManufacturingWorkOrderItem::create([
                    'work_order_id' => $model->id,
                    'schedule_item_id' => $item['schedule_item_id'],
                    'planned_qty' => $item['planned_qty'],
                ]);
            }

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created work order: " . $model->reference);
            session()->flash('app_message', 'Work Order created successfully');
            return redirect()->route('manufacturing.work_orders.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(Show $request, ManufacturingWorkOrder $work_order)
    {
        $work_order->load([
            'schedule.productionOrder',
            'items.scheduleItem.productionOrderItem.bom.finishProduct',
            'team', 'machine', 'branch',
            'createdBy', 'postedBy',
            'requisitions',
        ]);

        // Calculate required materials
        $materials = $work_order->getRequiredMaterials();

        // Add product/store names for display
        foreach ($materials as &$material) {
            $product = Product::find($material['product_id']);
            $store = StoreModel::find($material['store_id']);
            $material['product_name'] = $product ? $product->name : '';
            $material['store_name'] = $store ? $store->name : '';
        }

        return view('pages.manufacturing.processing.work_orders.show', [
            'record' => $work_order,
            'materials' => $materials,
        ]);
    }

    public function post(Post $request, ManufacturingWorkOrder $work_order)
    {
        if (!$work_order->canBePosted()) {
            session()->flash('app_error', 'Only pending work orders can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            $work_order->post();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Posted work order: " . $work_order->reference);
            session()->flash('app_message', 'Work Order posted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(Destroy $request, ManufacturingWorkOrder $work_order)
    {
        if (!$work_order->isPending()) {
            session()->flash('app_error', 'Only pending work orders can be deleted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            ManufacturingWorkOrderItem::where('work_order_id', $work_order->id)->delete();
            $reference = $work_order->reference;
            $work_order->delete();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Deleted work order: " . $reference);
            session()->flash('app_message', 'Work Order deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('manufacturing.work_orders.index');
    }

    /**
     * AJAX: Get schedule items for selected schedule.
     */
    public function getScheduleItems(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:daily_manufacturing_schedules,id',
        ]);

        $schedule = DailyManufacturingSchedule::with([
            'items.productionOrderItem.bom.finishProduct',
            'items.productionOrderItem.bom.materials.product',
            'items.productionOrderItem.bom.materials.sourceStore',
        ])->find($request->schedule_id);

        $items = [];

        // Calculate how much of each schedule item has been allocated to existing work orders
        $existingAllocations = ManufacturingWorkOrderItem::whereHas('workOrder', function ($q) use ($schedule) {
            $q->where('daily_schedule_id', $schedule->id);
        })->get()->groupBy('schedule_item_id');

        foreach ($schedule->items as $item) {
            $bom = $item->productionOrderItem->bom ?? null;
            $allocatedQty = isset($existingAllocations[$item->id])
                ? $existingAllocations[$item->id]->sum('planned_qty')
                : 0;
            $remainingQty = (float) $item->scheduled_qty - (float) $allocatedQty;

            if ($remainingQty <= 0) continue;

            // Build per-unit bom_materials array for dynamic JS recalculation
            $bomMaterials = [];
            if ($bom && $bom->materials) {
                foreach ($bom->materials as $bomMat) {
                    $bomMaterials[] = [
                        'product_id' => $bomMat->product_id,
                        'product_name' => $bomMat->product->name ?? '',
                        'store_id' => $bomMat->store_id,
                        'store_name' => $bomMat->sourceStore->name ?? '',
                        'bom_qty' => (float) $bomMat->quantity,
                    ];
                }
            }

            $items[] = [
                'schedule_item_id' => $item->id,
                'bom_reference' => $bom->reference ?? '',
                'finish_product' => $bom->finishProduct->name ?? '',
                'bom_type' => $bom->bom_type ?? '',
                'scheduled_qty' => (float) $item->scheduled_qty,
                'remaining_qty' => $remainingQty,
                'bom_materials' => $bomMaterials,
            ];
        }

        return response()->json([
            'status' => true,
            'items' => $items,
        ]);
    }
}
