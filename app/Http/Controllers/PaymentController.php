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
use App\Notifications\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function payments()
    {
        $user_branch = User::userBranchAction();
        $payments = Payment::select('payments.*')
            ->where('branch_id', $user_branch)
            ->orderBy('status', 'ASC')->orderBy('id', 'DESC')
            ->where('date', '>', Carbon::now()->subDays(7))
            ->get();
        return view('pages.payments.payment', ['payments' => $payments]);
    }
    public function search(Request $request)
    {
        $search_value = $request->refno;
        $payments = Payment::select('payments.*')
            ->where('branch_id', User::userBranchAction())
            ->where('receipt_no', 'LIKE', "%$search_value%")
            ->orderBy('receipt_no', 'DESC')->take(100)->get();
        return view('pages.payments.payment', ['payments' => $payments]);
    }
    public function show(Payment $payment)
    {
        $company = Setting::where('branch_id', User::userBranchAction())->latest()->first();
        return view('pages.payments.preview', compact('payment', 'company'));
    }
    public function pay(Request $request)
    {
        $payment_id = $request->payment_id;
        $amount = str_replace(',', '', $request->amount_paid);
        $bank_account_id = $request->account_id;
        $date = $request->payment_date;
        $ym = Carbon::parse($date)->format('ym');
        $reference_no = Payment::generateNewNumber('PAY', 4, $ym);

        $description = $request->payment_ref;
        $user_branch = User::userBranchAction();
        $status = false;
        $insert_id = 0;
        $ledger_id = 0;
        $payer_id = $request->payer_id;
        $payer_type = $request->type;
        $record = Payment::find($payment_id);
        DB::beginTransaction();
        try {
            if (!$record) {
                $record = new Payment();
                $record->receipt_no = $reference_no;
                $record->created_by = auth()->id();
                $record->status = 0;
            }
            $record->amount = $amount;
            $record->date = $date;
            $record->description = $description;
            $record->model_id = $payer_id;
            $record->model_name = $payer_type;
            $record->charged_account_id = $bank_account_id;
            $record->charged_account_name = 'GeneralAccount';
            $record->branch_id = $user_branch;
            $record->status = 0;
            if ($record->save()) {
                //                if(Transaction::receipt($payer_id, $payer_type, $bank_account_id, 'GeneralAccount', $amount, $reference_no, $date)){
                $action = "Made/Edited payment of $amount for : " . $reference_no;
                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Payment generated successfully');
                DB::commit();

            }
            return redirect()->route('payment.show', $record->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Failed to make payment');
            throw $e;
        }

        return redirect()->route('payment.show', $record->id);

    }

    public function makePayment(Request $request)
    {
        $payment_id = $request->payment_id;
        $payment = Payment::find($payment_id);
        $user_branch = User::userBranchAction();
        $accounts = GeneralAccount::active()->whereIn('class', ['A11', 'A12', 'A13'])->orderBy('number')->get();
        $customers = Customer::active()->whereIn('type', ['Retail', 'Wholesale'])->where('branch_id', $user_branch)->orderBy('name')->get();
        $model = new GeneralAccountLedger;
        if ($payment)
            $model = $payment;
        return view('pages.payments.create_payment', compact('accounts', 'customers', 'model'));
    }
    public function delete(Payment $payment)
    {
        $payment->delete();
        return back();
    }
    public function post(Payment $payment)
    {
        if ($payment->status == 0) {
            $payment->status = 1;
            $payment->posted_by = auth()->id();
            DB::beginTransaction();
            if ($payment->save()) {
                if (
                    Transaction::receipt(
                        $payment->model_id,
                        $payment->model_name,
                        $payment->charged_account_id,
                        $payment->charged_account_name,
                        $payment->amount,
                        $payment->receipt_no,
                        $payment->date
                    )
                ) {
                    $action = "Made payment of $payment->amount for : " . $payment->receipt_no;
                    AuditLog::auditLog(auth()->id(), $action);
                    session()->flash('app_message', 'Payment generated successfully');
                    $phone = null;
                    if ($payment->model_name == "Customer")
                        $phone = $payment->customer->phone;
                    if ($payment->model_name == "Supplier")
                        $phone = $payment->supplier->phone;
                    if ($phone)
                        SMS::sendSms(formatPhoneNumber($phone), "Testing testing, $payment->amount has been paid to your account. Kindly confirm! Albabello Testing SMS", "", "promotional");
                    DB::commit();
                } else {
                    DB::rollBack();
                    session()->flash('app_message', 'Something went wrong');
                }
            }
        }
        return back();
    }
    public function reverse(Payment $payment)
    {
        $payment->status = 0;
        if ($payment->save()) {
            Transaction::reversal($payment->receipt_no);
        }
        return back();
    }
    public function printPaymentReceipt(Payment $payment, $papersize = "A4")
    {
        return view('pages.payments.print_payment', ['payment' => $payment, 'setting' => Setting::first(), 'papersize' => $papersize]);
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
            $payment->delete();
            $action = "Deleted $payment->amount that was posted with invoice $receipt_no ";
            AuditLog::auditLog(auth()->user()->id, $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Payment update failed');
            throw $e;
        }
        return redirect()->back();
    }
    public function generateReceiptNo()
    {
        $invoice = DB::table('general_account_ledgers')->select(DB::raw('MAX(SUBSTR(receipt_no,8,17)) as max'))->where(DB::raw('SUBSTR(receipt_no,1,3)', 'PAY'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->first();

        return 'PAY' . date('y') . str_pad(($invoice->max + 1), 10, "0", STR_PAD_LEFT);
    }
}
