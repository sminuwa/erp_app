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
        $payments = GeneralAccountLedger::select('General_account_ledgers.*')
            ->whereIn('model_id', GeneralAccount::where('class','A11')->get('id')->toArray())
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
        $insert_id = 0;
        $ledger_id = 0;

        DB::beginTransaction();
        try {

            DB::table('bank_transactions')->insert([
                'bank_account_id' => $bank_account_id,
                'trans_date' => $request->payment_date,
                'cr' => $amount,
                'dr' => 0,
                'ref_no' => $request->receipt_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            if ($request->has('type') && $request->type == "Customer") {
                $customer_id = $request->payer_id;
                DB::table('general_account_ledgers')->insert([
                    'model_id' => $customer_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => User::userBranchAction(),
                    'description' => $request->payment_ref,
                    'reference' => $request->receipt_no,
                    'cr' => $request->amount,
                    'date' => $request->payment_date,
                    'user_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $ledger_id = DB::table('customer_ledgers')->insertGetId([
                    'customer_id' => $customer_id,
                    'order_id' => 0,
                    'systemid' => gethostname(),
                    'description' => $request->payment_ref,
                    'ref' => $request->payment_ref,
                    'teller_no' => $request->teller_no,
                    'receipt_no' => $request->receipt_no,
                    'bank_account_id' => $bank_account_id,
                    'date' => $request->payment_date,
                    'cr' => $amount,
                    'user_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            if ($request->has('type') && $request->type == "Supplier") {
                $supplier_id = $request->payer_id;
                $ledger_id = DB::table('supplier_ledgers')->insertGetId([
                    'supplier_id' => $supplier_id,
                    'purchase_id' => 0,
                    'description' => $request->payment_ref,
                    'ref' => $request->receipt_no,
                    'teller_no' => $request->receipt_no,
                    'bank_account_id' => $bank_account_id,
                    'date' => $request->payment_date,
                    'cr' => $amount,
                    'user_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $balance = 0;

            $action = "Generated receipt  $request->amount_paid for : " . $request->receipt_no;
            AuditLog::auditLog(auth()->id(), $action);
            DB::commit();
            session()->flash('app_message', 'Receipt generated successfully');
            if ($request->has('customer_id2'))
                return $insert_id;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Failed to generated receipt');
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
        $invoice = DB::table('customer_ledgers')->select(DB::raw('MAX(SUBSTR(receipt_no,10,15)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where('cr', "=", 0)->first();

        return auth()->user()->user_code . '-CP-' . date('y') . str_pad(($invoice->max + 1), 4, "0", STR_PAD_LEFT);
    }
}