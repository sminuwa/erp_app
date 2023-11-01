<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\ReturnDebit;
use App\Models\Setting;
use App\Models\StoreProduct;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class ReturnDebitController extends Controller
{
    public function customerReturnDebit()
    {
        $payments = ReturnDebit::orderBy('return_debits.created_at', 'DESC')->take(10)->get();
        $model = new CustomerController();
        return view('pages.inventories.return_debit.return_debit', ['payments' => $payments, 'model' => $model]);
    }
    public function createReturnDebit(Order $order = null)
    {
        $user_branch = User::userBranchAction();
        $orders = Order::where('status', 1)
            ->where('branch_id', 'LIKE', $user_branch)
            ->whereNotIn('invoice_no',DB::table('credit_notes')->select('invoice_no')->pluck('invoice_no')->toArray())
            ->orderBy('order_date', 'DESC')->take(20)->get();

            $stores = StoreProduct::select('store_products.id', 'products.name', 'products.code', 'stores.name AS store', 'qty_available', 'selling_price', 'cost_price', 'unit')->distinct()
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('branch_product_prices', function ($join) {
                $join->on('branch_product_prices.product_id', '=', 'products.id')
                    ->on('branch_product_prices.branch_id', '=', 'branches.id');

            })
            ->where('stores.branch_id', 'LIKE', $user_branch)
            ->where('branch_product_prices.status', 1)
            ->where('store_products.qty_available', '>', 0)
            ->orderBy('products.name')->orderBy('stores.name')->get();

        if ($order == null)
            \Cart::clear();
        $model = new Customer;
        $cart_products = \Cart::getContent();
        return view('pages.inventories.return_debit.create_return_debit', compact('orders', 'model', 'cart_products', 'order','stores'));
    }
    public function payReturnDebit(Request $request)
    {
        return "To call your function 8888";
        $order_id = $request->order_id;
        $comment = $request->comment;
        $order = Order::find($order_id);
        $reference = $this->generateCreditNoteInvoice();
        DB::beginTransaction();
        try {
            //Bank Withdrawal
            DB::table('bank_transactions')->insert([
                'bank_account_id' => $order->customer_id,
                'trans_date' => date('Y-m-d'),
                'cr' => 0,
                'dr' => $order->total,
                'ref_no' => $order->invoice_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('return_debits')->insert([
                'invoice_no' => $order->invoice_no,
                'reference_no' => $reference,
                'customer_id' => $order->customer_id,
                'amount' => $order->total,
                'comment' => $request->comment,
                'branch_id' => User::userBranchAction()
            ]);
            session()->flash('app_message', 'Credit note captured successfully');
            $action = "Posted credit note $order->invoice_no for customer: " . $order->customer->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->back();
    }

    public function searchReturnDebit(Request $request)
    {
        $search_value = $request->refno;

        $payments = Order::where('status', 1)
            ->where('invoice_no', 'LIKE', "%$search_value%")
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('order_date', 'DESC')->get();
        return view('pages.inventories.return_debit.return_debit', ['payments' => $payments]);
    }
    public function printCreditnoteReceipt(ReturnDebit $returnDebit)
    {
        return view('pages.inventories.return_debit.print_return_debit_receipt', ['payment' => $returnDebit, 'setting' => Setting::first()]);
    }
    public function loadInvoices(Request $request)
    {
        $word_search = $request->search;
        if (strlen($word_search) > 0) {
            $orders = Order::where('status', 1)
                ->where('invoice_no', 'LIKE', "%$word_search%")
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->orderBy('order_date', 'DESC')->get();
        } else {
            $orders = Order::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('order_date', 'DESC')->take(20)->get();
        }
        return view('pages.inventories.return_debit.load_order_invoices', ['orders' => $orders]);
    }
    public function loadToCart(Request $request)
    {
        $invoice_no = $request->invoice_no;
        $order = Order::where('invoice_no', $invoice_no)->first();

        \Cart::clear();
        foreach ($order->order_items()->where('status', 1)->get() as $data) {
            $qty = $data->quantity == 0 ? 1 : $data->quantity;
            \Cart::add([
                'id' => $data->store_product_id,
                'name' => $data->storeProduct->product->name ?? 'No name found',
                'price' => $data->sold_price,
                'quantity' => $qty,
                'attributes' => array('cost_price' => $data->cost_price, 'selling_price' => $data->selling_price, 'discount' => 0),
            ]);
        }
        $cart_products = \Cart::getContent();
        return view('pages.inventories.return_debit.load_products', ['cart_products' => $cart_products, 'invoice_no' => $invoice_no, 'order' => $order]);
    }
    public function addToCart(Request $request)
    {

        $validated = $request->validate([
            'id' => 'required',
            'name' => 'required',
            'code' => 'required',
            'sold_price' => 'required',
            'qty' => 'required',
            'cost_price' => 'required'
        ]);
        $qty = $request->qty;
        $selling_price = $request->selling_price;
        $cost_price = $request->cost_price;
        $qty_available = $request->qty_available;
        $store = $request->store;
        $add = \Cart::add([
            'id' => $request->id,
            'name' => $request->name,
            'price' => $request->sold_price,
            'quantity' => $qty == 0 ? 1 : $qty,
            'attributes' => array('cost_price' => $cost_price, 'code' => $request->code, 'selling_price' => $selling_price, 'qty_available' => $qty_available, 'discount' => 0, 'store' => $store),
        ]);
        //dd(\Cart::getContent());
        if ($add) {
            session()->flash('success', 'Product is Added to Cart Successfully !');
            //return redirect()->back();
            return redirect()->route('customers.return.debit.create', Order::find($request->order));

        } else {

            session()->flash('Product not added to cart');
            return redirect()->back();
        }
    }

    public function updateCart(Request $request)
    {
        $sold_price = $request->sold_price;

        \Cart::update(
            $request->id,
            [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity
                ],
                'price' => $sold_price,
                'attributes' => array('cost_price' => $request->cost_price, 'selling_price' => $request->selling_price, 'code' => $request->code, 'discount' => $request->selling_price - $request->sold_price, 'qty_available' => $request->qty_available, 'store' => $request->store)
            ]
        );

        session()->flash('success', 'Item Cart is Updated Successfully !');
        if ($request->ajax()) {
            return \Cart::getTotal();
        }
        return redirect()->back();
    }

    public function removeCart(Request $request, $id)
    {
        \Cart::remove($request->id);
        session()->flash('success', 'Item Cart Remove Successfully !');
        return redirect()->route('customers.return.debit.create', Order::find($request->order));
        //return redirect()->back()->with('order',Order::find($request->order));
    }
}
