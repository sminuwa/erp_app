<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CreditLimit;
use App\Http\Requests\CreditLimits\Index;
use App\Http\Requests\CreditLimits\Show;
use App\Http\Requests\CreditLimits\Create;
use App\Http\Requests\CreditLimits\Store;
use App\Http\Requests\CreditLimits\Edit;
use App\Http\Requests\CreditLimits\Update;
use App\Http\Requests\CreditLimits\Destroy;
use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Description of CreditLimitController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class CreditLimitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.credit_limits.index', ['records' => CreditLimit::paginate(10)]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  CreditLimit  $creditlimit
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, CreditLimit $creditlimit)
    {
        return view('pages.credit_limits.show', [
            'record' => $creditlimit,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.credit_limits.create', [
            'model' => new CreditLimit,
            'customers' => Customer::all(['id', 'name']),

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new CreditLimit;
        $model->fill($request->all());

        if ($model->save()) {
            $action = "Set a Credit limit of : " . $model->customer->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'CreditLimit saved successfully');
            return redirect()->route('credit_limits.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving CreditLimit');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  CreditLimit  $creditlimit
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, CreditLimit $creditlimit)
    {

        return view('pages.credit_limits.edit', [
            'model' => $creditlimit,
            'customers' => Customer::all(['id', 'name']),


        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  CreditLimit  $creditlimit
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, CreditLimit $creditlimit)
    {
        $creditlimit->fill($request->all());

        if ($creditlimit->save()) {
            $action = "Modified a credit limit of : " . $creditlimit->customer->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'CreditLimit successfully updated');
            return redirect()->route('credit_limits.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating CreditLimit');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  CreditLimit  $creditlimit
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, CreditLimit $creditlimit)
    {
        if ($creditlimit->delete()) {
            $action = "Deleted a credit limit of : " . $creditlimit->customer->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'CreditLimit successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting CreditLimit');
        }

        return redirect()->back();
    }
}
