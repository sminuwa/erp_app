<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use App\Models\Branch;
use App\Models\Category;
use App\Models\CreditNote;
use App\Models\Order;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Http\Requests\Customers\Index;
use App\Http\Requests\Customers\Show;
use App\Http\Requests\Customers\Create;
use App\Http\Requests\Customers\Store;
use App\Http\Requests\Customers\Edit;
use App\Http\Requests\Customers\Update;
use App\Http\Requests\Customers\Destroy;
use App\Models\BankAccount;
use App\Models\PaymentMode;
use App\Models\CustomerLedger;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Directory;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\AuditLog;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

/**
 * Description of CustomerController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */
class CustomerController extends Controller
{

    public function index(Index $request)
    {
        $user_branch = User::userBranchAction();
        $customers = Customer::where('branch_id', 'LIKE', $user_branch)->orderBy('created_at', 'desc')->limit(10)->get();
        return view('pages.customers.index', compact('customers'));
    }

    public function show(Show $request, Customer $customer)
    { //dd($customer->ledgers()->get());
        return view('pages.customers.show', [
            'record' => $customer,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Create $request
     * @return \Illuminate\Http\Response
     */
    public function create(Create $request)
    {

        return view('pages.customers.create', [
            'model' => new Customer,

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Store $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $model = new Customer;
        $model->fill($request->all());
        $model->credit_limit = str_replace(',', '', $request->credit_limit);
        $model->type = ($request->account_type == 'R' ? 'Retail' : "Wholesale");
        $model->branch_id = $request->branch_id;
        $model->relation_officer = $request->relation_officer;
        $model->status = $request->status ?? 1;
        if ($model->save()) {
            if ($request->category == 'staff') {
                $model->code = $request->code;
            } else {
                $model->code = Customer::generateNewCode($request->branch_id, $request->account_type);
            }
            $model->save();
            $action = "Added a new credit customer : " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Customer saved successfully');
            if ($request->has('modal'))
                return back();
            return redirect()->route('customers.index');
        } else {
            session()->flash('app_message', 'Something is wrong while saving Customer');
        }
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Edit $request
     * @param Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, Customer $customer)
    {
        $this->getOverDueInvoices($customer)->get();
        return view('pages.customers.edit', [
            'model' => $customer,
        ]);
    }

    /**
     * Update a existing resource in storage.
     *
     * @param Update $request
     * @param Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, Customer $customer)
    { //return $request;

        // return $request;
        $customer->fill($request->except('name'));
        $customer->type = ($request->account_type == 'R' ? 'Retail' : "Wholesale");
        $customer->branch_id = $request->branch_id;
        $customer->relation_officer = $request->relation_officer;
        $customer->status = $request->status ?? 1;
        if ($customer->save()) {
            $action = "Updated a credit customer : " . $customer->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Customer successfully updated');
            return redirect()->route('customers.index');
        } else {
            session()->flash('app_error', 'Something is wrong while updating Customer');
        }
        return redirect()->back();
    }

    /**
     * Delete a  resource from  storage.
     *
     * @param Destroy $request
     * @param Customer $customer
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Destroy $request, Customer $customer)
    {
        if ($customer->ledgers()->count() == 0) {
            if ($customer->delete()) {
                $action = "Deleted a credit customer : " . $customer->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Customer successfully deleted');
            } else {
                session()->flash('app_error', 'Error occurred while deleting Customer');
            }
        } else {
            session()->flash('app_error', 'Error occurred while deleting Customer');
        }
        return redirect()->back();
    }

    public function generateCustomerLedger()
    {
        return view('pages.customers.general_ledger', [
            'customers' => Customer::where('type', 'credit')->where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get()
        ]);
    }

    public function loadLedger(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer = Customer::find($request->customer_id);
        $query = CustomerLedger::where('customer_id', $request->customer_id)->whereBetween('date', [$from_date, $to_date]);
        $sum_cr_b_d = CustomerLedger::where('customer_id', $request->customer_id)->where('date', '<', $from_date)->sum('cr');
        $sum_dr_b_d = CustomerLedger::where('customer_id', $request->customer_id)->where('date', '<', $from_date)->sum('dr');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d; //balance before date
        $ledgers = $query->orderBy('date')->orderBy('id', 'ASC')->get();

        return view('pages.customers.load_ledger', compact('ledgers', 'customer', 'from_date', 'to_date', 'balance_b_d', 'sum_cr_b_d', 'sum_dr_b_d'));
    }

    public function printLedger($from_date, $to_date, $customer_id)
    {
        $customer = Customer::find($customer_id);
        $query = CustomerLedger::where('customer_id', $customer_id)->whereBetween('date', [$from_date, $to_date]);
        $sum_cr_b_d = CustomerLedger::where('customer_id', $customer_id)->where('date', '<', $from_date)->sum('cr');
        $sum_dr_b_d = CustomerLedger::where('customer_id', $customer_id)->where('date', '<', $from_date)->sum('dr');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d; //balance before date
        $ledgers = $query->orderBy('date')->get();

        return view('pages.customers.print_ledger', compact('ledgers', 'customer', 'from_date', 'to_date', 'balance_b_d', 'sum_cr_b_d', 'sum_dr_b_d'));
    }

    public function loadGeneralCustomerLedger(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;
        $type = $request->type;

        if ($customer_id == 'all' && $type == 'Retail') {
            $customer_id = '%';
            $type = "Retail";
        }
        if ($customer_id == 'all' && $type == 'Wholesale') {
            $customer_id = '%';
            $type = "Wholesale";
        }
        if ($customer_id == 'all' && $type == '') {
            $customer_id = '%';
            $type = '%';
        }
        if ($request->has('print')) {
            return $this->printLedger($from_date, $to_date, $customer_id);
        }
        $query = CustomerLedger::where('customer_id', 'LIKE', $customer_id)
            ->where('type', 'LIKE', $type)
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->whereBetween('date', [$from_date, $to_date]);
        $ledgers = $query->orderBy('date')->orderBy('customer_ledgers.id')->get();
        $sum_cr_b_d = CustomerLedger::where('customer_id', $customer_id)->where('date', '<', $from_date)->sum('cr');
        $sum_dr_b_d = CustomerLedger::where('customer_id', $customer_id)->where('date', '<', $from_date)->sum('dr');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d; //balance before date

        $customer = Customer::find($customer_id);
        return view('pages.customers.load_general_ledger', compact('ledgers', 'from_date', 'to_date', 'balance_b_d', 'customer', 'sum_cr_b_d', 'sum_dr_b_d'));
    }

    public function payForMoreInvoices(Customer $customer, $amount)
    {
        //clear other unpaid invoice if the amount is more than the selected invoice
        $over_due_invoices = $this->unPaidInvoices($customer)->get();
        $balance = 0;
        foreach ($over_due_invoices as $invoice) {
            if ($amount > 0) {
                $amount_due = DB::table('orders')->where('id', $invoice->id)->first();
                if ($amount > $amount_due->due) { // if the amount to pay id more than the amount due, we calculate the balance
                    $balance = $amount - $amount_due->due; // get the balance after paid for the selected invoice
                    $amount = $amount_due->due; //pay for the amount due
                }
                //Clear first invoice selected
                DB::table('orders')->where('id', $invoice->id)->increment('pay', $amount);
                DB::table('orders')->where('id', $invoice->id)->decrement('due', $amount);

                DB::table('customers')->where(['id' => $customer->id])->decrement('opening_balance', $amount);
                if ($balance > 0) {
                    DB::table('customers')->where(['id' => $customer->id])->decrement('opening_balance', $balance);
                }

            }
        }

    }

    public function createOpeningBalance()
    {
        $user_branch = User::userBranchAction();
        $customers = Customer::where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        return view('pages.customers.create_opening_balance', [
            'customers' => $customers,
            'model' => null
        ]);
    }

    public function openingBalanceStore(Request $request)
    {
        $customer_id = $request->customer_id;
        $amount = $request->amount;
        $reciept_no = "OP" . $customer_id;
        Customer::where(['id' => $customer_id])->update(['opening_balance' => $amount]);
        $ledger_id = DB::table('customer_ledgers')->updateOrInsert(['customer_id' => $customer_id, 'teller_no' => $reciept_no], [
            'customer_id' => $customer_id,
            'order_id' => 0,
            'systemid' => gethostname(),
            'description' => 'Opening Balance',
            'ref' => $reciept_no,
            'teller_no' => $reciept_no,
            'receipt_no' => $reciept_no,
            'date' => date('Y-m-d'),
            'payment_mode' => 'Credit',
            'cr' => $amount,
            'user_id' => Auth::id(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $action = "Set  $amount to an opening balance of customer : " . Customer::find($customer_id)->name;
        AuditLog::auditLog(Auth::id(), $action);
        session()->flash('app_message', 'Customer opening balance successfully defined');
        return redirect()->route('customers.index');
    }

    public function getCustomerBalance(Request $request)
    {
        $balance = Customer::find($request->customer_id)->runningBalance() ?? 0;
        return $balance;
    }

    public function getCustomerCreditLimit(Request $request)
    {
        return Customer::find($request->customer_id)->credit_limit ?? 0;
    }

    public function updateCreditLimit(Request $request)
    {
        if ($request->customer_id != null) {
            $customer_id = $request->customer_id;
            $new_amount = str_replace(',', '', $request->new_amount);
            $customer = Customer::find($customer_id);
            $customer->credit_limit = $new_amount;
            $customer->save();
            $action = "Updated  opening balance of customer ($request->new_amount) : " . Customer::find($customer_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Customer credit limit successfully updated');
        } else {
            session()->flash('app_error', 'Customer must be selected');
        }

        return back()->withInput();
    }

    public function getOverDueInvoices(Customer $customer)
    {
        return $customer->orders()->whereDate('due_date', '<=', 'CURDATE()')->where('due', '>', 0)->where('payment_mode', 'Credit')->orderBy('due_date', 'ASC');
    }

    public function unPaidInvoices(Customer $customer)
    {
        return $customer->orders()->where('due', '>', 0)->where('payment_mode', 'Credit')->orderBy('due_date', 'ASC');
    }

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
        $model = new Customer;
        $cart_products = \Cart::getContent();
        return view('pages.inventories.credit_notes.create_credit_note', compact('orders', 'model', 'cart_products', 'order', 'stores'));
    }

    public function payCreditNote(Request $request)
    {
        return "To call your function";
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
            ->where('invoice_no', 'LIKE', "%$search_value%")
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('order_date', 'DESC')->get();
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
        return view('pages.inventories.credit_notes.load_products', ['cart_products' => $cart_products, 'invoice_no' => $invoice_no, 'order' => $order]);
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

    public function importForm()
    {
        $this->authorize('customers.import.form');
        return view('pages.customers.import');
    }

    public function import(Request $request)
    {
        $this->authorize('customers.import');
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $file = $request->file('file');
        $import = new CustomerImport();
        $rows = Excel::toCollection($import, $file)->first();
        $user_branch = User::userBranchAction();
        $faileds = [];
        $count = 0;
        $data = array();
        try {
            foreach ($rows as $row) {
                Customer::updateOrInsert(
                    ['code' => $row['code']],
                    [
                        'name' => trim($row['name']),
                        'email' => $row['email'],
                        'phone' => $row['phone'],
                        'address' => $row['address'],
                        'type' => $row['customer_type'],
                        'credit_limit' => $row['credit_limit'],
                        'branch_id' => Branch::where('code', trim($row['branch_code']))->first()->id ?? 0,
                        'relation_officer' => User::where('user_code', $row['relation_officer_code'])->first()->id ?? 0,
                        'referred_by' => User::where('user_code', $row['referer_code'])->first()->id ?? 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
                $count++;
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
        //dd($faileds);
        session()->flash('app_message', 'File imported and records updated/inserted successfully!');
        return view('pages.customers.import', ['count' => $count]);
    }

    public function verifyNIN($nin)
    {
        $response = Http::withHeaders([
            'x-api-key' => 'your_api_key',
        ])->post('https://api.nimc.gov.ng/verification', [
            'nin' => $nin,
            // Include other required parameters as per NIMC's API documentation
        ]);

        if ($response->successful()) {
            $data = $response->json();
            // Process the verified data
            return $data;
        } else {
            // Handle errors
            return null;
        }
    }
}

