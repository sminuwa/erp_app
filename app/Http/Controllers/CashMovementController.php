<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Http\Requests\CashMovements\Index;
use App\Http\Requests\CashMovements\Show;
use App\Http\Requests\CashMovements\Create;
use App\Http\Requests\CashMovements\Store;
use App\Http\Requests\CashMovements\Edit;
use App\Http\Requests\CashMovements\Update;
use App\Http\Requests\CashMovements\Destroy;
use App\Models\BankAccount;
use App\Models\User;
use Symfony\Component\Mime\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;
use Carbon\Carbon;
use App\Models\AuditLog;


/**
 * Description of CashMovementController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class CashMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.cash_movements.index', [
            'records' => CashMovement::select('cash_movements.*')
            ->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('date_withdraw', 'desc')
            ->orderBy('date_deposit', 'desc')
            ->take(10)->get()]);
    }
    public function search(Request $request)
    {
        $search_value = $request->refno;
        $records = CashMovement::select('cash_movements.*')
            ->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('slip_no', 'LIKE', "%$search_value%")->orderBy('created_at', 'desc')->get();
        return view('pages.cash_movements.index', ['records' => $records]);
    }
    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  CashMovement  $cashmovement
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, CashMovement $cashmovement)
    {
        return view('pages.cash_movements.show', [
            'record' => $cashmovement,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.cash_movements.create', [
            'model' => new CashMovement,
            'bank_accounts' => BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('account_name')->get(),
            'users' => User::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new CashMovement;
        if ($request->from_account == $request->to_account) {
            session()->flash('app_error', 'Withdraw and deposit accounts cannot be the same');
            return redirect()->back()->withInput();
        }
        if (CashMovement::where('slip_no', $request->slip_no)->count('slip_no') > 0) {
            session()->flash('app_error', 'Slip number already exists!');
            return redirect()->back()->withInput();
        }
        $from_balance = intval(preg_replace('/[^\d.]/', '', $request->from_balance));
        $to_balance = intval(preg_replace('/[^\d.]/', '', $request->to_balance));
        DB::beginTransaction();
        try {
            DB::table('cash_movements')->insert([
                'amount' => $request->amount,
                'source_account_id' => $request->from_account,
                'destination_account_id' => $request->to_account,
                'withdraw_by' => Auth::id(), //$request->withdraw_by,
                'deposited_by' => Auth::id(), //$request->deposited_by,
                'sent_by' => $request->sent_by,
                'type' => 'Both', //both operation: withdrawal and deposit
                'slip_no' => $request->slip_no,
                'captured_by' => Auth::id(),
                'date_withdraw' => $request->date_withdraw,
                'date_deposit' => $request->date_deposit,
                'source_balance_before' => $from_balance,
                'destination_balance_before' => $to_balance,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('bank_accounts')->where('id', $request->from_account)->decrement('account_balance', $request->amount);
            DB::table('bank_accounts')->where('id', $request->to_account)->increment('account_balance', $request->amount);
            //Bank Withdrawal
            DB::table('bank_transactions')->insert(['bank_account_id' => $request->from_account,
                'trans_date' => $request->date_withdraw,
                'cr' => 0,
                'dr' => $request->amount,
                'ref_no' => $request->slip_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            //Bank Deposit
            DB::table('bank_transactions')->insert(['bank_account_id' => $request->to_account,
                'trans_date' => $request->date_deposit,
                'cr' => $request->amount,
                'dr' => 0,
                'ref_no' => $request->slip_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $action = "Moved $request->amount cash from " . BankAccount::find($request->from_account)->account_name . " to " . BankAccount::find($request->to_account)->account_name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Transaction saved successfully');
        }
        catch (\Exception $e) {
            session()->flash('app_error', 'Something is wrong while saving the transaction');
            DB::rollBack();
            throw $e;

        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  CashMovement  $cashmovement
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, CashMovement $cashmovement)
    {

        return view('pages.cash_movements.edit', [
            'model' => $cashmovement,
            'bank_accounts' => BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('account_name')->get(),
            'users' => User::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  CashMovement  $cashmovement
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, CashMovement $cashmovement)
    {
        if (CashMovement::where('slip_no', $request->slip_no)->count('slip_no') > 0) {
            session()->flash('app_error', 'Slip number already exists!');
            return redirect()->back()->withInput();
        }
        $from_balance = intval(preg_replace('/[^\d.]/', '', $request->from_balance));
        $to_balance = intval(preg_replace('/[^\d.]/', '', $request->to_balance));
        DB::beginTransaction();
        try {
            DB::table('cash_movements')->where('id', $cashmovement->id)->update([
                'amount' => $request->amount,
                'source_account_id' => $request->from_account,
                'destination_account_id' => $request->to_account,
                'withdraw_by' => Auth::id(), //$request->withdraw_by,
                'deposited_by' => Auth::id(), //$request->deposited_by,
                'sent_by' => $request->sent_by,
                'type' => 'Both', //both operation: withdrawal and deposit
                'slip_no' => $request->slip_no,
                'date_withdraw' => $request->date_withdraw,
                'date_deposit' => $request->date_deposit,
                'source_balance_before' => $from_balance,
                'destination_balance_before' => $to_balance,
                'updated_at' => Carbon::now(),
            ]);

            DB::table('bank_accounts')->where('id', $request->from_account)->increment('account_balance', $cashmovement->amount);
            DB::table('bank_accounts')->where('id', $request->to_account)->decrement('account_balance', $cashmovement->amount);

            DB::table('bank_accounts')->where('id', $request->from_account)->decrement('account_balance', $request->amount);
            DB::table('bank_accounts')->where('id', $request->to_account)->increment('account_balance', $request->amount);

            //Bank withdrawal
            DB::table('bank_transactions')->where(['ref_no' => $cashmovement->slip_no, 'bank_account_id' => $cashmovement->source_account_id])->update([
                'trans_date' => $request->date_deposit,
                'cr' => 0,
                'dr' => $request->amount,
                'ref_no' => $request->slip_no,
                'updated_at' => Carbon::now(),
            ]);
            //Bank Deposit
            DB::table('bank_transactions')->where(['ref_no' => $cashmovement->slip_no, 'bank_account_id' => $cashmovement->destination_account_id])->update([
                'trans_date' => $request->date_deposit,
                'cr' => $request->amount,
                'dr' => 0,
                'ref_no' => $request->slip_no,
                'updated_at' => Carbon::now(),
            ]);

            $action = "Modified movement of $request->amount cash from " . BankAccount::find($request->from_account)->account_name . " to " . BankAccount::find($request->to_account)->account_name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Transaction updated successfully');
        }
        catch (\Exception $e) {
            session()->flash('app_error', 'Something is wrong while updating the transaction');
            DB::rollBack();
            throw $e;

        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  CashMovement  $cashmovement
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, CashMovement $cashmovement)
    {
        DB::beginTransaction();
        try {
            
            DB::table('bank_accounts')->where('id', $cashmovement->source_account_id)->increment('account_balance', $cashmovement->amount);
            DB::table('bank_accounts')->where('id', $cashmovement->destination_account_id)->decrement('account_balance', $cashmovement->amount);

            
            //Delete Bank withdrawal
            DB::table('bank_transactions')->where(['ref_no' => $cashmovement->slip_no, 'bank_account_id' => $cashmovement->source_account_id])->delete();
            //Delete Bank Deposit
            DB::table('bank_transactions')->where(['ref_no' => $cashmovement->slip_no, 'bank_account_id' => $cashmovement->destination_account_id])->delete();
            
            DB::table('cash_movements')->where('id', $cashmovement->id)->delete();
            $action = "Deleted movement of $cashmovement->amount  cash from " . BankAccount::find($cashmovement->source_account_id)->account_name . " to " . BankAccount::find($cashmovement->destination_account_id)->account_name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Transaction deleted successfully');
        }
        catch (\Exception $e) {
            session()->flash('app_error', 'Something is wrong while deleting the transaction');
            DB::rollBack();
            throw $e;

        }
        return redirect()->back();
    }
    public function showDeposit(Request $request, CashMovement $deposit)
    {
        return view('pages.cash_movements.show', [
            'record' => $deposit,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function createDeposit(Request $request)
    {

        return view('pages.cash_movements.create_deposit', [
            'model' => new CashMovement,
            'bank_accounts' => BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('account_name')->get(),
            'users' => User::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function storeDeposit(Request $request)
    {
        $validated = $request->validate([
            'slip_no' => 'required|unique:cash_movements',
            'amount' => 'required|numeric',
            'from_account' => 'required|numeric',
        ]);
        $model = new CashMovement;
        $balance = intval(preg_replace('/[^\d.]/', '', $request->balance));
        DB::beginTransaction();
        try {
            DB::table('cash_movements')->insert([
                'amount' => $request->amount,
                'source_account_id' => $request->from_account,
                'destination_account_id' => 0,
                'withdraw_by' => 0,
                'deposited_by' => Auth::id(), //$request->deposited_by,
                'sent_by' => Auth::id(),
                'type' => 'Deposit', //both operation: withdrawal and deposit
                'slip_no' => $request->slip_no,
                'captured_by' => Auth::id(),
                'date_withdraw' => null,
                'date_deposit' => $request->date_deposit,
                'source_balance_before' => $balance,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('bank_accounts')->where('id', $request->from_account)->increment('account_balance', $request->amount);
            //Bank Deposit
            DB::table('bank_transactions')->insert(['bank_account_id' => $request->from_account,
                'trans_date' => $request->date_deposit,
                'cr' => $request->amount,
                'dr' => 0,
                'ref_no' => $request->slip_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $action = "Deposited $request->amount to " . BankAccount::find($request->from_account)->account_name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Transaction saved successfully');
        }
        catch (\Exception $e) {
            session()->flash('app_error', 'Something is wrong while saving the transaction');
            DB::rollBack();
            throw $e;

        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  CashMovement  $cashmovement
  * @return \Illuminate\Http\Response
  */
    public function editDeposit(Request $request, CashMovement $deposit)
    {

        return view('pages.cash_movements.edit_deposit', [
            'model' => $deposit,
            'bank_accounts' => BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('account_name')->get(),
            'users' => User::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  CashMovement  $cashmovement
  * @return \Illuminate\Http\Response
  */
    public function updateDeposit(Request $request, CashMovement $deposit)
    {
        $validated = $request->validate([
            'slip_no' => "required|unique:cash_movements,slip_no,{$deposit->id}|max:50",
            'amount' => 'required|numeric',
            'from_account' => 'required|numeric',
        ]);
        $balance = intval(preg_replace('/[^\d.]/', '', $request->balance));
        DB::beginTransaction();
        try {
            DB::table('cash_movements')->where('id', $deposit->id)->update([
                'amount' => $request->amount,
                'source_account_id' => $request->from_account,
                'destination_account_id' => 0,
                'withdraw_by' => 0,
                'deposited_by' => Auth::id(), //$request->deposited_by,
                'sent_by' => Auth::id(), //$request->deposited_by,
                'type' => 'Deposit', //both operation: withdrawal and deposit
                'slip_no' => $request->slip_no,
                'date_withdraw' => null,
                'date_deposit' => $request->date_deposit,
                'source_balance_before' => $balance,
                'updated_at' => Carbon::now(),
            ]);

            DB::table('bank_accounts')->where('id', $deposit->source_account_id)->decrement('account_balance', $deposit->amount);
            DB::table('bank_accounts')->where('id', $request->from_account)->increment('account_balance', $request->amount);
            //Bank Deposit
            DB::table('bank_transactions')->where(['ref_no' => $deposit->slip_no, 'bank_account_id' => $deposit->source_account_id])->update([
                'trans_date' => $request->date_deposit,
                'cr' => $request->amount,
                'dr' => 0,
                'ref_no' => $request->slip_no,
                'updated_at' => Carbon::now(),
            ]);
            $action = "Modified $request->amount deposited to " . BankAccount::find($request->from_account)->account_name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Transaction updated successfully');
        }
        catch (\Exception $e) {
            session()->flash('app_error', 'Something is wrong while updating the transaction');
            DB::rollBack();
            throw $e;

        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  CashMovement  $cashmovement
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroyDeposit(Request $request, CashMovement $deposit)
    {
        DB::beginTransaction();
        try {

            DB::table('bank_accounts')->where('id', $deposit->source_account_id)->decrement('account_balance', $deposit->amount);

            DB::table('cash_movements')->where('id', $deposit->id)->delete();
            //Delete Bank Deposit
            DB::table('bank_transactions')->where(['ref_no' => $deposit->slip_no, 'bank_account_id' => $deposit->source_account_id])->delete();
            $action = "Deleted $deposit->amount deposited to " . BankAccount::find($deposit->source_account_id)->account_name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Transaction deleted successfully');
        }
        catch (\Exception $e) {
            session()->flash('app_error', 'Something is wrong while deleting the transaction');
            DB::rollBack();
            throw $e;

        }
        return redirect()->back();
    }

    public function showWithdraw(Request $request, CashMovement $withdraw)
    {
        return view('pages.cash_movements.show', [
            'record' => $withdraw,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function createWithdraw(Request $request)
    {

        return view('pages.cash_movements.create_withdraw', [
            'model' => new CashMovement,
            'bank_accounts' => BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('account_name')->get(),
            'users' => User::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function storeWithdraw(Request $request)
    {

        $validated = $request->validate([
            'slip_no' => 'required|unique:cash_movements',
            'amount' => 'required|numeric',
            'from_account' => 'required|numeric',
        ]);

        $model = new CashMovement;
        $balance = intval(preg_replace('/[^\d.]/', '', $request->balance));
        DB::beginTransaction();
        try {
            DB::table('cash_movements')->insert([
                'amount' => $request->amount,
                'source_account_id' => $request->from_account,
                'destination_account_id' => 0,
                'withdraw_by' => Auth::id(), //$request->withdraw_by,
                'deposited_by' => 0,
                'sent_by' => Auth::id(), //$request->withdraw_by,
                'type' => 'Withdraw', //both operation: withdrawal and deposit
                'slip_no' => $request->slip_no,
                'captured_by' => Auth::id(),
                'date_withdraw' => $request->date_withdraw,
                'date_deposit' => null,
                'source_balance_before' => $balance,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('bank_accounts')->where('id', $request->from_account)->decrement('account_balance', $request->amount);
            //Bank Withdrawal
            DB::table('bank_transactions')->insert(['bank_account_id' => $request->from_account,
                'trans_date' => $request->date_withdraw,
                'cr' => 0,
                'dr' => $request->amount,
                'ref_no' => $request->slip_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $action = "Withdrawn $request->amount from " . BankAccount::find($request->from_account)->account_name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Transaction saved successfully');
        }
        catch (\Exception $e) {
            session()->flash('app_error', 'Something is wrong while saving the transaction');
            DB::rollBack();
            throw $e;

        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  CashMovement  $cashmovement
  * @return \Illuminate\Http\Response
  */
    public function editWithdraw(Request $request, CashMovement $withdraw)
    {

        return view('pages.cash_movements.edit_withdraw', [
            'model' => $withdraw,
            'bank_accounts' => BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('account_name')->get(),
            'users' => User::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  CashMovement  $cashmovement
  * @return \Illuminate\Http\Response
  */
    public function updateWithdraw(Request $request, CashMovement $withdraw)
    {
        $validated = $request->validate([
            'slip_no' => "required|unique:cash_movements,slip_no,{$withdraw->id}|max:50",
            'amount' => 'required|numeric',
            'from_account' => 'required|numeric',
        ]);

        $balance = intval(preg_replace('/[^\d.]/', '', $request->balance));
        DB::beginTransaction();
        try {
            DB::table('cash_movements')->where('id', $withdraw->id)->update([
                'amount' => $request->amount,
                'source_account_id' => $request->from_account,
                'destination_account_id' => 0,
                'withdraw_by' => Auth::id(), //$request->withdraw_by,
                'deposited_by' => 0,
                'sent_by' => Auth::id(), //$request->withdraw_by,
                'type' => 'Withdraw', //both operation: withdrawal and deposit
                'slip_no' => $request->slip_no,
                'captured_by' => Auth::id(),
                'date_withdraw' => $request->date_withdraw,
                'date_deposit' => null,
                'source_balance_before' => $balance,
                'updated_at' => Carbon::now(),
            ]);

            DB::table('bank_accounts')->where('id', $withdraw->source_account_id)->increment('account_balance', $withdraw->amount);
            DB::table('bank_accounts')->where('id', $request->from_account)->decrement('account_balance', $request->amount);
            //Bank withdrawal
            DB::table('bank_transactions')->where(['ref_no' => $withdraw->slip_no, 'bank_account_id' => $withdraw->source_account_id])->update([
                'trans_date' => $request->date_deposit,
                'cr' => 0,
                'dr' => $request->amount,
                'ref_no' => $request->slip_no,
                'updated_at' => Carbon::now(), ]);
            $action = "Modified $request->amount withdrawn from " . BankAccount::find($request->from_account)->account_name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Transaction updated successfully');
        }
        catch (\Exception $e) {
            session()->flash('app_error', 'Something is wrong while updating the transaction');
            DB::rollBack();
            throw $e;

        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  CashMovement  $cashmovement
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroyWithdraw(Request $request, CashMovement $withdraw)
    {
        DB::beginTransaction();
        try {
            DB::table('bank_accounts')->where('id', $withdraw->source_account_id)->increment('account_balance', $withdraw->amount);

            DB::table('cash_movements')->where('id', $withdraw->id)->delete();
            //Delete Bank withdrawal
            DB::table('bank_transactions')->where(['ref_no' => $withdraw->slip_no, 'bank_account_id' => $withdraw->source_account_id])->delete();
            $action = "Deleted $withdraw->amount withdrawn from " . BankAccount::find($withdraw->source_account_id)->account_name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Transaction deleted successfully');
        }
        catch (\Exception $e) {
            session()->flash('app_error', 'Something is wrong while deleting the transaction');
            DB::rollBack();
            throw $e;

        }
        return redirect()->back();
    }
    public function print(CashMovement $cashmovement)
    {
        return view('pages.cash_movements.print', ['record' => $cashmovement]);
    }
}
