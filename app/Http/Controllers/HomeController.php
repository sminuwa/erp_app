<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\GeneralAccountLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        try {
            Artisan::call('optimize:clear');
            echo 'Cleared cache';

        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {


    //     $today_date = date('Y-m-d');
    //     $user = Auth::user();
    //     $user_sales_per_branch = Order::select(DB::raw('SUM(total) AS total'), 'name')->distinct()
    //         ->join('users', 'users.id', 'orders.sold_by')
    //         ->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Cash', 'orders.status' => 1])->whereDate('orders.order_date', $today_date);
    //     if ($user->hasRole('Sales-Manager'))
    //         $user_sales_per_branch = $user_sales_per_branch->where('sold_by', $user->id);
    //     $user_sales_per_branch = $user_sales_per_branch->groupBy('sold_by')->get();
    //     //Yotal Cash Sales
    //     $today = Order::whereDate('order_date', $today_date)->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Cash', 'status' => 1])->get();
    //     $yesterday = Order::whereDate('order_date', date('Y-m-d', strtotime('-1 day')))->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Cash', 'orders.status' => 1])->get();

    //     $month_date = date('m');
    //     $month = Order::distinct()->whereMonth('order_date', $month_date)->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Cash', 'status' => 1])->get();
    //     $previous_month = Order::whereMonth('order_date', date('m', strtotime('-1 month')))->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Cash', 'orders.status' => 1])->get();

    //     $year_date = date('Y');
    //     $year = Order::distinct()->whereYear('order_date', $year_date)->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Cash', 'status' => 1])->get();
    //     $previous_year = Order::whereYear('order_date', date('Y', strtotime('-1 year')))->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Cash', 'orders.status' => 1])->get();
    //      $sales = Order::where([
    //         'orders.branch_id' => User::userBranchAction(),
    //         'orders.status' => 1
    //      ])->get();
    //     $sales_data = Order::where([
    //         'orders.branch_id' => User::userBranchAction(),
    //         'orders.status' => 1
    //     ])
    //         ->whereYear('orders.created_at', date('Y'))
    //         ->selectRaw("MONTH(created_at) as month, SUM(total) as total")
    //         ->groupBy('month')
    //         ->pluck('total', 'month');

    //     $sale_months = [];
    //     $sale_amounts = [];

    //     for ($i = 1; $i <= 12; $i++) {
    //         $sale_months[] = date('F', mktime(0, 0, 0, $i, 1));
    //         $sale_amounts[] = isset($sales_data[$i]) ? (float) $sales_data[$i] : 0;
    //     }

    //     //Total Credit Sales
    //     $today_due = Order::whereDate('order_date', $today_date)->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Credit', 'status' => 1])->where('due', '>', 0)->get();
    //     $yesterday_due = Order::whereDate('order_date', date('Y-m-d', strtotime('-1 day')))->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Credit', 'orders.status' => 1])->where('due', '>', 0)->get();


    //     $month_due = Order::distinct()->whereMonth('order_date', $month_date)->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Credit', 'status' => 1])->where('due', '>', 0)->get();
    //     $previous_month_due = Order::whereMonth('order_date', date('m', strtotime('-1 month')))->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Credit', 'orders.status' => 1])->where('due', '>', 0)->get();


    //     $year_due = Order::distinct()->whereYear('order_date', $year_date)->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Credit', 'status' => 1])->where('due', '>', 0)->get();
    //     $previous_year_due = Order::whereYear('order_date', date('Y', strtotime('-1 year')))->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Credit', 'orders.status' => 1])->where('due', '>', 0)->get();

    //     $sales_due = Order::where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Credit', 'orders.status' => 1])->where('due', '>', 0)->get();

    //     $today_expenses = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', User::userBranchAction())
    //         ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //         ->whereDate('general_account_ledgers.date', $today_date)
    //         ->get();

    //     $yesterday_expenses = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', User::userBranchAction())
    //         ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //         ->whereDate('general_account_ledgers.date', date('Y-m-d', strtotime('-1 day')))
    //         ->get();

    //     $month_expenses = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', User::userBranchAction())
    //         ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //         ->whereMonth('general_account_ledgers.date', $month_date)
    //         ->whereYear('general_account_ledgers.date', $year_date)
    //         ->get();
    //     $monthly_expenses = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', User::userBranchAction())
    //         ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //         ->whereYear('general_account_ledgers.date', date('Y'))
    //         ->selectRaw("MONTH(general_account_ledgers.date) as month, SUM(debit) as total")
    //         ->groupBy('month')
    //         ->pluck('total', 'month');

    //     $months = [];
    //     $amounts = [];

    //     for ($i = 1; $i <= 12; $i++) {
    //         $months[] = date('F', mktime(0, 0, 0, $i, 1));
    //         $amounts[] = isset($monthly_expenses[$i]) ? (float) $monthly_expenses[$i] : 0;
    //     }

    //     $previous_month_expenses = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', User::userBranchAction())
    //         ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //         ->whereYear('general_account_ledgers.date', $year_date)
    //         ->whereMonth('general_account_ledgers.date', date('m', strtotime('-1 month')))
    //         ->get();

    //     $year_expenses = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', User::userBranchAction())
    //         ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //         ->whereYear('general_account_ledgers.date', $year_date)
    //         ->get();


    //     $previous_year_expenses = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', User::userBranchAction())
    //         ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //         ->whereYear('general_account_ledgers.date', date('Y', strtotime('-1 year')))
    //         ->get();

    //     $expenses = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', User::userBranchAction())
    //         ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //         ->get();


    //     // for charts
    //     $current_sales = Order::select(
    //         DB::raw('sum(total) as sums'),
    //         DB::raw("DATE_FORMAT(order_date,'%m') as months"),
    //         DB::raw("DATE_FORMAT(order_date,'%Y') as year")
    //     )
    //         ->whereYear('order_date', date('Y'))->where(['orders.branch_id' => User::userBranchAction(), 'payment_mode' => 'Cash', 'orders.status' => 1])
    //         ->groupBy('months', 'order_date')->get();

    //     $current_expenses = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', User::userBranchAction())
    //         ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //         ->whereYear('general_account_ledgers.date', date('Y'))
    //         ->select(
    //             DB::raw('SUM(debit) as sums'),
    //             DB::raw("DATE_FORMAT(general_account_ledgers.date,'%m') as months"),
    //             DB::raw("DATE_FORMAT(general_account_ledgers.date,'%Y') as year")
    //         )
    //         ->groupBy('months', 'year')
    //         ->get();

    //     $companies = Company::orderBy('name')->get();
    //     $branches = Branch::orderBy('name')->get();

    //     $q_month = date('n'); // Numeric month (1-12)
    //     $quarter = match (true) {
    //         $q_month >= 1 && $q_month <= 3 => 'Q1',
    //         $q_month >= 4 && $q_month <= 6 => 'Q2',
    //         $q_month >= 7 && $q_month <= 9 => 'Q3',
    //         default => 'Q4'
    //     };

    //     $today_budget = DB::table('budgets')
    //         ->where('branch_id', auth()->user()->branch_id)
    //         ->where('budget_year', date('Y'))
    //         ->where('quarter', $quarter)
    //         ->sum(DB::raw('IFNULL(month1, 0) + IFNULL(month2, 0) + IFNULL(month3, 0)'));

    //     $month_budget = DB::table('budgets')
    //         ->where('branch_id', auth()->user()->branch_id)
    //         ->where('budget_year', date('Y'))
    //         ->where('quarter', match (true) {
    //             date('n') >= 1 && date('n') <= 3 => 'Q1',
    //             date('n') >= 4 && date('n') <= 6 => 'Q2',
    //             date('n') >= 7 && date('n') <= 9 => 'Q3',
    //             default => 'Q4'
    //         })
    //         ->sum(DB::raw('IFNULL(month1, 0) + IFNULL(month2, 0) + IFNULL(month3, 0)'));

    //     $year_budget = DB::table('budgets')
    //         ->where('branch_id', auth()->user()->branch_id)
    //         ->where('budget_year', date('Y'))
    //         ->sum(DB::raw('IFNULL(month1, 0) + IFNULL(month2, 0) + IFNULL(month3, 0)'));


    //     return view('home', compact(
    //         'today',
    //         'yesterday',
    //         'month',
    //         'previous_month',
    //         'year',
    //         'previous_year',
    //         'sales',
    //         'today_due',
    //         'yesterday_due',
    //         'month_due',
    //         'previous_month_due',
    //         'year_due',
    //         'previous_year_due',
    //         'sales_due',
    //         'today_expenses',
    //         'yesterday_expenses',
    //         'month_expenses',
    //         'previous_month_expenses',
    //         'year_expenses',
    //         'previous_year_expenses',
    //         'expenses',
    //         'current_sales',
    //         'current_expenses',
    //         'user_sales_per_branch',
    //         'branches',
    //         'companies',
    //         'today_budget',
    //         'month_budget',
    //         'year_budget',
    //         'months',
    //         'amounts',
    //         'sale_months',
    //         'sale_amounts'
    //     ));
    // }
    public function index()
    {
        $today_date = date('Y-m-d');
        $month_date = date('m');
        $year_date = date('Y');
        $branchId = User::userBranchAction();
        $user = Auth::user();

        // Sales data
        $salesQuery = Order::where('orders.branch_id', $branchId)
            ->where('orders.status', 1);

        $today = (clone $salesQuery)->whereDate('order_date', $today_date)->where('payment_mode', 'Cash')->get();
        $yesterday = (clone $salesQuery)->whereDate('order_date', date('Y-m-d', strtotime('-1 day')))->where('payment_mode', 'Cash')->get();
        $month = (clone $salesQuery)->whereMonth('order_date', $month_date)->where('payment_mode', 'Cash')->get();
        $previous_month = (clone $salesQuery)->whereMonth('order_date', date('m', strtotime('-1 month')))->where('payment_mode', 'Cash')->get();
        $year = (clone $salesQuery)->whereYear('order_date', $year_date)->where('payment_mode', 'Cash')->get();
        $previous_year = (clone $salesQuery)->whereYear('order_date', date('Y', strtotime('-1 year')))->where('payment_mode', 'Cash')->get();
        $sales = (clone $salesQuery)->get();

        $sales_data = (clone $salesQuery)->whereYear('created_at', $year_date)
            ->selectRaw("MONTH(created_at) as month, SUM(total) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $sale_months = [];
        $sale_amounts = [];
        for ($i = 1; $i <= 12; $i++) {
            $sale_months[] = date('F', mktime(0, 0, 0, $i, 1));
            $sale_amounts[] = isset($sales_data[$i]) ? (float) $sales_data[$i] : 0;
        }

        // Credit Sales Due
        $dueQuery = (clone $salesQuery)->where('payment_mode', 'Credit')->where('due', '>', 0);
        $today_due = (clone $dueQuery)->whereDate('order_date', $today_date)->get();
        $yesterday_due = (clone $dueQuery)->whereDate('order_date', date('Y-m-d', strtotime('-1 day')))->get();
        $month_due = (clone $dueQuery)->whereMonth('order_date', $month_date)->get();
        $previous_month_due = (clone $dueQuery)->whereMonth('order_date', date('m', strtotime('-1 month')))->get();
        $year_due = (clone $dueQuery)->whereYear('order_date', $year_date)->get();
        $previous_year_due = (clone $dueQuery)->whereYear('order_date', date('Y', strtotime('-1 year')))->get();
        $sales_due = $dueQuery->get();

        // User sales per branch
        $user_sales_per_branch = Order::select(DB::raw('SUM(total) AS total'), 'name')
            ->join('users', 'users.id', 'orders.sold_by')
            ->where('orders.branch_id', $branchId)
            ->where('payment_mode', 'Cash')
            ->where('orders.status', 1)
            ->whereDate('orders.order_date', $today_date);
        if ($user->hasRole('Sales-Manager')) {
            $user_sales_per_branch = $user_sales_per_branch->where('sold_by', $user->id);
        }
        $user_sales_per_branch = $user_sales_per_branch->groupBy('sold_by')->get();

        // Expenses
        $expensesQuery = DB::table('general_account_ledgers')
            ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->where('general_account_ledgers.branch_id', $branchId)
            ->whereBetween('general_accounts.class', ['C51', 'C63']);

        $today_expenses = (clone $expensesQuery)->whereDate('general_account_ledgers.date', $today_date)->get();
        $yesterday_expenses = (clone $expensesQuery)->whereDate('general_account_ledgers.date', date('Y-m-d', strtotime('-1 day')))->get();
        $month_expenses = (clone $expensesQuery)->whereMonth('general_account_ledgers.date', $month_date)->whereYear('general_account_ledgers.date', $year_date)->get();
        $previous_month_expenses = (clone $expensesQuery)->whereMonth('general_account_ledgers.date', date('m', strtotime('-1 month')))->whereYear('general_account_ledgers.date', $year_date)->get();
        $year_expenses = (clone $expensesQuery)->whereYear('general_account_ledgers.date', $year_date)->get();
        $previous_year_expenses = (clone $expensesQuery)->whereYear('general_account_ledgers.date', date('Y', strtotime('-1 year')))->get();
        $expenses = $expensesQuery->get();

        // Expense Budgets
        $expense_budgets = DB::table('budget_expenditures')
            ->where('branch_id', $branchId)
            ->where('budget_year', $year_date)
            ->select(DB::raw('SUM(proposed_budget) as total'))
            ->value('total');

        // Charts
        $monthly_expenses = (clone $expensesQuery)
            ->whereYear('general_account_ledgers.date', $year_date)
            ->selectRaw("MONTH(general_account_ledgers.date) as month, SUM(debit) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $months = [];
        $amounts = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('F', mktime(0, 0, 0, $i, 1));
            $amounts[] = isset($monthly_expenses[$i]) ? (float) $monthly_expenses[$i] : 0;
        }

        $current_sales = (clone $salesQuery)
            ->select(
                DB::raw('sum(total) as sums'),
                DB::raw("DATE_FORMAT(order_date,'%m') as months"),
                DB::raw("DATE_FORMAT(order_date,'%Y') as year")
            )
            ->where('payment_mode', 'Cash')
            ->groupBy('months', 'order_date')
            ->get();

        $current_expenses = (clone $expensesQuery)
            ->select(
                DB::raw('SUM(debit) as sums'),
                DB::raw("DATE_FORMAT(general_account_ledgers.date,'%m') as months"),
                DB::raw("DATE_FORMAT(general_account_ledgers.date,'%Y') as year")
            )
            ->groupBy('months', 'year')
            ->get();

        $companies = Company::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        $q_month = date('n');
        $quarter = match (true) {
            $q_month >= 1 && $q_month <= 3 => 'Q1',
            $q_month >= 4 && $q_month <= 6 => 'Q2',
            $q_month >= 7 && $q_month <= 9 => 'Q3',
            default => 'Q4'
        };

        $today_budget = DB::table('budgets')
            ->where('branch_id', $branchId)
            ->where('budget_year', $year_date)
            ->where('quarter', $quarter)
            ->sum(DB::raw('IFNULL(month1, 0) + IFNULL(month2, 0) + IFNULL(month3, 0)'));

        $month_budget = DB::table('budgets')
            ->where('branch_id', $branchId)
            ->where('budget_year', $year_date)
            ->where('quarter', $quarter)
            ->sum(DB::raw('IFNULL(month1, 0) + IFNULL(month2, 0) + IFNULL(month3, 0)'));

        $year_budget = DB::table('budgets')
            ->where('branch_id', $branchId)
            ->where('budget_year', $year_date)
            ->sum(DB::raw('IFNULL(month1, 0) + IFNULL(month2, 0) + IFNULL(month3, 0)'));

        return view('home', compact(
            'today',
            'yesterday',
            'month',
            'previous_month',
            'year',
            'previous_year',
            'sales',
            'today_due',
            'yesterday_due',
            'month_due',
            'previous_month_due',
            'year_due',
            'previous_year_due',
            'sales_due',
            'today_expenses',
            'yesterday_expenses',
            'month_expenses',
            'previous_month_expenses',
            'year_expenses',
            'previous_year_expenses',
            'expenses',
            'current_sales',
            'current_expenses',
            'user_sales_per_branch',
            'branches',
            'companies',
            'today_budget',
            'month_budget',
            'year_budget',
            'expense_budgets',
            'months',
            'amounts',
            'sale_months',
            'sale_amounts'
        ));
    }


    public function sampleReport()
    {
        $reports = GeneralAccountLedger::all();
        return view('sample-report', compact('reports'));
    }
    public function summaryAjax(Request $request)
    {
        $year = $request->year;
        $quarter = $request->quarter;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;

        $quarterMonths = [
            'Q1' => [1, 2, 3],
            'Q2' => [4, 5, 6],
            'Q3' => [7, 8, 9],
            'Q4' => [10, 11, 12],
        ];
        $months = $quarter ? ($quarterMonths[$quarter] ?? []) : range(1, 12);


        $summaries = [];

        // 1. Specific branch selected
        if ($branch_id) {
            $actual = Order::whereYear('order_date', $year)
                ->whereIn(DB::raw('MONTH(order_date)'), $months)
                ->where('branch_id', $branch_id)
                ->where('status', 1)
                ->sum('total');

            $budgetQuery = DB::table('budgets')
                ->where('budget_year', $year)
                ->where('branch_id', $branch_id);

            if ($quarter) {
                $budgetQuery->where('quarter', $quarter);
            } else {
                $budgetQuery->where('quarter', 'LIKE', '%');
            }

            //$budget = $budgetQuery->sum(DB::raw('IFNULL(month1,0) + IFNULL(month2,0) + IFNULL(month3,0)'));
            $budget = $budgetQuery->sum('total');


            $branchName = Branch::find($branch_id)?->name ?? 'Branch';

            $summaries[] = [
                'label' => $branchName,
                'actual' => $actual,
                'budget' => $budget,
                'type' => 'Branch'
            ];
        }

        // 2. Specific company selected, branch not selected
        elseif ($company_id) {
            $company = Company::with('branches')->find($company_id);
            if ($company) {
                foreach ($company->branches as $branch) {
                    $actual = Order::whereYear('order_date', $year)
                        ->whereIn(DB::raw('MONTH(order_date)'), $months)
                        ->where('branch_id', $branch->id)
                        ->where('status', 1)
                        ->sum('total');

                    $budget = DB::table('budgets')
                        ->where('budget_year', $year)
                        ->where('quarter', $quarter)
                        ->where('branch_id', $branch->id)
                        ->sum('total');
                    //->sum(DB::raw('IFNULL(month1,0) + IFNULL(month2,0) + IFNULL(month3,0)'));

                    $summaries[] = [
                        'label' => $branch->name,
                        'actual' => $actual,
                        'budget' => $budget,
                        'type' => 'Branch'
                    ];
                }

                // Optional: add overall company summary
                $companyBranchIds = $company->branches->pluck('id')->toArray();

                $companyTotal = Order::whereYear('order_date', $year)
                    ->whereIn(DB::raw('MONTH(order_date)'), $months)
                    ->whereIn('branch_id', $companyBranchIds)
                    ->where('status', 1)
                    ->sum('total');

                $companyBudgetQuery = DB::table('budgets')
                    ->where('budget_year', $year)
                    ->whereIn('branch_id', $companyBranchIds);

                if ($quarter) {
                    $companyBudgetQuery->where('quarter', $quarter);
                } else {
                    $companyBudgetQuery->where('quarter', 'LIKE', '%');
                }

                //$companyBudget = $companyBudgetQuery->sum(DB::raw('IFNULL(month1,0) + IFNULL(month2,0) + IFNULL(month3,0)'));
                $companyBudget = $companyBudgetQuery->sum('total');

                $summaries[] = [
                    'label' => $company->name . ' (Total)',
                    'actual' => $companyTotal,
                    'budget' => $companyBudget,
                    'type' => 'Company'
                ];
            }
        }

        //  3. No company/branch specified — loop all companies
        else {
            // $companies = Company::with('branches')->get();

            // foreach ($companies as $company) {
            //     $branch_ids = $company->branches->pluck('id')->toArray();

            //     $actual = Order::whereYear('order_date', $year)
            //         ->whereIn(DB::raw('MONTH(order_date)'), $months)
            //         ->whereIn('branch_id', $branch_ids)
            //         ->where('status', 1)
            //         ->sum('total');

            //     $budgetQuery = DB::table('budgets')
            //         ->where('budget_year', $year)
            //         ->whereIn('branch_id', $branch_ids);

            //     if ($quarter) {
            //         $budgetQuery->where('quarter', $quarter);
            //     } else {
            //         $budgetQuery->where('quarter', 'LIKE', '%');
            //     }

            //     // $budget = $budgetQuery->sum(DB::raw('IFNULL(month1,0) + IFNULL(month2,0) + IFNULL(month3,0)'));
            //     $budget = $budgetQuery->sum('total');

            //     $summaries[] = [
            //         'label' => $company->name,
            //         'actual' => $actual,
            //         'budget' => $budget,
            //         'type' => 'Company'
            //     ];
            // }
            $companies = Company::with('branches')->get();

            foreach ($companies as $company) {
                $branch_ids = $company->branches->pluck('id')->toArray();

                // Company-wide total
                $companyActual = Order::whereYear('order_date', $year)
                    ->whereIn(DB::raw('MONTH(order_date)'), $months)
                    ->whereIn('branch_id', $branch_ids)
                    ->where('status', 1)
                    ->sum('total');

                $companyBudgetQuery = DB::table('budgets')
                    ->where('budget_year', $year)
                    ->whereIn('branch_id', $branch_ids);

                if ($quarter) {
                    $companyBudgetQuery->where('quarter', $quarter);
                } else {
                    $companyBudgetQuery->where('quarter', 'LIKE', '%');
                }

                $companyBudget = $companyBudgetQuery->sum(DB::raw('IFNULL(month1,0) + IFNULL(month2,0) + IFNULL(month3,0)'));

                $summaries[] = [
                    'label' => $company->name,
                    'actual' => $companyActual,
                    'budget' => $companyBudget,
                    'type' => 'Company',
                ];

                foreach ($company->branches as $branch) {
                    $branchActual = Order::whereYear('order_date', $year)
                        ->whereIn(DB::raw('MONTH(order_date)'), $months)
                        ->where('branch_id', $branch->id)
                        ->where('status', 1)
                        ->sum('total');

                    $branchBudgetQuery = DB::table('budgets')
                        ->where('budget_year', $year)
                        ->where('branch_id', $branch->id);

                    if ($quarter) {
                        $branchBudgetQuery->where('quarter', $quarter);
                    } else {
                        $branchBudgetQuery->where('quarter', 'LIKE', '%');
                    }

                    $branchBudget = $branchBudgetQuery->sum(DB::raw('IFNULL(month1,0) + IFNULL(month2,0) + IFNULL(month3,0)'));

                    $summaries[] = [
                        'label' => $branch->name,
                        'actual' => $branchActual,
                        'budget' => $branchBudget,
                        'type' => 'Branch',
                        'company_label' => $company->name,
                    ];
                }
            }

        }
        //dd($summaries);
        return view('dashboard_summary', [
            'summaries' => $summaries,
            'selected_quarter' => $quarter ?? 'All'
        ]);
    }

}
