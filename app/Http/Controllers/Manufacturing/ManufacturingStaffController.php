<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingStaff;
use App\Models\Branch;
use App\Http\Requests\Manufacturing\Staff\Index;
use App\Http\Requests\Manufacturing\Staff\Create;
use App\Http\Requests\Manufacturing\Staff\Store;
use App\Http\Requests\Manufacturing\Staff\Edit;
use App\Http\Requests\Manufacturing\Staff\Update;
use App\Http\Requests\Manufacturing\Staff\Destroy;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ManufacturingStaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Index $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        $records = ManufacturingStaff::with('branch')
            ->orderBy('name')
            ->get();

        return view('pages.manufacturing.setup.staff.index', [
            'records' => $records
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Create $request
     * @return \Illuminate\Http\Response
     */
    public function create(Create $request)
    {
        $model = new ManufacturingStaff;
        $model->status = 1;

        return view('pages.manufacturing.setup.staff.create', [
            'model' => $model,
            'branches' => Branch::orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Store $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $model = new ManufacturingStaff;
        $model->fill($request->all());
        $model->created_by = Auth::id();

        if ($model->save()) {
            $action = "Added a new manufacturing staff: " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Manufacturing Staff saved successfully');
            return redirect()->route('manufacturing.staff.index');
        } else {
            session()->flash('app_error', 'Something went wrong while saving Manufacturing Staff');
        }
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Edit $request
     * @param ManufacturingStaff $staff
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, ManufacturingStaff $staff)
    {
        return view('pages.manufacturing.setup.staff.edit', [
            'model' => $staff,
            'branches' => Branch::orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update $request
     * @param ManufacturingStaff $staff
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, ManufacturingStaff $staff)
    {
        $staff->fill($request->all());
        $staff->updated_by = Auth::id();

        if ($staff->save()) {
            $action = "Updated manufacturing staff: " . $staff->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Manufacturing Staff updated successfully');
            return redirect()->route('manufacturing.staff.index');
        } else {
            session()->flash('app_error', 'Something went wrong while updating Manufacturing Staff');
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Destroy $request
     * @param ManufacturingStaff $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy(Destroy $request, ManufacturingStaff $staff)
    {
        if ($staff->delete()) {
            $action = "Deleted manufacturing staff: " . $staff->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Manufacturing Staff deleted successfully');
        } else {
            session()->flash('app_error', 'Error occurred while deleting Manufacturing Staff');
        }

        return redirect()->back();
    }
}
