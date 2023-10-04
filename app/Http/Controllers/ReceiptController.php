<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountLedger;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use App\Models\CustomerLedger;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PaymentMode;

class ReceiptController extends Controller
{
    public function receipts()
    {
        $user_branch = User::userBranchAction();
        $payments = CustomerLedger::select('customer_ledgers.*')
            ->where('dr', '>', 0)
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('branch_id', 'LIKE', $user_branch)
            ->where('receipt_no', '<>', null)
            ->orderBy('date', 'DESC')->take(10)->get();
        $accounts = BankAccount::all();
        return view('pages.receipts.receipt_payment', ['payments' => $payments, 'accounts' => $accounts]);
    }
    public function search(Request $request)
    {
        $search_value = $request->refno;
        $payments = CustomerLedger::select('customer_ledgers.*')
            ->where('dr', '>', 0)
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('receipt_no', 'LIKE', "%$search_value%")->orderBy('customer_id')
            ->orderBy('receipt_no', 'DESC')->take(10)->get();
        $accounts = BankAccount::all();
        return view('pages.receipts.receipt_payment', ['payments' => $payments, 'accounts' => $accounts]);
    }
    public function payReciept(Request $request)
    { return $request;
        if ($request->has('customer_id2')) {
            $customer_id = $request->customer_id2;
        } else {
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
            DB::table('bank_transactions')->insert([
                'bank_account_id' => $bank_account_id,
                'trans_date' => $request->payment_date,
                'cr' => $amount,
                'dr' => 0,
                'ref_no' => $request->receipt_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $ledger_id = DB::table('customer_ledgers')->insertGetId([
                'customer_id' => $customer_id,
                'order_id' => 0,
                //$request->invoice,
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
            } else { //clear other unpaid invoice if the amount is more than the selected invoice
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
            AuditLog::auditLog(auth()->id(), $action);
            DB::commit();
            if ($request->has('sms')) { //SEND SMS NOTIFICATION
                $customer_phone = $customer->phone;
                $msg = "Dear $customer->name, %0a Your account debited with N" . number_format($request->amount_paid, 2) . ".%0aYour new account balance is N" . number_format($customer->runningBalance(), 2, '.', ',');
                $url = "http://portal.nigeriabulksms.com/api/";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$url");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt(
                    $ch,
                    CURLOPT_POSTFIELDS,
                    "username=engrabusadik@gmail.com&password=Aisha123&message=$msg&sender=ALBABELLO&mobiles=$customer_phone"
                );

                // Receive server response
                $server_output = curl_exec($ch);
                curl_close($ch);
                //return $server_output;
            }
            session()->flash('app_message', 'Payment successfully added');
            if ($request->has('customer_id2'))
                return $insert_id;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Payment failed');
            throw $e;
        }
        //$this->payForMoreInvoices($customer, $balance);

        //return redirect()->route('customers.show', $customer)->with(['record' => Customer::find($customer_id)]);
        //return redirect()->route('debtor.payment.print', $ledger_id);
        return redirect()->back()->with(['prev_id' => $ledger_id]);

    }
    public function loadPayers(Request $request)
    {
        $type = $request->get('type');
        if ($type == "Customer")
            $payers = Customer::orderBy('code')->orderBy('name')->get();
        if ($type == "Supplier")
            $payers = Supplier::orderBy('code')->orderBy('name')->get();

        return view('pages.receipts.load_data_payer', ['payers' => $payers]);
    }
    public function createReciept()
    {
        $user_branch = User::userBranchAction();
        $accounts = GeneralAccount::where('class', 'A11')->orderBy('description')->get();
        $customers = Customer::whereIn('type', ['Retail', 'Wholesale'])->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $model = new GeneralAccountLedger;
        $receipt_no = $this->generateReceiptNo();
        return view('pages.receipts.create_receipt_payment', compact('accounts', 'receipt_no', 'customers', 'model'));
    }
    public function printPaymentReceipt(CustomerLedger $payment)
    {
        return view('pages.receipts.print_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function printPoSPaymentReceipt(CustomerLedger $payment)
    {
        return view('pages.receipts.print_pos_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
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
            AuditLog::auditLog(auth()->user()->id, $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Payment update failed');
            throw $e;
        }
        return redirect()->route('receipts.payments');
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
            AuditLog::auditLog(auth()->user()->id, $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Payment update failed');
            throw $e;
        }
        return redirect()->route('receipts.payments');
    }
    public function generateReceiptNo()
    {
        $invoice = DB::table('customer_ledgers')->select(DB::raw('MAX(SUBSTR(receipt_no,10,13)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where('cr', "=", 0)->first();

        return auth()->user()->user_code . '-CP-' . date('y') . str_pad(($invoice->max + 1), 4, "0", STR_PAD_LEFT);
    }
}