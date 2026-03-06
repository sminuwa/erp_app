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
use App\Models\ManufacturingMachine;
use App\Models\ProductionOrder;
use App\Models\DailyManufacturingSchedule;
use App\Models\MaterialsRequisition;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Category;
use App\Exports\Manufacturing\ManufacturingReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
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
                'reference' => $item->reference,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->code ?? 'N/A',
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
                'reference' => $item->reference,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->code ?? 'N/A',
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
                'reference' => $item->reference,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->code ?? 'N/A',
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
                'reference' => $item->reference,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->code ?? 'N/A',
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
                'reference' => $item->reference,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->code ?? 'N/A',
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
                'reference' => $item->reference,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->code ?? 'N/A',
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
                'reference' => $item->reference,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->code ?? 'N/A',
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
                'reference' => $item->reference,
                'batch_number' => $item->batch_number,
                'product_code' => $item->bom->finishProduct->code ?? 'N/A',
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

    // ================================================================
    // PRODUCTION ORDERS REPORT
    // ================================================================

    public function ordersReport(Request $request)
    {
        $this->authorize('manufacturing.reports.orders');

        $user = Auth::user();

        return view('pages.manufacturing.reports.orders.index', [
            'branches' => Branch::orderBy('name')->get(),
            'userBranch' => $user->branch_id
        ]);
    }

    public function loadOrdersReport(Request $request)
    {
        $this->authorize('manufacturing.reports.orders');

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $status = $request->status;

        $query = ProductionOrder::with(['items.bom.finishProduct', 'branch', 'createdBy', 'approvedBy'])
            ->whereBetween('order_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('order_date', 'desc')->get();

        $totals = [
            'total_orders' => $orders->count(),
            'total_qty' => $orders->sum('total_finish_goods_qty'),
            'total_processed' => $orders->sum('processed_qty'),
            'pending' => $orders->where('status', 'pending')->count(),
            'confirmed' => $orders->where('status', 'confirmed')->count(),
            'approved' => $orders->where('status', 'approved')->count(),
            'closed' => $orders->where('status', 'closed')->count(),
        ];

        return view('pages.manufacturing.reports.orders.load', compact('orders', 'totals'));
    }

    public function printOrdersReport(Request $request)
    {
        $this->authorize('manufacturing.reports.orders');

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $status = $request->status;

        $query = ProductionOrder::with(['items.bom.finishProduct', 'branch', 'createdBy', 'approvedBy'])
            ->whereBetween('order_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('order_date', 'desc')->get();

        $totals = [
            'total_orders' => $orders->count(),
            'total_qty' => $orders->sum('total_finish_goods_qty'),
            'total_processed' => $orders->sum('processed_qty'),
        ];

        return view('pages.manufacturing.reports.orders.print', compact('orders', 'totals', 'dateFrom', 'dateTo'));
    }

    // ================================================================
    // DAILY MANUFACTURING SCHEDULES REPORT
    // ================================================================

    public function schedulesReport(Request $request)
    {
        $this->authorize('manufacturing.reports.schedules');

        $user = Auth::user();

        return view('pages.manufacturing.reports.schedules.index', [
            'branches' => Branch::orderBy('name')->get(),
            'teams' => ManufacturingTeam::orderBy('name')->get(),
            'machines' => ManufacturingMachine::where('status', 1)->orderBy('description')->get(),
            'userBranch' => $user->branch_id
        ]);
    }

    public function loadSchedulesReport(Request $request)
    {
        $this->authorize('manufacturing.reports.schedules');

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $teamId = $request->team_id;
        $machineId = $request->machine_id;
        $status = $request->status;

        $query = DailyManufacturingSchedule::with([
                'productionOrder', 'team', 'machine', 'branch',
                'items.productionOrderItem.bom.finishProduct', 'requisitions', 'createdBy'
            ])
            ->whereBetween('schedule_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($teamId) {
            $query->where('team_id', $teamId);
        }
        if ($machineId) {
            $query->where('machine_id', $machineId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $schedules = $query->orderBy('schedule_date', 'desc')->get();

        $totals = [
            'total_schedules' => $schedules->count(),
            'pending' => $schedules->where('status', 'pending')->count(),
            'approved' => $schedules->where('status', 'approved')->count(),
            'total_scheduled_qty' => $schedules->sum(function($s) {
                return $s->items->sum('scheduled_qty');
            }),
            'total_requisitioned_qty' => $schedules->sum(function($s) {
                return $s->items->sum('requisitioned_qty');
            }),
        ];

        return view('pages.manufacturing.reports.schedules.load', compact('schedules', 'totals'));
    }

    public function printSchedulesReport(Request $request)
    {
        $this->authorize('manufacturing.reports.schedules');

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $teamId = $request->team_id;
        $machineId = $request->machine_id;
        $status = $request->status;

        $query = DailyManufacturingSchedule::with([
                'productionOrder', 'team', 'machine', 'branch',
                'items.productionOrderItem.bom.finishProduct', 'requisitions', 'createdBy'
            ])
            ->whereBetween('schedule_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($teamId) {
            $query->where('team_id', $teamId);
        }
        if ($machineId) {
            $query->where('machine_id', $machineId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $schedules = $query->orderBy('schedule_date', 'desc')->get();

        $totals = [
            'total_schedules' => $schedules->count(),
            'total_scheduled_qty' => $schedules->sum(function($s) {
                return $s->items->sum('scheduled_qty');
            }),
        ];

        return view('pages.manufacturing.reports.schedules.print', compact('schedules', 'totals', 'dateFrom', 'dateTo'));
    }

    // ================================================================
    // MATERIALS REQUISITIONS REPORT
    // ================================================================

    public function requisitionsReport(Request $request)
    {
        $this->authorize('manufacturing.reports.requisitions');

        $user = Auth::user();

        return view('pages.manufacturing.reports.requisitions.index', [
            'branches' => Branch::orderBy('name')->get(),
            'userBranch' => $user->branch_id
        ]);
    }

    public function loadRequisitionsReport(Request $request)
    {
        $this->authorize('manufacturing.reports.requisitions');

        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $status = $request->status;
        $bomType = $request->bom_type;

        $query = MaterialsRequisition::with([
                'schedule.productionOrder', 'bom.finishProduct', 'items.product',
                'items.sourceStore', 'createdBy'
            ])
            ->whereBetween('requisition_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($bomType) {
            $query->whereHas('bom', function($q) use ($bomType) {
                $q->where('bom_type', $bomType);
            });
        }

        $requisitions = $query->orderBy('requisition_date', 'desc')->get();

        $totals = [
            'total_requisitions' => $requisitions->count(),
            'pending' => $requisitions->where('status', 'pending')->count(),
            'approved' => $requisitions->where('status', 'approved')->count(),
            'issued' => $requisitions->where('status', 'issued')->count(),
            'received' => $requisitions->where('status', 'received')->count(),
            'total_required_qty' => $requisitions->sum(function($r) {
                return $r->items->sum('required_qty');
            }),
            'total_issued_qty' => $requisitions->sum(function($r) {
                return $r->items->sum('issued_qty');
            }),
            'total_cost' => $requisitions->sum(function($r) {
                return $r->items->sum(fn($i) => $i->required_qty * ($i->unit_cost ?? 0));
            }),
        ];

        return view('pages.manufacturing.reports.requisitions.load', compact('requisitions', 'totals'));
    }

    public function printRequisitionsReport(Request $request)
    {
        $this->authorize('manufacturing.reports.requisitions');

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $branchId = $request->branch_id;
        $status = $request->status;
        $bomType = $request->bom_type;

        $query = MaterialsRequisition::with([
                'schedule.productionOrder', 'bom.finishProduct', 'items.product',
                'items.sourceStore', 'createdBy'
            ])
            ->whereBetween('requisition_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($bomType) {
            $query->whereHas('bom', function($q) use ($bomType) {
                $q->where('bom_type', $bomType);
            });
        }

        $requisitions = $query->orderBy('requisition_date', 'desc')->get();

        $totals = [
            'total_requisitions' => $requisitions->count(),
            'total_required_qty' => $requisitions->sum(function($r) {
                return $r->items->sum('required_qty');
            }),
            'total_cost' => $requisitions->sum(function($r) {
                return $r->items->sum(fn($i) => $i->required_qty * ($i->unit_cost ?? 0));
            }),
        ];

        return view('pages.manufacturing.reports.requisitions.print', compact('requisitions', 'totals', 'dateFrom', 'dateTo'));
    }

    // ================================================================
    // BATCH CONVERSION REPORT
    // ================================================================

    public function batchConversionReport(Request $request)
    {
        $this->authorize('manufacturing.reports.batch_conversion');

        $user = Auth::user();

        return view('pages.manufacturing.reports.batch_conversion.index', [
            'branches'   => Branch::orderBy('name')->get(),
            'userBranch' => $user->branch_id
        ]);
    }

    public function loadBatchConversionReport(Request $request)
    {
        $this->authorize('manufacturing.reports.batch_conversion');

        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from'
        ]);

        $conversions = $this->fetchBatchConversions($request);
        $totals = $this->calcBatchConversionTotals($conversions);

        return view('pages.manufacturing.reports.batch_conversion.load', compact('conversions', 'totals'));
    }

    public function printBatchConversionReport(Request $request)
    {
        $this->authorize('manufacturing.reports.batch_conversion');

        $dateFrom    = $request->date_from;
        $dateTo      = $request->date_to;
        $conversions = $this->fetchBatchConversions($request);
        $totals      = $this->calcBatchConversionTotals($conversions);

        return view('pages.manufacturing.reports.batch_conversion.print', compact('conversions', 'totals', 'dateFrom', 'dateTo'));
    }

    private function fetchBatchConversions(Request $request)
    {
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;
        $branchId = $request->branch_id;
        $status   = $request->status;

        $conversions = BatchConversion::with([
                'batchProduction.bom.finishProduct',
                'batchProduction.bom',
                'finishProduct',
                'branch',
                'createdBy',
                'postedBy'
            ])
            ->whereBetween('conversion_date', [$dateFrom, $dateTo]);

        if ($branchId) {
            $conversions->where('branch_id', $branchId);
        }
        if ($status) {
            $conversions->where('status', $status);
        }

        $conversions = $conversions->orderBy('conversion_date', 'desc')->get();

        // Enrich each conversion with tolerance computations
        foreach ($conversions as $conv) {
            $batch       = $conv->batchProduction;
            $bom         = $batch ? $batch->bom : null;
            $expectedQty = ($batch && $bom) ? $batch->quantity * ($bom->actual_output ?? 1) : 0;
            $minQty      = $bom ? $expectedQty * (1 - ($bom->accepted_shortage / 100)) : $expectedQty;
            $maxQty      = $bom ? $expectedQty * (1 + ($bom->accepted_excess / 100))   : $expectedQty;

            $producedQty = (float) $conv->produced_qty;
            if ($producedQty < $minQty) {
                $toleranceDiff = $producedQty - $minQty;  // negative → shortage
                $diffType      = 'shortage';
            } elseif ($producedQty > $maxQty) {
                $toleranceDiff = $producedQty - $maxQty;  // positive → excess
                $diffType      = 'excess';
            } else {
                $toleranceDiff = 0;
                $diffType      = 'within';
            }

            $conv->expected_qty      = $expectedQty;
            $conv->min_qty           = $minQty;
            $conv->max_qty           = $maxQty;
            $conv->tolerance_diff    = $toleranceDiff;
            $conv->diff_type         = $diffType;
            $conv->accepted_shortage = $bom->accepted_shortage ?? 0;
            $conv->accepted_excess   = $bom->accepted_excess   ?? 0;
            $conv->actual_shortage   = max(0, $expectedQty - $producedQty);
            $conv->actual_excess     = max(0, $producedQty - $expectedQty);
        }

        return $conversions;
    }

    private function calcBatchConversionTotals($conversions)
    {
        return [
            'total_conversions' => $conversions->count(),
            'pending'           => $conversions->where('status', 'pending')->count(),
            'posted'            => $conversions->where('status', 'posted')->count(),
            'total_produced'    => $conversions->sum('produced_qty'),
            'total_expected'    => $conversions->sum('expected_qty'),
            'total_wip_cost'    => $conversions->sum('wip_cost_deducted'),
            'total_cost'        => $conversions->sum('total_cost'),
            'shortage_count'    => $conversions->where('diff_type', 'shortage')->count(),
            'excess_count'      => $conversions->where('diff_type', 'excess')->count(),
            'within_count'      => $conversions->where('diff_type', 'within')->count(),
        ];
    }

    // ================================================================
    // EXPORT METHODS (PDF & EXCEL) FOR ALL REPORTS
    // ================================================================

    /**
     * Generic helper: render a print view to PDF and return download response
     */
    private function exportPdf(Request $request, string $printMethod, string $filename, string $orientation = 'landscape')
    {
        $response = $this->$printMethod($request);
        $html = $response->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', $orientation)
            ->setOptions([
                'defaultFont' => 'Helvetica',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->download($filename);
    }

    /**
     * Generic helper: render a print view to Excel and return download response
     */
    private function exportExcel(Request $request, string $printMethod, string $viewName, array $viewData, string $filename)
    {
        return Excel::download(
            new ManufacturingReportExport($viewName, $viewData),
            $filename
        );
    }

    // --- History Report Exports ---

    public function exportHistoryPDF(Request $request)
    {
        $this->authorize('manufacturing.reports.history');
        return $this->exportPdf($request, 'printHistoryReport', 'manufacturing_history_report.pdf');
    }

    public function exportHistoryExcel(Request $request)
    {
        $this->authorize('manufacturing.reports.history');
        $response = $this->printHistoryReport($request);
        $data = $response->getData();

        return Excel::download(
            new ManufacturingReportExport('pages.manufacturing.reports.history.print', $data, 'History'),
            'manufacturing_history_report.xlsx'
        );
    }

    // --- Teams Report Exports ---

    public function exportTeamsPDF(Request $request)
    {
        $this->authorize('manufacturing.reports.teams');
        return $this->exportPdf($request, 'printTeamsReport', 'manufacturing_teams_report.pdf');
    }

    public function exportTeamsExcel(Request $request)
    {
        $this->authorize('manufacturing.reports.teams');
        $response = $this->printTeamsReport($request);
        $data = $response->getData();

        return Excel::download(
            new ManufacturingReportExport('pages.manufacturing.reports.teams.print', $data, 'Teams'),
            'manufacturing_teams_report.xlsx'
        );
    }

    // --- Team Ledger Report Exports ---

    public function exportTeamLedgerPDF(Request $request)
    {
        $this->authorize('manufacturing.reports.team_ledger');
        return $this->exportPdf($request, 'printTeamLedgerReport', 'team_ledger_report.pdf');
    }

    public function exportTeamLedgerExcel(Request $request)
    {
        $this->authorize('manufacturing.reports.team_ledger');
        $response = $this->printTeamLedgerReport($request);
        $data = $response->getData();

        return Excel::download(
            new ManufacturingReportExport('pages.manufacturing.reports.team_ledger.print', $data, 'Team Ledger'),
            'team_ledger_report.xlsx'
        );
    }

    // --- Production Orders Report Exports ---

    public function exportOrdersPDF(Request $request)
    {
        $this->authorize('manufacturing.reports.orders');
        return $this->exportPdf($request, 'printOrdersReport', 'production_orders_report.pdf');
    }

    public function exportOrdersExcel(Request $request)
    {
        $this->authorize('manufacturing.reports.orders');
        $response = $this->printOrdersReport($request);
        $data = $response->getData();

        return Excel::download(
            new ManufacturingReportExport('pages.manufacturing.reports.orders.print', $data, 'Orders'),
            'production_orders_report.xlsx'
        );
    }

    // --- Daily Schedules Report Exports ---

    public function exportSchedulesPDF(Request $request)
    {
        $this->authorize('manufacturing.reports.schedules');
        return $this->exportPdf($request, 'printSchedulesReport', 'daily_schedules_report.pdf');
    }

    public function exportSchedulesExcel(Request $request)
    {
        $this->authorize('manufacturing.reports.schedules');
        $response = $this->printSchedulesReport($request);
        $data = $response->getData();

        return Excel::download(
            new ManufacturingReportExport('pages.manufacturing.reports.schedules.print', $data, 'Schedules'),
            'daily_schedules_report.xlsx'
        );
    }

    // --- Materials Requisitions Report Exports ---

    public function exportRequisitionsPDF(Request $request)
    {
        $this->authorize('manufacturing.reports.requisitions');
        return $this->exportPdf($request, 'printRequisitionsReport', 'requisitions_report.pdf');
    }

    public function exportRequisitionsExcel(Request $request)
    {
        $this->authorize('manufacturing.reports.requisitions');
        $response = $this->printRequisitionsReport($request);
        $data = $response->getData();

        return Excel::download(
            new ManufacturingReportExport('pages.manufacturing.reports.requisitions.print', $data, 'Requisitions'),
            'requisitions_report.xlsx'
        );
    }

    // --- Batch Conversion Report Exports ---

    public function exportBatchConversionPDF(Request $request)
    {
        $this->authorize('manufacturing.reports.batch_conversion');
        return $this->exportPdf($request, 'printBatchConversionReport', 'batch_conversion_report.pdf');
    }

    public function exportBatchConversionExcel(Request $request)
    {
        $this->authorize('manufacturing.reports.batch_conversion');
        $response = $this->printBatchConversionReport($request);
        $data = $response->getData();

        return Excel::download(
            new ManufacturingReportExport('pages.manufacturing.reports.batch_conversion.print', $data, 'Batch Conversion'),
            'batch_conversion_report.xlsx'
        );
    }
}
