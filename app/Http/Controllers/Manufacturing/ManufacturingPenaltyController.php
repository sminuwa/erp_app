<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingPenalty;
use App\Models\ManufacturingTeam;
use App\Models\ManufacturingStaff;
use App\Models\SingleProductManufacturing;
use App\Models\BatchConversion;
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

        $user = Auth::user();
        $records = ManufacturingPenalty::with(['team', 'staff', 'createdBy'])
            ->forBranch($user->branch_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

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

        // Get posted productions for selection (Production Document No field)
        $singleManufacturing = SingleProductManufacturing::posted()
            ->forBranch($user->branch_id)
            ->with('bom.finishProduct')
            ->orderBy('reference')
            ->get();

        $batchConversions = BatchConversion::posted()
            ->forBranch($user->branch_id)
            ->with('batchProduction.bom.finishProduct')
            ->orderBy('reference')
            ->get();

        return view('pages.manufacturing.processing.penalties.create', [
            'model' => $model,
            'teams' => ManufacturingTeam::active()->forBranch($user->branch_id)->get(),
            'staff' => ManufacturingStaff::where('status', 1)->orderBy('name')->get(),
            'singleManufacturing' => $singleManufacturing,
            'batchConversions' => $batchConversions
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.penalties.create');

        $request->validate([
            'reference' => 'required|unique:manufacturing_penalties,reference',
            'penalty_date' => 'required|date',
            'penalty_type' => 'required|in:team,staff',
            'amount_charged' => 'required|numeric|min:0.01',
            'total_loss_incurred' => 'nullable|numeric|min:0',
            'production_reference' => 'nullable|string',
            'description' => 'required|string'
        ]);

        // Validate team_id or staff_id based on penalty_type
        if ($request->penalty_type === 'team') {
            $request->validate(['team_id' => 'required|exists:manufacturing_teams,id']);
        } else {
            $request->validate(['staff_id' => 'required|exists:manufacturing_staff,id']);
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $model = new ManufacturingPenalty;
            $model->reference = $request->reference;
            $model->penalty_date = $request->penalty_date;
            $model->penalty_type = $request->penalty_type;
            $model->team_id = $request->penalty_type === 'team' ? $request->team_id : null;
            $model->staff_id = $request->penalty_type === 'staff' ? $request->staff_id : null;
            $model->amount_charged = $request->amount_charged;
            $model->total_loss_incurred = $request->total_loss_incurred ?? 0;
            $model->production_reference = $request->production_reference;
            $model->description = $request->description;
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
                $team->addPenalty($penalty->amount_charged);
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
