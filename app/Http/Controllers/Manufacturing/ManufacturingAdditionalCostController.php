<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingAdditionalCost;
use App\Models\SingleProductManufacturing;
use App\Models\BatchConversion;
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

        $records = ManufacturingAdditionalCost::with(['account', 'createdBy'])
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

        // Get productions for selection (any status for now, ideally should be 'posted')
        $singleManufacturing = SingleProductManufacturing::forBranch($user->branch_id)
            ->orderBy('reference')
            ->get();
        
        $batchProductions = BatchConversion::forBranch($user->branch_id)
            ->orderBy('reference')
            ->get();

        return view('pages.manufacturing.processing.additional_costs.create', [
            'model' => $model,
            'singleManufacturing' => $singleManufacturing,
            'batchProductions' => $batchProductions,
            'accounts' => GeneralAccount::where('class', 'M11')->orderBy('number')->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manufacturing.additional_costs.create');

        $request->validate([
            'reference' => 'required|unique:manufacturing_additional_costs,reference',
            'cost_date' => 'required|date',
            'account_id' => 'required|exists:general_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'production_type' => 'required|in:single_product,batch_conversion'
        ]);

        // Validate production_id based on type
        if ($request->production_type === 'single_product') {
            $request->validate(['single_manufacturing_id' => 'required|exists:single_product_manufacturing,id']);
            $productionId = $request->single_manufacturing_id;
        } else {
            $request->validate(['batch_production_id' => 'required|exists:batch_conversions,id']);
            $productionId = $request->batch_production_id;
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $model = new ManufacturingAdditionalCost;
            $model->reference = $request->reference;
            $model->cost_date = $request->cost_date;
            $model->production_type = $request->production_type;
            $model->production_id = $productionId;
            $model->account_id = $request->account_id;
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

        $cost->load(['account', 'createdBy', 'postedBy']);

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
            // Get the production and its finish product
            $production = $cost->getProduction();
            
            if (!$production) {
                throw new \Exception('Production record not found.');
            }

            // Get finish product ID from production
            $finishProductId = $production->finish_product_id ?? $production->bom->finish_product_id ?? null;
            
            if (!$finishProductId) {
                throw new \Exception('Finish product not found for this production.');
            }

            // Add cost to product (distributes cost across produced quantity)
            $result = ManufacturingCostPrice::addCostToExistingProduct(
                $finishProductId,
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
