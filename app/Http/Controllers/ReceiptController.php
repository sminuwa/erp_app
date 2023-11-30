<?php

namespace App\Http\Controllers;

use App\Classes\Transaction;
use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountLedger;
use App\Models\Receipt;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use App\Models\CustomerLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    public function receipts()
    {
        $date = \Carbon\Carbon::today()->subDays(7);
        $user_branch = User::userBranchAction();
        $payments = Receipt::select('receipts.*')
            ->where('branch_id', 'LIKE', $user_branch)
            ->where('created_at','>=',$date)
            ->orderBy('id', 'DESC')->get();
        return view('pages.receipts.receipt_payment', ['payments' => $payments]);
    }

    public function search(Request $request)
    {
        $search_value = $request->refno;
        $payments = Receipt::select('receipts.*')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('receipt_no', 'LIKE', "%$search_value%")
            ->orderBy('receipt_no', 'DESC')->take(10)->get();
        return view('pages.receipts.receipt_payment', ['payments' => $payments]);
    }

    public function payReciept(Request $request)
    {
        $receipt_id = $request->receipt_id;
        $amount = $request->amount_paid;
        $bank_account_id = $request->account_id;
        $reference_no = Receipt::generateNewNumber();
        $date = $request->payment_date;
        $description = $request->payment_ref;
        $user_branch = User::userBranchAction();
        $status = false;
        $insert_id = 0;
        $ledger_id = 0;
        $payer_id = $request->payer_id;
        $payer_type = $request->type;
        $record = Receipt::find($receipt_id);
        DB::beginTransaction();
        try {
            if(!$record){
                $record = new Receipt();
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
            if($record->save()){
//                if(Transaction::receipt($bank_account_id, 'GeneralAccount',$payer_id, $payer_type,  $amount, $reference_no, $date)){
                    $action = "Generated receipt of $amount for : " . $reference_no;
                    AuditLog::auditLog(auth()->id(), $action);
                    session()->flash('app_message', 'Receipt generated successfully');
                    DB::commit();
//                }else {
//                    DB::rollBack();
//                    return 'something went wrong';
//                }
            }
            return redirect()->back()->with(['prev_id' => $ledger_id]);

            /*if ($request->has('type') && $request->type == "Customer") {
                $customer_id = $request->payer_id;
                $status = Transaction::receipt($bank_account_id, 'GeneralAccount',$customer_id, 'Customer',  $amount, $reference_no, $date);
                $ledger_id = DB::table('receipts')->insertGetId([
                    'amount' => $amount,
                    'date' => $date,
                    'receipt_no' => $reference_no,
                    'description' => $description,
                    'recieved_by' => auth()->id(),
                    'model_id' => $customer_id,
                    'model_name' => 'Customer',
                    'charged_account_id' => $bank_account_id,
                    'charged_account_name' => 'GeneralAccount',
                    'branch_id' => $user_branch,

                ]);

            }
            if ($request->has('type') && $request->type == "Supplier") {
                $supplier_id = $request->payer_id;
                $status = Transaction::receipt($bank_account_id, 'GeneralAccount', $supplier_id, 'Supplier', $amount, $reference_no, $date);
                $ledger_id = DB::table('receipts')->insertGetId([
                    'amount' => $amount,
                    'date' => $date,
                    'receipt_no' => $reference_no,
                    'description' => $description,
                    'recieved_by' => auth()->id(),
                    'model_id' => $supplier_id,
                    'model_name' => 'Supplier',
                    'charged_account_id' => $bank_account_id,
                    'charged_account_name' => 'GeneralAccount',
                    'branch_id' => $user_branch,

                ]);
            }
            if ($request->has('type') && $request->type == "GeneralAccount") {
                $supplier_id = $request->payer_id;
                $status = Transaction::receipt($bank_account_id, 'GeneralAccount', $supplier_id, 'GeneralAccount', $amount, $reference_no, $date);
                $ledger_id = DB::table('receipts')->insertGetId([
                    'amount' => $amount,
                    'date' => $date,
                    'receipt_no' => $reference_no,
                    'description' => $description,
                    'recieved_by' => auth()->id(),
                    'model_id' => $supplier_id,
                    'model_name' => 'GeneralAccount',
                    'charged_account_id' => $bank_account_id,
                    'charged_account_name' => 'GeneralAccount',
                    'branch_id' => $user_branch,

                ]);
            }
            DB::commit();
            if ($status == true) {
                $action = "Generated receipt of $amount for : " . $refence_no;
                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Receipt generated successfully');
            }*/

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
            $payers = Customer::where('branch_id', auth()->user()->branch->id)->orderBy('code')->orderBy('code')->get();
        if ($type == "Supplier")
            $payers = Supplier::orderBy('code')->orderBy('code')->get();
        if ($type == "GeneralAccount")
            $payers = GeneralAccount::orderBy('number')->orderBy('number')->get();

        return view('pages.receipts.load_data_payer', ['payers' => $payers]);
    }

    public function createReciept(Request $request)
    {
        $receipt_id = $request->receipt_id;
        $receipt = Receipt::find($receipt_id);
//        return $receipt;
        $user_branch = User::userBranchAction();
        $accounts = GeneralAccount::whereIn('class', ['A11', 'A12', 'A13'])->orderBy('number')->get();
        $customers = Customer::whereIn('type', ['Retail', 'Wholesale'])->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $model = new GeneralAccountLedger;
        if($receipt)
            $model = $receipt;
        return view('pages.receipts.create_receipt_payment', compact('accounts', 'customers', 'model'));
    }

    public function post(Receipt $receipt) {
        $receipt->status = 1;
        $receipt->posted_by = auth()->id();
        DB::beginTransaction();
        if($receipt->save()){
            if(Transaction::receipt(
                $receipt->charged_account_id,
                $receipt->charged_account_name,
                $receipt->model_id,
                $receipt->model_name,
                $receipt->amount,
                $receipt->receipt_no,
                $receipt->date)
            ){
                $ledger = new CustomerLedger();
                $action = "Generated receipt of $receipt->amount for : " . $receipt->receipt_no;
                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Receipt generated successfully');
                DB::commit();
            }else {
                DB::rollBack();
                session()->flash('app_message', 'Something went wrong');
            }
        }
        return back();
    }

    public function reverse(Receipt $receipt) {
         $receipt->status = 0;
        $receipt->updated_by = auth()->id();
         DB::beginTransaction();
        if($receipt->save()){
            if(Transaction::reversal($receipt->receipt_no)['status']){
                $action = "Generated receipt of $receipt->amount for : " . $receipt->receipt_no;
                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Receipt generated successfully');
                DB::commit();
            }else{
                DB::rollBack();
                session()->flash('app_message', 'Something went wrong');
            }
        }
         return back();
    }

    public function delete(Receipt $receipt) {
        if($receipt->delete()){

        }
        return back();
    }

    public function printReceipt(Receipt $payment)
    {
        return view('pages.receipts.print_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }

    public function printPoSPaymentReceipt(Receipt $payment)
    {
        return view('pages.receipts.print_pos_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }

    public function updatePayment(Request $request, Receipt $payment)
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

            DB::table('receipts')->insert([
                'amount' => $amount,
                'date' => $date,
                'receipt_no' => $refence_no,
                'description' => $description,
                'recieved_by' => auth()->id(),
                'model_id' => $customer_id,
                'model_name' => 'Supplier',
                'branch_id' => $user_branch,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
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
        $invoice = DB::table('general_account_ledgers')->select(DB::raw('MAX(SUBSTR(receipt_no,8,17)) as max'))->where(DB::raw('SUBSTR(receipt_no,1,3)'), '=', 'RCT')->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->first();
        $number = $invoice == null ? 1 : $invoice->max + 1;
        return 'RCT' . date('y') . str_pad($number, 10, "0", STR_PAD_LEFT);
    }
}
