<?php

namespace App\Http\Controllers;

use App\Classes\Transaction;
use App\Models\IntersiteAdditionalCost;
use App\Models\IntersiteAdditionalCostItem;
use App\Models\IntersiteTransfer;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IntersiteAdditionalCostController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('intersite.additional_cost.index');

        $user = Auth::user();
        $records = IntersiteAdditionalCost::with(['intersiteTransfer', 'supplier', 'createdBy'])
            ->forBranch($user->branch->id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('pages.inventories.transfers.inter_site.additional_cost.index', [
            'records' => $records,
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('intersite.additional_cost.create');

        $model = new IntersiteAdditionalCost;
        $model->reference = IntersiteAdditionalCost::generateNewNumber();
        $model->date = date('Y-m-d');

        $user = Auth::user();

        $intersites = IntersiteTransfer::where('status', 1)
            ->where('destination_branch_id', User::userBranchAction())
            ->with('source')
            ->orderBy('reference', 'desc')
            ->get();

        $suppliers = Supplier::orderBy('name')->get(['id', 'name', 'code']);

        return view('pages.inventories.transfers.inter_site.additional_cost.create', [
            'model' => $model,
            'intersites' => $intersites,
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('intersite.additional_cost.create');

        $request->validate([
            'reference' => 'required|unique:intersite_additional_costs,reference',
            'date' => 'required|date',
            'intersite_transfer_id' => 'required|exists:intersite_transfers,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'cost_mode' => 'required|in:by_amount,by_qty',
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Verify the intersite is posted (status=1)
        $intersite = IntersiteTransfer::find($request->intersite_transfer_id);
        if (!$intersite || $intersite->status != 1) {
            session()->flash('app_error', 'Selected intersite transfer is not available for additional cost.');
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $model = new IntersiteAdditionalCost;
            $model->reference = $request->reference;
            $model->intersite_transfer_id = $request->intersite_transfer_id;
            $model->supplier_id = $request->supplier_id;
            $model->cost_mode = $request->cost_mode;
            $model->amount = $request->amount;
            $model->date = $request->date;
            $model->truck_number = $request->truck_number;
            $model->waybill_no = $request->waybill_no;
            $model->description = $request->description;
            $model->branch_id = $user->branch->id;
            $model->status = IntersiteAdditionalCost::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            // Calculate and save distribution
            $this->calculateDistribution($model, $intersite);

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created intersite additional cost: " . $model->reference);
            session()->flash('app_message', 'Intersite Additional Cost created successfully');
            return redirect()->route('intersite.additional_cost.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(IntersiteAdditionalCost $cost)
    {
        $this->authorize('intersite.additional_cost.show');

        $cost->load(['intersiteTransfer.source', 'intersiteTransfer.destination', 'supplier', 'createdBy', 'postedBy', 'items.product']);

        return view('pages.inventories.transfers.inter_site.additional_cost.show', [
            'record' => $cost,
        ]);
    }

    public function edit(IntersiteAdditionalCost $cost)
    {
        $this->authorize('intersite.additional_cost.edit');

        if (!$cost->canBeEdited()) {
            session()->flash('app_error', 'Only pending costs can be edited.');
            return redirect()->back();
        }

        $user = Auth::user();

        $intersites = IntersiteTransfer::where('status', 1)
            ->where('destination_branch_id', User::userBranchAction())
            ->with('source')
            ->orderBy('reference', 'desc')
            ->get();

        // Include the currently selected intersite even if it's no longer status=1
        if (!$intersites->contains('id', $cost->intersite_transfer_id)) {
            $currentIntersite = IntersiteTransfer::with('source')->find($cost->intersite_transfer_id);
            if ($currentIntersite) {
                $intersites->push($currentIntersite);
            }
        }

        $suppliers = Supplier::orderBy('name')->get(['id', 'name', 'code']);

        return view('pages.inventories.transfers.inter_site.additional_cost.create', [
            'model' => $cost,
            'intersites' => $intersites,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, IntersiteAdditionalCost $cost)
    {
        $this->authorize('intersite.additional_cost.edit');

        if (!$cost->canBeEdited()) {
            session()->flash('app_error', 'Only pending costs can be edited.');
            return redirect()->back();
        }

        $request->validate([
            'date' => 'required|date',
            'intersite_transfer_id' => 'required|exists:intersite_transfers,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'cost_mode' => 'required|in:by_amount,by_qty',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $intersite = IntersiteTransfer::find($request->intersite_transfer_id);
        if (!$intersite || $intersite->status != 1) {
            session()->flash('app_error', 'Selected intersite transfer is not available for additional cost.');
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();

        try {
            $cost->intersite_transfer_id = $request->intersite_transfer_id;
            $cost->supplier_id = $request->supplier_id;
            $cost->cost_mode = $request->cost_mode;
            $cost->amount = $request->amount;
            $cost->date = $request->date;
            $cost->truck_number = $request->truck_number;
            $cost->waybill_no = $request->waybill_no;
            $cost->description = $request->description;
            $cost->save();

            // Recalculate distribution
            $cost->items()->delete();
            $this->calculateDistribution($cost, $intersite);

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Updated intersite additional cost: " . $cost->reference);
            session()->flash('app_message', 'Intersite Additional Cost updated successfully');
            return redirect()->route('intersite.additional_cost.show', $cost->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function post(IntersiteAdditionalCost $cost)
    {
        $this->authorize('intersite.additional_cost.post');

        if (!$cost->isPending()) {
            session()->flash('app_error', 'Only pending costs can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Post to ledger
            $result = Transaction::intersite_additional_cost_post($cost->id);
            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            $cost->post();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Posted intersite additional cost: " . $cost->reference);
            session()->flash('app_message', 'Intersite Additional Cost posted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function reverse(IntersiteAdditionalCost $cost)
    {
        $this->authorize('intersite.additional_cost.post');

        if (!$cost->canBeReversed()) {
            session()->flash('app_error', 'This cost cannot be reversed. It must be posted and the intersite must not yet be received.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Reverse ledger entries
            $result = Transaction::intersite_additional_cost_reverse($cost->id);
            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            $cost->status = IntersiteAdditionalCost::STATUS_REVERSED;
            $cost->save();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Reversed intersite additional cost: " . $cost->reference);
            session()->flash('app_message', 'Intersite Additional Cost reversed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(IntersiteAdditionalCost $cost)
    {
        $this->authorize('intersite.additional_cost.delete');

        if (!$cost->canBeDeleted()) {
            session()->flash('app_error', 'Only pending costs can be deleted.');
            return redirect()->back();
        }

        $reference = $cost->reference;
        $cost->items()->delete();
        $cost->delete();

        AuditLog::auditLog(Auth::id(), "Deleted intersite additional cost: " . $reference);
        session()->flash('app_message', 'Intersite Additional Cost deleted successfully');

        return redirect()->route('intersite.additional_cost.index');
    }

    public function searchIntersite(Request $request)
    {
        $term = $request->get('term', '');

        $intersites = IntersiteTransfer::where('status', 1)
            ->where('destination_branch_id', User::userBranchAction())
            ->where(function ($query) use ($term) {
                $query->where('reference', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('source', function ($q) use ($term) {
                        $q->where('name', 'LIKE', '%' . $term . '%');
                    });
            })
            ->with('source')
            ->orderBy('reference', 'desc')
            ->limit(20)
            ->get();

        $results = $intersites->map(function ($intersite) {
            return [
                'id' => $intersite->id,
                'text' => $intersite->reference . ' - From: ' . ($intersite->source->name ?? 'N/A'),
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function intersiteDetails(IntersiteTransfer $intersiteTransfer)
    {
        $intersiteTransfer->load(['source', 'destination', 'products.product']);

        $products = $intersiteTransfer->products->map(function ($p) {
            return [
                'code' => $p->product->code ?? '',
                'name' => $p->product->name ?? '',
                'quantity' => number_format($p->quantity, 2),
                'cost_price' => number_format($p->cost_price, 2),
                'total' => number_format($p->cost_price * $p->quantity, 2),
            ];
        });

        $totalValue = $intersiteTransfer->products->sum(function ($p) {
            return $p->cost_price * $p->quantity;
        });

        return response()->json([
            'reference' => $intersiteTransfer->reference,
            'source' => ($intersiteTransfer->source->code ?? '') . ' - ' . ($intersiteTransfer->source->name ?? ''),
            'destination' => ($intersiteTransfer->destination->code ?? '') . ' - ' . ($intersiteTransfer->destination->name ?? ''),
            'date' => $intersiteTransfer->date,
            'vehicle_no' => $intersiteTransfer->vehicle_no,
            'product_count' => $intersiteTransfer->products->count(),
            'total_value' => number_format($totalValue, 2),
            'products' => $products,
        ]);
    }

    private function calculateDistribution(IntersiteAdditionalCost $cost, IntersiteTransfer $intersite)
    {
        $products = $intersite->products;

        if ($products->isEmpty()) {
            return;
        }

        $items = [];
        $totalAllocated = 0;

        if ($cost->cost_mode === 'by_amount') {
            // Distribute proportionally based on value (cost_price * quantity)
            $totalValue = $products->sum(function ($p) {
                return $p->cost_price * $p->quantity;
            });

            if ($totalValue <= 0) {
                return;
            }

            foreach ($products as $index => $product) {
                $productValue = $product->cost_price * $product->quantity;
                $isLast = ($index === $products->count() - 1);

                if ($isLast) {
                    // Last item gets the remainder to avoid rounding errors
                    $allocated = round($cost->amount - $totalAllocated, 2);
                } else {
                    $allocated = round(($productValue / $totalValue) * $cost->amount, 2);
                }

                $totalAllocated += $allocated;
                $additionalUnitCost = $product->quantity > 0 ? round($allocated / $product->quantity, 4) : 0;

                $items[] = [
                    'intersite_additional_cost_id' => $cost->id,
                    'intersite_transfer_product_id' => $product->id,
                    'product_id' => $product->product_id,
                    'quantity' => $product->quantity,
                    'original_cost' => $product->cost_price,
                    'allocated_amount' => $allocated,
                    'additional_unit_cost' => $additionalUnitCost,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        } else {
            // Distribute equally based on quantity
            $totalQty = $products->sum('quantity');

            if ($totalQty <= 0) {
                return;
            }

            foreach ($products as $index => $product) {
                $isLast = ($index === $products->count() - 1);

                if ($isLast) {
                    $allocated = round($cost->amount - $totalAllocated, 2);
                } else {
                    $allocated = round(($product->quantity / $totalQty) * $cost->amount, 2);
                }

                $totalAllocated += $allocated;
                $additionalUnitCost = $product->quantity > 0 ? round($allocated / $product->quantity, 4) : 0;

                $items[] = [
                    'intersite_additional_cost_id' => $cost->id,
                    'intersite_transfer_product_id' => $product->id,
                    'product_id' => $product->product_id,
                    'quantity' => $product->quantity,
                    'original_cost' => $product->cost_price,
                    'allocated_amount' => $allocated,
                    'additional_unit_cost' => $additionalUnitCost,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        IntersiteAdditionalCostItem::insert($items);
    }
}
