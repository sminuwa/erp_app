<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingPenalty;
use App\Models\ManufacturingTeam;
use App\Models\ManufacturingStaff;
use App\Classes\Manufacturing\ManufacturingTransaction;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManufacturingPenaltyController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manufacturing.penalties.index');

        $records = ManufacturingPenalty::with(['team', 'staff', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.manufacturing.processing.penalties.index', [
            'records' => $records
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('manufacturing.penalties.create');

        $model = new ManufacturingPenalty;
        $model->reference = ManufacturingPenalty::generateNewNumber();
        $model->penalty_date = date('Y-m-d');

        $user = Auth::user();

        return view('pages.manufacturing.processing.penalties.create', [
            'model' => $model,
            'teams' => ManufacturingTeam::active()->forBranch($user->branch_id)->get(),
            'staff' => ManufacturingStaff::where('status', 1)->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.penalties.create');

        $request->validate([
            'reference' => 'required|unique:manufacturing_penalties,reference',
            'penalty_type' => 'required|in:team,staff',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|max:500'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $model = new ManufacturingPenalty;
            $model->reference = $request->reference;
            $model->penalty_date = $request->penalty_date;
            $model->penalty_type = $request->penalty_type;
            $model->team_id = $request->team_id;
            $model->staff_id = $request->staff_id;
            $model->amount = $request->amount;
            $model->reason = $request->reason;
            $model->branch_id = $user->branch_id;
            $model->status = ManufacturingPenalty::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created penalty: " . $model->reference);
            session()->flash('app_message', 'Penalty created successfully');
            return redirect()->route('manufacturing.penalties.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(ManufacturingPenalty $penalty)
    {
        $this->authorize('manufacturing.penalties.show');

        $penalty->load(['team', 'staff', 'createdBy', 'postedBy']);

        return view('pages.manufacturing.processing.penalties.show', [
            'record' => $penalty
        ]);
    }

    public function post(ManufacturingPenalty $penalty)
    {
        $this->authorize('manufacturing.penalties.post');

        if (!$penalty->isPending()) {
            session()->flash('app_error', 'Only pending penalties can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Add penalty to team ledger balance
            if ($penalty->penalty_type === 'team' && $penalty->team_id) {
                $team = ManufacturingTeam::find($penalty->team_id);
                $team->addPenalty($penalty->amount);
            }

            // Post to ledger
            ManufacturingTransaction::penalty($penalty->id);

            $penalty->post();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Posted penalty: " . $penalty->reference);
            session()->flash('app_message', 'Penalty posted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(ManufacturingPenalty $penalty)
    {
        $this->authorize('manufacturing.penalties.delete');

        if (!$penalty->isPending()) {
            session()->flash('app_error', 'Only pending penalties can be deleted.');
            return redirect()->back();
        }

        $reference = $penalty->reference;
        $penalty->delete();

        AuditLog::auditLog(Auth::id(), "Deleted penalty: " . $reference);
        session()->flash('app_message', 'Penalty deleted successfully');

        return redirect()->route('manufacturing.penalties.index');
    }
}
