<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Http\Requests\Banks\Index;
use App\Http\Requests\Banks\Show;
use App\Http\Requests\Banks\Create;
use App\Http\Requests\Banks\Store;
use App\Http\Requests\Banks\Edit;
use App\Http\Requests\Banks\Update;
use App\Http\Requests\Banks\Destroy;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Description of BankController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {

        return view('pages.banks.index', ['records' => Bank::orderBy('name')->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  Bank  $bank
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, Bank $bank)
    {
        return view('pages.banks.show', [
            'record' => $bank,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.banks.create', [
            'model' => new Bank,

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new Bank;
        $model->fill($request->all());

        if ($model->save()) {
            AuditLog::auditLog(Auth::id(), "Added a new bank: " . $model->name);
            session()->flash('app_message', 'Bank saved successfully');
            return redirect()->route('banks.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving Bank');

        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  Bank  $bank
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, Bank $bank)
    {

        return view('pages.banks.edit', [
            'model' => $bank,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  Bank  $bank
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, Bank $bank)
    {
        $bank->fill($request->all());

        if ($bank->save()) {
            AuditLog::auditLog(Auth::id(), "Updated a bank: " . $bank->name);
            session()->flash('app_message', 'Bank successfully updated');
            return redirect()->route('banks.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating Bank');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  Bank  $bank
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, Bank $bank)
    {
        if ($bank->delete()) {
            AuditLog::auditLog(Auth::id(), "Deleted a bank: " . $bank->name);
            session()->flash('app_message', 'Bank successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting Bank');
        }

        return redirect()->back();
    }
}
