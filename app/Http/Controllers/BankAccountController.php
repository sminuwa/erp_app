<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Http\Requests\BankAccounts\Index;
use App\Http\Requests\BankAccounts\Show;
use App\Http\Requests\BankAccounts\Create;
use App\Http\Requests\BankAccounts\Store;
use App\Http\Requests\BankAccounts\Edit;
use App\Http\Requests\BankAccounts\Update;
use App\Http\Requests\BankAccounts\Destroy;
use App\Models\BankBranch;
use App\Models\Bank;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;
use App\Models\User;


/**
 * Description of BankAccountController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.bank_accounts.index', ['records' => BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  BankAccount  $bankaccount
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, BankAccount $bankaccount)
    {
        return view('pages.bank_accounts.show', [
            'record' => $bankaccount,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {
        $banks = Bank::all(['id', 'name']);
        $branches = Branch::where('id','LIKE',User::userBranchAction())->get();
        return view('pages.bank_accounts.create', [
            'model' => new BankAccount,
            'banks' => $banks,
            'branches' => $branches,

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new BankAccount;
        $model->fill($request->all());

        if ($model->save()) {
            AuditLog::auditLog(Auth::id(), "Added a new bank account: " . $model->account_name);
            session()->flash('app_message', 'BankAccount saved successfully');
            return redirect()->route('bank_accounts.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving BankAccount');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  BankAccount  $bankaccount
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, BankAccount $bankaccount)
    {
        $banks = Bank::all(['id', 'name']);
        $branches = Branch::where('id','LIKE',User::userBranchAction())->get();
        return view('pages.bank_accounts.edit', [
            'model' => $bankaccount,
            'banks' => $banks,
            'branches' => $branches,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  BankAccount  $bankaccount
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, BankAccount $bankaccount)
    {
        $bankaccount->fill($request->all());

        if ($bankaccount->save()) {
            AuditLog::auditLog(Auth::id(), "Updated bank account: " . $bankaccount->account_name);
            session()->flash('app_message', 'BankAccount successfully updated');
            return redirect()->route('bank_accounts.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating BankAccount');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  BankAccount  $bankaccount
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, BankAccount $bankaccount)
    {
        if ($bankaccount->delete()) {
            AuditLog::auditLog(Auth::id(), "Deleted bank account: " . $bankaccount->account_name);
            session()->flash('app_message', 'BankAccount successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting BankAccount');
        }

        return redirect()->back();
    }
}
