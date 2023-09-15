<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BankBranch;
use App\Http\Requests\BankBranches\Index;
use App\Http\Requests\BankBranches\Show;
use App\Http\Requests\BankBranches\Create;
use App\Http\Requests\BankBranches\Store;
use App\Http\Requests\BankBranches\Edit;
use App\Http\Requests\BankBranches\Update;
use App\Http\Requests\BankBranches\Destroy;
use App\Models\Bank;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


/**
 * Description of BankBranchController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class BankBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.bank_branches.index', ['records' => BankBranch::orderBy('name')->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  BankBranch  $bankbranch
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, BankBranch $bankbranch)
    {
        return view('pages.bank_branches.show', [
            'record' => $bankbranch,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {
        $banks = Bank::orderBy('name')->get();
        return view('pages.bank_branches.create', [
            'model' => new BankBranch,
            'banks' => $banks,

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new BankBranch;
        $model->fill($request->all());

        if ($model->save()) {
            AuditLog::auditLog(Auth::id(), "Added a new bank branch: " . $model->name);
            session()->flash('app_message', 'BankBranch saved successfully');
            return redirect()->route('bank_branches.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving BankBranch');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  BankBranch  $bankbranch
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, BankBranch $bankbranch)
    {
        $banks = Bank::orderBy('name')->get();
        return view('pages.bank_branches.edit', [
            'model' => $bankbranch,
            'banks' => $banks,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  BankBranch  $bankbranch
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, BankBranch $bankbranch)
    {
        $bankbranch->fill($request->all());

        if ($bankbranch->save()) {
            AuditLog::auditLog(Auth::id(), "Updated bank branch: " . $bankbranch->name);
            session()->flash('app_message', 'BankBranch successfully updated');
            return redirect()->route('bank_branches.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating BankBranch');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  BankBranch  $bankbranch
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, BankBranch $bankbranch)
    {
        if ($bankbranch->delete()) {
            AuditLog::auditLog(Auth::id(), "Added a new bank branch: " . $bankbranch->name);
            session()->flash('app_message', 'BankBranch successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting BankBranch');
        }

        return redirect()->back();
    }
}
