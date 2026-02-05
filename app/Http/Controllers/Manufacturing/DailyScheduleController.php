<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
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
    public function index(Request $request)
    {
        $this->authorize('manufacturing.schedules.index');

        $records = DailyManufacturingSchedule::with(['productionOrder', 'branch', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.manufacturing.processing.schedules.index', [
            'records' => $records
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('manufacturing.schedules.create');

        $model = new DailyManufacturingSchedule;
        $model->reference = DailyManufacturingSchedule::generateNewNumber();
        $model->schedule_date = date('Y-m-d');

        $user = Auth::user();
        $orders = ProductionOrder::approved()->forBranch($user->branch_id)->orderBy('reference')->get();

        return view('pages.manufacturing.processing.schedules.create', [
            'model' => $model,
            'productionOrders' => $orders,
            'teams' => \App\Models\ManufacturingTeam::active()->forBranch($user->branch_id)->get(),
            'machines' => \App\Models\ManufacturingMachine::active()->forBranch($user->branch_id)->get(),
            'boms' => \App\Models\ManufacturingBom::active()->forBranch($user->branch_id)->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.schedules.create');

        $request->validate([
            'reference' => 'required|unique:daily_manufacturing_schedules,reference',
            'schedule_date' => 'required|date',
            'order_id' => 'required|exists:production_orders,id',
            'items' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $model = new DailyManufacturingSchedule;
            $model->reference = $request->reference;
            $model->schedule_date = $request->schedule_date;
            $model->order_id = $request->order_id;
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
                        'order_item_id' => $item['order_item_id'],
                        'quantity' => $item['quantity']
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

    public function show(DailyManufacturingSchedule $schedule)
    {
        $this->authorize('manufacturing.schedules.show');

        $schedule->load(['productionOrder', 'items.orderItem.bom.finishProduct', 'branch', 'createdBy', 'approvedBy']);

        return view('pages.manufacturing.processing.schedules.show', [
            'record' => $schedule
        ]);
    }

    public function approve(DailyManufacturingSchedule $schedule)
    {
        $this->authorize('manufacturing.schedules.approve');

        if (!$schedule->isPending()) {
            session()->flash('app_error', 'Only pending schedules can be approved.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Validate inventory availability and make reservations
            $schedule->load(['items.orderItem.bom']);

            foreach ($schedule->items as $item) {
                $materialsData = ProductionCalculator::calculateMaterials(
                    $item->orderItem->bom_id,
                    $item->quantity,
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
                $item->orderItem->scheduled_qty += $item->quantity;
                $item->orderItem->save();
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

    public function destroy(DailyManufacturingSchedule $schedule)
    {
        $this->authorize('manufacturing.schedules.delete');

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
}
