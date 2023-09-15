<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Users\Index;
use App\Http\Requests\Users\Show;
use App\Http\Requests\Users\Create;
use App\Http\Requests\Users\Store;
use App\Http\Requests\Users\Edit;
use App\Http\Requests\Users\Update;
use App\Http\Requests\Users\Destroy;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ModelHasRoleRequest;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Http\Requests\ModelHasPermissionRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

/**
 * Description of UserController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        $user_branch = User::userBranchAction();


        return view('pages.users.index', ['records' => User::where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  User  $user
  * @return \Illuminate\Http\Response
  */

    public function show(Show $request, User $user)
    {
        return view('pages.users.show', [
            'record' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {
        $user_branch = Auth::user()->branch_id;
        if (Auth::user()->hasAnyRole('Super-admin')) {
            $user_branch = '%';
        }

        return view('pages.users.create', [
            'model' => new User,
            'branches' => Branch::where('id', 'LIKE', $user_branch)->get(),

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new User;
        $model->fill($request->all());
        $model->password = bcrypt(123456);
        $model->created_by = Auth::id();
        if ($model->save()) {
            $action = "Added a new user: " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'User saved successfully');
            return redirect()->route('users.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving User');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  User  $user
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, User $user)
    {
        $user_branch = Auth::user()->branch_id;
        if (Auth::user()->hasAnyRole('Super-admin')) {
            $user_branch = '%';
        }

        return view('pages.users.edit', [
            'model' => $user,
            'branches' => Branch::where('id', 'LIKE', $user_branch)->get(),
        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  User  $user
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, User $user)
    {
        $user->fill($request->all());

        if ($user->save()) {
            $action = "Updated a user: " . $user->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'User successfully updated');
            return redirect()->route('users.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating User');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  User  $user
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, User $user)
    {
        if ($user->delete()) {
            $action = "Updated a user: " . $user->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'User successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting User');
        }

        return redirect()->back();
    }
    protected function getUserPermissions($user)
    {
        return $permissions = $user->getAllPermissions();
    }

    public function storeUserRole(ModelHasRoleRequest $request, User $user)
    {
        $status = $request->status;
        $user = User::find($request->model_id);

        if ($status == 1) {
            if ($user->hasRole(Role::find($request->role_id)->name) == false) {
                $model = new ModelHasRole();
                $model->fill($request->all());
                $model->save();
                $action = "Assigned role to the user: " . $user->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Role successfully saved');
            }
            else {
                session()->flash('app_error', 'Role has already been assigned');
            }
        }

        if ($status == 0) {
            $model = ModelHasRole::where(['model_id' => $request->model_id, 'role_id' => $request->role_id])->delete();
            $action = "Revoked role to the user: " . $user->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Role successfully revoked');
        }
        //ActivityLog::create(['action'=>"Assigned user to a role $user->name.'-'.$request->role->name",'user_id'=>Auth::id()]);
        return redirect()->route('users.show', ['user' => $user,
            'permissions' => $user->getPermisions(),
            'roles' => Role::orderBy('name'),
        ]);

    }

    public function storeUserPermission(ModelHasPermissionRequest $request, User $user)
    {
        $permissions = $request->permissions;
        $status = $request->status;
        if ($status == 1) {
            $user->givePermissionTo($permissions);
            $action = "Assigned permission to the user: " . $user->name;
            AuditLog::auditLog(Auth::id(), $action);
        }
        if ($status == 0) {
            foreach ($permissions as $permission)
                $user->revokePermissionTo($permission);
            $action = "Assigned permission to the user: " . $user->name;
            AuditLog::auditLog(Auth::id(), $action);
        }
        return redirect()->route('user.assign.role', ['user' => $user,
            'permissions' => $user->getPermisions(),
            'roles' => $user->getRoles()
        ]);

    }
    public function changeAccountStatus(User $user, $status)
    {

        $user->status = $status;
        $user->save();
        $action = "Block/Enable user account: " . $user->name;
        AuditLog::auditLog(Auth::id(), $action);
        return redirect()->route('user.assign.role', ['user' => $user,
            'permissions' => $user->getPermisions(),
            'roles' => $user->getRoles()
        ]);
    }

}