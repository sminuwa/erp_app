<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\MaterialsRequisition;
use App\Models\MaterialsRequisitionItem;
use App\Models\DailyManufacturingSchedule;
use App\Models\ManufacturingBom;
use App\Classes\Manufacturing\ProductionCalculator;
use App\Classes\Manufacturing\InventoryReservationService;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Manufacturing\Requisitions\Index;
use App\Http\Requests\Manufacturing\Requisitions\Create;
use App\Http\Requests\Manufacturing\Requisitions\Store;
use App\Http\Requests\Manufacturing\Requisitions\Show;
use App\Http\Requests\Manufacturing\Requisitions\Approve;
use App\Http\Requests\Manufacturing\Requisitions\Issue;
use App\Http\Requests\Manufacturing\Requisitions\Receive;
use App\Http\Requests\Manufacturing\Requisitions\Verify;
use App\Http\Requests\Manufacturing\Requisitions\Destroy;
use App\Models\ManufacturingWorkOrder;

class MaterialsRequisitionController extends Controller
{
    public function index(Index $request)
    {
        $this->authorize('manufacturing.requisitions.index');

        $records = MaterialsRequisition::with(['schedule', 'bom', 'branch', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.manufacturing.processing.requisitions.index', [
            'records' => $records
        ]);
    }

    public function create(Create $request)
    {
        $this->authorize('manufacturing.requisitions.create');

        $model = new MaterialsRequisition;
        $model->reference = MaterialsRequisition::generateNewNumber();
        $model->requisition_date = date('Y-m-d');

        $user = Auth::user();

        // Exclude schedules already linked to a requisition
        $usedScheduleIds = MaterialsRequisition::whereNotNull('schedule_id')->pluck('schedule_id');

        // Get posted work orders for work-order-based requisitions
        $usedWorkOrderIds = MaterialsRequisition::whereNotNull('work_order_id')->pluck('work_order_id');
        $workOrders = ManufacturingWorkOrder::where('status', ManufacturingWorkOrder::STATUS_POSTED)
            ->where('branch_id', $user->branch_id)
            ->whereNotIn('id', $usedWorkOrderIds)
            ->with('schedule')
            ->get();

        return view('pages.manufacturing.processing.requisitions.create', [
            'model' => $model,
            'workOrders' => $workOrders,
            'schedules' => DailyManufacturingSchedule::approved()->forBranch($user->branch_id)->whereNotIn('id', $usedScheduleIds)->get(),
            'boms' => ManufacturingBom::active()->forBranch($user->branch_id)->get(),
            'products' => \App\Models\Product::orderBy('name')->get(),
            'stores' => \App\Models\Store::where('branch_id', $user->branch_id)->orderBy('name')->get()
        ]);
    }

