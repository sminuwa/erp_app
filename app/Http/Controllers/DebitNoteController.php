<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseExpense;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\DebitNote;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Order;
use App\Models\StoreProduct;
use App\Models\User;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DebitNoteController extends Controller
{
    public function supplierDebitNote()
    {
        $payments = DebitNote::orderBy('debit_notes.created_at', 'DESC')->take(10)->get();
        $model = new SupplierLedger();
        return view('pages.inventories.debit_notes.debit_note', ['payments' => $payments, 'model' => $model]);
    }
    public function createDebitNote(Purchase $purchase = null)
    {
        $user_branch = User::userBranchAction();
        $purchases = Purchase::where('status', 1)
            ->whereNotIn('reference', DB::table('debit_notes')->select('reference_no')->pluck('reference_no')->toArray())
            ->orderBy('purchase_date', 'DESC')->take(20)->get();

        //$suppliers = Supplier::where('branch_id', $user_branch)->get();
        $suppliers = Supplier::all();

        if ($purchase == null)
            \Cart::clear();
        $model = new Supplier;
        $cart_products = \Cart::getContent();
        return view('pages.inventories.debit_notes.create_debit_note', compact('purchases', 'model', 'cart_products', 'purchase', 'suppliers'));
    }
    public function expense(Request $request)
    {
        DB::table('purchase_expenses')->updateOrInsert(['purchase_id' => $request->purchase_id, 'supplier_id' => $request->supplier_id, 'description' => $request->description], ['amount' => $request->amount, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $action = "Added purchase expense $request->name";
        AuditLog::auditLog(Auth::id(), $action);
        $cart_products = PurchaseExpense::where('purchase_id', $request->purchase_id)->get();
        return view('pages.inventories.debit_notes.create_debit_note', compact('cart_products'));
    }
    public function payDebitNote(Request $request)
    {
        return "To call your function for debit note";
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
                'cr' => $order->total,
                'dr' => 0,
                'ref_no' => $order->invoice_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('debit_notes')->insert([
                'invoice_no' => $order->invoice_no,
                'reference_no' => $reference,
                'supplier_id' => $order->supplier_id,
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

    public function searchDebitNote(Request $request)
    {
        $search_value = $request->refno;

        $payments = Purchase::where('status', 1)
            ->where('invoice_no', 'LIKE', "%$search_value%")
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('order_date', 'DESC')->get();
        return view('pages.inventories.debit_notes.debit_note', ['payments' => $payments]);
    }
    public function printCreditnoteReceipt(DebitNote $debit_note)
    {
        return view('pages.inventories.debit_notes.print_debit_note_receipt', ['payment' => $debit_note, 'setting' => Setting::first()]);
    }
    public function loadInvoices(Request $request)
    {
        $word_search = $request->search;
        if (strlen($word_search) > 0) {
            $purchases = Purchase::where('status', 1)
                ->where('invoice_no', 'LIKE', "%$word_search%")
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->orderBy('order_date', 'DESC')->get();
        } else {
            $purchases = Purchase::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('purchase_date', 'DESC')->take(20)->get();
        }
        return view('pages.inventories.debit_notes.load_order_invoices', ['purchases' => $purchases]);
    }
    public function loadToCart(Request $request)
    {
        $reference = $request->reference;
        $purchase = Purchase::where('reference', $reference)->first();
        \Cart::clear();

        foreach (PurchaseExpense::where('purchase_id', $purchase->id)->where('status', 1)->get() as $data) {
            \Cart::add([
                'id' => $data->id,
                'name' => $data->description,
                'price' => $data->amount,
                'quantity' => 1,
                'attributes' => array('supplier_id' => $data->supplier_id),
            ]);
        }
        $cart_products = \Cart::getContent();

        return view('pages.inventories.debit_notes.load_expenses', ['cart_products' => $cart_products, 'reference' => $reference, 'purchase' => $purchase]);
    }
    public function addToCart(Request $request)
    {

        $validated = $request->validate([
            'reference' => 'required',
            'purchase_id' => 'required',
            'amount' => 'required',
        ]);

        $price = $request->price;
        $supplier_id = $request->supplier_id;
        $purchase_id = $request->purchase_id;
        $reference = $request->reference;
        $add = \Cart::add([
            'id' => $this->generateRandomString(),
            'name' => $request->description,
            'price' => $request->amount,
            'quantity' => 1,
            'attributes' => array('supplier_id' => $request->supplier_id)
        ]);
        //dd(\Cart::getContent());
        // if ($add) {
        //     session()->flash('success', 'Purchase is added to Cart Successfully !');
        //     //return redirect()->back();
        //     return redirect()->route('suppliers.debit.note.create', Order::find($request->order));

        // } else {

        //     session()->flash('Purchase not added to cart');
        //     return redirect()->back();
        // }
        $cart_products = \Cart::getContent();
        //dd($cart_products);
        $purchase = Purchase::find($purchase_id);
        return view('pages.inventories.debit_notes.load_expenses', compact('cart_products', 'reference', 'purchase'));
    }

    public function updateCart(Request $request)
    {
        $price = $request->price;

        \Cart::update(
            $request->id,
            [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity
                ],
                'price' => $price,
                'name' => $request->name,
                'attributes' => array('supplier_id' => $request->supplier_id)
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
        return redirect()->route('suppliers.debit.note.create', Purchase::find($request->purchase));
        //return redirect()->back()->with('order',Order::find($request->order));
    }
    public function generateRandomString($length = 5)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
