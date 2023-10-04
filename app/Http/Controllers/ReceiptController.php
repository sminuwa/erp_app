<?php

namespace App\Http\Controllers;

use App\Classes\Transaction;
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

class ReceiptController extends Controller
{
    public function receipts()
    {
        $user_branch = User::userBranchAction();
        $payments = GeneralAccountLedger::select('General_account_ledgers.*')
            ->whereIn('model_id', GeneralAccount::where('class', 'A11')->get('id')->toArray())
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
    {

        $amount = $request->amount_paid;
        $bank_account_id = $request->account_id;
        $refence_no = $request->receipt_no;
        $date = $request->payment_date;
        $status = false;
        $insert_id = 0;
        $ledger_id = 0;

        DB::beginTransaction();
        try {

            DB::table('bank_transactions')->insert([
                'bank_account_id' => $bank_account_id,
                'trans_date' => $request->payment_date,
                'cr' => $amount,
                'dr' => 0,
                'ref_no' => $refence_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            if ($request->has('type') && $request->type == "Customer") {
                $customer_id = $request->payer_id;
                $status = Transaction::receipt($customer_id, 'Customer', $bank_account_id, 'GeneralLedger', $amount, $refence_no, $date);

            }
            if ($request->has('type') && $request->type == "Supplier") {
                $supplier_id = $request->payer_id;
                $status = Transaction::receipt($supplier_id, 'Supplier', $bank_account_id, 'GeneralLedger', $amount, $refence_no, $date);
            }



            DB::commit();
            if ($status == true) {
                $action = "Generated receipt  $request->amount_paid for : " . $request->receipt_no;
                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Receipt generated successfully');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Failed to generated receipt');
            throw $e;
        }

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
    public function printPaymentReceipt(GeneralAccountLedger $payment)
    {
        return view('pages.receipts.print_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function printPoSPaymentReceipt(GeneralAccountLedger $payment)
    {
        return view('pages.receipts.print_pos_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function updatePayment(Request $request, GeneralAccountLedger $ledger)
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
        $invoice = DB::table('general_account_ledgers')->select(DB::raw('MAX(SUBSTR(receipt_no,1,7)) as max'))->where(DB::raw('SUBSTR(receipt_no,1,3)'), '=', 'RCT')->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->first();
        $number = $invoice == null ? 1 : $invoice->max + 1;
        return 'RCT' . date('y') . str_pad($number, 10, "0", STR_PAD_LEFT);
    }
}