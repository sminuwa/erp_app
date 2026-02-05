<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingAdditionalCost;
use App\Models\Product;
use App\Models\GeneralAccount;
use App\Classes\Manufacturing\ManufacturingTransaction;
use App\Classes\Manufacturing\ManufacturingCostPrice;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManufacturingAdditionalCostController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manufacturing.additional_costs.index');

        $records = ManufacturingAdditionalCost::with(['product', 'expenseAccount', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.manufacturing.processing.additional_costs.index', [
            'records' => $records
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('manufacturing.additional_costs.create');

        $model = new ManufacturingAdditionalCost;
        $model->reference = ManufacturingAdditionalCost::generateNewNumber();
        $model->cost_date = date('Y-m-d');

        $user = Auth::user();

        return view('pages.manufacturing.processing.additional_costs.create', [
            'model' => $model,
            'products' => Product::orderBy('name')->get(),
            'accounts' => GeneralAccount::where('class', 'expense')->orderBy('number')->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.additional_costs.create');

        $request->validate([
            'reference' => 'required|unique:manufacturing_additional_costs,reference',
            'product_id' => 'required|exists:products,id',
            'expense_account_id' => 'required|exists:general_accounts,id',
            'amount' => 'required|numeric|min:0.01'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $model = new ManufacturingAdditionalCost;
            $model->reference = $request->reference;
            $model->cost_date = $request->cost_date;
            $model->product_id = $request->product_id;
            $model->expense_account_id = $request->expense_account_id;
            $model->amount = $request->amount;
            $model->description = $request->description;
            $model->branch_id = $user->branch_id;
            $model->status = ManufacturingAdditionalCost::STATUS_PENDING;
            $model->created_by = Auth::id();
            $model->save();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Created additional cost: " . $model->reference);
            session()->flash('app_message', 'Additional Cost created successfully');
            return redirect()->route('manufacturing.additional_costs.show', $model->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(ManufacturingAdditionalCost $cost)
    {
        $this->authorize('manufacturing.additional_costs.show');

        $cost->load(['product', 'expenseAccount', 'createdBy', 'postedBy']);

        return view('pages.manufacturing.processing.additional_costs.show', [
            'record' => $cost
        ]);
    }

    public function post(ManufacturingAdditionalCost $cost)
    {
        $this->authorize('manufacturing.additional_costs.post');

        if (!$cost->isPending()) {
            session()->flash('app_error', 'Only pending costs can be posted.');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            // Add cost to product
            $result = ManufacturingCostPrice::addCostToExistingProduct(
                $cost->product_id,
                $cost->amount,
                $cost->branch_id
            );

            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            // Post to ledger
            ManufacturingTransaction::additionalCost($cost->id);

            $cost->post();

            DB::commit();

            AuditLog::auditLog(Auth::id(), "Posted additional cost: " . $cost->reference);
            session()->flash('app_message', 'Additional Cost posted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy(ManufacturingAdditionalCost $cost)
    {
        $this->authorize('manufacturing.additional_costs.delete');

        if (!$cost->isPending()) {
            session()->flash('app_error', 'Only pending costs can be deleted.');
            return redirect()->back();
        }

        $reference = $cost->reference;
        $cost->delete();

        AuditLog::auditLog(Auth::id(), "Deleted additional cost: " . $reference);
        session()->flash('app_message', 'Additional Cost deleted successfully');

        return redirect()->route('manufacturing.additional_costs.index');
    }
}
