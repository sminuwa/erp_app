<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleHasPermissionsRequest;
use App\Models\RolehasPermission;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\throwException;
use App\Models\AuditLog;

/**
 * Description of RoleHasPermissionController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */
class RoleHasPermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');

    }

    public function index()
    {
        /*try {
            if (!$this->checkAccess(Auth::user(), 'role.create')) {
                return view("pages.error.access_denied");
            }
        } catch (\Exception $e) {
            return view("pages.error.technical");
        }

        return $this->checkAccessView(Auth::user(), "role.create",
            view('pages.role_has_permissions.index', ['roles' => Role::all()]));*/
        return view('pages.role_has_permissions.index', ['roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        /*$this->validate($request,[
            'role'=>'required|unique:role,name',
            'permission'=>'required|unique:permissions,name',
        ]);*/

        $roleid = $request->role;
        $newPermission = $request->permissions;
        $role = Role::find($roleid);
        $oldpermissions = $this->rolePermissionToArray($role);

        DB::beginTransaction();
        try {
            if ($newPermission != null) {
                DB::table('role_has_permissions')->whereIn('permission_id', $oldpermissions)->where(['role_id' => $roleid])->delete();
                foreach ($newPermission as $key => $val) {
                    DB::table('role_has_permissions')->insert(['permission_id' => $val, 'role_id' => $roleid]);

                }

            }
            //$action = "Assigned permissons to $role->name";
            //ActivityLog::create(['action' => $action, 'user_id' => Auth::id()]);
            $action = "Assigned permissons to: " . $role->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            return throwException($e);
            // something went wrong
        }
        if ($request->has('permissionid'))
            return 'Permissions have been updated';
        else
            return 'Permissions have been added to the specified role';
        //return  redirect()->back();
    }

    public function edit($id)
    {
        //$model = RoleHasPermission::find($id);
        //return view('pages.staff.settings.assignpermissiontorole',compact('model'));
    }

    public function show(Request $request)
    {
        $role = Role::find($request->role_id);
        $rolepermssions = $this->rolePermissionToArray($role);
        $permissions = Permission::where('active', 1)->orderby('description')->get();
        return view('pages.role_has_permissions.load_permissions', ['permissions' => $permissions, 'rolepermssions' => $rolepermssions]);
    }

    public function rolePermissionToArray($role)
    {
        $toarray = array();
        $rolepermssions = $role->haspermissions;
        foreach ($rolepermssions as $rolepermssion)
            $toarray[] = $rolepermssion->id;
        return $toarray;
    }
}
