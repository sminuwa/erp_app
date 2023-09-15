<?php

namespace App\Http\Controllers;

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

        }
        catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {


        $today_date = date('Y-m-d');
        $user = Auth::user();
        $user_sales_per_branch = Order::select(DB::raw('SUM(total) AS total'), 'name')->distinct()
            ->join('users', 'users.id', 'orders.sold_by')
            ->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Cash','orders.status'=>1])->whereDate('orders.order_date', $today_date);
            if($user->hasRole('Sales-Manager'))
                $user_sales_per_branch = $user_sales_per_branch->where('sold_by',$user->id);
            $user_sales_per_branch = $user_sales_per_branch->groupBy('sold_by')->get();
        //Yotal Cash Sales
        $today = Order::whereDate('order_date', $today_date)->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Cash','status'=>1])->get();
        $yesterday = Order::whereDate('order_date', date('Y-m-d', strtotime('-1 day')))->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Cash','orders.status'=>1])->get();

        $month_date = date('m');
        $month = Order::distinct()->whereMonth('order_date', $month_date)->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Cash','status'=>1])->get();
        $previous_month = Order::whereMonth('order_date', date('m', strtotime('-1 month')))->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Cash','orders.status'=>1])->get();

        $year_date = date('Y');
        $year = Order::distinct()->whereYear('order_date', $year_date)->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Cash','status'=>1])->get();
        $previous_year = Order::whereYear('order_date', date('Y', strtotime('-1 year')))->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Cash','orders.status'=>1])->get();

        $sales = Order::where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Cash','orders.status'=>1])->get();

        //Total Credit Sales
        $today_due = Order::whereDate('order_date', $today_date)->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Credit','status'=>1])->where('due','>',0)->get();
        $yesterday_due = Order::whereDate('order_date', date('Y-m-d', strtotime('-1 day')))->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Credit','orders.status'=>1])->where('due','>',0)->get();

        
        $month_due = Order::distinct()->whereMonth('order_date', $month_date)->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Credit','status'=>1])->where('due','>',0)->get();
        $previous_month_due = Order::whereMonth('order_date', date('m', strtotime('-1 month')))->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Credit','orders.status'=>1])->where('due','>',0)->get();

        
        $year_due = Order::distinct()->whereYear('order_date', $year_date)->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Credit','status'=>1])->where('due','>',0)->get();
        $previous_year_due = Order::whereYear('order_date', date('Y', strtotime('-1 year')))->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Credit','orders.status'=>1])->where('due','>',0)->get();

        $sales_due = Order::where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Credit','orders.status'=>1])->where('due','>',0)->get();

        $today_expenses = Expense::join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
        ->where(['bank_accounts.branch_id' => User::userBranchAction()])->whereDate('expenses.date', $today_date)->get();
        $yesterday_expenses = Expense::join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
        ->where(['bank_accounts.branch_id' => User::userBranchAction()])->whereDate('expenses.date', date('Y-m-d', strtotime('-1 day')))->get();

        $month_expenses = Expense::join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
        ->where(['bank_accounts.branch_id' => User::userBranchAction()])->whereMonth('expenses.date', $month_date)->get();
        $previous_month_expenses = Expense::join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
        ->where(['bank_accounts.branch_id' => User::userBranchAction()])->whereMonth('expenses.date', date('m', strtotime('-1 month')))->get();

        $year_expenses = Expense::join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
        ->where(['bank_accounts.branch_id' => User::userBranchAction()])->whereYear('expenses.date', $year_date)->get();
        $previous_year_expenses = Expense::join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
        ->where(['bank_accounts.branch_id' => User::userBranchAction()])->whereYear('expenses.date', date('Y', strtotime('-1 year')))->get();

        $expenses = Expense::join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
            ->where(['bank_accounts.branch_id' => User::userBranchAction()])->get();

        // for charts
        $current_sales = Order::select(
            DB::raw('sum(total) as sums'),
            DB::raw("DATE_FORMAT(order_date,'%m') as months"),
            DB::raw("DATE_FORMAT(order_date,'%Y') as year"))
            ->whereYear('order_date', date('Y'))->where(['orders.branch_id' => User::userBranchAction(),'payment_mode'=>'Cash','orders.status'=>1])
            ->groupBy('months', 'order_date')->get();

        $current_expenses = Expense::select(
            DB::raw('sum(amount) as sums'),
            DB::raw("DATE_FORMAT(expenses.date,'%m') as months"),
            DB::raw("DATE_FORMAT(expenses.date,'%Y') as year"))
            ->join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
            ->where(['bank_accounts.branch_id' => User::userBranchAction()])
            ->whereYear('expenses.date', date('Y'))
            ->groupBy('months', 'expenses.date')->get();

        return view('home', compact('today', 'yesterday', 'month', 'previous_month', 'year', 'previous_year', 'sales',
        'today_due', 'yesterday_due', 'month_due', 'previous_month_due', 'year_due', 'previous_year_due', 'sales_due',
         'today_expenses', 'yesterday_expenses', 'month_expenses', 'previous_month_expenses', 'year_expenses', 'previous_year_expenses', 'expenses', 'current_sales', 'current_expenses', 'user_sales_per_branch'));
    }
}
