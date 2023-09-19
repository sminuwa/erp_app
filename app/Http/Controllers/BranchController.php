<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Http\Requests\Branches\Index;
use App\Http\Requests\Branches\Show;
use App\Http\Requests\Branches\Create;
use App\Http\Requests\Branches\Store;
use App\Http\Requests\Branches\Edit;
use App\Http\Requests\Branches\Update;
use App\Http\Requests\Branches\Destroy;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


/**
 * Description of BranchController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.branches.index', ['records' => Branch::orderBy('code')->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  Branch  $branch
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, Branch $branch)
    {
        return view('pages.branches.show', [
            'record' => $branch,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.branches.create', [
            'model' => new Branch,

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new Branch;
        $model->fill($request->all());

        if ($model->save()) {
            AuditLog::auditLog(Auth::id(), "Added a new office branch: " . $model->name);
            session()->flash('app_message', 'Branch saved successfully');
            return redirect()->route('branches.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving Branch');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  Branch  $branch
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, Branch $branch)
    {

        return view('pages.branches.edit', [
            'model' => $branch,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  Branch  $branch
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, Branch $branch)
    {
        $branch->fill($request->all());

        if ($branch->save()) {
            AuditLog::auditLog(Auth::id(), "Updated an office branch: " . $branch->name);
            session()->flash('app_message', 'Branch successfully updated');
            return redirect()->route('branches.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating Branch');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  Branch  $branch
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, Branch $branch)
    {
        if ($branch->delete()) {
            AuditLog::auditLog(Auth::id(), "Deleted an office branch: " . $branch->name);
            session()->flash('app_message', 'Branch successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting Branch');
        }

        return redirect()->back();
    }
}
