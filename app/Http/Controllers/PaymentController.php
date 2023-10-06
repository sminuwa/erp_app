<?php

namespace App\Http\Controllers;


use App\Classes\Transaction;
use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountLedger;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use App\Models\CustomerLedger;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    public function payments()
    {
        $user_branch = User::userBranchAction();
        $payments = Payment::select('payments.*')
            ->where('branch_id', 'LIKE', $user_branch)
            ->orderBy('date', 'DESC')->take(10)->get();
        return view('pages.payments.payment', ['payments' => $payments]);
    }
    public function search(Request $request)
    {
        $search_value = $request->refno;
        $payments = Payment::select('payments.*')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('receipt_no', 'LIKE', "%$search_value%")
            ->orderBy('receipt_no', 'DESC')->take(10)->get();
        return view('pages.payments.payment', ['payments' => $payments]);
    }
    public function pay(Request $request)
    {

        $amount = $request->amount_paid;
        $bank_account_id = $request->account_id;
        $refence_no = $this->generateReceiptNo();
        $date = $request->payment_date;
        $description = $request->payment_ref;
        $user_branch = User::userBranchAction();
        $status = false;
        $insert_id = 0;
        $ledger_id = 0;

        DB::beginTransaction();
        try {

            if ($request->has('type') && $request->type == "Customer") {
                $customer_id = $request->payer_id;
                $status = Transaction::payment($customer_id, 'Customer', $bank_account_id, 'GeneralAccount', $amount, $refence_no, $date);
                $ledger_id = DB::table('payments')->insertGetId([
                    'amount' => $amount,
                    'date' => $date,
                    'receipt_no' => $refence_no,
                    'description' => $description,
                    'recieved_by' => auth()->id(),
                    'model_id' => $customer_id,
                    'model_name' => 'Customer',
                    'charged_account_id' => $bank_account_id,
                    'charged_account_name' => 'GeneralAccount',
                    'branch_id' => $user_branch,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            }
            if ($request->has('type') && $request->type == "Supplier") {
                $supplier_id = $request->payer_id;
                $status = Transaction::payment($supplier_id, 'Supplier', $bank_account_id, 'GeneralAccount', $amount, $refence_no, $date);
                $ledger_id = DB::table('payments')->insertGetId([
                    'amount' => $amount,
                    'date' => $date,
                    'receipt_no' => $refence_no,
                    'description' => $description,
                    'recieved_by' => auth()->id(),
                    'model_id' => $supplier_id,
                    'model_name' => 'Supplier',
                    'charged_account_id' => $bank_account_id,
                    'charged_account_name' => 'GeneralAccount',
                    'branch_id' => $user_branch,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
            if ($request->has('type') && $request->type == "GeneralAccount") {
                $supplier_id = $request->payer_id;
                $status = Transaction::payment($supplier_id, 'GeneralAccount', $bank_account_id, 'GeneralAccount', $amount, $refence_no, $date);
                $ledger_id = DB::table('payments')->insertGetId([
                    'amount' => $amount,
                    'date' => $date,
                    'receipt_no' => $refence_no,
                    'description' => $description,
                    'recieved_by' => auth()->id(),
                    'model_id' => $supplier_id,
                    'model_name' => 'GeneralAccount',
                    'charged_account_id' => $bank_account_id,
                    'charged_account_name' => 'GeneralAccount',
                    'branch_id' => $user_branch,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
            DB::commit();
            if ($status == true) {
                $action = "Posted payment of $amount for : " . $refence_no;
                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Posted  successfully');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Failed to make payment');
            throw $e;
        }

        return redirect()->back()->with(['prev_id' => $ledger_id]);

    }

    public function makePayment()
    {
        $user_branch = User::userBranchAction();
        $accounts = GeneralAccount::whereIn('class', ['A11','A12','A13'])->orderBy('number')->get();
        $customers = Customer::whereIn('type', ['Retail', 'Wholesale'])->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $model = new GeneralAccountLedger;
        return view('pages.payments.create_payment', compact('accounts', 'customers', 'model'));
    }
    public function reverse(Payment $payment) {
        $payment->status = 0;
        $payment->save();
        return back();
    }
    public function printPaymentReceipt(Payment $payment)
    {
        return view('pages.payments.print_payment', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function printPoSPaymentReceipt(Payment $payment)
    {
        return view('pages.payments.print_pos_payment', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function updatePayment(Request $request, Payment $ledger)
    {
        DB::beginTransaction();
        try {
            DB::table('general_account_ledgers')
                ->where('id', $ledger->id)
                ->update(['dr' => $request->amount_paid, 'updated_at' => Carbon::now()]);

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
    public function deletePayment(Request $request, Payment $payment)
    {
        DB::beginTransaction();
        try {
            $receipt_no = $payment->receipt_no;
            Transaction::reversal($receipt_no, 'REVERSAL');

            DB::table('bank_transactions')->where(['ref_no' => $receipt_no])->delete();

            $action = "Deleted $payment->amount that was posted with invoice $receipt_no ";
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
        $invoice = DB::table('general_account_ledgers')->select(DB::raw('MAX(SUBSTR(receipt_no,8,17)) as max'))->where(DB::raw('SUBSTR(receipt_no,1,3)', 'PAY'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->first();

        return 'PAY' . date('y') . str_pad(($invoice->max + 1), 10, "0", STR_PAD_LEFT);
    }
}
