<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingTeam;
use App\Models\ManufacturingTeamSupervisor;
use App\Models\ManufacturingTeamMember;
use App\Models\ManufacturingStaff;
use App\Models\User;
use App\Models\Branch;
use App\Http\Requests\Manufacturing\Teams\Index;
use App\Http\Requests\Manufacturing\Teams\Create;
use App\Http\Requests\Manufacturing\Teams\Store;
use App\Http\Requests\Manufacturing\Teams\Show;
use App\Http\Requests\Manufacturing\Teams\Edit;
use App\Http\Requests\Manufacturing\Teams\Update;
use App\Http\Requests\Manufacturing\Teams\Destroy;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManufacturingTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Index $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        $records = ManufacturingTeam::with(['branch', 'supervisors.user', 'members.staff'])
            ->orderBy('name')
            ->get();

        return view('pages.manufacturing.setup.teams.index', [
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
        $model = new ManufacturingTeam;
        $model->status = 1;

        return view('pages.manufacturing.setup.teams.create', [
            'model' => $model,
            'branches' => Branch::orderBy('name')->get(),
            'users' => User::where('status', 1)->orderBy('surname')->orderBy('firstname')->get(),
            'staff' => ManufacturingStaff::where('status', 1)->orderBy('name')->get()
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
        DB::beginTransaction();

        try {
            $model = new ManufacturingTeam;
            $model->name = $request->name;
            $model->branch_id = $request->branch_id;
            $model->status = $request->status;
            $model->created_by = Auth::id();
            $model->save();

            // Save supervisors
            if ($request->has('supervisors') && is_array($request->supervisors)) {
                foreach ($request->supervisors as $userId) {
                    if (!empty($userId)) {
                        ManufacturingTeamSupervisor::create([
                            'team_id' => $model->id,
                            'user_id' => $userId
                        ]);
                    }
                }
            }

            // Save members
            if ($request->has('members') && is_array($request->members)) {
                foreach ($request->members as $staffId) {
                    if (!empty($staffId)) {
                        ManufacturingTeamMember::create([
                            'team_id' => $model->id,
                            'staff_id' => $staffId
                        ]);
                    }
                }
            }

            DB::commit();

            $action = "Added a new manufacturing team: " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Manufacturing Team saved successfully');
            return redirect()->route('manufacturing.teams.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Something went wrong: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Show $request
     * @param ManufacturingTeam $team
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, ManufacturingTeam $team)
    {
        $team->load(['branch', 'supervisors.user', 'members.staff']);

        return view('pages.manufacturing.setup.teams.show', [
            'record' => $team
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Edit $request
     * @param ManufacturingTeam $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, ManufacturingTeam $team)
    {
        $team->load(['supervisors', 'members']);

        return view('pages.manufacturing.setup.teams.edit', [
            'model' => $team,
            'branches' => Branch::orderBy('name')->get(),
            'users' => User::where('status', 1)->orderBy('surname')->orderBy('firstname')->get(),
            'staff' => ManufacturingStaff::where('status', 1)->orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update $request
     * @param ManufacturingTeam $team
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request, ManufacturingTeam $team)
    {
        DB::beginTransaction();

        try {
            $team->name = $request->name;
            $team->branch_id = $request->branch_id;
            $team->status = $request->status;
            $team->updated_by = Auth::id();
            $team->save();

            // Delete existing supervisors and members
            ManufacturingTeamSupervisor::where('team_id', $team->id)->delete();
            ManufacturingTeamMember::where('team_id', $team->id)->delete();

            // Save supervisors
            if ($request->has('supervisors') && is_array($request->supervisors)) {
                foreach ($request->supervisors as $userId) {
                    if (!empty($userId)) {
                        ManufacturingTeamSupervisor::create([
                            'team_id' => $team->id,
                            'user_id' => $userId
                        ]);
                    }
                }
            }

            // Save members
            if ($request->has('members') && is_array($request->members)) {
                foreach ($request->members as $staffId) {
                    if (!empty($staffId)) {
                        ManufacturingTeamMember::create([
                            'team_id' => $team->id,
                            'staff_id' => $staffId
                        ]);
                    }
                }
            }

            DB::commit();

            $action = "Updated manufacturing team: " . $team->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Manufacturing Team updated successfully');
            return redirect()->route('manufacturing.teams.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Something went wrong: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Destroy $request
     * @param ManufacturingTeam $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Destroy $request, ManufacturingTeam $team)
    {
        DB::beginTransaction();

        try {
            // Delete related records first
            ManufacturingTeamSupervisor::where('team_id', $team->id)->delete();
            ManufacturingTeamMember::where('team_id', $team->id)->delete();

            $teamName = $team->name;
            $team->delete();

            DB::commit();

            $action = "Deleted manufacturing team: " . $teamName;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Manufacturing Team deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error occurred while deleting Manufacturing Team: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
