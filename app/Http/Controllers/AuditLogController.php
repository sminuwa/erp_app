<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Http\Requests\AuditLogs\Index;
use App\Http\Requests\AuditLogs\Show;
use App\Http\Requests\AuditLogs\Create;
use App\Http\Requests\AuditLogs\Store;
use App\Http\Requests\AuditLogs\Edit;
use App\Http\Requests\AuditLogs\Update;
use App\Http\Requests\AuditLogs\Destroy;


/**
 * Description of AuditLogController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class AuditLogController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.audit_logs.index', ['records' => AuditLog::where('user_id',$request->user_id)->latest()->get()]);
    }    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  AuditLog  $auditlog
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, AuditLog $auditlog)
    {
        return view('pages.audit_logs.show', [
                'record' =>$auditlog,
        ]);

    }    /**
     * Show the form for creating a new resource.
     *
     * @param  Create  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Create $request)
    {

        return view('pages.audit_logs.create', [
            'model' => new AuditLog,

        ]);
    }    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $model=new AuditLog;
        $model->fill($request->all());

        if ($model->save()) {
            
            session()->flash('app_message', 'AuditLog saved successfully');
            return redirect()->route('audit_logs.index');
            } else {
                session()->flash('app_message', 'Something is wrong while saving AuditLog');
            }
        return redirect()->back();
    } /**
     * Show the form for editing the specified resource.
     *
     * @param  Edit  $request
     * @param  AuditLog  $auditlog
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, AuditLog $auditlog)
    {

        return view('pages.audit_logs.edit', [
            'model' => $auditlog,

            ]);
    }    /**
     * Update a existing resource in storage.
     *
     * @param  Update  $request
     * @param  AuditLog  $auditlog
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request,AuditLog $auditlog)
    {
        $auditlog->fill($request->all());

        if ($auditlog->save()) {
            
            session()->flash('app_message', 'AuditLog successfully updated');
            return redirect()->route('audit_logs.index');
            } else {
                session()->flash('app_error', 'Something is wrong while updating AuditLog');
            }
        return redirect()->back();
    }    /**
     * Delete a  resource from  storage.
     *
     * @param  Destroy  $request
     * @param  AuditLog  $auditlog
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Destroy $request, AuditLog $auditlog)
    {
        if ($auditlog->delete()) {
                session()->flash('app_message', 'AuditLog successfully deleted');
            } else {
                session()->flash('app_error', 'Error occurred while deleting AuditLog');
            }

        return redirect()->back();
    }
    public function logs(User $user)
    {
        return view('admin.users.logs', [
            'user' => $user,
            'users' => User::all(),
        ]);
    }
    public function viewLogs(Request $request)
    {
        $records = AuditLog::whereDate(DB::raw('DATE(created_at)'), '>=', $request->from_date)->whereDate(DB::raw('DATE(created_at)'), '<=', $request->to_date)->where('user_id', $request->user_id)->latest()->get();
        return view('admin.users.load_user_logs', [
            'records' => $records,
            'user' => User::find($request->user_id),
            'from_date' => $request->from_date,
            'to_date' => $request->to_date
        ]);
    }
    public function printLogs($from_date, $to_date, $user_id)
    {
        $records = AuditLog::whereDate(DB::raw('DATE(created_at)'), '>=', $from_date)->whereDate(DB::raw('DATE(created_at)'), '<=', $to_date)->where('user_id', $user_id)->latest()->get();
        return view('admin.users.user_logs_print', [
            'records' => $records,
            'user' => User::find($user_id),
            'from_date' => $from_date,
            'to_date' => $to_date
        ]);
    }
}
