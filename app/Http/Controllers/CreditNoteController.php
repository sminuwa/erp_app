<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\StoreProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function customerCreditNote()
    {
        $payments = CreditNote::orderBy('credit_notes.created_at', 'DESC')->take(10)->get();
        $model = new CustomerLedger();
        return view('pages.inventories.credit_notes.credit_note', ['payments' => $payments, 'model' => $model]);
    }
    public function createCreditNote(Order $order = null)
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
        $customers = Customer::where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $model = new Customer;
        $cart_products = \Cart::getContent();
        return view('pages.inventories.credit_notes.create_credit_note', compact('orders', 'customers', 'cart_products', 'order','stores'));
    }

    public function payCreditNote(Request $request)
    {
//        return "To call your function";
        $order_id = $request->order_id;
        $comment = $request->comment;
        $order = Order::find($order_id);
        $credit_note_id = $request->credit_note_id;
        $reference = CreditNote::generateNewNumber();
        $credit_note = CreditNote::find($credit_note_id);

        DB::beginTransaction();
        try {
            if(!$credit_note)
                $credit_note = new CreditNote();
            $credit_note->comment =
            //Bank Withdrawal
            DB::table('credit_notes')->insert([
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

    public function searchCreditNote(Request $request)
    {
        $search_value = $request->refno;

        $payments = Order::where('status', 1)
            ->where('reference', 'LIKE', "%$search_value%")
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('id', 'DESC')->get();
        return view('pages.suppliers.credit_note', ['payments' => $payments]);
    }
    public function printCreditnoteReceipt(CreditNote $credit_note)
    {
        return view('pages.inventories.credit_notes.print_credit_note_receipt', ['payment' => $credit_note, 'setting' => Setting::first()]);
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
        return view('pages.inventories.credit_notes.load_order_invoices', ['orders' => $orders]);
    }
    public function loadToCart(Request $request)
    {
        $reference = $request->reference;
        $order = Order::where('reference', $reference)->first();
        $order_items = OrderDetail::where('order_id', $order->id)->where('status', 1)->get();
        \Cart::clear();
        foreach ($order_items as $data) {
            $qty = $data->quantity == 0 ? 1 : $data->quantity;
            \Cart::add([
                'id' => $data->store_product_id,
                'name' => $data->storeProduct->product->name ?? 'No name found',
                'price' => $data->sold_price,
                'quantity' => $qty,
                'attributes' => array(
                    'cost_price' => $data->cost_price,
                    'selling_price' => $data->selling_price,
                    'sold_price' => $data->sold_price,
                    'discount' => 0,
                    'unit' => $data->storeProduct->product->unit
                ),
            ]);
        }
        $cart_products = \Cart::getContent();
        return view('pages.inventories.credit_notes.load_products', ['cart_products' => $cart_products, 'reference' => $reference, 'order' => $order]);
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
            return redirect()->route('customers.credit.note.create', Order::find($request->order));

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
        return redirect()->route('customers.credit.note.create', Order::find($request->order));
        //return redirect()->back()->with('order',Order::find($request->order));
    }
}
