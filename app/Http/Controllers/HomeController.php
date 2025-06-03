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
    //     $month_date = date('m');
    //     $year_date = date('Y');
    //     $branchId = User::userBranchAction();
    //     $user = Auth::user();

    //     // Sales data
    //     $salesQuery = Order::where('orders.branch_id', $branchId)
    //         ->where('orders.status', 1);

    //     $today = (clone $salesQuery)->whereDate('order_date', $today_date)->where('payment_mode', 'Cash')->get();
    //     $yesterday = (clone $salesQuery)->whereDate('order_date', date('Y-m-d', strtotime('-1 day')))->where('payment_mode', 'Cash')->get();
    //     $month = (clone $salesQuery)->whereMonth('order_date', $month_date)->where('payment_mode', 'Cash')->get();
    //     $previous_month = (clone $salesQuery)->whereMonth('order_date', date('m', strtotime('-1 month')))->where('payment_mode', 'Cash')->get();
    //     $year = (clone $salesQuery)->whereYear('order_date', $year_date)->where('payment_mode', 'Cash')->get();
    //     $previous_year = (clone $salesQuery)->whereYear('order_date', date('Y', strtotime('-1 year')))->where('payment_mode', 'Cash')->get();
    //     $sales = (clone $salesQuery)->get();

    //     $sales_data = (clone $salesQuery)->whereYear('created_at', $year_date)
    //         ->selectRaw("MONTH(created_at) as month, SUM(total) as total")
    //         ->groupBy('month')
    //         ->pluck('total', 'month');

    //     $sale_months = [];
    //     $sale_amounts = [];
    //     for ($i = 1; $i <= 12; $i++) {
    //         $sale_months[] = date('F', mktime(0, 0, 0, $i, 1));
    //         $sale_amounts[] = isset($sales_data[$i]) ? (float) $sales_data[$i] : 0;
    //     }

    //     // Credit Sales Due
    //     // Paid (Supplier payments)
    //     $today_paid = DB::table('general_account_ledgers')
    //         ->join('suppliers', 'general_account_ledgers.model_id', '=', 'suppliers.id')
    //         ->where('model_name', 'Supplier')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereDate('general_account_ledgers.date', $today_date)
    //         ->sum('debit');

    //     $month_paid = DB::table('general_account_ledgers')
    //         ->join('suppliers', 'general_account_ledgers.model_id', '=', 'suppliers.id')
    //         ->where('model_name', 'Supplier')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereMonth('general_account_ledgers.date', $month_date)
    //         ->whereYear('general_account_ledgers.date', $year_date)
    //         ->sum('debit');

    //     $year_paid = DB::table('general_account_ledgers')
    //         ->join('suppliers', 'general_account_ledgers.model_id', '=', 'suppliers.id')
    //         ->where('model_name', 'Supplier')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereYear('general_account_ledgers.date', $year_date)
    //         ->sum('debit');

    //     $total_paid = DB::table('general_account_ledgers')
    //         ->join('suppliers', 'general_account_ledgers.model_id', '=', 'suppliers.id')
    //         ->where('model_name', 'Supplier')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->sum('debit');

    //     // Due (Customer balances)
    //     $today_due = DB::table('general_account_ledgers')
    //         ->join('customers', 'general_account_ledgers.model_id', '=', 'customers.id')
    //         ->where('model_name', 'Customer')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereDate('general_account_ledgers.date', $today_date)
    //         ->sum('debit');
    //     $yesterday_due = DB::table('general_account_ledgers')
    //         ->join('customers', 'general_account_ledgers.model_id', '=', 'customers.id')
    //         ->where('model_name', 'Customer')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereDate('general_account_ledgers.date', date('Y-m-d', strtotime('-1 day')))
    //         ->sum('debit');
    //     $month_due = DB::table('general_account_ledgers')
    //         ->join('customers', 'general_account_ledgers.model_id', '=', 'customers.id')
    //         ->where('model_name', 'Customer')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereMonth('general_account_ledgers.date', $month_date)
    //         ->whereYear('general_account_ledgers.date', $year_date)
    //         ->sum('debit');

    //     $previous_month_due = DB::table('general_account_ledgers')
    //         ->join('customers', 'general_account_ledgers.model_id', '=', 'customers.id')
    //         ->where('model_name', 'Customer')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereMonth('general_account_ledgers.date', date('m', strtotime('-1 month')))
    //         ->whereYear('general_account_ledgers.date', $year_date)
    //         ->sum('debit');

    //     $year_due = DB::table('general_account_ledgers')
    //         ->join('customers', 'general_account_ledgers.model_id', '=', 'customers.id')
    //         ->where('model_name', 'Customer')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereYear('general_account_ledgers.date', $year_date)
    //         ->sum('debit');

    //     $previous_year_due = DB::table('general_account_ledgers')
    //         ->join('customers', 'general_account_ledgers.model_id', '=', 'customers.id')
    //         ->where('model_name', 'Customer')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereYear('general_account_ledgers.date', date('Y', strtotime('-1 year')))
    //         ->sum('debit');
    //     $total_due = DB::table('general_account_ledgers')
    //         ->join('customers', 'general_account_ledgers.model_id', '=', 'customers.id')
    //         ->where('model_name', 'Customer')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->sum('debit');

    //     // User sales per branch
    //     $user_sales_per_branch = Order::select(DB::raw('SUM(total) AS total'), 'name')
    //         ->join('users', 'users.id', 'orders.sold_by')
    //         ->where('orders.branch_id', $branchId)
    //         ->where('payment_mode', 'Cash')
    //         ->where('orders.status', 1)
    //         ->whereDate('orders.order_date', $today_date);
    //     if ($user->hasRole('Sales-Manager')) {
    //         $user_sales_per_branch = $user_sales_per_branch->where('sold_by', $user->id);
    //     }
    //     $user_sales_per_branch = $user_sales_per_branch->groupBy('sold_by')->get();

    //     // Expenses
    //     $expensesQuery = DB::table('general_account_ledgers')
    //         ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //         ->where('general_account_ledgers.branch_id', $branchId)
    //         ->whereBetween('general_accounts.class', ['C51', 'C63']);

    //     $today_expenses = (clone $expensesQuery)->whereDate('general_account_ledgers.date', $today_date)->get();
    //     $yesterday_expenses = (clone $expensesQuery)->whereDate('general_account_ledgers.date', date('Y-m-d', strtotime('-1 day')))->get();
    //     $month_expenses = (clone $expensesQuery)->whereMonth('general_account_ledgers.date', $month_date)->whereYear('general_account_ledgers.date', $year_date)->get();
    //     $previous_month_expenses = (clone $expensesQuery)->whereMonth('general_account_ledgers.date', date('m', strtotime('-1 month')))->whereYear('general_account_ledgers.date', $year_date)->get();
    //     $year_expenses = (clone $expensesQuery)->whereYear('general_account_ledgers.date', $year_date)->get();
    //     $previous_year_expenses = (clone $expensesQuery)->whereYear('general_account_ledgers.date', date('Y', strtotime('-1 year')))->get();
    //     $expenses = $expensesQuery->get();

    //     // Expense Budgets
    //     $expense_budgets = DB::table('budget_expenditures')
    //         ->where('branch_id', $branchId)
    //         ->where('budget_year', $year_date)
    //         ->select(DB::raw('SUM(proposed_budget) as total'))
    //         ->value('total');

    //     // Charts
    //     $monthly_expenses = (clone $expensesQuery)
    //         ->whereYear('general_account_ledgers.date', $year_date)
    //         ->selectRaw("MONTH(general_account_ledgers.date) as month, SUM(debit) as total")
    //         ->groupBy('month')
    //         ->pluck('total', 'month');

    //     $months = [];
    //     $amounts = [];
    //     for ($i = 1; $i <= 12; $i++) {
    //         $months[] = date('F', mktime(0, 0, 0, $i, 1));
    //         $amounts[] = isset($monthly_expenses[$i]) ? (float) $monthly_expenses[$i] : 0;
    //     }

    //     $current_sales = (clone $salesQuery)
    //         ->select(
    //             DB::raw('sum(total) as sums'),
    //             DB::raw("DATE_FORMAT(order_date,'%m') as months"),
    //             DB::raw("DATE_FORMAT(order_date,'%Y') as year")
    //         )
    //         ->where('payment_mode', 'Cash')
    //         ->groupBy('months', 'order_date')
    //         ->get();

    //     $current_expenses = (clone $expensesQuery)
    //         ->select(
    //             DB::raw('SUM(debit) as sums'),
    //             DB::raw("DATE_FORMAT(general_account_ledgers.date,'%m') as months"),
    //             DB::raw("DATE_FORMAT(general_account_ledgers.date,'%Y') as year")
    //         )
    //         ->groupBy('months', 'year')
    //         ->get();

    //     $companies = Company::orderBy('name')->get();
    //     $branches = Branch::orderBy('name')->get();

    //     $q_month = date('n');
    //     $quarter = match (true) {
    //         $q_month >= 1 && $q_month <= 3 => 'Q1',
    //         $q_month >= 4 && $q_month <= 6 => 'Q2',
    //         $q_month >= 7 && $q_month <= 9 => 'Q3',
    //         default => 'Q4'
    //     };

    //     $today_budget = DB::table('budgets')
    //         ->where('branch_id', $branchId)
    //         ->where('budget_year', $year_date)
    //         ->where('quarter', $quarter)
    //         ->sum(DB::raw('IFNULL(month1, 0) + IFNULL(month2, 0) + IFNULL(month3, 0)'));

    //     $month_budget = DB::table('budgets')
    //         ->where('branch_id', $branchId)
    //         ->where('budget_year', $year_date)
    //         ->where('quarter', $quarter)
    //         ->sum(DB::raw('IFNULL(month1, 0) + IFNULL(month2, 0) + IFNULL(month3, 0)'));

    //     $year_budget = DB::table('budgets')
    //         ->where('branch_id', $branchId)
    //         ->where('budget_year', $year_date)
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
    //         'total_due',
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
    //         'expense_budgets',
    //         'months',
    //         'amounts',
    //         'sale_months',
    //         'sale_amounts'
    //     ));
    // }

    public function index()
    {
        $today_date = now()->format('Y-m-d');
        $month_date = now()->format('m');
        $year_date = now()->format('Y');
        $branchId = User::userBranchAction();
        $user = Auth::user();

        // Calculate date ranges once
        $yesterday_date = now()->subDay()->format('Y-m-d');
        $previous_month = now()->subMonth()->format('m');
        $previous_year = now()->subYear()->format('Y');

        // Get all sales data in one optimized query
        $salesData = $this->getSalesData($branchId, $year_date);

        // Get all account ledger data efficiently
        $ledgerData = $this->getLedgerData($branchId, $year_date);

        // Get expense data efficiently
        $expenseData = $this->getExpenseData($branchId, $year_date);

        // Get budget data
        $budgetData = $this->getBudgetData($branchId, $year_date);

        // Get user sales per branch
        $user_sales_per_branch = $this->getUserSalesPerBranch($branchId, $today_date, $user);

        // Get companies and branches (consider caching these)
        $companies = Company::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        // Generate chart data
        $chartData = $this->generateChartData($salesData['monthly'], $expenseData['monthly']);

        return view('home', array_merge(
            $salesData['summary'],
            $ledgerData,
            $expenseData['summary'],
            $budgetData,
            $chartData,
            compact('user_sales_per_branch', 'branches', 'companies')
        ));
    }

    private function getSalesData($branchId, $year_date)
    {
        // Get all sales data with single query using conditional aggregation
        $salesSummary = Order::where('branch_id', $branchId)
            ->where('status', 1)
            ->where('payment_mode', 'Cash')
            ->selectRaw("
            SUM(CASE WHEN DATE(order_date) = CURDATE() THEN total ELSE 0 END) as today_total,
            SUM(CASE WHEN DATE(order_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN total ELSE 0 END) as yesterday_total,
            SUM(CASE WHEN MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE()) THEN total ELSE 0 END) as month_total,
            SUM(CASE WHEN MONTH(order_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(order_date) = YEAR(CURDATE()) THEN total ELSE 0 END) as previous_month_total,
            SUM(CASE WHEN YEAR(order_date) = YEAR(CURDATE()) THEN total ELSE 0 END) as year_total,
            SUM(CASE WHEN YEAR(order_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) THEN total ELSE 0 END) as previous_year_total,
            COUNT(*) as total_orders
        ")
            ->first();

        // Get monthly sales data for charts
        $monthlySales = Order::where('branch_id', $branchId)
            ->where('status', 1)
            ->where('payment_mode', 'Cash')
            ->whereYear('order_date', $year_date)
            ->selectRaw("MONTH(order_date) as month, SUM(total) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        // Convert to collections for consistency with original code
        $today = collect([['total' => $salesSummary->today_total]]);
        $yesterday = collect([['total' => $salesSummary->yesterday_total]]);
        $month = collect([['total' => $salesSummary->month_total]]);
        $previous_month = collect([['total' => $salesSummary->previous_month_total]]);
        $year = collect([['total' => $salesSummary->year_total]]);
        $previous_year = collect([['total' => $salesSummary->previous_year_total]]);

        // Generate chart data
        $sale_months = [];
        $sale_amounts = [];
        for ($i = 1; $i <= 12; $i++) {
            $sale_months[] = date('F', mktime(0, 0, 0, $i, 1));
            $sale_amounts[] = $monthlySales[$i] ?? 0;
        }

        return [
            'summary' => compact('today', 'yesterday', 'month', 'previous_month', 'year', 'previous_year'),
            'monthly' => $monthlySales,
            'chart' => compact('sale_months', 'sale_amounts')
        ];
    }

    private function getLedgerData($branchId, $year_date)
    {
        // Get supplier payments (paid amounts)
        $paidData = DB::table('general_account_ledgers')
            ->join('suppliers', 'general_account_ledgers.model_id', '=', 'suppliers.id')
            ->where('model_name', 'Supplier')
            ->where('general_account_ledgers.branch_id', $branchId)
            ->selectRaw("
            SUM(CASE WHEN DATE(general_account_ledgers.date) = CURDATE() THEN debit ELSE 0 END) as today_paid,
            SUM(CASE WHEN MONTH(general_account_ledgers.date) = MONTH(CURDATE()) AND YEAR(general_account_ledgers.date) = YEAR(CURDATE()) THEN debit ELSE 0 END) as month_paid,
            SUM(CASE WHEN YEAR(general_account_ledgers.date) = YEAR(CURDATE()) THEN debit ELSE 0 END) as year_paid,
            SUM(debit) as total_paid
        ")
            ->first();

        // Get customer dues
        $dueData = DB::table('general_account_ledgers')
            ->join('customers', 'general_account_ledgers.model_id', '=', 'customers.id')
            ->where('model_name', 'Customer')
            ->where('general_account_ledgers.branch_id', $branchId)
            ->selectRaw("
            SUM(CASE WHEN DATE(general_account_ledgers.date) = CURDATE() THEN debit ELSE 0 END) as today_due,
            SUM(CASE WHEN DATE(general_account_ledgers.date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN debit ELSE 0 END) as yesterday_due,
            SUM(CASE WHEN MONTH(general_account_ledgers.date) = MONTH(CURDATE()) AND YEAR(general_account_ledgers.date) = YEAR(CURDATE()) THEN debit ELSE 0 END) as month_due,
            SUM(CASE WHEN MONTH(general_account_ledgers.date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(general_account_ledgers.date) = YEAR(CURDATE()) THEN debit ELSE 0 END) as previous_month_due,
            SUM(CASE WHEN YEAR(general_account_ledgers.date) = YEAR(CURDATE()) THEN debit ELSE 0 END) as year_due,
            SUM(CASE WHEN YEAR(general_account_ledgers.date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) THEN debit ELSE 0 END) as previous_year_due,
            SUM(debit) as total_due
        ")
            ->first();

        return [
            'today_paid' => $paidData->today_paid ?? 0,
            'month_paid' => $paidData->month_paid ?? 0,
            'year_paid' => $paidData->year_paid ?? 0,
            'total_paid' => $paidData->total_paid ?? 0,
            'today_due' => collect([['debit' => $dueData->today_due ?? 0]]),
            'yesterday_due' => collect([['debit' => $dueData->yesterday_due ?? 0]]),
            'month_due' => collect([['debit' => $dueData->month_due ?? 0]]),
            'previous_month_due' => collect([['debit' => $dueData->previous_month_due ?? 0]]),
            'year_due' => collect([['debit' => $dueData->year_due ?? 0]]),
            'previous_year_due' => collect([['debit' => $dueData->previous_year_due ?? 0]]),
            'total_due' => collect([['debit' => $dueData->total_due ?? 0]])
        ];
    }

    private function getExpenseData($branchId, $year_date)
    {
        // Get all expense data with conditional aggregation
        $expenseSummary = DB::table('general_account_ledgers')
            ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->where('general_account_ledgers.branch_id', $branchId)
            ->whereBetween('general_accounts.class', ['C51', 'C63'])
            ->selectRaw("
            SUM(CASE WHEN DATE(general_account_ledgers.date) = CURDATE() THEN debit ELSE 0 END) as today_expenses,
            SUM(CASE WHEN DATE(general_account_ledgers.date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN debit ELSE 0 END) as yesterday_expenses,
            SUM(CASE WHEN MONTH(general_account_ledgers.date) = MONTH(CURDATE()) AND YEAR(general_account_ledgers.date) = YEAR(CURDATE()) THEN debit ELSE 0 END) as month_expenses,
            SUM(CASE WHEN MONTH(general_account_ledgers.date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(general_account_ledgers.date) = YEAR(CURDATE()) THEN debit ELSE 0 END) as previous_month_expenses,
            SUM(CASE WHEN YEAR(general_account_ledgers.date) = YEAR(CURDATE()) THEN debit ELSE 0 END) as year_expenses,
            SUM(CASE WHEN YEAR(general_account_ledgers.date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) THEN debit ELSE 0 END) as previous_year_expenses,
            SUM(debit) as total_expenses
        ")
            ->first();

        // Get monthly expenses for charts
        $monthlyExpenses = DB::table('general_account_ledgers')
            ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->where('general_account_ledgers.branch_id', $branchId)
            ->whereBetween('general_accounts.class', ['C51', 'C63'])
            ->whereYear('general_account_ledgers.date', $year_date)
            ->selectRaw("MONTH(general_account_ledgers.date) as month, SUM(debit) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        // Get expense budgets
        $expense_budgets = DB::table('budget_expenditures')
            ->where('branch_id', $branchId)
            ->where('budget_year', $year_date)
            ->sum('proposed_budget');

        // Convert to collections for consistency
        $today_expenses = collect([['debit' => $expenseSummary->today_expenses ?? 0]]);
        $yesterday_expenses = collect([['debit' => $expenseSummary->yesterday_expenses ?? 0]]);
        $month_expenses = collect([['debit' => $expenseSummary->month_expenses ?? 0]]);
        $previous_month_expenses = collect([['debit' => $expenseSummary->previous_month_expenses ?? 0]]);
        $year_expenses = collect([['debit' => $expenseSummary->year_expenses ?? 0]]);
        $previous_year_expenses = collect([['debit' => $expenseSummary->previous_year_expenses ?? 0]]);
        $expenses = collect([['debit' => $expenseSummary->total_expenses ?? 0]]);

        return [
            'summary' => compact(
                'today_expenses',
                'yesterday_expenses',
                'month_expenses',
                'previous_month_expenses',
                'year_expenses',
                'previous_year_expenses',
                'expenses',
                'expense_budgets'
            ),
            'monthly' => $monthlyExpenses
        ];
    }

    private function getBudgetData($branchId, $year_date)
    {
        $q_month = date('n');
        $quarter = match (true) {
            $q_month >= 1 && $q_month <= 3 => 'Q1',
            $q_month >= 4 && $q_month <= 6 => 'Q2',
            $q_month >= 7 && $q_month <= 9 => 'Q3',
            default => 'Q4'
        };

        $quarterBudget = DB::table('budgets')
            ->where('branch_id', $branchId)
            ->where('budget_year', $year_date)
            ->where('quarter', $quarter)
            ->sum(DB::raw('COALESCE(month1, 0) + COALESCE(month2, 0) + COALESCE(month3, 0)'));

        $yearBudget = DB::table('budgets')
            ->where('branch_id', $branchId)
            ->where('budget_year', $year_date)
            ->sum(DB::raw('COALESCE(month1, 0) + COALESCE(month2, 0) + COALESCE(month3, 0)'));

        return [
            'today_budget' => $quarterBudget, // Assuming today's budget is the quarter budget
            'month_budget' => $quarterBudget, // Assuming month's budget is the quarter budget
            'year_budget' => $yearBudget
        ];
    }

    private function getUserSalesPerBranch($branchId, $today_date, $user)
    {
        $query = Order::select(DB::raw('SUM(total) AS total'), 'name')
            ->join('users', 'users.id', 'orders.sold_by')
            ->where('orders.branch_id', $branchId)
            ->where('payment_mode', 'Cash')
            ->where('orders.status', 1)
            ->whereDate('orders.order_date', $today_date);

        if ($user->hasRole('Sales-Manager')) {
            $query->where('sold_by', $user->id);
        }

        return $query->groupBy('sold_by')->get();
    }

    private function generateChartData($salesData, $expenseData)
    {
        $months = [];
        $sale_amounts = [];
        $expense_amounts = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('F', mktime(0, 0, 0, $i, 1));
            $sale_amounts[] = (float) ($salesData[$i] ?? 0);
            $expense_amounts[] = (float) ($expenseData[$i] ?? 0);
        }

        return [
            'months' => $months,
            'amounts' => $expense_amounts, // For expense chart
            'sale_months' => $months,
            'sale_amounts' => $sale_amounts
        ];
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

    // public function summaryAjax(Request $request)
    // {
    //     $year = $request->year;
    //     $quarter = $request->quarter;
    //     $company_id = $request->company_id;
    //     $branch_id = $request->branch_id;

    //     $quarterMonths = [
    //         'Q1' => [1, 2, 3],
    //         'Q2' => [4, 5, 6],
    //         'Q3' => [7, 8, 9],
    //         'Q4' => [10, 11, 12],
    //     ];
    //     $months = $quarter ? ($quarterMonths[$quarter] ?? []) : range(1, 12);

    //     $summaries = [];

    //     // 1. Specific branch selected
    //     if ($branch_id) {
    //         // Sales data
    //         $actual_sales = Order::whereYear('order_date', $year)
    //             ->whereIn(DB::raw('MONTH(order_date)'), $months)
    //             ->where('branch_id', $branch_id)
    //             ->where('status', 1)
    //             ->where('payment_mode', 'Cash')
    //             ->sum('total');

    //         // Expense data
    //         $actual_expenses = DB::table('general_account_ledgers')
    //             ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //             ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //             ->where('general_account_ledgers.branch_id', $branch_id)
    //             ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //             ->whereYear('general_account_ledgers.date', $year)
    //             ->whereIn(DB::raw('MONTH(general_account_ledgers.date)'), $months)
    //             ->sum('debit');

    //         // Sales budget
    //         $budgetQuery = DB::table('budgets')
    //             ->where('budget_year', $year)
    //             ->where('branch_id', $branch_id);

    //         if ($quarter) {
    //             $budgetQuery->where('quarter', $quarter);
    //         }

    //         $sales_budget = $budgetQuery->sum(DB::raw('COALESCE(month1, 0) + COALESCE(month2, 0) + COALESCE(month3, 0)'));

    //         // Expense budget
    //         $expense_budget = DB::table('budget_expenditures')
    //             ->where('branch_id', $branch_id)
    //             ->where('budget_year', $year)
    //             ->sum('proposed_budget');

    //         $branchName = Branch::find($branch_id)?->name ?? 'Branch';

    //         $summaries[] = [
    //             'label' => $branchName,
    //             'actual_sales' => $actual_sales,
    //             'sales_budget' => $sales_budget,
    //             'actual_expenses' => $actual_expenses,
    //             'expense_budget' => $expense_budget,
    //             'type' => 'Branch'
    //         ];
    //     }

    //     // 2. Specific company selected, branch not selected
    //     elseif ($company_id) {
    //         $company = Company::with('branches')->find($company_id);
    //         if ($company) {
    //             foreach ($company->branches as $branch) {
    //                 // Sales data
    //                 $actual_sales = Order::whereYear('order_date', $year)
    //                     ->whereIn(DB::raw('MONTH(order_date)'), $months)
    //                     ->where('branch_id', $branch->id)
    //                     ->where('status', 1)
    //                     ->where('payment_mode', 'Cash')
    //                     ->sum('total');

    //                 // Expense data
    //                 $actual_expenses = DB::table('general_account_ledgers')
    //                     ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //                     ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //                     ->where('general_account_ledgers.branch_id', $branch->id)
    //                     ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //                     ->whereYear('general_account_ledgers.date', $year)
    //                     ->whereIn(DB::raw('MONTH(general_account_ledgers.date)'), $months)
    //                     ->sum('debit');

    //                 // Sales budget
    //                 $sales_budget = DB::table('budgets')
    //                     ->where('budget_year', $year)
    //                     ->where('branch_id', $branch->id)
    //                     ->when($quarter, fn($q) => $q->where('quarter', $quarter))
    //                     ->sum(DB::raw('COALESCE(month1, 0) + COALESCE(month2, 0) + COALESCE(month3, 0)'));

    //                 // Expense budget
    //                 $expense_budget = DB::table('budget_expenditures')
    //                     ->where('branch_id', $branch->id)
    //                     ->where('budget_year', $year)
    //                     ->sum('proposed_budget');

    //                 $summaries[] = [
    //                     'label' => $branch->name,
    //                     'actual_sales' => $actual_sales,
    //                     'sales_budget' => $sales_budget,
    //                     'actual_expenses' => $actual_expenses,
    //                     'expense_budget' => $expense_budget,
    //                     'type' => 'Branch'
    //                 ];
    //             }

    //             // Company-wide totals
    //             $companyBranchIds = $company->branches->pluck('id')->toArray();

    //             $company_sales_total = Order::whereYear('order_date', $year)
    //                 ->whereIn(DB::raw('MONTH(order_date)'), $months)
    //                 ->whereIn('branch_id', $companyBranchIds)
    //                 ->where('status', 1)
    //                 ->where('payment_mode', 'Cash')
    //                 ->sum('total');

    //             $company_expenses_total = DB::table('general_account_ledgers')
    //                 ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //                 ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //                 ->whereIn('general_account_ledgers.branch_id', $companyBranchIds)
    //                 ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //                 ->whereYear('general_account_ledgers.date', $year)
    //                 ->whereIn(DB::raw('MONTH(general_account_ledgers.date)'), $months)
    //                 ->sum('debit');

    //             $company_sales_budget = DB::table('budgets')
    //                 ->where('budget_year', $year)
    //                 ->whereIn('branch_id', $companyBranchIds)
    //                 ->when($quarter, fn($q) => $q->where('quarter', $quarter))
    //                 ->sum(DB::raw('COALESCE(month1, 0) + COALESCE(month2, 0) + COALESCE(month3, 0)'));

    //             $company_expense_budget = DB::table('budget_expenditures')
    //                 ->whereIn('branch_id', $companyBranchIds)
    //                 ->where('budget_year', $year)
    //                 ->sum('proposed_budget');

    //             $summaries[] = [
    //                 'label' => $company->name . ' (Total)',
    //                 'actual_sales' => $company_sales_total,
    //                 'sales_budget' => $company_sales_budget,
    //                 'actual_expenses' => $company_expenses_total,
    //                 'expense_budget' => $company_expense_budget,
    //                 'type' => 'Company'
    //             ];
    //         }
    //     }

    //     // 3. No company/branch specified — loop all companies
    //     else {
    //         $companies = Company::with('branches')->get();

    //         foreach ($companies as $company) {
    //             $branch_ids = $company->branches->pluck('id')->toArray();

    //             // Company-wide totals
    //             $company_sales_total = Order::whereYear('order_date', $year)
    //                 ->whereIn(DB::raw('MONTH(order_date)'), $months)
    //                 ->whereIn('branch_id', $branch_ids)
    //                 ->where('status', 1)
    //                 ->where('payment_mode', 'Cash')
    //                 ->sum('total');

    //             $company_expenses_total = DB::table('general_account_ledgers')
    //                 ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //                 ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //                 ->whereIn('general_account_ledgers.branch_id', $branch_ids)
    //                 ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //                 ->whereYear('general_account_ledgers.date', $year)
    //                 ->whereIn(DB::raw('MONTH(general_account_ledgers.date)'), $months)
    //                 ->sum('debit');

    //             $company_sales_budget = DB::table('budgets')
    //                 ->where('budget_year', $year)
    //                 ->whereIn('branch_id', $branch_ids)
    //                 ->when($quarter, fn($q) => $q->where('quarter', $quarter))
    //                 ->sum(DB::raw('COALESCE(month1, 0) + COALESCE(month2, 0) + COALESCE(month3, 0)'));

    //             $company_expense_budget = DB::table('budget_expenditures')
    //                 ->whereIn('branch_id', $branch_ids)
    //                 ->where('budget_year', $year)
    //                 ->sum('proposed_budget');

    //             $summaries[] = [
    //                 'label' => $company->name,
    //                 'actual_sales' => $company_sales_total,
    //                 'sales_budget' => $company_sales_budget,
    //                 'actual_expenses' => $company_expenses_total,
    //                 'expense_budget' => $company_expense_budget,
    //                 'type' => 'Company',
    //             ];

    //             foreach ($company->branches as $branch) {
    //                 $branch_sales_total = Order::whereYear('order_date', $year)
    //                     ->whereIn(DB::raw('MONTH(order_date)'), $months)
    //                     ->where('branch_id', $branch->id)
    //                     ->where('status', 1)
    //                     ->where('payment_mode', 'Cash')
    //                     ->sum('total');

    //                 $branch_expenses_total = DB::table('general_account_ledgers')
    //                     ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //                     ->where('general_account_ledgers.model_name', 'GeneralAccount')
    //                     ->where('general_account_ledgers.branch_id', $branch->id)
    //                     ->whereBetween('general_accounts.class', ['C51', 'C63'])
    //                     ->whereYear('general_account_ledgers.date', $year)
    //                     ->whereIn(DB::raw('MONTH(general_account_ledgers.date)'), $months)
    //                     ->sum('debit');

    //                 $branch_sales_budget = DB::table('budgets')
    //                     ->where('budget_year', $year)
    //                     ->where('branch_id', $branch->id)
    //                     ->when($quarter, fn($q) => $q->where('quarter', $quarter))
    //                     ->sum(DB::raw('COALESCE(month1, 0) + COALESCE(month2, 0) + COALESCE(month3, 0)'));

    //                 $branch_expense_budget = DB::table('budget_expenditures')
    //                     ->where('branch_id', $branch->id)
    //                     ->where('budget_year', $year)
    //                     ->sum('proposed_budget');

    //                 $summaries[] = [
    //                     'label' => $branch->name,
    //                     'actual_sales' => $branch_sales_total,
    //                     'sales_budget' => $branch_sales_budget,
    //                     'actual_expenses' => $branch_expenses_total,
    //                     'expense_budget' => $branch_expense_budget,
    //                     'type' => 'Branch',
    //                     'company_label' => $company->name,
    //                 ];
    //             }
    //         }
    //     }

    //     return view('dashboard_summary', [
    //         'summaries' => $summaries,
    //         'selected_quarter' => $quarter ?? 'All'
    //     ]);
    // }
}
