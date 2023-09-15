<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Http\Requests\Permissions\Index;
use App\Http\Requests\Permissions\Show;
use App\Http\Requests\Permissions\Create;
use App\Http\Requests\Permissions\Store;
use App\Http\Requests\Permissions\Edit;
use App\Http\Requests\Permissions\Update;
use App\Http\Requests\Permissions\Destroy;


/**
 * Description of PermissionController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class PermissionController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.permissions.index', ['records' => Permission::orderBy('name')->get()]);
    }    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, Permission $permission)
    {
        return view('pages.permissions.show', [
                'record' =>$permission,
        ]);

    }    /**
     * Show the form for creating a new resource.
     *
     * @param  Create  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Create $request)
    {

        return view('pages.permissions.create', [
            'model' => new Permission,

        ]);
    }    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $model=new Permission;
        $model->fill($request->all());

        if ($model->save()) {
            
            session()->flash('app_message', 'Permission saved successfully');
            return redirect()->route('permissions.index');
            } else {
                session()->flash('app_message', 'Something is wrong while saving Permission');
            }
        return redirect()->back();
    } /**
     * Show the form for editing the specified resource.
     *
     * @param  Edit  $request
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, Permission $permission)
    {

        return view('pages.permissions.edit', [
            'model' => $permission,

            ]);
    }    /**
     * Update a existing resource in storage.
     *
     * @param  Update  $request
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request,Permission $permission)
    {
        $permission->fill($request->all());

        if ($permission->save()) {
            
            session()->flash('app_message', 'Permission successfully updated');
            return redirect()->route('permissions.index');
            } else {
                session()->flash('app_error', 'Something is wrong while updating Permission');
            }
        return redirect()->back();
    }    /**
     * Delete a  resource from  storage.
     *
     * @param  Destroy  $request
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Destroy $request, Permission $permission)
    {
        if ($permission->delete()) {
                session()->flash('app_message', 'Permission successfully deleted');
            } else {
                session()->flash('app_error', 'Error occurred while deleting Permission');
            }

        return redirect()->back();
    }
}
