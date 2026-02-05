<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use App\Models\ProductionOrderMaterial;
use App\Models\ManufacturingBom;
use App\Models\Branch;
use App\Classes\Manufacturing\ProductionCalculator;
use App\Http\Requests\Manufacturing\ProductionOrders\Index;
use App\Http\Requests\Manufacturing\ProductionOrders\Create;
use App\Http\Requests\Manufacturing\ProductionOrders\Store;
use App\Http\Requests\Manufacturing\ProductionOrders\Show;
use App\Http\Requests\Manufacturing\ProductionOrders\Approve;
use App\Http\Requests\Manufacturing\ProductionOrders\Close;
use App\Http\Requests\Manufacturing\ProductionOrders\Destroy;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Index $request)
    {
        $records = ProductionOrder::with(['branch', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.manufacturing.processing.production_orders.index', [
            'records' => $records
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Create $request)
    {
        $model = new ProductionOrder;
        $model->reference = ProductionOrder::generateNewNumber();
        $model->order_date = date('Y-m-d');
        $model->status = ProductionOrder::STATUS_PENDING;

        $user = Auth::user();

        return view('pages.manufacturing.processing.production_orders.create', [
            'model' => $model,
            'boms' => ManufacturingBom::active()->forBranch($user->branch_id)->orderBy('reference')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            $model = new ProductionOrder;
            $model->reference = $request->reference;
            $model->order_date = $request->order_date;
            $model->start_date = $request->start_date;
            $model->end_date = $request->end_date;
            $model->notes = $request->notes;
            $model->branch_id = $user->branch_id;
            $model->status = ProductionOrder::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            // Save order items
            $allMaterials = [];
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    if (!empty($item['bom_id']) && !empty($item['quantity'])) {
                        ProductionOrderItem::create([
                            'production_order_id' => $model->id,
                            'bom_id' => $item['bom_id'],
                            'quantity_to_produce' => $item['quantity'],
                            'scheduled_qty' => 0,
                            'produced_qty' => 0
                        ]);

                        // Calculate materials for this item
                        $materialsData = ProductionCalculator::calculateMaterials(
                            $item['bom_id'],
                            $item['quantity'],
                            $model->branch_id
                        );

                        foreach ($materialsData['materials'] as $material) {
                            $key = $material['product_id'] . '_' . $material['store_id'];
                            if (!isset($allMaterials[$key])) {
                                $allMaterials[$key] = $material;
                            } else {
                                $allMaterials[$key]['quantity'] += $material['quantity'];
                                $allMaterials[$key]['total_cost'] += $material['total_cost'];
                            }
                        }
                    }
                }
            }

            // Save aggregated materials
            foreach ($allMaterials as $material) {
                ProductionOrderMaterial::create([
                    'production_order_id' => $model->id,
                    'product_id' => $material['product_id'],
                    'source_store_id' => $material['store_id'],
                    'required_qty' => $material['quantity'],
                    'unit_cost' => $material['unit_cost'],
                    'total_cost' => $material['total_cost']
                ]);
            }

            DB::commit();

            $action = "Created production order: " . $model->reference;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Production Order created successfully');
            return redirect()->route('manufacturing.production_orders.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Something went wrong: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Show $request, ProductionOrder $production_order)
    {
        $production_order->load([
            'items.bom.finishProduct',
            'materials.product',
            'materials.sourceStore',
            'branch',
            'createdBy',
            'approvedBy'
        ]);

        return view('pages.manufacturing.processing.production_orders.show', [
            'record' => $production_order
        ]);
    }

    /**
     * Approve the production order.
     */
    public function approve(Approve $request, ProductionOrder $production_order)
    {
        if (!$production_order->isPending()) {
            session()->flash('app_error', 'Only pending orders can be approved.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            $production_order->approve();

            DB::commit();

            $action = "Approved production order: " . $production_order->reference;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Production Order approved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Something went wrong: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Close the production order.
     */
    public function close(Close $request, ProductionOrder $production_order)
    {
        if (!$production_order->isApproved()) {
            session()->flash('app_error', 'Only approved orders can be closed.');
            return redirect()->back();
        }

        // Check if any schedules are linked
        if ($production_order->schedules()->exists()) {
            session()->flash('app_error', 'Cannot close order. It has linked schedules.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            $production_order->close();

            DB::commit();

            $action = "Closed production order: " . $production_order->reference;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Production Order closed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Something went wrong: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destroy $request, ProductionOrder $production_order)
    {
        if (!$production_order->isPending()) {
            session()->flash('app_error', 'Only pending orders can be deleted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Delete related records
            ProductionOrderItem::where('production_order_id', $production_order->id)->delete();
            ProductionOrderMaterial::where('production_order_id', $production_order->id)->delete();

            $reference = $production_order->reference;
            $production_order->delete();

            DB::commit();

            $action = "Deleted production order: " . $reference;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Production Order deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error occurred: ' . $e->getMessage());
        }

        return redirect()->route('manufacturing.production_orders.index');
    }

    /**
     * Get BOM details via AJAX
     */
    public function getBomDetails(Request $request)
    {
        $bomId = $request->bom_id;
        $quantity = $request->quantity ?? 1;
        $branchId = Auth::user()->branch_id;

        $costData = ProductionCalculator::calculateProductionCost($bomId, $quantity, $branchId);
        $bom = ManufacturingBom::with('finishProduct')->find($bomId);

        return response()->json([
            'status' => true,
            'bom' => $bom,
            'cost_data' => $costData
        ]);
    }

    /**
     * Print production order
     */
    public function print(ProductionOrder $production_order)
    {
        $production_order->load([
            'items.bom.finishProduct',
            'materials.product',
            'materials.sourceStore',
            'branch',
            'createdBy',
            'approvedBy'
        ]);

        return view('pages.manufacturing.processing.production_orders.print', [
            'record' => $production_order
        ]);
    }
}
