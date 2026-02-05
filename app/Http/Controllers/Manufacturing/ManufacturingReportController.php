<?php

namespace App\Http\Controllers\Manufacturing;

use App\Http\Controllers\Controller;
use App\Models\SingleProductManufacturing;
use App\Models\BatchProduction;
use App\Models\ManufacturingTeam;
use App\Models\ManufacturingBom;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ManufacturingReportController extends Controller
{
    /**
     * Manufacturing History Report - Index with filters
     */
    public function historyReport(Request $request)
    {
        $this->authorize('manufacturing.reports.history');

        $user = Auth::user();

        return view('pages.manufacturing.reports.history.index', [
            'branches' => Branch::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'boms' => ManufacturingBom::with('finishProduct')->orderBy('reference')->get(),
            'teams' => ManufacturingTeam::active()->orderBy('name')->get(),
            'userBranch' => $user->branch_id
        ]);
    }

    /**
     * Load Manufacturing History Report data
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
        $productId = $request->product_id;
        $bomId = $request->bom_id;
        $teamId = $request->team_id;
        $reportType = $request->report_type ?? 'all';

        $singleManufacturing = collect([]);
        $batchProductions = collect([]);

        // Get Single Product Manufacturing records
        if ($reportType == 'all' || $reportType == 'single') {
            $singleQuery = SingleProductManufacturing::with(['bom.finishProduct', 'team', 'createdBy'])
                ->where('status', 'posted')
                ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);

            if ($branchId) {
                $singleQuery->where('branch_id', $branchId);
            }
            if ($bomId) {
                $singleQuery->where('bom_id', $bomId);
            }
            if ($teamId) {
                $singleQuery->where('team_id', $teamId);
            }
            if ($productId) {
                $singleQuery->whereHas('bom', function($q) use ($productId) {
                    $q->where('finish_product_id', $productId);
                });
            }

            $singleManufacturing = $singleQuery->orderBy('manufacturing_date', 'desc')->get();
        }

        // Get Batch Production records
        if ($reportType == 'all' || $reportType == 'batch') {
            $batchQuery = BatchProduction::with(['bom.finishProduct', 'team', 'createdBy'])
                ->where('status', 'posted')
                ->whereBetween('production_date', [$dateFrom, $dateTo]);

            if ($branchId) {
                $batchQuery->where('branch_id', $branchId);
            }
            if ($bomId) {
                $batchQuery->where('bom_id', $bomId);
            }
            if ($teamId) {
                $batchQuery->where('team_id', $teamId);
            }
            if ($productId) {
                $batchQuery->whereHas('bom', function($q) use ($productId) {
                    $q->where('finish_product_id', $productId);
                });
            }

            $batchProductions = $batchQuery->orderBy('production_date', 'desc')->get();
        }

        // Calculate totals
        $totals = [
            'single_count' => $singleManufacturing->count(),
            'single_qty' => $singleManufacturing->sum('quantity'),
            'single_cost' => $singleManufacturing->sum('total_cost'),
            'batch_count' => $batchProductions->count(),
            'batch_qty' => $batchProductions->sum('quantity'),
            'batch_wip' => $batchProductions->sum('wip_value'),
            'batch_converted' => $batchProductions->sum('converted_qty')
        ];

        return view('pages.manufacturing.reports.history.load', [
            'singleManufacturing' => $singleManufacturing,
            'batchProductions' => $batchProductions,
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
        $productId = $request->product_id;
        $bomId = $request->bom_id;
        $teamId = $request->team_id;
        $reportType = $request->report_type ?? 'all';

        $singleManufacturing = collect([]);
        $batchProductions = collect([]);

        // Get Single Product Manufacturing records
        if ($reportType == 'all' || $reportType == 'single') {
            $singleQuery = SingleProductManufacturing::with(['bom.finishProduct', 'team', 'createdBy'])
                ->where('status', 'posted')
                ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);

            if ($branchId) {
                $singleQuery->where('branch_id', $branchId);
            }
            if ($bomId) {
                $singleQuery->where('bom_id', $bomId);
            }
            if ($teamId) {
                $singleQuery->where('team_id', $teamId);
            }
            if ($productId) {
                $singleQuery->whereHas('bom', function($q) use ($productId) {
                    $q->where('finish_product_id', $productId);
                });
            }

            $singleManufacturing = $singleQuery->orderBy('manufacturing_date', 'desc')->get();
        }

        // Get Batch Production records
        if ($reportType == 'all' || $reportType == 'batch') {
            $batchQuery = BatchProduction::with(['bom.finishProduct', 'team', 'createdBy'])
                ->where('status', 'posted')
                ->whereBetween('production_date', [$dateFrom, $dateTo]);

            if ($branchId) {
                $batchQuery->where('branch_id', $branchId);
            }
            if ($bomId) {
                $batchQuery->where('bom_id', $bomId);
            }
            if ($teamId) {
                $batchQuery->where('team_id', $teamId);
            }
            if ($productId) {
                $batchQuery->whereHas('bom', function($q) use ($productId) {
                    $q->where('finish_product_id', $productId);
                });
            }

            $batchProductions = $batchQuery->orderBy('production_date', 'desc')->get();
        }

        // Calculate totals
        $totals = [
            'single_count' => $singleManufacturing->count(),
            'single_qty' => $singleManufacturing->sum('quantity'),
            'single_cost' => $singleManufacturing->sum('total_cost'),
            'batch_count' => $batchProductions->count(),
            'batch_qty' => $batchProductions->sum('quantity'),
            'batch_wip' => $batchProductions->sum('wip_value'),
            'batch_converted' => $batchProductions->sum('converted_qty')
        ];

        return view('pages.manufacturing.reports.history.print', [
            'singleManufacturing' => $singleManufacturing,
            'batchProductions' => $batchProductions,
            'totals' => $totals,
            'filters' => $request->all(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    /**
     * Teams Report - Index with filters
     */
    public function teamsReport(Request $request)
    {
        $this->authorize('manufacturing.reports.teams');

        $user = Auth::user();

        return view('pages.manufacturing.reports.teams.index', [
            'branches' => Branch::orderBy('name')->get(),
            'teams' => ManufacturingTeam::orderBy('name')->get(),
            'userBranch' => $user->branch_id
        ]);
    }

    /**
     * Load Teams Report data
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

        // Get team performance data
        $teamsQuery = ManufacturingTeam::with(['supervisors.employee', 'members.staff'])
            ->withCount([
                'singleManufacturing as single_count' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);
                },
                'batchProductions as batch_count' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('production_date', [$dateFrom, $dateTo]);
                }
            ])
            ->withSum([
                'singleManufacturing as single_qty' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);
                }
            ], 'quantity')
            ->withSum([
                'singleManufacturing as single_cost' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);
                }
            ], 'total_cost')
            ->withSum([
                'batchProductions as batch_qty' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('production_date', [$dateFrom, $dateTo]);
                }
            ], 'quantity')
            ->withSum([
                'batchProductions as batch_wip' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('production_date', [$dateFrom, $dateTo]);
                }
            ], 'wip_value')
            ->withSum([
                'penalties as total_penalties' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('penalty_date', [$dateFrom, $dateTo]);
                }
            ], 'amount_charged');

        if ($branchId) {
            $teamsQuery->where('branch_id', $branchId);
        }
        if ($teamId) {
            $teamsQuery->where('id', $teamId);
        }

        $teams = $teamsQuery->orderBy('name')->get();

        // Calculate overall totals
        $totals = [
            'total_single_count' => $teams->sum('single_count'),
            'total_single_qty' => $teams->sum('single_qty'),
            'total_single_cost' => $teams->sum('single_cost'),
            'total_batch_count' => $teams->sum('batch_count'),
            'total_batch_qty' => $teams->sum('batch_qty'),
            'total_batch_wip' => $teams->sum('batch_wip'),
            'total_penalties' => $teams->sum('total_penalties')
        ];

        return view('pages.manufacturing.reports.teams.load', [
            'teams' => $teams,
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

        // Get team performance data
        $teamsQuery = ManufacturingTeam::with(['supervisors.employee', 'members.staff'])
            ->withCount([
                'singleManufacturing as single_count' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);
                },
                'batchProductions as batch_count' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('production_date', [$dateFrom, $dateTo]);
                }
            ])
            ->withSum([
                'singleManufacturing as single_qty' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);
                }
            ], 'quantity')
            ->withSum([
                'singleManufacturing as single_cost' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('manufacturing_date', [$dateFrom, $dateTo]);
                }
            ], 'total_cost')
            ->withSum([
                'batchProductions as batch_qty' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('production_date', [$dateFrom, $dateTo]);
                }
            ], 'quantity')
            ->withSum([
                'batchProductions as batch_wip' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('production_date', [$dateFrom, $dateTo]);
                }
            ], 'wip_value')
            ->withSum([
                'penalties as total_penalties' => function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'posted')
                      ->whereBetween('penalty_date', [$dateFrom, $dateTo]);
                }
            ], 'amount_charged');

        if ($branchId) {
            $teamsQuery->where('branch_id', $branchId);
        }
        if ($teamId) {
            $teamsQuery->where('id', $teamId);
        }

        $teams = $teamsQuery->orderBy('name')->get();

        // Calculate overall totals
        $totals = [
            'total_single_count' => $teams->sum('single_count'),
            'total_single_qty' => $teams->sum('single_qty'),
            'total_single_cost' => $teams->sum('single_cost'),
            'total_batch_count' => $teams->sum('batch_count'),
            'total_batch_qty' => $teams->sum('batch_qty'),
            'total_batch_wip' => $teams->sum('batch_wip'),
            'total_penalties' => $teams->sum('total_penalties')
        ];

        return view('pages.manufacturing.reports.teams.print', [
            'teams' => $teams,
            'totals' => $totals,
            'filters' => $request->all(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }
}
