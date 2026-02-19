<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\SingleProductManufacturing;
use App\Models\BatchProduction;
use App\Models\BatchConversion;
use App\Models\ManufacturingTeam;
use App\Models\ManufacturingStaff;
use App\Models\ManufacturingPenalty;
use App\Models\ManufacturingBom;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManufacturingReportController extends Controller
{
    /**
     * Manufacturing History Report - Index with filters
     * Per PDF: Filters: Date Range, Factory, Category, Batch Number
     */
    public function historyReport(Request $request)
    {
        $this->authorize('manufacturing.reports.history');

        $user = Auth::user();

        return view('pages.manufacturing.reports.history.index', [
            'branches' => Branch::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'userBranch' => $user->branch_id
        ]);
    }

    /**
     * Load Manufacturing History Report data
     * Per PDF: Fields: S/N, Product Code, Product Description, Quantity, Unit Cost, Total Cost, Batch Number
     */
    public function loadHistoryReport(Request $request)
    {
        $this->authorize('manufacturing.reports.history');

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $categoryId = $request->category_id;
        $batchNumber = $request->batch_number;

        // Get detailed production records in unified format
        $productions = collect([]);

        // Get Single Product Manufacturing records
        $singleQuery = SingleProductManufacturing::with(['bom.finishProduct', 'bom.category'])
            ->where('status', 'posted')
            ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $singleQuery->where('branch_id', $branchId);
        }
        if ($categoryId) {
            $singleQuery->whereHas('bom', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        if ($batchNumber) {
            $singleQuery->where('reference', 'like', '%' . $batchNumber . '%');
        }

        $singleManufacturing = $singleQuery->orderBy('manufacturing_date', 'desc')->get();

        // Transform single manufacturing to standard format per PDF
        foreach ($singleManufacturing as $item) {
            $productions->push([
                'type' => 'Single',
                'date' => $item->manufacturing_date,
                'batch_number' => $item->reference,
                'product_code' => $item->bom->finishProduct->product_code ?? 'N/A',
                'product_name' => $item->bom->finishProduct->name ?? 'N/A',
                'category' => $item->bom->category->name ?? 'N/A',
                'quantity' => $item->quantity,
                'unit_cost' => $item->unit_cost ?? 0,
                'total_cost' => $item->total_cost ?? 0,
            ]);
        }

        // Get Batch Production records
        $batchQuery = BatchProduction::with(['bom.finishProduct', 'bom.category', 'conversions'])
            ->where('status', 'posted')
            ->whereBetween('production_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $batchQuery->where('branch_id', $branchId);
        }
        if ($categoryId) {
            $batchQuery->whereHas('bom', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        if ($batchNumber) {
            $batchQuery->where('batch_number', 'like', '%' . $batchNumber . '%');
        }

        $batchProductions = $batchQuery->orderBy('production_date', 'desc')->get();

        // Transform batch production to standard format per PDF
        foreach ($batchProductions as $item) {
            $convertedQty = $item->conversions->where('status', 'posted')->sum('converted_qty');
            $convertedCost = $item->conversions->where('status', 'posted')->sum('total_cost');

            // Use converted values if available, otherwise use batch values
            $quantity = $convertedQty > 0 ? $convertedQty : $item->quantity;
            $totalCost = $convertedQty > 0 ? $convertedCost : $item->wip_value;
            $unitCost = $quantity > 0 ? ($totalCost / $quantity) : 0;

            $productions->push([
                'type' => 'Batch',
                'date' => $item->production_date,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->product_code ?? 'N/A',
                'product_name' => $item->bom->finishProduct->name ?? 'N/A',
                'category' => $item->bom->category->name ?? 'N/A',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ]);
        }

        // Sort by date descending
        $productions = $productions->sortByDesc('date')->values();

        // Calculate totals
        $totals = [
            'total_records' => $productions->count(),
            'total_qty' => $productions->sum('quantity'),
            'total_cost' => $productions->sum('total_cost'),
        ];

        return view('pages.manufacturing.reports.history.load', [
            'productions' => $productions,
            'totals' => $totals,
            'filters' => $request->all()
        ]);
    }

    /**
     * Print Manufacturing History Report
     */
    public function printHistoryReport(Request $request)
    {
        $this->authorize('manufacturing.reports.history');

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $categoryId = $request->category_id;
        $batchNumber = $request->batch_number;

        // Get detailed production records in unified format
        $productions = collect([]);

        // Get Single Product Manufacturing records
        $singleQuery = SingleProductManufacturing::with(['bom.finishProduct', 'bom.category'])
            ->where('status', 'posted')
            ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $singleQuery->where('branch_id', $branchId);
        }
        if ($categoryId) {
            $singleQuery->whereHas('bom', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        if ($batchNumber) {
            $singleQuery->where('reference', 'like', '%' . $batchNumber . '%');
        }

        $singleManufacturing = $singleQuery->orderBy('manufacturing_date', 'desc')->get();

        // Transform single manufacturing to standard format per PDF
        foreach ($singleManufacturing as $item) {
            $productions->push([
                'type' => 'Single',
                'date' => $item->manufacturing_date,
                'batch_number' => $item->reference,
                'product_code' => $item->bom->finishProduct->product_code ?? 'N/A',
                'product_name' => $item->bom->finishProduct->name ?? 'N/A',
                'category' => $item->bom->category->name ?? 'N/A',
                'quantity' => $item->quantity,
                'unit_cost' => $item->unit_cost ?? 0,
                'total_cost' => $item->total_cost ?? 0,
            ]);
        }

        // Get Batch Production records
        $batchQuery = BatchProduction::with(['bom.finishProduct', 'bom.category', 'conversions'])
            ->where('status', 'posted')
            ->whereBetween('production_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $batchQuery->where('branch_id', $branchId);
        }
        if ($categoryId) {
            $batchQuery->whereHas('bom', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        if ($batchNumber) {
            $batchQuery->where('batch_number', 'like', '%' . $batchNumber . '%');
        }

        $batchProductions = $batchQuery->orderBy('production_date', 'desc')->get();

        // Transform batch production to standard format per PDF
        foreach ($batchProductions as $item) {
            $convertedQty = $item->conversions->where('status', 'posted')->sum('converted_qty');
            $convertedCost = $item->conversions->where('status', 'posted')->sum('total_cost');

            // Use converted values if available, otherwise use batch values
            $quantity = $convertedQty > 0 ? $convertedQty : $item->quantity;
            $totalCost = $convertedQty > 0 ? $convertedCost : $item->wip_value;
            $unitCost = $quantity > 0 ? ($totalCost / $quantity) : 0;

            $productions->push([
                'type' => 'Batch',
                'date' => $item->production_date,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->product_code ?? 'N/A',
                'product_name' => $item->bom->finishProduct->name ?? 'N/A',
                'category' => $item->bom->category->name ?? 'N/A',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ]);
        }

        // Sort by date descending
        $productions = $productions->sortByDesc('date')->values();

        // Calculate totals
        $totals = [
            'total_records' => $productions->count(),
            'total_qty' => $productions->sum('quantity'),
            'total_cost' => $productions->sum('total_cost'),
        ];

        return view('pages.manufacturing.reports.history.print', [
            'productions' => $productions,
            'totals' => $totals,
            'filters' => $request->all(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    /**
     * Teams Report - Index with filters
     * Per PDF: Filters: Date Range, Factory, Team, Category, Batch Number
     */
    public function teamsReport(Request $request)
    {
        $this->authorize('manufacturing.reports.teams');

        $user = Auth::user();

        return view('pages.manufacturing.reports.teams.index', [
            'branches' => Branch::orderBy('name')->get(),
            'teams' => ManufacturingTeam::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'userBranch' => $user->branch_id
        ]);
    }

    /**
     * Load Teams Report data
     * Per PDF: Shows detailed production records with S/N, Product Code, Product Description,
     * Quantity, Cost, Total Cost, Batch Number, Unit Amount, Total Amount
     */
    public function loadTeamsReport(Request $request)
    {
        $this->authorize('manufacturing.reports.teams');

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $teamId = $request->team_id;
        $categoryId = $request->category_id;
        $batchNumber = $request->batch_number;

        // Get detailed production records
        $productions = collect([]);

        // Get Single Product Manufacturing records
        $singleQuery = SingleProductManufacturing::with(['bom.finishProduct', 'bom.category', 'team'])
            ->where('status', 'posted')
            ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $singleQuery->where('branch_id', $branchId);
        }
        if ($teamId) {
            $singleQuery->where('team_id', $teamId);
        }
        if ($categoryId) {
            $singleQuery->whereHas('bom', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        if ($batchNumber) {
            $singleQuery->where('reference', 'like', '%' . $batchNumber . '%');
        }

        $singleManufacturing = $singleQuery->orderBy('manufacturing_date', 'desc')->get();

        // Transform single manufacturing to standard format
        foreach ($singleManufacturing as $item) {
            $productions->push([
                'type' => 'Single',
                'date' => $item->manufacturing_date,
                'batch_number' => $item->reference,
                'product_code' => $item->bom->finishProduct->product_code ?? 'N/A',
                'product_name' => $item->bom->finishProduct->name ?? 'N/A',
                'category' => $item->bom->category->name ?? 'N/A',
                'quantity' => $item->quantity,
                'unit_cost' => $item->unit_cost ?? 0,
                'total_cost' => $item->total_cost ?? 0,
                'team_id' => $item->team_id,
                'team_name' => $item->team->name ?? 'N/A',
            ]);
        }

        // Get Batch Production records
        $batchQuery = BatchProduction::with(['bom.finishProduct', 'bom.category', 'team', 'conversions'])
            ->where('status', 'posted')
            ->whereBetween('production_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $batchQuery->where('branch_id', $branchId);
        }
        if ($teamId) {
            $batchQuery->where('team_id', $teamId);
        }
        if ($categoryId) {
            $batchQuery->whereHas('bom', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        if ($batchNumber) {
            $batchQuery->where('batch_number', 'like', '%' . $batchNumber . '%');
        }

        $batchProductions = $batchQuery->orderBy('production_date', 'desc')->get();

        // Transform batch production to standard format
        foreach ($batchProductions as $item) {
            $convertedQty = $item->conversions->where('status', 'posted')->sum('converted_qty');
            $convertedCost = $item->conversions->where('status', 'posted')->sum('total_cost');

            $productions->push([
                'type' => 'Batch',
                'date' => $item->production_date,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->product_code ?? 'N/A',
                'product_name' => $item->bom->finishProduct->name ?? 'N/A',
                'category' => $item->bom->category->name ?? 'N/A',
                'quantity' => $convertedQty > 0 ? $convertedQty : $item->quantity,
                'unit_cost' => $convertedQty > 0 ? ($convertedCost / $convertedQty) : ($item->wip_value / max($item->quantity, 1)),
                'total_cost' => $convertedQty > 0 ? $convertedCost : $item->wip_value,
                'team_id' => $item->team_id,
                'team_name' => $item->team->name ?? 'N/A',
            ]);
        }

        // Sort by date descending
        $productions = $productions->sortByDesc('date')->values();

        // Group by team for display
        $productionsByTeam = $productions->groupBy('team_name');

        // Calculate totals
        $totals = [
            'total_qty' => $productions->sum('quantity'),
            'total_cost' => $productions->sum('total_cost'),
            'total_records' => $productions->count(),
        ];

        return view('pages.manufacturing.reports.teams.load', [
            'productions' => $productions,
            'productionsByTeam' => $productionsByTeam,
            'totals' => $totals,
            'filters' => $request->all()
        ]);
    }

    /**
     * Print Teams Report
     */
    public function printTeamsReport(Request $request)
    {
        $this->authorize('manufacturing.reports.teams');

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $teamId = $request->team_id;
        $categoryId = $request->category_id;
        $batchNumber = $request->batch_number;

        // Get detailed production records
        $productions = collect([]);

        // Get Single Product Manufacturing records
        $singleQuery = SingleProductManufacturing::with(['bom.finishProduct', 'bom.category', 'team'])
            ->where('status', 'posted')
            ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $singleQuery->where('branch_id', $branchId);
        }
        if ($teamId) {
            $singleQuery->where('team_id', $teamId);
        }
        if ($categoryId) {
            $singleQuery->whereHas('bom', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        if ($batchNumber) {
            $singleQuery->where('reference', 'like', '%' . $batchNumber . '%');
        }

        $singleManufacturing = $singleQuery->orderBy('manufacturing_date', 'desc')->get();

        // Transform single manufacturing to standard format
        foreach ($singleManufacturing as $item) {
            $productions->push([
                'type' => 'Single',
                'date' => $item->manufacturing_date,
                'batch_number' => $item->reference,
                'product_code' => $item->bom->finishProduct->product_code ?? 'N/A',
                'product_name' => $item->bom->finishProduct->name ?? 'N/A',
                'category' => $item->bom->category->name ?? 'N/A',
                'quantity' => $item->quantity,
                'unit_cost' => $item->unit_cost ?? 0,
                'total_cost' => $item->total_cost ?? 0,
                'team_id' => $item->team_id,
                'team_name' => $item->team->name ?? 'N/A',
            ]);
        }

        // Get Batch Production records
        $batchQuery = BatchProduction::with(['bom.finishProduct', 'bom.category', 'team', 'conversions'])
            ->where('status', 'posted')
            ->whereBetween('production_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $batchQuery->where('branch_id', $branchId);
        }
        if ($teamId) {
            $batchQuery->where('team_id', $teamId);
        }
        if ($categoryId) {
            $batchQuery->whereHas('bom', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        if ($batchNumber) {
            $batchQuery->where('batch_number', 'like', '%' . $batchNumber . '%');
        }

        $batchProductions = $batchQuery->orderBy('production_date', 'desc')->get();

        // Transform batch production to standard format
        foreach ($batchProductions as $item) {
            $convertedQty = $item->conversions->where('status', 'posted')->sum('converted_qty');
            $convertedCost = $item->conversions->where('status', 'posted')->sum('total_cost');

            $productions->push([
                'type' => 'Batch',
                'date' => $item->production_date,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->product_code ?? 'N/A',
                'product_name' => $item->bom->finishProduct->name ?? 'N/A',
                'category' => $item->bom->category->name ?? 'N/A',
                'quantity' => $convertedQty > 0 ? $convertedQty : $item->quantity,
                'unit_cost' => $convertedQty > 0 ? ($convertedCost / $convertedQty) : ($item->wip_value / max($item->quantity, 1)),
                'total_cost' => $convertedQty > 0 ? $convertedCost : $item->wip_value,
                'team_id' => $item->team_id,
                'team_name' => $item->team->name ?? 'N/A',
            ]);
        }

        // Sort by date descending
        $productions = $productions->sortByDesc('date')->values();

        // Group by team for display
        $productionsByTeam = $productions->groupBy('team_name');

        // Calculate totals
        $totals = [
            'total_qty' => $productions->sum('quantity'),
            'total_cost' => $productions->sum('total_cost'),
            'total_records' => $productions->count(),
        ];

        return view('pages.manufacturing.reports.teams.print', [
            'productions' => $productions,
            'productionsByTeam' => $productionsByTeam,
            'totals' => $totals,
            'filters' => $request->all(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    /**
     * Team & Staff Ledger Report - Index with filters
     */
    public function teamLedgerReport(Request $request)
    {
        $this->authorize('manufacturing.reports.team_ledger');

        $user = Auth::user();

        return view('pages.manufacturing.reports.team_ledger.index', [
            'branches' => Branch::orderBy('name')->get(),
            'teams' => ManufacturingTeam::orderBy('name')->get(),
            'staff' => ManufacturingStaff::where('status', 1)->orderBy('name')->get(),
            'userBranch' => $user->branch_id
        ]);
    }

    /**
     * Load Team & Staff Ledger Report data
     */
    public function loadTeamLedgerReport(Request $request)
    {
        $this->authorize('manufacturing.reports.team_ledger');

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $teamId = $request->team_id;
        $staffId = $request->staff_id;

        // Query penalties (posted + reversed)
        $query = ManufacturingPenalty::with(['team', 'staff', 'createdBy'])
            ->whereIn('status', ['posted', 'reversed'])
            ->whereBetween('penalty_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($teamId) {
            $query->where('team_id', $teamId);
        }
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $penalties = $query->orderBy('penalty_date', 'asc')->get();

        // Build ledger entries with running balance
        $entries = collect();
        $runningBalance = 0;

        foreach ($penalties as $penalty) {
            $debit = 0;
            $credit = 0;

            if ($penalty->status === 'posted') {
                $debit = $penalty->amount_charged;
            } elseif ($penalty->status === 'reversed') {
                $credit = $penalty->amount_charged;
            }

            $runningBalance += $debit - $credit;

            $entries->push([
                'date' => $penalty->penalty_date,
                'reference' => $penalty->reference,
                'description' => $penalty->description,
                'penalty_type' => $penalty->penalty_type,
                'team_name' => $penalty->team->name ?? 'N/A',
                'staff_name' => $penalty->staff->name ?? 'N/A',
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $runningBalance,
                'status' => $penalty->status,
            ]);
        }

        // Get current team ledger balances
        $teamBalances = collect();
        $teamsQuery = ManufacturingTeam::orderBy('name');
        if ($branchId) {
            $teamsQuery->where('branch_id', $branchId);
        }
        if ($teamId) {
            $teamsQuery->where('id', $teamId);
        }
        $teamBalances = $teamsQuery->get();

        $totals = [
            'total_debit' => $entries->sum('debit'),
            'total_credit' => $entries->sum('credit'),
            'total_records' => $entries->count(),
        ];

        return view('pages.manufacturing.reports.team_ledger.load', [
            'entries' => $entries,
            'teamBalances' => $teamBalances,
            'totals' => $totals,
            'filters' => $request->all()
        ]);
    }

    /**
     * Print Team & Staff Ledger Report
     */
    public function printTeamLedgerReport(Request $request)
    {
        $this->authorize('manufacturing.reports.team_ledger');

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $teamId = $request->team_id;
        $staffId = $request->staff_id;

        $query = ManufacturingPenalty::with(['team', 'staff', 'createdBy'])
            ->whereIn('status', ['posted', 'reversed'])
            ->whereBetween('penalty_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($teamId) {
            $query->where('team_id', $teamId);
        }
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $penalties = $query->orderBy('penalty_date', 'asc')->get();

        $entries = collect();
        $runningBalance = 0;

        foreach ($penalties as $penalty) {
            $debit = 0;
            $credit = 0;

            if ($penalty->status === 'posted') {
                $debit = $penalty->amount_charged;
            } elseif ($penalty->status === 'reversed') {
                $credit = $penalty->amount_charged;
            }

            $runningBalance += $debit - $credit;

            $entries->push([
                'date' => $penalty->penalty_date,
                'reference' => $penalty->reference,
                'description' => $penalty->description,
                'penalty_type' => $penalty->penalty_type,
                'team_name' => $penalty->team->name ?? 'N/A',
                'staff_name' => $penalty->staff->name ?? 'N/A',
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $runningBalance,
                'status' => $penalty->status,
            ]);
        }

        $teamsQuery = ManufacturingTeam::orderBy('name');
        if ($branchId) {
            $teamsQuery->where('branch_id', $branchId);
        }
        if ($teamId) {
            $teamsQuery->where('id', $teamId);
        }
        $teamBalances = $teamsQuery->get();

        $totals = [
            'total_debit' => $entries->sum('debit'),
            'total_credit' => $entries->sum('credit'),
            'total_records' => $entries->count(),
        ];

        return view('pages.manufacturing.reports.team_ledger.print', [
            'entries' => $entries,
            'teamBalances' => $teamBalances,
            'totals' => $totals,
            'filters' => $request->all(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }
}
