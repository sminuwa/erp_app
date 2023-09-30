<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Http\Requests\Employees\Index;
use App\Http\Requests\Employees\Show;
use App\Http\Requests\Employees\Create;
use App\Http\Requests\Employees\Store;
use App\Http\Requests\Employees\Edit;
use App\Http\Requests\Employees\Update;
use App\Http\Requests\Employees\Destroy;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


/**
 * Description of EmployeeController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.users.index', ['records' => User::orderBy('name')->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  Employee  $employee
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, Employee $employee)
    {
        return view('pages.employees.show', [
            'record' => $employee,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.employees.create', [
            'model' => new Employee,

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new Employee;
        $model->fill($request->all());

        if ($model->save()) {
            $action = "Added a new staff: " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Employee saved successfully');
            return redirect()->route('employees.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving Employee');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  Employee  $employee
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, Employee $employee)
    {

        return view('pages.employees.edit', [
            'model' => $employee,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  Employee  $employee
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, Employee $employee)
    {
        $employee->fill($request->all());

        if ($employee->save()) {
            $action = "Updated a staff: " . $employee->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Employee successfully updated');
            return redirect()->route('employees.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating Employee');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  Employee  $employee
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, Employee $employee)
    {
        if ($employee->delete()) {
            $action = "Deleted a staff: " . $employee->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Employee successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting Employee');
        }

        return redirect()->back();
    }
}
