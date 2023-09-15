<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Http\Requests\Loans\Index;
use App\Http\Requests\Loans\Show;
use App\Http\Requests\Loans\Create;
use App\Http\Requests\Loans\Store;
use App\Http\Requests\Loans\Edit;
use App\Http\Requests\Loans\Update;
use App\Http\Requests\Loans\Destroy;
use Illuminate\Support\Facades\DB;
use App\Models\BankAccount;
use Carbon\Carbon;
use App\Models\LoanCollector;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


/**
 * Description of LoanController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.loans.index', ['records' => Loan::latest('loans.created_at')
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->join('bank_accounts', 'bank_accounts.id', 'loans.bank_account_id')
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->where('loan_collectors.branch_id', 'LIKE', User::userBranchAction())
            ->take(10)->get()]);
    }

    public function search(Request $request)
    {
        $search_value = $request->refno;
        $records = Loan::where('receipt_no', 'LIKE', "%$search_value%")
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->join('bank_accounts', 'bank_accounts.id', 'loans.bank_account_id')
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->where('loan_collectors.branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('date', 'DESC')->get();
        return view('pages.loans.index.index', ['records' => $records]);
    }
    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, Loan $loan)
    {
        return view('pages.loans.show', [
            'record' => $loan,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.loans.create', [
            'model' => new Loan,
            'receipt_no' => $this->receiptNo(),
            'collectors' => LoanCollector::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get()
        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new Loan;
        $bank_account_id = $request->bank_account_id;
        $amount = $request->amount;
        $model->fill($request->all());
        if ($request->payment_mode == "Cash") {
            $bank_account_id = BankAccount::where(['account_type' => 'Cash', 'status' => 1])->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        }
        if ($model->save()) {
            $model->balance = $amount;
            $model->bank_account_id = $bank_account_id;
            $model->save();
            DB::table('bank_accounts')->where(['id' => $bank_account_id])->decrement('account_balance', $amount);
            //Bank Deposit
            DB::table('bank_transactions')->insert(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->date,
                'cr' => 0,
                'dr' => $amount,
                'ref_no' => $request->receipt_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $action = "Issued loan of $amount to  " . $model->collector->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Loan saved successfully');
            return redirect()->route('loans.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving Loan');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  Loan  $loan
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, Loan $loan)
    {

        return view('pages.loans.edit', [
            'model' => $loan,
            'collectors' => LoanCollector::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get()

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  Loan  $loan
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, Loan $loan)
    {
        $loan->fill($request->all());
        $bank_account_id = $request->bank_account_id;
        $amount = $request->amount;
        if ($request->payment_mode == "Cash") {
            $bank_account_id = BankAccount::where(['account_type' => 'Cash', 'status' => 1])->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        }
        if ($loan->save()) {
            $loan->bank_account_id = $bank_account_id;
            $loan->save();
            //Put the money back
            DB::table('bank_accounts')->where(['id' => $loan->bank_account_id])->increment('account_balance', $loan->amount);
            //Withdraw from the account
            DB::table('bank_accounts')->where(['id' => $bank_account_id])->decrement('account_balance', $amount);
            //Bank Deposit
            DB::table('bank_transactions')->where(['bank_account_id' => $loan->bank_account_id, 'receipt_no' => $loan->receipt_no])->update(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->date,
                'cr' => 0,
                'dr' => $amount,
                'ref_no' => $request->receipt_no,
                'updated_at' => Carbon::now(),
            ]);
            $action = "Modified loan of $amount issued to  " . $loan->collector->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Loan successfully updated');
            return redirect()->route('loans.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating Loan');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  Loan  $loan
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, Loan $loan)
    {
        if ($loan->payments()->count('*') > 0) {
            session()->flash('app_error', 'Record cannot be deleted because the person has made payment for the loan collected');
            return redirect()->back();
        }
        $bank_account_id = $request->bank_account_id;
        $amount = $request->amount;
        if ($request->payment_mode == "Cash") {
            $bank_account_id = BankAccount::where(['account_type' => 'Cash', 'status' => 1])->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        }
        if ($loan->delete()) {
            //Put the money back
            DB::table('bank_accounts')->where(['id' => $loan->bank_account_id])->increment('account_balance', $loan->amount);
            DB::table('bank_transactions')->where(['bank_account_id' => $loan->bank_account_id, 'receipt_no' => $loan->receipt_no])->delete();
            $action = "Deleted loan of $amount issued to  " . $loan->collector->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Loan successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting Loan');
        }

        return redirect()->back();
    }
    public function receiptNo()
    {
        $invoice = DB::table('loans')->select(DB::raw('MAX(SUBSTR(receipt_no,3,5)) as max'))->first();
        return 'LG' . str_pad(($invoice->max + 1), 3, "0", STR_PAD_LEFT);
    }
}