    public function store(Store $request)
    {
        $this->authorize('manufacturing.requisitions.create');

        $request->validate([
            'reference' => 'required|unique:materials_requisitions,reference',
            'requisition_date' => 'required|date',
            'schedule_id' => 'nullable|exists:daily_manufacturing_schedules,id|required_without_all:bom_id,work_order_id',
            'bom_id' => 'nullable|exists:manufacturing_boms,id|required_without_all:schedule_id,work_order_id',
            'work_order_id' => 'nullable|exists:manufacturing_work_orders,id',
            'quantity' => 'nullable|numeric|min:0.0001',
        ], [
            'schedule_id.required_without_all' => 'Either a Daily Schedule, Work Order, or BOM must be selected.',
            'bom_id.required_without_all' => 'Either a Daily Schedule, Work Order, or BOM must be selected.',
        ]);

        $filledSources = collect(['schedule_id', 'bom_id', 'work_order_id'])->filter(fn($f) => $request->filled($f));
        if ($filledSources->count() > 1) {
            return redirect()->back()->withInput()->withErrors([
                'schedule_id' => 'Select only one source: Daily Schedule, Work Order, or BOM.'
            ]);
        }

        // Prevent duplicate requisitions for the same schedule
        if ($request->filled('schedule_id')) {
            $existing = MaterialsRequisition::where('schedule_id', $request->schedule_id)->exists();
            if ($existing) {
                return redirect()->back()->withInput()->withErrors([
                    'schedule_id' => 'A requisition already exists for the selected schedule.'
                ]);
            }
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $model = new MaterialsRequisition;
            $model->reference = $request->reference;
            $model->requisition_date = $request->requisition_date;
            $model->schedule_id = $request->schedule_id;
            $model->work_order_id = $request->work_order_id;
            $model->bom_id = $request->bom_id;
            // For schedule-based requisitions, auto-calculate total quantity from schedule items
            if ($request->schedule_id) {
                $schedule = DailyManufacturingSchedule::with('items')->find($request->schedule_id);
                $model->quantity = $schedule ? $schedule->items->sum('scheduled_qty') : ($request->quantity ?? 1);
            } else {
                $model->quantity = $request->quantity ?? 1;
            }
            $model->notes = $request->notes;
            $model->branch_id = $user->branch_id;
            $model->status = MaterialsRequisition::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            // Calculate and save materials based on work order, schedule, or BOM
            $materials = [];
            if ($model->work_order_id) {
                // Get materials from work order items
                $workOrder = ManufacturingWorkOrder::with('items.scheduleItem.productionOrderItem.bom')->find($model->work_order_id);
                foreach ($workOrder->items as $woItem) {
                    $materialsData = ProductionCalculator::calculateMaterials(
                        $woItem->scheduleItem->productionOrderItem->bom_id,
                        $woItem->planned_qty,
                        $model->branch_id
                    );
                    foreach ($materialsData['materials'] as $m) {
                        $key = $m['product_id'] . '_' . $m['store_id'];
                        if (!isset($materials[$key])) {
                            $materials[$key] = $m;
                        } else {
                            $materials[$key]['quantity'] += $m['quantity'];
                            $materials[$key]['total_cost'] += $m['total_cost'];
                        }
                    }
                }
            } elseif ($model->schedule_id) {
                // Get materials from schedule's BOMs
                $schedule = DailyManufacturingSchedule::with('items.productionOrderItem.bom')->find($model->schedule_id);
                foreach ($schedule->items as $item) {
                    $materialsData = ProductionCalculator::calculateMaterials(
                        $item->productionOrderItem->bom_id,
                        $item->scheduled_qty,
                        $model->branch_id
                    );
                    foreach ($materialsData['materials'] as $m) {
                        $key = $m['product_id'] . '_' . $m['store_id'];
                        if (!isset($materials[$key])) {
                            $materials[$key] = $m;
                        } else {
                            $materials[$key]['quantity'] += $m['quantity'];
                            $materials[$key]['total_cost'] += $m['total_cost'];
                        }
                    }

                    // Mark this schedule item as fully requisitioned
                    $item->requisitioned_qty = $item->scheduled_qty;
                    $item->save();
                }
            } elseif ($model->bom_id) {
                $materialsData = ProductionCalculator::calculateMaterials(
                    $model->bom_id,
                    $model->quantity,
                    $model->branch_id
                );
                $materials = $materialsData['materials'];
            } elseif ($request->has('items')) {
                // Manual item entry from form
                foreach ($request->items as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity'])) {
                        $materials[] = [
                            'product_id' => $item['product_id'],
                            'store_id' => $item['store_id'] ?? null,
                            'quantity' => $item['quantity'],
                            'unit_cost' => $item['unit_cost'] ?? 0,
                            'total_cost' => ($item['unit_cost'] ?? 0) * $item['quantity']
                        ];
                    }
                }
            }

            // Save materials
            foreach ($materials as $m) {
                MaterialsRequisitionItem::create([
                    'requisition_id' => $model->id,
                    'product_id' => $m['product_id'],
                    'source_store_id' => $m['store_id'],
                    'required_qty' => $m['quantity'],
                    'issued_qty' => 0,
                    'received_qty' => 0,
                    'unit_cost' => $m['unit_cost']
                ]);
            }

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created materials requisition: " . $model->reference);
            session()->flash('app_message', 'Materials Requisition created successfully');
            return redirect()->route('manufacturing.requisitions.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(Show $request, MaterialsRequisition $requisition)
    {
        $this->authorize('manufacturing.requisitions.show');

        $requisition->load([
            'schedule.productionOrder',
            'schedule.items.productionOrderItem.bom.finishProduct',
            'workOrder.schedule', 'workOrder.machine', 'workOrder.team',
            'bom.finishProduct', 'bom.outputStore', 'bom.materials.product',
            'items.product', 'items.sourceStore',
            'branch', 'createdBy', 'approvedBy', 'issuedBy', 'verifiedBy', 'receivedBy',
            'singleProductManufacturing', 'batchProductions'
        ]);

        // Calculate manufacturing progress
        $totalManufactured = 0;
        if ($requisition->bom && $requisition->bom->bom_type === 'single') {
            $totalManufactured = $requisition->singleProductManufacturing->sum('quantity');
        } elseif ($requisition->bom && $requisition->bom->bom_type === 'batch') {
            $totalManufactured = $requisition->batchProductions->sum('quantity');
        }

        return view('pages.manufacturing.processing.requisitions.show', [
            'record' => $requisition,
            'totalManufactured' => $totalManufactured,
            'remainingQty' => max(0, ($requisition->quantity ?? 0) - $totalManufactured),
        ]);
    }

    public function approve(Approve $request, MaterialsRequisition $requisition)
    {
        $this->authorize('manufacturing.requisitions.approve');

        if (!$requisition->isPending()) {
            session()->flash('app_error', 'Only pending requisitions can be approved.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // If using BOM directly (not from schedule), make inventory reservations
            if ($requisition->bom_id && !$requisition->schedule_id) {
                $items = [];
                foreach ($requisition->items as $item) {
                    $items[] = [
                        'product_id' => $item->product_id,
                        'store_id' => $item->store_id,
                        'qty' => $item->required_qty
                    ];
                }

                $result = InventoryReservationService::reserveMultiple(
                    'requisition',
                    $requisition->id,
                    $items
                );

                if (!$result['status']) {
                    throw new \Exception($result['message']);
                }
            }

            $requisition->status = MaterialsRequisition::STATUS_APPROVED;
            $requisition->approved_by = Auth::id();
            $requisition->approved_at = now();
            $requisition->save();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Approved materials requisition: " . $requisition->reference);
            session()->flash('app_message', 'Materials Requisition approved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function issue(Issue $request, MaterialsRequisition $requisition)
    {
        $this->authorize('manufacturing.requisitions.issue');

        if ($requisition->status !== MaterialsRequisition::STATUS_APPROVED) {
            session()->flash('app_error', 'Only approved requisitions can be issued.');
            return redirect()->back();
        }

        $requisition->status = MaterialsRequisition::STATUS_ISSUED;
        $requisition->issued_by = Auth::id();
        $requisition->issued_at = now();
        $requisition->save();

        // Update issued quantities
        foreach ($requisition->items as $item) {
            $item->issued_qty = $item->required_qty;
            $item->save();
        }

        AuditLog::auditLog(Auth::id(), "Issued materials requisition: " . $requisition->reference);
        session()->flash('app_message', 'Materials Requisition issued successfully');

        return redirect()->back();
    }

    public function receive(Receive $request, MaterialsRequisition $requisition)
    {
        $this->authorize('manufacturing.requisitions.receive');

        if (!$requisition->canBeReceived()) {
            session()->flash('app_error', 'Only verified requisitions can be received.');
            return redirect()->back();
        }

        $requisition->status = MaterialsRequisition::STATUS_RECEIVED;
        $requisition->received_by = Auth::id();
        $requisition->received_at = now();
        $requisition->save();

        // Update received quantities
        foreach ($requisition->items as $item) {
            $item->received_qty = $item->issued_qty;
            $item->save();
        }

        AuditLog::auditLog(Auth::id(), "Received materials requisition: " . $requisition->reference);
        session()->flash('app_message', 'Materials Requisition received successfully');

        return redirect()->back();
    }

    public function verify(Verify $request, MaterialsRequisition $requisition)
    {
        if (!$requisition->canBeVerified()) {
            session()->flash('app_error', 'Only issued requisitions can be verified.');
            return redirect()->back();
        }

        $requisition->verify();

        AuditLog::auditLog(Auth::id(), "Verified materials requisition: " . $requisition->reference);
        session()->flash('app_message', 'Materials Requisition verified successfully');

        return redirect()->back();
    }

    public function destroy(Destroy $request, MaterialsRequisition $requisition)
    {
        $this->authorize('manufacturing.requisitions.delete');

        if (!$requisition->isPending()) {
            session()->flash('app_error', 'Only pending requisitions can be deleted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            MaterialsRequisitionItem::where('requisition_id', $requisition->id)->delete();
            $reference = $requisition->reference;
            $requisition->delete();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Deleted materials requisition: " . $reference);
            session()->flash('app_message', 'Materials Requisition deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('manufacturing.requisitions.index');
    }
}
