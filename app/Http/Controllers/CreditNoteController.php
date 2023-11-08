<?php

namespace App\Http\Controllers;

use App\Classes\CostPrice;
use App\Classes\Transaction;
use App\Models\AuditLog;
use App\Models\CreditNote;
use App\Models\CreditNoteDetail;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\StoreProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
            ->whereNotIn('invoice_no', DB::table('credit_notes')->select('invoice_no')->pluck('invoice_no')->toArray())
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
        return view('pages.inventories.credit_notes.create_credit_note', compact('orders', 'customers', 'cart_products', 'order', 'stores'));
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
        $total = \Cart::getTotal();
        DB::beginTransaction();
        try {
            if (!$credit_note) {
                $credit_note = new CreditNote();
                $credit_note->reference = $reference;
                $credit_note->customer_id = $request->customer_id;
                $credit_note->order_id = $request->order_id;
                $credit_note->status = 0;
                $credit_note->branch_id = $order->branch_id;
                $credit_note->created_by = auth()->id();
            }
            $credit_note->comment = $request->comment;
            $credit_note->amount = $total;
            if ($credit_note->save()) {
                CreditNoteDetail::where('credit_note_id', $credit_note->id)->delete();
                $contents = \Cart::getContent();
                foreach ($contents as $content) {
                    $store = StoreProduct::find($content->id);
                    $qtyAval = $store->qty_available;
                    //$store->qty_available = $qtyAval - $content->quantity;
                    $credit_note_detail = new CreditNoteDetail();
                    $credit_note_detail->credit_note_id = $credit_note->id;
                    $credit_note_detail->store_product_id = $content->id;
                    $credit_note_detail->quantity = $content->quantity;
                    $credit_note_detail->original_quantity_sold = $content->quantity;
                    $credit_note_detail->selling_price = $content->attributes['selling_price'];
                    $credit_note_detail->sold_price = $content->price;
                    $credit_note_detail->cost_price = $content->attributes['cost_price'];
                    $credit_note_detail->total = $content->getPriceSum();
                    $credit_note_detail->avail_qty_before_sale = $qtyAval;
                    $credit_note_detail->save();
                }

                $action = "Create/Updated credit note for $credit_note->reference : $total";
                AuditLog::auditLog(Auth::id(), $action);
                DB::commit();
            }
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
    public function post(CreditNote $credit_note)
    {
        $credit_note->status = 1;
        $credit_note->posted_by = auth()->id();
        $items = $credit_note->credit_note_items;
        DB::beginTransaction();
        if ($credit_note->save()) {
            $products = $new_cost_price = [];
            foreach ($items as $item) {
                $products[$item->store_product_id] = [
                    'quantity' => $item->quantity,
                    'cost_price' => $item->cost_price,
                    'sold_price' => $item->sold_price
                ];
                $new_cost_price[$item->store_product_id] = [
                    'quantity' => $item->quantity,
                    'price' => $item->cost_price,
                    'store_id' => $item->store_product->store_id,
                    'expiry_date' => ''
                ];
            }
            if (
                Transaction::credit_note(
                    $products,
                    $credit_note->customer_id,
                    $credit_note->reference,
                    $credit_note->order_date
                )['status']
            ) {

                //update stock and calculate new cost price
                if (
                    CostPrice::newCostPrice(
                        $new_cost_price,
                        $credit_note->reference,
                        $credit_note->branch_id
                    )['status']
                )
                    $action = "Credit Note of $credit_note->total for : " . $credit_note->reference;

                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Credit note posted successfully');
                DB::commit();
            } else {
                DB::rollBack();
                session()->flash('app_message', 'Something went wrong.');
            }
        }
        return back();
    }

    public function searchCreditNote(Request $request)
    {
        $search_value = $request->refno;

        $payments = Order::where('status', 1)
            ->where('reference', 'LIKE', "%$search_value%")
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereNotIn('id', DB::table('credit_notes')->pluck('order_id')->toArray())
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

    public function updateCreditNoteCart(Request $request)
    {
        //        $sold_price = $request->sold_price;

        \Cart::update(
            $request->store_product_id,
            [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity
                ]
            ]
        );

        session()->flash('success', 'Item Cart is Updated Successfully !');

        if ($request->ajax()) {
            return \Cart::getTotal();
        }

        return \Cart::getTotal();
    }

    public function removeCart(Request $request, $id)
    {
        \Cart::remove($request->id);
        session()->flash('success', 'Item Cart Remove Successfully !');
        return redirect()->route('customers.credit.note.create', Order::find($request->order));
        //return redirect()->back()->with('order',Order::find($request->order));
    }
}
