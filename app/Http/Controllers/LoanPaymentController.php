<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LoanPayment;
use App\Http\Requests\LoanPayments\Index;
use App\Http\Requests\LoanPayments\Show;
use App\Http\Requests\LoanPayments\Create;
use App\Http\Requests\LoanPayments\Store;
use App\Http\Requests\LoanPayments\Edit;
use App\Http\Requests\LoanPayments\Update;
use App\Http\Requests\LoanPayments\Destroy;
use Illuminate\Support\Facades\DB;
use App\Models\LoanCollector;
use App\Models\Loan;
use App\Models\BankAccount;
use Carbon\Carbon;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


/**
 * Description of LoanPaymentController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class LoanPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.loan_payments.index', ['records' => LoanPayment::latest('date')
            ->join('loans', 'loans.id', 'loan_payments.loan_id')
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->join('bank_accounts', 'bank_accounts.id', 'loans.bank_account_id')
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->where('loan_collectors.branch_id', 'LIKE', User::userBranchAction())
            ->get()]);
    }

    public function search(Request $request)
    {
        $search_value = $request->refno;
        $records = LoanPayment::where('loan_payments.receipt_no', 'LIKE', "%$search_value%")
            ->join('loans', 'loans.id', 'loan_payments.loan_id')
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->join('bank_accounts', 'bank_accounts.id', 'loans.bank_account_id')
            ->orWhere('loan_payments.cheque_no', 'LIKE', "%$search_value%")
            ->orWhere('loans.receipt_no', 'LIKE', "%$search_value%")
            ->orWhere('loan_collectors.reg_code', 'LIKE', "%$search_value%")
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->where('loan_collectors.branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('date', 'DESC')->get();
        return view('pages.loan_payments.index', ['records' => $records]);
    }
    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  LoanPayment  $loanpayment
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, LoanPayment $loanpayment)
    {
        return view('pages.loan_payments.show', [
            'record' => $loanpayment,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.loan_payments.create', [
            'model' => new LoanPayment,
            'receipt_no' => $this->receiptNo(),
            'loans' => Loan::select('name', 'reg_code', 'loan_collector_id', DB::raw("sum(balance) AS total"))
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->where('loan_collectors.branch_id', 'LIKE', User::userBranchAction())
            ->where('balance', '>', 0)->groupBy('loan_collector_id')->orderBy('date', "DESC")->get(),

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {

        $model = new LoanPayment;
        $bank_account_id = $request->bank_account_id;
        $amount = $request->amount;
        //$model->fill($request->all());
        if ($request->payment_mode == "Cash") {
            $bank_account_id = BankAccount::where(['account_type' => 'Cash', 'status' => 1])->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        }
        $payment_ids = [];
        if ($amount > 0) {
            $original_amount = $amount;
            $loan_id = $request->loan_id;
            $loans = Loan::where('loan_collector_id', $loan_id)->where('balance', '>', 0)->get();
            foreach ($loans as $loan) {
                while ($amount > 0 && $loan->balance > 0) {
                    $posted_amount = $loan->balance;
                    $payment = new LoanPayment();
                    $payment->loan_id = $loan->id;
                    $payment->amount = $posted_amount;
                    $payment->payment_mode = $request->payment_mode;
                    $payment->bank_account_id = $bank_account_id;
                    $payment->cheque_no = $request->cheque_no;
                    $payment->receipt_no = $request->receipt_no;
                    $payment->received_by = $request->received_by;
                    $payment->save();
                    $payment_ids[] = $payment->id;
                    //if ($payment->save()) {
                    $loan->decrement('balance', $posted_amount);
                    $loan->increment('amount_paid', $posted_amount);
                    $amount = $amount - $posted_amount;
                //}
                }

            }
            DB::table('bank_accounts')->where(['id' => $bank_account_id])->increment('account_balance', $original_amount);
            //Bank Deposit
            DB::table('bank_transactions')->insert(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->date,
                'cr' => $original_amount,
                'dr' => 0,
                'ref_no' => $request->receipt_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $action = "Posted $amount as loan payment issued to  " . Loan::find($loan_id)->collector->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Loan Payment saved successfully');
            //return redirect()->route('loan_payments.index');
            return redirect()->route('loan_payments.print', implode(',', $payment_ids));
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving LoanPayment');
        }
        return redirect()->back();

    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  LoanPayment  $loanpayment
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, LoanPayment $loanpayment)
    {

        return view('pages.loan_payments.edit', [
            'model' => $loanpayment,
            'loans' => Loan::select('name', 'reg_code', 'loan_collector_id', DB::raw("sum(balance) AS total"))
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->where('loan_collectors.branch_id', 'LIKE', User::userBranchAction())
            ->groupBy('loan_collector_id')->orderBy('date', "DESC")->get(),

        ]);
    }
    public function print($loanpayment)
    {
        $ids = explode(',', $loanpayment);
        $payments = LoanPayment::whereIn('id', $ids)->get();
        $collector = LoanPayment::find($ids[0])->loan->collector->name;
        return view('pages.loan_payments.print', [
            'payments' => $payments,
            'collector' => $collector

        ]);
    }
    /**
     * Update a existing resource in storage.
     *
     * @param  Update  $request
     * @param  LoanPayment  $loanpayment
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, LoanPayment $loanpayment)
    {
        $bank_account_id = $request->bank_account_id;
        $loanpayment->fill($request->all());
        $amount = $request->amount;
        if ($request->payment_mode == "Cash") {
            $bank_account_id = BankAccount::where(['account_type' => 'Cash', 'status' => 1])->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        }
        if ($loanpayment->save()) {
            $loanpayment->bank_account_id = $bank_account_id;
            $loanpayment->loan()->decrement('amount_paid', $loanpayment->amount);
            $loanpayment->loan()->increment('amount_paid', $amount);

            $loanpayment->loan()->increment('balance', $loanpayment->amount);
            $loanpayment->loan()->decrement('balance', $amount);

            $loanpayment->save();
            //Undo the previous one
            DB::table('bank_accounts')->where(['id' => $loanpayment->bank_account_id])->decrement('account_balance', $loanpayment->amount);
            //redo with the modified amount
            DB::table('bank_accounts')->where(['id' => $bank_account_id])->increment('account_balance', $amount);
            //Bank Deposit
            DB::table('bank_transactions')->where(['bank_account_id' => $loanpayment->bank_account_id, 'ref_no' => $loanpayment->receipt_no])->update(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->date,
                'cr' => $amount,
                'dr' => 0,
                'ref_no' => $request->receipt_no,
                'updated_at' => Carbon::now(),
            ]);
            $action = "Modified $amount as loan payment issued to  " . $loanpayment->loan->collector->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'LoanPayment successfully updated');
            return redirect()->route('loan_payments.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating LoanPayment');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  LoanPayment  $loanpayment
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, LoanPayment $loanpayment)
    {
        if ($loanpayment->delete()) {
            DB::table('bank_accounts')->where(['id' => $loanpayment->bank_account_id])->decrement('account_balance', $loanpayment->amount);
            //Bank Deposit
            DB::table('bank_transactions')->where(['bank_account_id' => $loanpayment->bank_account_id, $loanpayment->receipt_no])->delete();
            $action = "Deleted $loanpayment->amount as loan payment issued to  " . $loanpayment->loan->collector->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Loan Payment successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting LoanPayment');
        }

        return redirect()->back();
    }
    public function receiptNo()
    {
        $invoice = DB::table('loan_payments')->select(DB::raw('MAX(SUBSTR(receipt_no,3,5)) as max'))->first();
        return 'LP' . str_pad(($invoice->max + 1), 3, "0", STR_PAD_LEFT);
    }
}
