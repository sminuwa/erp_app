<?php

namespace App\Http\Controllers;

use App\Classes\CostPrice;
use App\Classes\Transaction;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\ReturnDebit;
use App\Models\ReturnDebitItem;
use App\Models\Setting;
use App\Models\StoreProduct;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnDebitController extends Controller
{
    public function returnDebit()
    {
        \Cart::clear();
        $payments = ReturnDebit::where('branch_id', User::userBranchAction())->orderBy('return_debits.created_at', 'DESC')->take(100)->get();
        $model = null;
        return view('pages.inventories.return_debit.return_debit', ['payments' => $payments, 'model' => $model]);
    }
    public function createReturnDebit(Purchase $purchase = null)
    {
        $user_branch = User::userBranchAction();
        //This is to get all purchases that have been returned and debited and posted to be excluded in the create page
        $purchase_ids = ReturnDebit::where('branch_id', User::userBranchAction())
            ->where('status', 1)
            ->get()->pluck('purchase_id')->toArray();
        $purchases = Purchase::where('status', 1)
            ->where('branch_id', 'LIKE', $user_branch)
            ->whereNotIn('id', $purchase_ids)
            ->orderBy('purchase_date', 'DESC')->get();

        $stores = StoreProduct::select('store_products.id', 'products.name', 'products.code', 'stores.name AS store', 'qty_available', 'selling_price', 'retail_selling_price', 'whole_selling_price', 'cost_price', 'unit')->distinct()
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('branch_product_prices', function ($join) {
                $join->on('branch_product_prices.product_id', '=', 'products.id')
                    ->on('branch_product_prices.branch_id', '=', 'branches.id');

            })
            ->where('stores.branch_id', 'LIKE', $user_branch)
            ->where('branch_product_prices.status', 1)
            ->where('products.status', 1)
            //->where('store_products.qty_available', '>', 0)
            ->orderBy('products.name')->orderBy('stores.name')->get();

        if ($purchase == null)
            \Cart::clear();
        $model = new Customer;
        $cart_products = \Cart::getContent();
        return view('pages.inventories.return_debit.create_return_debit', compact('purchases', 'model', 'cart_products', 'purchase', 'stores'));
    }

    public function storeReturnDebit(Request $request)
    {

        //return "To call your function 8888";
        $purchase_id = $request->purchase_id;
        $comment = $request->comment;
        $order = Purchase::find($purchase_id);
        $reference = ReturnDebit::generateNewNumber();
        $items = \Cart::getContent();
        $total = \Cart::getTotal();

        DB::beginTransaction();
        try {

            $return_debit_id = DB::table('return_debits')->insertGetId([
                'purchase_id' => $order->id,
                'invoice_no' => $order->reference,
                'reference' => $reference,
                'supplier_id' => $order->supplier_id,
                'date' => $request->date,
                'amount' => $total,
                'comment' => $comment,
                'branch_id' => User::userBranchAction(),
                'created_by' => Auth::id(),
            ]);

            foreach ($items as $item) {

                $purchase = $order->purchasedProducts()->where('id', $item->id)->first();
                $checks = StoreProduct::where(['product_id'=>$item->attributes['product_id'], 'store_id'=>$item->attributes['store_id']])->first();
                if($checks->qty_available < $item->quantity) {
                    session()->flash('app_error', 'Quantity is higher than current quantity. Total available is '.$checks->qty_available);
                    return back();
                }
                DB::table('return_debit_items')->insert([
                    'return_debit_id' => $return_debit_id,
                    'product_id' => $item->attributes['product_id'] ?? 0,
                    'store_id' => $item->attributes['store_id'] ?? 0,
                    'current_quantity' => $item->quantity,
                    'original_quantity_purchased' => $purchase->quantity,
                    'current_unit_cost' => $item->price,
                    'original_unit_cost' => $purchase->unit_price,
                ]);
            }

            session()->flash('app_message', 'Return and debit captured successfully');
            $action = "Posted return and debit $order->reference for customer: " . $order->supplier->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', $e);
            return back();
            throw $e;
        }

        return redirect()->route('return.debit.show', $return_debit_id);
    }
    public function show(Request $request, ReturnDebit $returndebit)
    {
        return view('pages.inventories.return_debit.preview_return_debit', compact('returndebit'));
    }
    public function edit(Request $request, ReturnDebit $returndebit)
    {
        foreach ($returndebit->returnItems()->get() as $data) {
            $qty = $data->current_quantity == 0 ? 1 : $data->current_quantity;

            \Cart::add([
                'id' => $data->id,
                'name' => $data->product->name ?? 'No name found',
                'price' => $data->current_unit_cost ?? 1,
                'quantity' => $qty,
                'attributes' => array(
                    'code' => $data->product->code,
                    'product_id' => $data->product_id,
                    'store_id' => $data->store_id
                ),
            ]);
        }
        $cart_products = \Cart::getContent();

        return view('pages.inventories.return_debit.edit_return_debit', ['cart_products' => $cart_products, 'reference' => $returndebit->reference, 'operation' => 'update', 'purchase' => $returndebit]);
    }

    public function searchReturnDebit(Request $request)
    {
        $search_value = $request->refno;

        $payments = ReturnDebit::where('status', 1)
            ->where('reference', 'LIKE', "%$search_value%")
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('order_date', 'DESC')->get();
        return view('pages.inventories.return_debit.return_debit', ['payments' => $payments]);
    }

    public function printReturnDebitReceipt(ReturnDebit $returnDebit, $papersize = "A4")
    {
        return view('pages.inventories.return_debit.print_return_debit_receipt', ['payment' => $returnDebit, 'setting' => Setting::first(),'papersize'=>$papersize]);
    }

    public function loadInvoices(Request $request)
    {
        \Cart::clear();
        $word_search = $request->search;
        $purchase_ids = ReturnDebit::where('branch_id', User::userBranchAction())
            ->where('status', 1)
            ->get()->pluck('purchase_id')->toArray();
        if (strlen($word_search) > 0) {
            $orders = Purchase::where('status', 1)
                ->where('reference', 'LIKE', "%$word_search%")
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->whereNotIn('id', $purchase_ids)
                ->orderBy('purchase_date', 'DESC')->take(50)->get();
        } else {
            $orders = Purchase::where('branch_id', 'LIKE', User::userBranchAction())
                ->whereNotIn('id', $purchase_ids)
                ->orderBy('purchase_date', 'DESC')->take(20)->get();
        }
        return view('pages.inventories.return_debit.load_order_invoices', ['orders' => $orders]);
    }

    public function loadToCart(Request $request)
    {
        $reference = $request->invoice_no;
        $purchase = Purchase::where('reference', $reference)->first();

        \Cart::clear();
        //dd($purchase->purchasedProducts()->where('status', 1)->get());
        foreach ($purchase->purchasedProducts()->where('status', 1)->get() as $data) {
            $qty = $data->quantity == 0 ? 1 : $data->quantity;
            $available = StoreProduct::where(['store_id' => $data->store_id, 'product_id' => $data->product_id])->first()->qty_available ?? $qty;
            \Cart::add([
                'id' => $data->id,
                'name' => $data->product->name ?? 'No name found',
                'price' => $data->unit_price,
                'quantity' => $qty,
                'attributes' => array(
                    'code' => $data->product->code,
                    'product_id' => $data->product_id,
                    'store_id' => $data->store_id,
                    'available' => $available,
                ),
            ]);
        }
        $cart_products = \Cart::getContent();
        return view('pages.inventories.return_debit.load_products', ['cart_products' => $cart_products, 'reference' => $reference, 'purchase' => $purchase]);
    }


    public function deletReturnDebit(Request $request, ReturnDebit $returnDebit)
    {
        //return "To call your function 8888";
        $reference = $returnDebit;
        DB::beginTransaction();
        try {
            $returnDebit->returnItems()->delete();
            $returnDebit->delete();
            session()->flash('app_message', 'Return and debit deleted successfully');
            $action = "Deleted return and debit with reference $reference";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('return.debit');
    }

    public function updateReturnDebit(Request $request, ReturnDebit $returndebit)
    {
        $contents = \Cart::getContent();
        DB::beginTransaction();
        try {
            $returndebit->comment = $request->comment;
            $returndebit->date = $request->date;
            $returndebit->save();
            foreach ($contents as $item) {
                ReturnDebitItem::where('id', $item->id)->update(['current_quantity' => $item->quantity, 'current_unit_cost' => $item->price]);
            }
            session()->flash('app_message', 'Return and debit updated successfully');
            $action = "Modifiied return and debit with reference $returndebit->reference";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return redirect()->route('return.debit');
    }

    public function post(Request $request, ReturnDebit $returndebit)
    {

        if ($returndebit->status == 0) {
            $this->authorize('purchase.post');
            $items = $returndebit->products;
            DB::beginTransaction();
            $returndebit->status = 1;
            $returndebit->posted_by = auth()->id();
            if ($returndebit->save()) {
                $new_cost_price = [];
                foreach ($items as $item) {
                    $checks = StoreProduct::where(['product_id'=>$item->product_id, 'store_id'=>$item->store_id])->first();
                    if($checks->qty_available < $item->current_quantity) {
                        session()->flash('app_error', 'Quantity is higher than current quantity. Total available is '.$checks->qty_available);
                        return back();
                    }
                    $new_cost_price[$item->product_id] = [
                        'quantity' => $item->current_quantity,
                        'price' => $item->current_unit_cost,
                        'store_id' => $item->store_id,
                        'expiry_date' => $item->expire_date ?? null,
                    ];
                }

                /*return CostPrice::newCostPrice(
                    $new_cost_price,
                    $returndebit->reference,
                    $returndebit->branch_id,
                    $returndebit->date,
                    TRANSACTION_TYPE_RETURN_DEBIT,
                    'out'
                );*/

                if (
                    Transaction::return_debit($returndebit->id, $returndebit->date)['status']
                    && CostPrice::newCostPrice(
                        $new_cost_price,
                        $returndebit->reference,
                        $returndebit->branch_id,
                        $returndebit->date,
                        TRANSACTION_TYPE_RETURN_DEBIT,
                        'out'
                    )['status']
                ) {
                    $action = "Posted purchase GRN with reference $request->reference from supplier: " . Supplier::find($returndebit->supplier_id)->name;
                    AuditLog::auditLog(Auth::id(), $action);
                    DB::commit();
                } else
                    DB::rollback();
                session()->flash('app_message', 'Purchase successfully posted');
            }
        }
        return back();
    }
}
