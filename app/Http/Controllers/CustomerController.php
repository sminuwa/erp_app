<?php

namespace App\Http\Controllers;

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

/**
 * Description of CustomerController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        $user_branch = User::userBranchAction();
        return view('pages.customers.index', ['records' => Customer::where('type', 'Credit')->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  Customer  $customer
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, Customer $customer)
    { //dd($customer->ledgers()->get());
        return view('pages.customers.show', [
            'record' => $customer,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.customers.create', [
            'model' => new Customer,

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new Customer;
        $model->fill($request->all());
        $model->type = 'Credit';
        if ($model->save()) {
            $model->branch_id = Auth::user()->branch_id;
            $model->save();
            $action = "Added a new credit customer : " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Customer saved successfully');
            if ($request->has('modal'))
                return back();
            return redirect()->route('customers.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving Customer');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  Customer  $customer
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, Customer $customer)
    {
        $this->getOverDueInvoices($customer)->get();
        return view('pages.customers.edit', [
            'model' => $customer,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  Customer  $customer
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, Customer $customer)
    {
        $customer->fill($request->all());

        if ($customer->save()) {
            $action = "Updated a credit customer : " . $customer->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Customer successfully updated');
            return redirect()->route('customers.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating Customer');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  Customer  $customer
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, Customer $customer)
    {
        if ($customer->customer()->count() == 0) {
            if ($customer->delete()) {
                $action = "Deleted a credit customer : " . $customer->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Customer successfully deleted');
            }
            else {
                session()->flash('app_error', 'Error occurred while deleting Customer');
            }
        }
        else {
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

        return view('pages.customers.load_ledger', compact('ledgers', 'customer', 'from_date', 'to_date', 'balance_b_d','sum_cr_b_d','sum_dr_b_d'));
    }
    public function printLedger($from_date, $to_date, $customer_id)
    {
        $customer = Customer::find($customer_id);
        $query = CustomerLedger::where('customer_id', $customer_id)->whereBetween('date', [$from_date, $to_date]);
        $sum_cr_b_d = CustomerLedger::where('customer_id', $customer_id)->where('date', '<', $from_date)->sum('cr');
        $sum_dr_b_d = CustomerLedger::where('customer_id', $customer_id)->where('date', '<', $from_date)->sum('dr');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d; //balance before date
        $ledgers = $query->orderBy('date')->get();

        return view('pages.customers.print_ledger', compact('ledgers', 'customer', 'from_date', 'to_date', 'balance_b_d','sum_cr_b_d','sum_dr_b_d'));
    }
    public function loadGeneralCustomerLedger(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;
        $type = $request->type;

        if ($customer_id == 'all' && $type == 'Credit') {
            $customer_id = '%';
            $type = "Credit";
        }
        if ($customer_id == 'all' && $type == 'Walked In') {
            $customer_id = '%';
            $type = "Walked In";
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
        return view('pages.customers.load_general_ledger', compact('ledgers', 'from_date', 'to_date', 'balance_b_d', 'customer','sum_cr_b_d','sum_dr_b_d'));
    }
    public function createDebtorpayment()
    {
        $user_branch = User::userBranchAction();
        $bank_accounts = BankAccount::where('branch_id',$user_branch)->orderBy('account_name')->get();
        $customers = Customer::where('type', 'credit')->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $payment_modes = PaymentMode::orderBy('name')->get();
        $model = new CustomerLedger;
        $receipt_no = $this->generateReceiptNo();
        return view('pages.customers.create_debtor_payment', compact('bank_accounts', 'receipt_no', 'customers', 'model', 'payment_modes'));
    }
    public function payDebt(Request $request)
    {
        if ($request->has('customer_id2')) {
            $customer_id = $request->customer_id2;
        }
        else {
            $customer_id = $request->customer_id;
        }

        $customer = Customer::find($customer_id);
        $amount = $request->amount_paid;
        $bank_account_id = $request->bank_account_id;
        $balance = 0;
        $insert_id = 0;
        $ledger_id = 0;
        if ($request->payment_mode == "Cash") {
            $bank_account_id = BankAccount::where(['account_type' => 'Cash', 'status' => 1])->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        }
        DB::beginTransaction();
        try {
            //track all payments made by customer
            $insert_id = DB::table('payments')->insertGetId([
                'amount' => $amount,
                'date_paid' => $request->payment_date,
                'customer_id' => $customer_id,
                'receipt_no' => $request->receipt_no,
                'recieved_by' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            // post the total amount paid to the bank account
            DB::table('bank_accounts')->where(['id' => $bank_account_id])->increment('account_balance', $amount);
            //Bank Deposit
            DB::table('bank_transactions')->insert(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->payment_date,
                'cr' => $amount,
                'dr' => 0,
                'ref_no' => $request->receipt_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $ledger_id = DB::table('customer_ledgers')->insertGetId([
                'customer_id' => $customer_id,
                'order_id' => 0, //$request->invoice,
                'systemid' => gethostname(),
                'description' => 'Payment by ' . $request->payment_mode,
                'ref' => $request->payment_ref,
                'teller_no' => $request->teller_no,
                'receipt_no' => $request->receipt_no,
                'bank_account_id' => $bank_account_id,
                'date' => $request->payment_date,
                'payment_mode' => $request->payment_mode,
                'dr' => $amount,
                'user_id' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $over_due_invoices = $this->unPaidInvoices($customer)->get();
            $balance = 0;
            if (count($over_due_invoices) == 0) {
                DB::table('customers')->where(['id' => $customer->id])->decrement('opening_balance', $amount);
            }
            else { //clear other unpaid invoice if the amount is more than the selected invoice
                DB::table('customers')->where(['id' => $customer->id])->decrement('opening_balance', $amount);
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
                        //DB::table('customers')->where(['id' => $customer->id])->decrement('opening_balance', $amount);
                        $amount = $balance;
                    }

                }
                DB::table('customers')->where(['id' => $customer->id])->decrement('opening_balance', $customer->amount()->sum('cr') - $customer->amount()->sum('dr'));
                if ($balance > 0) {
                    DB::table('customers')->where(['id' => $customer->id])->decrement('opening_balance', $balance);
                }
            }
            $action = "Posted  $request->amount_paid to a credit customer : " . $customer->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            if ($request->has('sms')) { //SEND SMS NOTIFICATION
                $customer_phone = $customer->phone;
                $msg = "Dear $customer->name, %0a Your account debited with N" . number_format($request->amount_paid, 2) . ".%0aYour new account balance is N" . number_format($customer->runningBalance(), 2, '.', ',');
                $url = "http://portal.nigeriabulksms.com/api/";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$url");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                    "username=engrabusadik@gmail.com&password=Aisha123&message=$msg&sender=ALBABELLO&mobiles=$customer_phone");

                // Receive server response
                $server_output = curl_exec($ch);
                curl_close($ch);
            //return $server_output;
            }
            session()->flash('app_message', 'Payment successfully added');
            if ($request->has('customer_id2'))
                return $insert_id;
        }
        catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Payment failed');
            throw $e;
        }
        //$this->payForMoreInvoices($customer, $balance);

        //return redirect()->route('customers.show', $customer)->with(['record' => Customer::find($customer_id)]);
        //return redirect()->route('debtor.payment.print', $ledger_id);
        return redirect()->back()->with(['prev_id' => $ledger_id]);

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
        $customers = Customer::where('type', 'credit')->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        return view('pages.customers.create_opening_balance', [
            'customers' => $customers,
            'model' => null]);
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
    public function loadCustomerBalance(Request $request)
    {
        $balance = CustomerLedger::where('customer_id', $request->customer_id)->sum('cr') - CustomerLedger::where('customer_id', $request->customer_id)->sum('dr');
        return $balance;
    }
    public function getCustomerCreditLimit(Request $request)
    {
        return Customer::find($request->customer_id)->credit_limit;
    }
    public function updateCreditLimit(Request $request)
    {
        if ($request->customer_id != null) {
            $customer_id = $request->customer_id;
            $new_amount = $request->new_amount;
            $customer = Customer::find($customer_id);
            $customer->credit_limit = $new_amount;
            $customer->save();
            $action = "Updated  opening balance of customer ($request->new_amount) : " . Customer::find($customer_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Customer credit limit successfully updated');
        }
        else {
            session()->flash('app_error', 'Customer must be selected');
        }

        return back()->withInput();
    }
    public function generateReceiptNo()
    {
        $invoice = DB::table('customer_ledgers')->select(DB::raw('MAX(SUBSTR(receipt_no,10,13)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where('cr',"=",0)->first();
        
        return Auth::user()->user_code . '-CP-' . date('y') . str_pad(($invoice->max + 1), 4, "0", STR_PAD_LEFT);
    }
    public function getOverDueInvoices(Customer $customer)
    {
        return $customer->orders()->whereDate('due_date', '<=', 'CURDATE()')->where('due', '>', 0)->where('payment_mode', 'Credit')->orderBy('due_date', 'ASC');
    }
    public function unPaidInvoices(Customer $customer)
    {
        return $customer->orders()->where('due', '>', 0)->where('payment_mode', 'Credit')->orderBy('due_date', 'ASC');
    }
    public function debtorPayments()
    {
        $user_branch = User::userBranchAction();
        $payments = CustomerLedger::select('customer_ledgers.*')
        ->where('dr', '>', 0)
        ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
        ->where('branch_id', 'LIKE', $user_branch)
        ->where('receipt_no', '<>', null)
        ->orderBy('date', 'DESC')->take(10)->get();
        $accounts = BankAccount::all();
        return view('pages.customers.debtor_payment', ['payments' => $payments, 'accounts' => $accounts]);
    }
    public function search(Request $request)
    {
        $search_value = $request->refno;
        $payments = CustomerLedger::select('customer_ledgers.*')
        ->where('dr', '>', 0)
        ->join('customers','customers.id','customer_ledgers.customer_id')
        ->where('branch_id', 'LIKE', User::userBranchAction())
        ->where('receipt_no', 'LIKE', "%$search_value%")->orderBy('customer_id')
        ->orderBy('receipt_no', 'DESC')->take(10)->get();
        $accounts = BankAccount::all();
        return view('pages.customers.debtor_payment', ['payments' => $payments, 'accounts' => $accounts]);
    }
    public function printPaymentReceipt(CustomerLedger $payment)
    {
        return view('pages.customers.print_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function printPoSPaymentReceipt(CustomerLedger $payment)
    {
        return view('pages.customers.print_pos_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function updatePayment(Request $request, CustomerLedger $ledger)
    {
        DB::beginTransaction();
        try {
            DB::table('customer_ledgers')
                ->where('id', $ledger->id)
                ->update(['dr' => $request->amount_paid, 'updated_at' => Carbon::now()]);

            DB::table('payments')
                ->where(['receipt_no' => $ledger->receipt_no, 'customer_id' => $ledger->customer_id])
                ->update(['amount' => $request->amount_paid, 'updated_at' => Carbon::now()]);

            DB::table('bank_transactions')->where(['ref_no' => $ledger->receipt_no])->update([
                'cr' => $request->amount_paid,
                'updated_at' => Carbon::now()
            ]);
            $action = "Modified $ledger->dr to $request->amount_paid for a credit customer  : " . $ledger->customer->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Payment update failed');
            throw $e;
        }
        return redirect()->route('debtor.payments');
    }
    public function deletePayment(Request $request, CustomerLedger $ledger)
    {
        DB::beginTransaction();
        try {
            DB::table('payments')
                ->where(['receipt_no' => $ledger->receipt_no, 'customer_id' => $ledger->customer_id])
                ->delete();

            DB::table('bank_transactions')->where(['ref_no' => $ledger->receipt_no])->delete();
            DB::table('customer_ledgers')
                ->where('id', $ledger->id)
                ->delete();
            $action = "Deleted $ledger->dr that was posted to a credit customer  : " . $ledger->customer->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Payment update failed');
            throw $e;
        }
        return redirect()->route('debtor.payments');
    }
}
