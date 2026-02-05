<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingMachine;
use App\Models\Branch;
use App\Http\Requests\Manufacturing\Machines\Index;
use App\Http\Requests\Manufacturing\Machines\Create;
use App\Http\Requests\Manufacturing\Machines\Store;
use App\Http\Requests\Manufacturing\Machines\Edit;
use App\Http\Requests\Manufacturing\Machines\Update;
use App\Http\Requests\Manufacturing\Machines\Destroy;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ManufacturingMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Index $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        $records = ManufacturingMachine::with('branch')
            ->orderBy('code')
            ->get();

        return view('pages.manufacturing.setup.machines.index', [
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
        $model = new ManufacturingMachine;
        $model->code = ManufacturingMachine::generateNewCode();
        $model->status = 1;

        return view('pages.manufacturing.setup.machines.create', [
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
        $model = new ManufacturingMachine;
        $model->fill($request->all());
        $model->created_by = Auth::id();

        if ($model->save()) {
            $action = "Added a new manufacturing machine: " . $model->description;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Manufacturing Machine saved successfully');
            return redirect()->route('manufacturing.machines.index');
        } else {
            session()->flash('app_error', 'Something went wrong while saving Manufacturing Machine');
        }
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Edit $request
     * @param ManufacturingMachine $machine
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, ManufacturingMachine $machine)
    {
        return view('pages.manufacturing.setup.machines.edit', [
            'model' => $machine,
            'branches' => Branch::orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update $request
     * @param ManufacturingMachine $machine
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, ManufacturingMachine $machine)
    {
        $machine->fill($request->all());
        $machine->updated_by = Auth::id();

        if ($machine->save()) {
            $action = "Updated manufacturing machine: " . $machine->description;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Manufacturing Machine updated successfully');
            return redirect()->route('manufacturing.machines.index');
        } else {
            session()->flash('app_error', 'Something went wrong while updating Manufacturing Machine');
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Destroy $request
     * @param ManufacturingMachine $machine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Destroy $request, ManufacturingMachine $machine)
    {
        if ($machine->delete()) {
            $action = "Deleted manufacturing machine: " . $machine->description;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Manufacturing Machine deleted successfully');
        } else {
            session()->flash('app_error', 'Error occurred while deleting Manufacturing Machine');
        }

        return redirect()->back();
    }
}
