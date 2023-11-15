<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountLedger;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;
use App\Models\TransferProduct;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\CashMovement;
use App\Models\User;
use App\Models\CustomerLedger;
use App\Models\Expense;
use App\Models\ExpenseItem;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Order;
use App\Models\LoanPayment;
use App\Models\SupplierLedger;
use App\Models\Loan;
use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\StoreProduct;
use App\Models\StockAdjustment;
use BranchProductPrice;
use App\Models\OrderDetail;
use App\Models\StockCard;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\AuditLog;
use App\Models\LoanCollector;


class ReportController extends Controller
{
    public function stockTransfer()
    {
        return view('pages.reports.stock_control.stock_transfer_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadStockTransferReport(Request $request)
    {
        //return $request->stock_in_out;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $from_to = $request->from_to;
        $transfer_to = $request->transfer_to;
        $category_id = $request->category_id;
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }

        $query = TransferProduct::select('nature', 'transfer_products.source_store_id', 'transfer_products.destination_store_id', 'products.name', 'transfer_products.updated_at', 'qty_available', 'qty_transfered', 'refno')
            ->where('transfer_products.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('transfer_products.nature', 'LIKE', 'Transfer')
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->join('stores', 'stores.id', 'transfer_products.source_store_id')
            ->join('products', 'products.id', 'transfer_products.product_id')
            ->whereBetween('transfer_products.updated_at', [$from_date, $to_date]);
        if ($from_to == "from")
            $query->where('source_store_id', 'LIKE', $store_id);
        if ($from_to == "to")
            $query->where('destination_store_id', 'LIKE', $store_id);
        $transfers = $query->get();
        if ($product_id == '%') {
            $product_id = 'all';

        }
        if ($category_id == '%') {
            $category_id = 'all';

        }
        if ($store_id == '%') {
            $store_id = 'all';

        }
        return view('pages.reports.stock_control.load_stock_transfer_report', compact('transfers', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'from_to'));
    }

    public function printStockTransfer($from_date, $to_date, $store_id, $category_id, $product_id, $from_to)
    {
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }

        $query = TransferProduct::select('nature', 'transfer_products.source_store_id', 'transfer_products.destination_store_id', 'products.name', 'transfer_products.updated_at', 'qty_available', 'qty_transfered', 'refno')
            ->where('transfer_products.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('transfer_products.nature', 'LIKE', 'Transfer')
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->join('stores', 'stores.id', 'transfer_products.source_store_id')
            ->join('products', 'products.id', 'transfer_products.product_id')
            ->whereBetween('transfer_products.updated_at', [$from_date, $to_date]);
        if ($from_to == "from")
            $query->where('source_store_id', 'LIKE', $store_id);
        if ($from_to == "to")
            $query->where('destination_store_id', 'LIKE', $store_id);
        $transfers = $query->get();
        //$query2 = $query;
        return view('pages.reports.stock_control.print_stock_transfer', compact('transfers', 'from_date', 'to_date'));
    }


    public function stockIn()
    {
        return view('pages.reports.stock_control.stock_in_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadStockInReport(Request $request)
    {
        //return $request->stock_in_out;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }

        $query = Purchase::select('purchase_date', 'source_store_id', 'qty_supplied', 'products.name', 'unit_price', 'stores.name AS store', 'branches.name AS branch', 'invoice')
            ->where('purchase_products.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->join('stores', 'stores.id', 'source_store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->whereBetween('purchase_date', [$from_date, $to_date]);
        $purchases = $query->get();
        if ($product_id == '%') {
            $product_id = 'all';

        }
        if ($category_id == '%') {
            $category_id = 'all';

        }
        if ($store_id == '%') {
            $store_id = 'all';

        }
        return view('pages.reports.stock_control.load_stock_in_report', compact('purchases', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id'));
    }

    public function printStockIn($from_date, $to_date, $store_id, $category_id, $product_id)
    {
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }

        $query = Purchase::select('purchase_date', 'source_store_id', 'qty_supplied', 'products.name', 'unit_price', 'stores.name AS store', 'branches.name AS branch', 'invoice')
            ->where('purchase_products.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->join('stores', 'stores.id', 'source_store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->whereBetween('purchase_date', [$from_date, $to_date]);
        $purchases = $query->get();
        return view('pages.reports.stock_control.print_stock_in', compact('purchases', 'from_date', 'to_date'));
    }

    public function generateBankLedger()
    {
        return view('pages.reports.bank_and_expenses.bank_ledger', [
            'bank_accounts' => BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('account_name')->get()
        ]);
    }

    public function loadBankLedger(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $bank_account = BankAccount::find($request->bank_account_id);
        $query = BankTransaction::where(['bank_account_id' => $request->bank_account_id, 'status' => 1])->whereBetween('trans_date', [$from_date, $to_date]);
        $sum_cr_b_d = BankTransaction::where(['bank_account_id' => $request->bank_account_id, 'status' => 1])->where('trans_date', '<', $from_date)->sum('cr');
        $sum_dr_b_d = BankTransaction::where(['bank_account_id' => $request->bank_account_id, 'status' => 1])->where('trans_date', '<', $from_date)->sum('dr');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d; //balance before date
        $ledgers = $query->orderBy('trans_date')->get();

        return view('pages.reports.bank_and_expenses.load_bank_ledger', compact('ledgers', 'bank_account', 'from_date', 'to_date', 'balance_b_d', 'sum_cr_b_d', 'sum_dr_b_d'));
    }

    public function printBankLedger($from_date, $to_date, $bank_account_id)
    {
        $bank_account = BankAccount::find($bank_account_id);
        $query = BankTransaction::where(['bank_account_id' => $bank_account_id, 'status' => 1])->whereBetween('trans_date', [$from_date, $to_date]);
        $sum_cr_b_d = BankTransaction::where(['bank_account_id' => $bank_account_id, 'status' => 1])->where('trans_date', '<', $from_date)->sum('cr');
        $sum_dr_b_d = BankTransaction::where(['bank_account_id' => $bank_account_id, 'status' => 1])->where('trans_date', '<', $from_date)->sum('dr');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d; //balance before date
        $ledgers = $query->orderBy('trans_date')->get();
        return view('pages.reports.bank_and_expenses.print_ledger', compact('ledgers', 'bank_account', 'from_date', 'to_date', 'balance_b_d', 'sum_dr_b_d', 'sum_cr_b_d'));
    }

    public function generateBankDeposit()
    {
        return view('pages.reports.bank_and_expenses.bank_deposit');
    }

    public function loadBankDeposit(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $query = CashMovement::select('cash_movements.*')
            ->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('type', 'Deposit')
            ->whereBetween('date_deposit', [$from_date, $to_date]);
        $deposits = $query->orderBy('date_deposit', 'DESC')->get();
        return view('pages.reports.bank_and_expenses.load_bank_deposit', ['deposits' => $deposits, 'from_date' => $from_date, 'to_date' => $to_date]);

    }

    public function printBankDeposit($from_date, $to_date)
    {
        $query = CashMovement::select('cash_movements.*')
            ->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
            ->where('type', 'Deposit')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date_deposit', [$from_date, $to_date]);
        $deposits = $query->orderBy('date_deposit', 'DESC')->get();
        return view('pages.reports.bank_and_expenses.print_bank_deposit', ['deposits' => $deposits, 'from_date' => $from_date, 'to_date' => $to_date]);
    }

    public function generateBankWithdraw()
    {
        return view('pages.reports.bank_and_expenses.bank_withdraw');
    }

    public function loadBankWithdraw(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $query = CashMovement::select('cash_movements.*')
            ->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
            ->where('type', 'Withdraw')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date_withdraw', [$from_date, $to_date]);
        $withdraws = $query->orderBy('date_withdraw', 'DESC')->get();
        return view('pages.reports.bank_and_expenses.load_bank_withdraw', ['withdraws' => $withdraws, 'from_date' => $from_date, 'to_date' => $to_date]);

    }

    public function printBankWithdraw($from_date, $to_date)
    {
        $query = CashMovement::select('cash_movements.*')
            ->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
            ->where('type', 'Withdraw')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date_withdraw', [$from_date, $to_date]);
        $withdraws = $query->orderBy('date_withdraw', 'DESC')->get();
        return view('pages.reports.bank_and_expenses.print_bank_withdraw', ['withdraws' => $withdraws, 'from_date' => $from_date, 'to_date' => $to_date]);
    }

    public function generateCashTransfer()
    {
        return view('pages.reports.bank_and_expenses.cash_transfer', ['users' => User::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get()]);
    }

    public function loadCashTransfer(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $user_id = $request->user_id;
        if ($user_id == 'all')
            $user_id = '%';
        $query = CashMovement::select('cash_movements.*')
            ->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
            ->where('type', 'Both')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('withdraw_by', 'LIKE', $user_id)
            ->whereBetween('date_withdraw', [$from_date, $to_date]);
        $withdraws = $query->orderBy('date_withdraw', 'DESC')->get();
        $user_id = 'all';
        return view('pages.reports.bank_and_expenses.load_cash_transfer', ['withdraws' => $withdraws, 'from_date' => $from_date, 'to_date' => $to_date, 'user_id' => $user_id]);

    }

    public function printCashTransfer($from_date, $to_date, $user_id)
    {
        if ($user_id == 'all')
            $user_id = '%';
        $query = CashMovement::select('cash_movements.*')
            ->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
            ->where('type', 'Both')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('withdraw_by', 'LIKE', $user_id)
            ->whereBetween('date_withdraw', [$from_date, $to_date]);
        $withdraws = $query->orderBy('date_withdraw', 'DESC')->get();
        $user_id = 'all';
        return view('pages.reports.bank_and_expenses.print_cash_transfer', ['withdraws' => $withdraws, 'from_date' => $from_date, 'to_date' => $to_date, 'user_id' => $user_id]);
    }

    public function generateBankBalance()
    {
        $records = BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('account_name')->get();
        return view('pages.reports.bank_and_expenses.generate_bank_balance', compact('records'));
    }

    public function printBankBalance()
    {
        $records = BankAccount::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('account_name')->get();
        return view('pages.reports.bank_and_expenses.print_bank_balance', compact('records'));
    }

    public function generateChequeCollected()
    {
        return view('pages.reports.bank_and_expenses.cheque_collected');
    }

    public function loadChequeCollected(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $query = CustomerLedger::where('payment_mode', '=', 'Cheque')
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date', [$from_date, $to_date]);
        $ledgers = $query->orderBy('date', 'ASC')->get();
        $balance_b_d = CustomerLedger::where('payment_mode', '=', 'Cheque')
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('date', '<', $from_date)->sum('dr');

        return view('pages.reports.bank_and_expenses.load_cheque_collected', ['ledgers' => $ledgers, 'from_date' => $from_date, 'to_date' => $to_date, 'balance_b_d' => $balance_b_d]);

    }

    public function printChequeCollected($from_date, $to_date)
    {
        $query = CustomerLedger::where('payment_mode', '=', 'Cheque')
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date', [$from_date, $to_date]);
        $ledgers = $query->orderBy('date', 'ASC')->get();
        $balance_b_d = CustomerLedger::where('payment_mode', '=', 'Cheque')
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('date', '<', $from_date)->sum('dr');
        return view('pages.reports.bank_and_expenses.print_cheque_collected', ['ledgers' => $ledgers, 'from_date' => $from_date, 'to_date' => $to_date, 'balance_b_d' => $balance_b_d]);
    }

    public function generateConsolidatedExpense()
    {
        return view('pages.reports.bank_and_expenses.consolidated_expense', ['items' => ExpenseItem::orderBy('name')->get()]);
    }

    public function loadConsolidatedExpense(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $item_id = $request->item_id;
        if ($item_id == "all")
            $item_id = "%";
        $query = Expense::select(DB::raw("sum(amount) as total"), 'name')
            ->join('expense_items', 'expense_items.id', 'expenses.expense_item_id')
            ->join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->where('expense_item_id', 'LIKE', $item_id)
            ->groupBy('expense_item_id')
            ->whereBetween('date', [$from_date, $to_date]);
        $expenses = $query->orderBy('date', 'ASC')->get();
        $item_id = $item_id == "%" ? "all" : $item_id;
        return view('pages.reports.bank_and_expenses.load_consolidated_expense', ['expenses' => $expenses, 'from_date' => $from_date, 'to_date' => $to_date, 'item_id' => $item_id]);

    }

    public function printConsolidatedExpense($from_date, $to_date, $item_id)
    {
        if ($item_id == "all")
            $item_id = "%";
        $query = Expense::select(DB::raw("sum(amount) as total"), 'name')
            ->join('expense_items', 'expense_items.id', 'expenses.expense_item_id')
            ->join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->where('expense_item_id', 'LIKE', $item_id)
            ->groupBy('expense_item_id')
            ->whereBetween('date', [$from_date, $to_date]);
        $expenses = $query->orderBy('date', 'ASC')->get();

        return view('pages.reports.bank_and_expenses.print_consolidated_expense', ['expenses' => $expenses, 'from_date' => $from_date, 'to_date' => $to_date, 'item_id' => $item_id]);
    }


    public function generateExpenseItem()
    {
        return view('pages.reports.bank_and_expenses.expense_item', ['items' => ExpenseItem::orderBy('name')->get()]);
    }

    public function loadExpenseItem(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $item_id = $request->item_id;
        if ($item_id == "all")
            $item_id = "%";
        $query = Expense::where('expense_item_id', 'LIKE', $item_id)
            ->join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date', [$from_date, $to_date]);
        $expenses = $query->orderBy('date', 'ASC')->get();
        $item_id = $item_id == "%" ? "all" : $item_id;
        return view('pages.reports.bank_and_expenses.load_expense_item', ['expenses' => $expenses, 'from_date' => $from_date, 'to_date' => $to_date, 'item_id' => $item_id]);

    }

    public function printExpenseItem($from_date, $to_date, $item_id)
    {
        if ($item_id == "all")
            $item_id = "%";
        $query = Expense::where('expense_item_id', 'LIKE', $item_id)
            ->join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date', [$from_date, $to_date]);
        $expenses = $query->orderBy('date', 'ASC')->get();

        return view('pages.reports.bank_and_expenses.print_expense_item', ['expenses' => $expenses, 'from_date' => $from_date, 'to_date' => $to_date, 'item_id' => $item_id]);
    }

    //Stock and Control Reports
    public function generateCurrentStock()
    {
        return view('pages.reports.stock_control.stock', [
            'categories' => Category::orderBy('name')->get(),
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get()
        ]);
    }

    public function loadCurrentStock(Request $request)
    {
        $categor_id = $request->category_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        if ($categor_id == "all")
            $categor_id = "%";
        if ($product_id == "all")
            $product_id = "%";
        if ($store_id == "all")
            $store_id = "%";
        $stores = DB::table('store_products')
            ->select('products.name', 'stores.name as store', 'store_products.qty_available', 'selling_price', 'cost_price', 'store_products.id')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $categor_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('products.name')
            ->get();
        if ($categor_id == "%")
            $categor_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        return view('pages.reports.stock_control.load_stock', ['stores' => $stores, 'product_id' => $product_id, 'category_id' => $categor_id, 'store_id' => $store_id]);
    }

    public function printCurrentStock($store_id, $categor_id, $product_id)
    {
        if ($categor_id == "all")
            $categor_id = "%";
        if ($product_id == "all")
            $product_id = "%";
        if ($store_id == "all")
            $store_id = "%";
        $stores = DB::table('store_products')
            ->select('products.name', 'stores.name as store', 'store_products.qty_available', 'selling_price', 'cost_price', 'store_products.id')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $categor_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('products.name')
            ->get();
        return view('pages.reports.stock_control.print_current_stock', ['stores' => $stores]);
    }

    public function dailyReport()
    {
        return view('pages.reports.bank_and_expenses.daily_cash_report');
    }

    public function loadDailyReport(Request $request)
    {
        $startDate = $request->from_date;
        $endDate = $request->to_date;

        $startDate = new Carbon($startDate);
        $endDate = new Carbon($endDate);
        $record = "";
        while ($startDate->lte($endDate)) {
            $date = $startDate->toDateString();
            $cash_sales = Order::join('branches', 'branches.id', 'orders.branch_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('order_date', $date)->where('payment_mode', 'Cash')->where('orders.status', 1)->sum('total');
            $discount = Order::join('branches', 'branches.id', 'orders.branch_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('order_date', $date)->where('payment_mode', 'Cash')->where('orders.status', 1)->sum('discount');
            $debtor_payment = CustomerLedger::join('customers', 'customers.id', 'customer_ledgers.customer_id')->where(['date' => $date, 'payment_mode' => 'Cash', 'order_id' => 0])->where('branch_id', 'LIKE', User::userBranchAction())->sum('dr');
            $loan_paid = LoanPayment::join('loans', 'loans.id', 'loan_payments.loan_id')->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')->where('branch_id', 'LIKE', User::userBranchAction())->where(DB::raw('DATE(loan_payments.updated_at)'), $date)->where('loan_payments.payment_mode', 'Cash')->sum('loan_payments.amount');
            $expense = Expense::join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('date', $date)->where('payment_mode', 'Cash')->sum('amount');
            //This is stricktly the amount deposited into Cash Account (Money comes in to Cash Account but not frm any bank account)
            $from_bank_deposited = DB::table('cash_movements')->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->where('date_deposit', $date)->where(['account_type' => 'Cash', 'type' => 'Deposit'])->sum('amount');
            //THis is stricly the amount comes into Cash Account from other accounts
            $from_bank_withdraw_deposited = DB::table('cash_movements')->join('bank_accounts', 'bank_accounts.id', 'cash_movements.destination_account_id')
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->where('date_withdraw', $date)->where(['account_type' => 'Cash', 'type' => 'Both'])->sum('amount');
            $from_bank = $from_bank_deposited + $from_bank_withdraw_deposited;
            //get amount tansferred to the other bank
            $to_bank_both = DB::table('cash_movements')->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->where('date_withdraw', $date)->where(['account_type' => 'Cash'])->where('type', 'Both')->sum('amount');
            //get amount withdrawn from Cash account without specifying the destination bank
            $to_bank_with = DB::table('cash_movements')->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->where('date_withdraw', $date)->where(['account_type' => 'Cash'])->where('type', 'Withdraw')->sum('amount');
            $to_bank = $to_bank_both + $to_bank_with; // get the summation of both transaction, if any
            $cash_purchase = SupplierLedger::where('date', $date)->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('payment_mode', 'Cash')->where('purchase_id', '<>', 0)->sum('cr');
            $loan_collected = Loan::where('date', $date)->join('bank_accounts', 'bank_accounts.id', 'loans.bank_account_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('payment_mode', 'Cash')->sum('amount');
            $payment_to_supplier = SupplierLedger::join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('date', $date)->where('payment_mode', 'Cash')->where('purchase_id', 0)->sum('dr');
            $startDate->addDay();
            $discount = 0;
            //The zero in the formular represents the discount which is already incoporated in the total frr each sale
            $balance = $cash_sales - $discount + $debtor_payment - $expense + $loan_paid + $from_bank - $to_bank - $cash_purchase - $loan_collected - $payment_to_supplier;
            $record .= "<tr><td>" . $date . "</td><td>&#8358;" .
                number_format($cash_sales, 2) . "</td><td>&#8358;" .
                number_format($discount, 2) . "</td><td> &#8358;" .
                number_format($debtor_payment, 2) . "</td><td> &#8358;" .
                number_format($loan_paid, 2) . "</td><td>&#8358;" .
                number_format($expense, 2) . "</td><td> &#8358;" .
                number_format($from_bank, 2) . "</td><td> &#8358;" .
                number_format($to_bank, 2) . "</td><td> &#8358;" .
                number_format($cash_purchase, 2) . "</td><td> &#8358;" .
                number_format($loan_collected, 2) . "</td><td> &#8358;" .
                number_format($payment_to_supplier, 2) . "</td><td>";
            if ($balance < 0)
                $record .= "&#8358;(" . number_format(abs($balance), 2) . ")";
            else
                $record .= "&#8358;" . number_format($balance, 2);

            $record . "</td></tr>";
        }

        return view('pages.reports.bank_and_expenses.load_daily_cash_report', [
            'result' => $record,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ]);
    }

    public function printDailyReport($from_date, $to_date)
    {
        $startDate = $from_date;
        $endDate = $to_date;

        $startDate = new Carbon($startDate);
        $endDate = new Carbon($endDate);
        $record = "";
        while ($startDate->lte($endDate)) {
            $date = $startDate->toDateString();
            $cash_sales = Order::join('branches', 'branches.id', 'orders.branch_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('order_date', $date)->where('payment_mode', 'Cash')->where('orders.status', 1)->sum('total');
            $discount = Order::join('branches', 'branches.id', 'orders.branch_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('order_date', $date)->where('payment_mode', 'Cash')->where('orders.status', 1)->sum('discount');
            $debtor_payment = CustomerLedger::join('customers', 'customers.id', 'customer_ledgers.customer_id')->where(['date' => $date, 'payment_mode' => 'Cash', 'order_id' => 0])->where('branch_id', 'LIKE', User::userBranchAction())->sum('dr');
            $loan_paid = LoanPayment::join('loans', 'loans.id', 'loan_payments.loan_id')->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')->where('branch_id', 'LIKE', User::userBranchAction())->where(DB::raw('DATE(loan_payments.updated_at)'), $date)->where('loan_payments.payment_mode', 'Cash')->sum('loan_payments.amount');
            $expense = Expense::join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('date', $date)->where('payment_mode', 'Cash')->sum('amount');
            //This is stricktly the amount deposited into Cash Account (Money comes in to Cash Account but not frm any bank account)
            $from_bank_deposited = DB::table('cash_movements')->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->where('date_deposit', $date)->where(['account_type' => 'Cash', 'type' => 'Deposit'])->sum('amount');
            //THis is stricly the amount comes into Cash Account from other accounts
            $from_bank_withdraw_deposited = DB::table('cash_movements')->join('bank_accounts', 'bank_accounts.id', 'cash_movements.destination_account_id')
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->where('date_withdraw', $date)->where(['account_type' => 'Cash', 'type' => 'Both'])->sum('amount');
            $from_bank = $from_bank_deposited + $from_bank_withdraw_deposited;
            //get amount tansferred to the other bank
            $to_bank_both = DB::table('cash_movements')->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->where('date_withdraw', $date)->where(['account_type' => 'Cash'])->where('type', 'Both')->sum('amount');
            //get amount withdrawn from Cash account without specifying the destination bank
            $to_bank_with = DB::table('cash_movements')->join('bank_accounts', 'bank_accounts.id', 'cash_movements.source_account_id')
                ->where('branch_id', 'LIKE', User::userBranchAction())
                ->where('date_withdraw', $date)->where(['account_type' => 'Cash'])->where('type', 'Withdraw')->sum('amount');
            $to_bank = $to_bank_both + $to_bank_with; // get the summation of both transaction, if any
            $cash_purchase = SupplierLedger::where('date', $date)->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('payment_mode', 'Cash')->where('purchase_id', '<>', 0)->sum('cr');
            $loan_collected = Loan::where('date', $date)->join('bank_accounts', 'bank_accounts.id', 'loans.bank_account_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('payment_mode', 'Cash')->sum('amount');
            $payment_to_supplier = SupplierLedger::join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')->where('branch_id', 'LIKE', User::userBranchAction())->where('date', $date)->where('payment_mode', 'Cash')->where('purchase_id', 0)->sum('dr');
            $startDate->addDay();
            $discount = 0;
            //The zero in the formular represents the discount which is already incoporated in the total frr each sale
            $balance = $cash_sales - $discount + $debtor_payment - $expense + $loan_paid + $from_bank - $to_bank - $cash_purchase - $loan_collected - $payment_to_supplier;
            $record .= "<tr><td>" . $date . "</td><td>&#8358;" .
                number_format($cash_sales, 2) . "</td><td>&#8358;" .
                number_format($discount, 2) . "</td><td> &#8358;" .
                number_format($debtor_payment, 2) . "</td><td> &#8358;" .
                number_format($loan_paid, 2) . "</td><td>&#8358;" .
                number_format($expense, 2) . "</td><td> &#8358;" .
                number_format($from_bank, 2) . "</td><td> &#8358;" .
                number_format($to_bank, 2) . "</td><td> &#8358;" .
                number_format($cash_purchase, 2) . "</td><td> &#8358;" .
                number_format($loan_collected, 2) . "</td><td> &#8358;" .
                number_format($payment_to_supplier, 2) . "</td><td>";
            if ($balance < 0)
                $record .= "&#8358;(" . number_format(abs($balance), 2) . ")";
            else
                $record .= "&#8358;" . number_format($balance, 2);

            $record . "</td></tr>";
        }

        return view('pages.reports.bank_and_expenses.print_daily_cash_report', [
            'result' => $record,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    public function storeLedger()
    {
        return view('pages.reports.stock_control.store_ledger_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadStoreLedger(Request $request)
    {
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }
        $stores = DB::table('store_products')
            ->select('products.name', 'stores.name as store', 'store_products.qty_available', 'selling_price', 'cost_price', 'store_products.id', 'categories.name AS category')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        return view('pages.reports.stock_control.load_store_ledger', ['stores' => $stores, 'product_id' => $product_id, 'category_id' => $category_id, 'store_id' => $store_id]);
    }

    public function printstoreLedger($store_id, $category_id, $product_id)
    {

        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }
        $stores = DB::table('store_products')
            ->select('products.name', 'stores.name as store', 'store_products.qty_available', 'selling_price', 'cost_price', 'store_products.id', 'categories.name AS category')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.branch_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        return view('pages.reports.stock_control.print_store_ledger', ['stores' => $stores, 'product_id' => $product_id, 'category_id' => $category_id, 'store_id' => $store_id]);
    }

    public function stockAdjustment()
    {
        return view('pages.reports.stock_control.stock_adjustment_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadStockAdjustment(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }
        $stores = DB::table('stock_adjustments')
            ->select('products.name', 'stores.name as store', 'adjusted_qty', 'date', 'refno')
            ->join('products', 'products.id', '=', 'stock_adjustments.product_id')
            ->join('stores', 'stores.id', '=', 'stock_adjustments.store_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stock_adjustments.product_id', 'LIKE', $product_id)
            ->where('stock_adjustments.store_id', 'LIKE', $store_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date', [$from_date, $to_date])
            ->orderBy('date', 'DESC')
            ->orderBy('refno', 'ASC')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";

        return view('pages.reports.stock_control.load_stock_adjustment', compact('stores', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id'));
    }

    public function printStockAdjustment($from_date, $to_date, $store_id, $category_id, $product_id)
    {
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }

        $stores = DB::table('stock_adjustments')
            ->select('products.name', 'stores.name as store', 'adjusted_qty', 'date', 'refno')
            ->join('products', 'products.id', '=', 'stock_adjustments.product_id')
            ->join('stores', 'stores.id', '=', 'stock_adjustments.store_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stock_adjustments.product_id', 'LIKE', $product_id)
            ->where('stock_adjustments.store_id', 'LIKE', $store_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date', [$from_date, $to_date])
            ->orderBy('date', 'DESC')
            ->orderBy('refno', 'ASC')
            ->get();
        return view('pages.reports.stock_control.print_stock_adjutment', compact('stores', 'from_date', 'to_date'));
    }

    //Sales and Cash Analysis
    public function generalSaleReport()
    {
        return view('pages.reports.sales_and_cash_analysis.general_sale', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadGeneralSaleReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $customer_id = $request->customer_id;
        $payment_mode = $request->payment_mode;
        $credit_walkedin = $request->credit_walkedin;

        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        if ($payment_mode == 'all') {
            $payment_mode = '%';
        }
        if ($credit_walkedin == 'all') {
            $credit_walkedin = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'orders.invoice_no', 'products.name AS product', 'stores.name AS store', 'order_details.quantity', 'sold_price', 'cost_price', 'order_date')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('orders.payment_mode', 'LIKE', $payment_mode)
            ->where('customers.type', 'LIKE', $credit_walkedin)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where('order_details.status', 1)
            //->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where(DB::raw("DATE(order_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(order_date)"), '<=', $to_date)
            ->orderBy('order_date')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($customer_id == "%")
            $customer_id = "all";
        if ($payment_mode == "%")
            $payment_mode = "all";
        if ($credit_walkedin == "%")
            $credit_walkedin = "all";


        return view('pages.reports.sales_and_cash_analysis.load_general_sale_report', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'customer_id', 'payment_mode', 'credit_walkedin'));
    }

    public function printGeneralSaleReport($from_date, $to_date, $store_id, $category_id, $product_id, $customer_id, $payment_mode, $credit_walkedin)
    {
        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        if ($payment_mode == 'all') {
            $payment_mode = '%';
        }
        if ($credit_walkedin == 'all') {
            $credit_walkedin = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'orders.invoice_no', 'products.name AS product', 'stores.name AS store', 'order_details.quantity', 'sold_price', 'cost_price', 'order_date')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('orders.payment_mode', 'LIKE', $payment_mode)
            ->where('customers.type', 'LIKE', $credit_walkedin)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where('order_details.status', 1)
            //->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where(DB::raw("DATE(order_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(order_date)"), '<=', $to_date)
            ->orderBy('order_date')
            ->get();
        return view('pages.reports.sales_and_cash_analysis.print_general_sale_report', compact('sales', 'from_date', 'to_date', 'payment_mode'));
    }


    public function staffSaleReport()
    {
        return view('pages.reports.sales_and_cash_analysis.staff_sale_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function loadStaffSaleReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $payment_mode = $request->payment_mode;
        $staff_id = $request->staff_id;

        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($payment_mode == 'all') {
            $payment_mode = '%';
        }
        if ($staff_id == 'all') {
            $staff_id = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'orders.invoice_no', 'products.name AS product', 'stores.name AS store', 'order_details.quantity', 'sold_price', 'cost_price', 'users.name AS user', 'order_date')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.sold_by', 'LIKE', $staff_id)
            ->where('orders.payment_mode', 'LIKE', $payment_mode)
            ->where('order_details.status', 1)
            ->where('orders.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->join('users', 'users.id', 'orders.sold_by')
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy('order_date')
            ->get();
        $total_cash = Order::where(['sold_by' => $staff_id, 'payment_mode' => 'Cash', 'status' => 1])->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])->sum('total');
        $total_debtors = CustomerLedger::where(['user_id' => $staff_id])->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])->sum('dr');
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($payment_mode == "%")
            $payment_mode = "all";
        if ($staff_id == "%")
            $staff_id = "all";
        $user = User::find($staff_id);
        return view('pages.reports.sales_and_cash_analysis.load_staff_sale_report', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'staff_id', 'payment_mode', 'total_cash', 'total_debtors', 'user'));
    }

    public function printStaffSaleReport($from_date, $to_date, $store_id, $category_id, $product_id, $staff_id, $payment_mode)
    {
        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($staff_id == 'all') {
            $staff_id = '%';
        }
        if ($payment_mode == 'all') {
            $payment_mode = '%';
        }

        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'orders.invoice_no', 'products.name AS product', 'stores.name AS store', 'order_details.quantity', 'sold_price', 'cost_price', 'users.name AS user', 'order_date')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.sold_by', 'LIKE', $staff_id)
            ->where('orders.payment_mode', 'LIKE', $payment_mode)
            ->where('order_details.status', 1)
            ->where('orders.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->join('users', 'users.id', 'orders.sold_by')
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy('order_date')
            ->get();
        $total_cash = Order::where(['sold_by' => $staff_id, 'payment_mode' => 'Cash', 'status' => 1])->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])->sum('total');
        $total_debtors = CustomerLedger::where(['user_id' => $staff_id])->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])->sum('dr');
        $user = User::find($staff_id);
        return view('pages.reports.sales_and_cash_analysis.print_staff_sale_report', compact('sales', 'from_date', 'to_date', 'payment_mode', 'total_cash', 'total_debtors', 'user'));
    }

    public function previousStockBalance()
    {
        return view('pages.reports.stock_control.previous_stock_balance_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadPreviousStockBalance(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }
        $stores = DB::table('store_product_balances')
            ->select('products.name', 'stores.name as store', 'qty', 'date', 'categories.name AS group')
            ->join('store_products', 'store_products.id', '=', 'store_product_balances.store_product_id')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date', [$from_date, $to_date])
            ->orderBy('date', 'DESC')
            ->orderBy('categories.name')
            ->orderBy('products.name')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";

        return view('pages.reports.stock_control.load_previous_stock_balance', compact('stores', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id'));
    }

    public function printPreviousStockBalance($from_date, $to_date, $store_id, $category_id, $product_id)
    {
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }

        $stores = DB::table('store_product_balances')
            ->select('products.name', 'stores.name as store', 'qty', 'date', 'categories.name AS group')
            ->join('store_products', 'store_products.id', '=', 'store_product_balances.store_product_id')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween('date', [$from_date, $to_date])
            ->orderBy('date', 'DESC')
            ->orderBy('categories.name')
            ->orderBy('products.name')
            ->get();
        return view('pages.reports.stock_control.print_previous_stock_balance', compact('stores', 'from_date', 'to_date'));
    }

    public function stockLedger()
    {
        return view('pages.reports.stock_control.stock_ledger_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadStockLedger(Request $request)
    {
        $startDate = $request->from_date;
        $endDate = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;

        $record = "";
        $count = 0;
        $qty_in_stock = 0;
        $cards = DB::table('stock_cards')->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate])->where(['store_id' => $store_id, 'product_id' => $product_id, 'status' => 1])->orderBy('date')->orderBy('priority')->get();
        foreach ($cards as $card) {
            $dd = new Carbon($card->date);
            $date = $dd->toDateString();
            if ($card->type == "Sale") {
                $data = Order::select('invoice_no AS refno', 'customers.name', 'quantity')
                    ->join('customers', 'customers.id', 'orders.customer_id')
                    ->join('order_details', 'order_details.order_id', 'orders.id')
                    ->join('store_products', 'store_products.id', 'order_details.store_product_id')
                    ->where(DB::raw('DATE(order_date)'), $date)
                    ->where('invoice_no', $card->refno)
                    ->where(['store_products.store_id' => $store_id, 'store_products.product_id' => $product_id, 'order_details.status' => 1])
                    ->where('orders.branch_id', 'LIKE', User::userBranchAction())
                    ->first();
                $name = optional($data)->name;
            }
            if ($card->type == "Purchase") {
                $data = Purchase::select('invoice AS refno', 'suppliers.name', 'qty_supplied')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
                    ->where(DB::raw('DATE(purchase_date)'), $date)
                    ->where('invoice', $card->refno)
                    ->where(['purchase_products.product_id' => $product_id, 'purchase_products.status' => 1])
                    ->where('suppliers.branch_id', 'LIKE', User::userBranchAction())
                    ->first();
                $name = optional($data)->name;
            }
            if ($card->type == "Transfer") {
                $data = TransferProduct::select('refno AS refno', 'stock_in_out')
                    ->join('stores', 'stores.id', 'transfer_products.source_store_id')
                    ->where(DB::raw('DATE(transfer_date)'), $date)
                    ->where('refno', $card->refno)
                    ->where('stores.branch_id', 'LIKE', User::userBranchAction())
                    ->where(['source_store_id' => $store_id, 'product_id' => $product_id, 'transfer_products.status' => 'Completed'])
                    ->first();
                $name = optional($data)->stock_in_out == "in" ? "Recieved Item" : "Transfered Item";
            }
            if ($card->type == "Adjustment") {
                $data = StockAdjustment::select('refno AS refno', 'adjusted_qty')
                    ->join('stores', 'stores.id', 'stock_adjustments.store_id')
                    ->where(DB::raw('DATE(date)'), $date)
                    ->where('refno', $card->refno)
                    ->where(['store_id' => $store_id, 'product_id' => $product_id])
                    ->where('branch_id', 'LIKE', User::userBranchAction())
                    ->first();
                $name = "Adjusted Item";
            }
            if ($card->type == "Opening Balance") {
                $name = "Opening Balance";
            }

            $qty_sold = StockCard::where(['status' => 1, 'type' => 'Sale'])->where('id', '=', $card->id)->first();
            $qty_puchase = StockCard::where(['status' => 1, 'type' => 'Purchase'])->where('id', '=', $card->id)->first();
            $qty_adjust = StockCard::where(['status' => 1, 'type' => 'Adjustment'])->where('id', '=', $card->id)->first();
            $qty_tran_recv = StockCard::where(['status' => 1, 'type' => 'Transfer'])->where('id', '=', $card->id)->first();
            $record .= "<tr><td>" . Carbon::parse($card->date)->toFormattedDateString() . "</td><td>" .
                optional($card)->refno . "</td><td>" .
                number_format($qty_in_stock, 0) . "</td><td>" .
                number_format(optional($qty_sold)->dr, 0) . "</td><td>" .
                number_format(optional($qty_puchase)->cr, 0) . "</td><td>" .
                number_format((optional($qty_adjust)->cr - optional($qty_adjust)->dr), 0) . "</td><td>" .
                number_format(optional($qty_tran_recv)->dr, 0) . "</td><td>" .
                number_format(optional($qty_tran_recv)->cr, 0) . "</td><td>" .
                $name . "</td><td>";

            $balance = $qty_in_stock + optional($qty_puchase)->cr - optional($qty_sold)->dr + (optional($qty_adjust)->cr - optional($qty_adjust)->dr) + optional($qty_tran_recv)->cr - optional($qty_tran_recv)->dr;
            /*if ($count == 0) {
                $in_stock = StockCard::where(['store_id' => $store_id, 'product_id' => $product_id, 'status' => 1])->where(DB::raw('DATE(date)'), '<', $card->date)->sum('cr');
                $out_stock = StockCard::where(['store_id' => $store_id, 'product_id' => $product_id, 'status' => 1])->where(DB::raw('DATE(date)'), '<', $card->date)->sum('dr');
                $qty_in_stock = $in_stock - $out_stock;
            }*/
            $qty_in_stock = $balance;
            if ($balance < 0)
                $record .= "(" . number_format(abs($balance), 0) . ")";
            else
                $record .= "" . number_format($balance, 0);

            $record . "</td></tr>";
            $count++;
        }
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product = Product::find($product_id);
        $product = StoreProduct::select('store_products.qty_available', 'products.name AS item', 'stores.name AS store')
            ->where(['store_id' => $store_id, 'product_id' => $product_id])
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->first();
        return view('pages.reports.stock_control.load_stock_ledger_report', [
            'result' => $record,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'store_id' => $store_id,
            'category_id' => $category_id,
            'product_id' => $product_id,
            'product' => $product
            ,
            'qty_in_stock' => $qty_in_stock
        ]);
    }

    public function printStockLedger($from_date, $to_date, $store_id, $category_id, $product_id)
    {
        $startDate = new Carbon($from_date);
        $endDate = new Carbon($to_date);
        $record = "";
        $count = 0;
        $qty_in_stock = 0;
        $cards = DB::table('stock_cards')->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate])->where(['store_id' => $store_id, 'product_id' => $product_id, 'status' => 1])->orderBy('date')->orderBy('priority')->get();
        foreach ($cards as $card) {
            $dd = new Carbon($card->date);
            $date = $dd->toDateString();
            if ($card->type == "Sale") {
                $data = Order::select('invoice_no AS refno', 'customers.name', 'quantity')
                    ->join('customers', 'customers.id', 'orders.customer_id')
                    ->join('order_details', 'order_details.order_id', 'orders.id')
                    ->join('store_products', 'store_products.id', 'order_details.store_product_id')
                    ->where(DB::raw('DATE(order_date)'), $date)
                    ->where('invoice_no', $card->refno)
                    ->where(['store_products.store_id' => $store_id, 'store_products.product_id' => $product_id, 'order_details.status' => 1])
                    ->where('orders.branch_id', 'LIKE', User::userBranchAction())
                    ->first();
                $name = optional($data)->name;
            }
            if ($card->type == "Purchase") {
                $data = Purchase::select('invoice AS refno', 'suppliers.name', 'qty_supplied')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
                    ->where(DB::raw('DATE(purchase_date)'), $date)
                    ->where('invoice', $card->refno)
                    ->where(['purchase_products.product_id' => $product_id, 'purchase_products.status' => 1])
                    ->where('suppliers.branch_id', 'LIKE', User::userBranchAction())
                    ->first();
                $name = optional($data)->name;
            }
            if ($card->type == "Transfer") {
                $data = TransferProduct::select('refno AS refno', 'stock_in_out')
                    ->join('stores', 'stores.id', 'transfer_products.source_store_id')
                    ->where(DB::raw('DATE(transfer_date)'), $date)
                    ->where('refno', $card->refno)
                    ->where('stores.branch_id', 'LIKE', User::userBranchAction())
                    ->where(['source_store_id' => $store_id, 'product_id' => $product_id, 'transfer_products.status' => 'Completed'])
                    ->first();
                $name = optional($data)->stock_in_out == "in" ? "Recieved Item" : "Transfered Item";
            }
            if ($card->type == "Adjustment") {
                $data = StockAdjustment::select('refno AS refno', 'adjusted_qty')
                    ->join('stores', 'stores.id', 'stock_adjustments.store_id')
                    ->where(DB::raw('DATE(date)'), $date)
                    ->where('refno', $card->refno)
                    ->where(['store_id' => $store_id, 'product_id' => $product_id])
                    ->where('branch_id', 'LIKE', User::userBranchAction())
                    ->first();
                $name = "Adjusted Item";
            }
            if ($card->type == "Opening Balance") {
                $name = "Opening Balance";
            }
            /*if ($count == 0) {
                $in_stock = StockCard::where(['store_id' => $store_id, 'product_id' => $product_id, 'status' => 1])->where(DB::raw('DATE(date)'), '<', $card->date)->sum('cr');
                $out_stock = StockCard::where(['store_id' => $store_id, 'product_id' => $product_id, 'status' => 1])->where(DB::raw('DATE(date)'), '<', $card->date)->sum('dr');
                $qty_in_stock = $in_stock - $out_stock;
            }*/

            $qty_sold = StockCard::where(['status' => 1, 'type' => 'Sale'])->where('id', '=', $card->id)->first();
            $qty_puchase = StockCard::where(['status' => 1, 'type' => 'Purchase'])->where('id', '=', $card->id)->first();
            $qty_adjust = StockCard::where(['status' => 1, 'type' => 'Adjustment'])->where('id', '=', $card->id)->first();
            $qty_tran_recv = StockCard::where(['status' => 1, 'type' => 'Transfer'])->where('id', '=', $card->id)->first();
            $record .= "<tr><td>" . Carbon::parse($card->date)->toFormattedDateString() . "</td><td>" .
                optional($card)->refno . "</td><td>" .
                number_format($qty_in_stock, 0) . "</td><td>" .
                number_format(optional($qty_sold)->dr, 0) . "</td><td>" .
                number_format(optional($qty_puchase)->cr, 0) . "</td><td>" .
                number_format((optional($qty_adjust)->cr - optional($qty_adjust)->dr), 0) . "</td><td>" .
                number_format(optional($qty_tran_recv)->dr, 0) . "</td><td>" .
                number_format(optional($qty_tran_recv)->cr, 0) . "</td><td>" .
                $name . "</td><td>";
            $balance = $qty_in_stock + optional($qty_puchase)->cr - optional($qty_sold)->dr + (optional($qty_adjust)->cr - optional($qty_adjust)->dr) + optional($qty_tran_recv)->cr - optional($qty_tran_recv)->dr;
            $qty_in_stock = $balance;
            if ($balance < 0)
                $record .= "(" . number_format(abs($balance), 0) . ")";
            else
                $record .= "" . number_format($balance, 0);

            $record . "</td></tr>";
            $count++;
        }

        $product = Product::find($product_id);
        $product = StoreProduct::select('store_products.qty_available', 'products.name AS item', 'stores.name AS store')
            ->where(['store_id' => $store_id, 'product_id' => $product_id])
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->first();
        return view('pages.reports.stock_control.print_stock_ledger_report', [
            'result' => $record,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'store_id' => $store_id,
            'category_id' => $category_id,
            'product_id' => $product_id,
            'product' => $product
            ,
            'qty_in_stock' => $qty_in_stock
        ]);
    }

    public function customerSaleReport()
    {
        return view('pages.reports.sales_and_cash_analysis.sale_report_with_common_name', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadCustomerSaleReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $customer = $request->customer;
        $payment_mode = $request->payment_mode;
        $credit_walkedin = $request->credit_walkedin;
        $matching = $request->matching;

        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }

        if ($payment_mode == 'all') {
            $payment_mode = '%';
        }
        if ($credit_walkedin == 'all') {
            $credit_walkedin = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', DB::raw("SUM(orders.total) AS total"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.payment_mode', 'LIKE', $payment_mode)
            ->where('customers.type', 'LIKE', $credit_walkedin)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->groupBy('orders.customer_id')
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date]);
        if ($matching == "similar")
            $sales = $sales->where('customers.name', 'LIKE', "%$customer%");
        if ($matching == "exact")
            $sales = $sales->where('customers.name', '=', $customer);
        $sales = $sales->orderBy('order_date')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($payment_mode == "%")
            $payment_mode = "all";
        if ($credit_walkedin == "%")
            $credit_walkedin = "all";

        return view('pages.reports.sales_and_cash_analysis.load_report_with_common_name', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'customer', 'payment_mode', 'credit_walkedin', 'matching'));
    }

    public function printCustomerSaleReport($from_date, $to_date, $store_id, $category_id, $product_id, $payment_mode, $customer, $credit_walkedin, $matching)
    {

        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }

        if ($payment_mode == 'all') {
            $payment_mode = '%';
        }
        if ($credit_walkedin == 'all') {
            $credit_walkedin = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', DB::raw("SUM(orders.total) AS total"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.payment_mode', 'LIKE', $payment_mode)
            ->where('customers.type', 'LIKE', $credit_walkedin)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->groupBy('orders.customer_id')
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date]);
        if ($matching == "similar")
            $sales = $sales->where('customers.name', 'LIKE', "%$customer%");
        if ($matching == "exact")
            $sales = $sales->where('customers.name', '=', $customer);
        $sales = $sales->orderBy('order_date')
            ->get();
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($payment_mode == "%")
            $payment_mode = "all";
        if ($credit_walkedin == "%")
            $credit_walkedin = "all";
        return view('pages.reports.sales_and_cash_analysis.print_report_with_common_name', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'customer', 'payment_mode', 'credit_walkedin'));
    }

    public function debtorBalanceReport()
    {
        return view('pages.reports.sales_and_cash_analysis.debtor_balance_report', [
            'customers' => Customer::orderBy('name')->where('type', 'Credit')->get(),
        ]);
    }

    public function loadDebtorBalanceReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all') {
            $customer_id = '%';
        }

        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'orders.total', 'pay', 'due', 'discount', 'invoice_no', 'order_date')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('orders.payment_mode', 'LIKE', 'Credit')
            ->where('customers.type', 'LIKE', 'Credit')
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy('order_date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.sales_and_cash_analysis.load_debtor_balance_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function printDebtorBalanceReport($from_date, $to_date, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'orders.total', 'pay', 'due', 'discount', 'invoice_no', 'order_date')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('orders.payment_mode', 'LIKE', 'Credit')
            ->where('customers.type', 'LIKE', 'Credit')
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy('order_date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.sales_and_cash_analysis.print_debtor_balance_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function mostSoldItemReport()
    {
        return view('pages.reports.sales_and_cash_analysis.most_sold_item_report');
    }

    public function loadMostSoldItemReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $number_limit = $request->number_limit;

        $sales = DB::table('orders')
            ->select('products.name AS item', DB::raw("SUM(order_details.quantity) AS quantity"), DB::raw("SUM(order_details.total) AS total"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy(DB::raw("SUM(order_details.total)"), 'DESC')
            ->groupBy('store_products.product_id')
            ->take($number_limit)
            ->get();
        return view('pages.reports.sales_and_cash_analysis.load_most_sold_item_report', compact('sales', 'from_date', 'to_date', 'number_limit'));
    }

    public function printMostSoldItemReport($from_date, $to_date, $number_limit)
    {
        $sales = DB::table('orders')
            ->select('products.name AS item', DB::raw("SUM(order_details.quantity) AS quantity"), DB::raw("SUM(order_details.total) AS total"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy(DB::raw("SUM(order_details.total)"), 'DESC')
            ->groupBy('store_products.product_id')
            ->take($number_limit)
            ->get();
        return view('pages.reports.sales_and_cash_analysis.print_most_sold_item_report', compact('sales', 'from_date', 'to_date', 'number_limit'));
    }

    public function totalItemSoldReport()
    {
        return view('pages.reports.sales_and_cash_analysis.item_sold_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadItemSoldReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $customer_id = $request->customer_id;
        $credit_walkedin = $request->credit_walkedin;

        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        if ($credit_walkedin == 'all') {
            $credit_walkedin = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'products.name AS product', 'stores.name AS store', DB::raw("SUM(order_details.quantity) AS quantity"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('customers.type', 'LIKE', $credit_walkedin)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->groupBy('order_details.quantity')
            ->groupBy('orders.customer_id')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($customer_id == "%")
            $customer_id = "all";
        if ($credit_walkedin == "%")
            $credit_walkedin = "all";

        return view('pages.reports.sales_and_cash_analysis.load_item_sold_report', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'customer_id', 'credit_walkedin'));
    }

    public function printItemSoldReport($from_date, $to_date, $store_id, $category_id, $product_id, $customer_id, $credit_walkedin)
    {
        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        if ($credit_walkedin == 'all') {
            $credit_walkedin = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'products.name AS product', 'stores.name AS store', DB::raw("SUM(order_details.quantity) AS quantity"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('customers.type', 'LIKE', $credit_walkedin)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->groupBy('order_details.quantity')
            ->groupBy('orders.customer_id')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        return view('pages.reports.sales_and_cash_analysis.print_item_sold_report', compact('sales', 'from_date', 'to_date', 'category_id', 'product_id'));
    }

    public function trackDiscount()
    {
        return view('pages.reports.sales_and_cash_analysis.track_discount_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadTrackDiscount(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $customer_id = $request->customer_id;
        $lower = $request->lower;
        $upper = $request->upper;
        $credit_walkedin = $request->credit_walkedin;

        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($customer_id == 'all') {
            $customer_id = '%';
        }

        if ($credit_walkedin == 'all') {
            $credit_walkedin = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'products.name AS item', 'stores.name AS store', 'order_details.quantity', 'order_date', 'order_details.selling_price', 'order_details.sold_price', 'users.name AS user', 'invoice_no', 'categories.name AS group')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('users', 'users.id', 'orders.sold_by')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('customers.type', 'LIKE', $credit_walkedin)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where(DB::raw('selling_price-sold_price'), '>=', $lower)
            ->where(DB::raw('selling_price-sold_price'), '<=', $upper)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->groupBy('order_details.quantity')
            ->groupBy('orders.customer_id')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($customer_id == "%")
            $customer_id = "all";
        if ($credit_walkedin == "%")
            $credit_walkedin = "all";

        return view('pages.reports.sales_and_cash_analysis.load_track_discount_report', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'customer_id', 'credit_walkedin', 'lower', 'upper'));
    }

    public function printTrackDiscount($from_date, $to_date, $store_id, $category_id, $product_id, $customer_id, $credit_walkedin, $lower, $upper)
    {
        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        if ($credit_walkedin == 'all') {
            $credit_walkedin = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'products.name AS item', 'stores.name AS store', 'order_details.quantity', 'order_date', 'order_details.selling_price', 'order_details.sold_price', 'users.name AS user', 'invoice_no', 'categories.name AS group')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('users', 'users.id', 'orders.sold_by')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('customers.type', 'LIKE', $credit_walkedin)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where(DB::raw('selling_price-sold_price'), '>=', $lower)
            ->where(DB::raw('selling_price-sold_price'), '<=', $upper)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->groupBy('order_details.quantity')
            ->groupBy('orders.customer_id')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        return view('pages.reports.sales_and_cash_analysis.print_track_discount_report', compact('sales', 'from_date', 'to_date', 'category_id', 'product_id', 'lower', 'upper'));
    }


    public function mostSoldItemQuantityReport()
    {
        return view('pages.reports.sales_and_cash_analysis.most_sold_item_by_quantity_report');
    }

    public function loadSoldItemQuantityReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $number_limit = $request->number_limit;

        $sales = DB::table('orders')
            ->select('products.name AS item', DB::raw("SUM(order_details.quantity) AS quantity"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('orders.payment_mode', 'LIKE', 'Credit')
            ->where('customers.type', 'LIKE', 'Credit')
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy(DB::raw("SUM(order_details.total)"), 'DESC')
            ->groupBy('store_products.product_id')
            ->get()->take($number_limit);
        return view('pages.reports.sales_and_cash_analysis.load_most_sold_item_quantity_report', compact('sales', 'from_date', 'to_date', 'number_limit'));
    }

    public function printSoldItemQuantityReport($from_date, $to_date, $number_limit)
    {
        $sales = DB::table('orders')
            ->select('products.name AS item', DB::raw("SUM(order_details.quantity) AS quantity"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('orders.payment_mode', 'LIKE', 'Credit')
            ->where('customers.type', 'LIKE', 'Credit')
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy(DB::raw("SUM(order_details.total)"), 'DESC')
            ->groupBy('store_products.product_id')
            ->take($number_limit)
            ->get();
        return view('pages.reports.sales_and_cash_analysis.print_most_sold_item_by_quantity_report', compact('sales', 'from_date', 'to_date', 'number_limit'));
    }

    public function customerDebtReport()
    {
        return view('pages.reports.customer_ledger_analysis.customer_total_debt_report', [
            'customers' => Customer::orderBy('name')->where('type', 'Credit')->get(),
        ]);
    }

    public function loadCustomerDebtReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all') {
            $customer_id = '%';
        }

        $sales = CustomerLedger::where('customer_id', 'LIKE', $customer_id)
            ->where('type', 'Credit')
            ->whereBetween('date', [$from_date, $to_date])
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->select('customers.name AS customer', DB::raw('SUM(cr) AS total'), DB::raw('SUM(dr) AS pay'), DB::raw('SUM(cr)-SUM(dr) AS due'))
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->orderBy('name')->groupBy('customer_id')->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.load_customer_total_debt_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function printCustomerDebtReport($from_date, $to_date, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = CustomerLedger::where('customer_id', 'LIKE', $customer_id)
            ->where('type', 'Credit')
            ->whereBetween('date', [$from_date, $to_date])
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->select('customers.name AS customer', DB::raw('SUM(cr) AS total'), DB::raw('SUM(dr) AS pay'), DB::raw('SUM(cr)-SUM(dr) AS due'))
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->orderBy('name')->groupBy('customer_id')->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.print_customer_total_debt_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function customerBalanceDetailReport()
    {
        return view('pages.reports.customer_ledger_analysis.customer_balance_details_report', [
            'customers' => Customer::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->where('type', 'Credit')->get(),
        ]);
    }

    public function loadCustomerBalanceDetailReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('customers')
            ->select('customers.name AS customer', 'customer_ledgers.dr', 'customer_ledgers.cr', 'receipt_no', 'systemid', 'description', 'date')
            ->join('customer_ledgers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('customer_ledgers.customer_id', 'LIKE', $customer_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(customer_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('customer_ledgers.date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.load_customer_balance_details_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function printCustomerBalanceDetailReport($from_date, $to_date, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('customers')
            ->select('customers.name AS customer', 'customer_ledgers.dr', 'customer_ledgers.cr', 'receipt_no', 'systemid', 'description', 'date')
            ->join('customer_ledgers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('customer_ledgers.customer_id', 'LIKE', $customer_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(customer_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('customer_ledgers.date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.print_customer_balance_details_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function ageingReport()
    {
        return view('pages.reports.customer_ledger_analysis.ageing_report', [
            'customers' => Customer::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->where('type', 'Credit')->get(),
        ]);
    }

    public function loadAgeingReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('customer_ledgers')
            ->select('customer_ledgers.dr', 'customer_ledgers.cr', 'receipt_no', 'systemid', 'description', 'date', 'name', 'phone', 'customer_ledgers.id')
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('customer_ledgers.customer_id', 'LIKE', $customer_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('customers.type', '=', "Credit");
        if ($from_date != null)
            $sales = $sales->whereDate(DB::raw("DATE(customer_ledgers.date)"), '>=', $from_date);
        if ($to_date != null)
            $sales = $sales->whereDate(DB::raw("DATE(customer_ledgers.date)"), '<=', $to_date);
        $sales = $sales->orderBy('customer_ledgers.date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        if ($from_date == null)
            $from_date = "all";
        if ($to_date == null)
            $to_date = "all";
        return view('pages.reports.customer_ledger_analysis.load_ageing_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function printAgeingReport($from_date, $to_date, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('customer_ledgers')
            ->select('customer_ledgers.dr', 'customer_ledgers.cr', 'receipt_no', 'systemid', 'description', 'date', 'name', 'phone', 'customer_ledgers.id')
            ->join('customers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('customer_ledgers.customer_id', 'LIKE', $customer_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('customers.type', '=', "Credit");
        if ($from_date != 'all')
            $sales = $sales->whereDate(DB::raw("DATE(customer_ledgers.date)"), '>=', $from_date);
        if ($to_date != 'all')
            $sales = $sales->whereDate(DB::raw("DATE(customer_ledgers.date)"), '<=', $to_date);
        $sales = $sales->orderBy('customer_ledgers.date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        if ($from_date == null)
            $from_date = "all";
        if ($to_date == null)
            $to_date = "all";
        return view('pages.reports.customer_ledger_analysis.print_ageing_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function lastTransaction()
    {
        return view('pages.reports.customer_ledger_analysis.customer_last_transaction_report', [
            'customers' => Customer::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->where('type', 'Credit')->get(),
        ]);
    }

    public function loadLastTransaction(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('customers')
            ->select('customers.name AS customer', DB::raw('SUM(customer_ledgers.cr) - SUM(customer_ledgers.dr) AS balance'), DB::raw('MAX(customer_ledgers.date) AS last_date'))
            ->join('customer_ledgers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('customer_ledgers.customer_id', 'LIKE', $customer_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(customer_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('last_date')
            ->groupBy('customer_ledgers.customer_id')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.load_customer_last_transaction_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function printLastTransaction($from_date, $to_date, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('customers')
            ->select('customers.name AS customer', DB::raw('SUM(customer_ledgers.cr) - SUM(customer_ledgers.dr) AS balance'), DB::raw('MAX(customer_ledgers.date) AS last_date'))
            ->join('customer_ledgers', 'customers.id', 'customer_ledgers.customer_id')
            ->where('customer_ledgers.customer_id', 'LIKE', $customer_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(customer_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('last_date')
            ->groupBy('customer_ledgers.customer_id')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.print_customer_last_transaction_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function customerPaymentReport()
    {
        return view('pages.reports.customer_ledger_analysis.customer_payment_report', [
            'customers' => Customer::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->where('type', 'Credit')->get(),
        ]);
    }

    public function loadCustomerPaymentReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('customers')
            ->select('customers.name AS customer', 'customers.id AS customer_id', 'customer_ledgers.id', 'customer_ledgers.dr', 'customer_ledgers.cr', 'teller_no', 'cr', 'account_name', 'date', 'payment_mode', 'customer_ledgers.created_at')
            ->join('customer_ledgers', 'customers.id', 'customer_ledgers.customer_id')
            ->join('bank_accounts', 'bank_accounts.id', 'customer_ledgers.bank_account_id')
            ->where('customer_ledgers.customer_id', 'LIKE', $customer_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('payment_mode', '<>', 'Credit')
            ->whereBetween(DB::raw("DATE(customer_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('customer_ledgers.date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.load_customer_payment_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function printCustomerPaymentReport($from_date, $to_date, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('customers')
            ->select('customers.name AS customer', 'customers.id AS customer_id', 'customer_ledgers.id', 'customer_ledgers.dr', 'customer_ledgers.cr', 'teller_no', 'cr', 'account_name', 'date', 'payment_mode', 'customer_ledgers.created_at')
            ->join('customer_ledgers', 'customers.id', 'customer_ledgers.customer_id')
            ->join('bank_accounts', 'bank_accounts.id', 'customer_ledgers.bank_account_id')
            ->where('customer_ledgers.customer_id', 'LIKE', $customer_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('payment_mode', '<>', 'Credit')
            ->whereBetween(DB::raw("DATE(customer_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('customer_ledgers.date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.print_customer_payment_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }


    public function debtorPaymentOverDueReport()
    {
        return view('pages.reports.customer_ledger_analysis.debtor_payment_overdue_report', [
            'customers' => Customer::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->where('type', 'Credit')->get(),
        ]);
    }

    public function loadDebtorPaymentOverDueReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all') {
            $customer_id = '%';
        }

        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'total', 'pay', 'due', 'invoice_no', 'order_date', 'due_date')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->where('orders.payment_mode', 'LIKE', 'Credit')
            ->where('customers.type', 'LIKE', 'Credit')
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('orders.due', '>', 0)
            ->where('orders.status', 1)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(due_date)"), [$from_date, $to_date])
            ->orderBy('orders.order_date')
            ->orderBy('orders.due_date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.load_debtor_payment_overdue_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function printDebtorPaymentOverDueReport($from_date, $to_date, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'total', 'pay', 'due', 'invoice_no', 'order_date', 'due_date')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->where('orders.payment_mode', 'LIKE', 'Credit')
            ->where('customers.type', 'LIKE', 'Credit')
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('orders.due', '>', 0)
            ->where('orders.status', 1)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(due_date)"), [$from_date, $to_date])
            ->orderBy('orders.order_date')
            ->orderBy('orders.due_date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.print_debtor_payment_overdue_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function deletedSaleReport()
    {
        return view('pages.reports.customer_ledger_analysis.deleted_sales_report', [
            'customers' => Customer::where('branch_id', 'LIKE', User::userBranchAction())->where('type', 'Credit')->get()
        ]);
    }

    public function loadDeletedSaleReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all') {
            $customer_id = '%';
        }

        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'orders.total', 'pay', 'due', 'invoice_no', 'order_date', 'due_date', 'products.name AS item', 'quantity', 'sold_price', 'stores.name AS store', 'orders.payment_mode', 'order_details.updated_at')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('orders.payment_mode', 'LIKE', 'Credit')
            ->where('customers.type', 'LIKE', 'Credit')
            ->where('order_details.status', 0)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy('order_date', 'DESC')
            ->orderBy('order_details.updated_at')
            ->groupBy('store_products.product_id')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.load_deleted_sales_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function printDeletedSaleReport($from_date, $to_date, $customer_id)
    {
        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'orders.total', 'pay', 'due', 'invoice_no', 'order_date', 'due_date', 'products.name AS item', 'quantity', 'sold_price', 'stores.name AS store', 'orders.payment_mode', 'order_details.updated_at')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('orders.payment_mode', 'LIKE', 'Credit')
            ->where('customers.type', 'LIKE', 'Credit')
            ->where('order_details.status', 0)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy('order_date', 'DESC')
            ->orderBy('order_details.updated_at')
            ->groupBy('store_products.product_id')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.print_deleted_sales_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function supplierBalanceReport()
    {
        return view('pages.reports.supplier_ledger_analysis.supplier_payment_report', [
            'suppliers' => Supplier::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),
        ]);
    }

    public function loadSupplierBalanceReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $supplier_id = $request->supplier_id;

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        $sales = DB::table('suppliers')
            ->select('suppliers.name AS supplier', 'supplier_ledgers.dr', 'supplier_ledgers.cr', 'teller_no', 'Ref', 'description', 'date')
            ->join('supplier_ledgers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('supplier_ledgers.supplier_id', 'LIKE', $supplier_id)
            ->where('supplier_ledgers.payment_mode', '<>', 'Credit Note')
            ->where('suppliers.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(supplier_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('supplier_ledgers.date')
            ->get();
        if ($supplier_id == "%")
            $supplier_id = "all";

        return view('pages.reports.supplier_ledger_analysis.load_supplier_payment_report', compact('sales', 'from_date', 'to_date', 'supplier_id'));
    }

    public function printSupplierBalanceReport($from_date, $to_date, $supplier_id)
    {

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        $sales = DB::table('suppliers')
            ->select('suppliers.name AS supplier', 'supplier_ledgers.dr', 'supplier_ledgers.cr', 'teller_no', 'Ref', 'description', 'date')
            ->join('supplier_ledgers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('supplier_ledgers.supplier_id', 'LIKE', $supplier_id)
            ->where('supplier_ledgers.payment_mode', '<>', 'Credit Note')
            ->where('suppliers.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(supplier_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('supplier_ledgers.date')
            ->get();
        if ($supplier_id == "%")
            $supplier_id = "all";
        return view('pages.reports.supplier_ledger_analysis.print_supplier_payment_report', compact('sales', 'from_date', 'to_date', 'supplier_id'));
    }

    public function supplierDebtReport()
    {
        return view('pages.reports.supplier_ledger_analysis.supplier_total_debt_report', [
            'suppliers' => Supplier::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),
        ]);
    }

    public function loadSupplierDebtReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $supplier_id = $request->supplier_id;

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }

        $sales = DB::table('supplier_ledgers')
            ->select('suppliers.name AS supplier', DB::raw('SUM(cr) - SUM(dr) AS debt'))
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('supplier_ledgers.supplier_id', 'LIKE', $supplier_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->orderBy('suppliers.name')
            ->groupBy('supplier_ledgers.supplier_id')
            ->get();
        if ($supplier_id == "%")
            $supplier_id = "all";
        return view('pages.reports.supplier_ledger_analysis.load_supplier_total_debt_report', compact('sales', 'from_date', 'to_date', 'supplier_id'));
    }

    public function printSupplierDebtReport($from_date, $to_date, $supplier_id)
    {

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        $sales = DB::table('supplier_ledgers')
            ->select('suppliers.name AS supplier', DB::raw('SUM(cr) - SUM(dr) AS debt'))
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('supplier_ledgers.supplier_id', 'LIKE', $supplier_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->orderBy('suppliers.name')
            ->groupBy('supplier_ledgers.supplier_id')
            ->get();
        if ($supplier_id == "%")
            $supplier_id = "all";
        return view('pages.reports.supplier_ledger_analysis.print_supplier_total_debt_report', compact('sales', 'from_date', 'to_date', 'supplier_id'));
    }

    public function creditNoteReport()
    {
        return view('pages.reports.supplier_ledger_analysis.creditor_note_report', [
            'suppliers' => Supplier::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),
        ]);
    }

    public function loadCreditNoteReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $supplier_id = $request->supplier_id;

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        $sales = SupplierLedger::where('payment_mode', '=', 'Credit Note')
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('supplier_id', 'LIKE', $supplier_id)
            ->orderBy('date', 'DESC')
            ->get();
        if ($supplier_id == "%")
            $supplier_id = "all";
        return view('pages.reports.supplier_ledger_analysis.load_creditor_note_report', compact('sales', 'from_date', 'to_date', 'supplier_id'));
    }

    public function printCreditNoteReport($from_date, $to_date, $supplier_id)
    {

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        $sales = SupplierLedger::where('payment_mode', '=', 'Credit Note')
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('supplier_id', 'LIKE', $supplier_id)
            ->orderBy('date', 'DESC')
            ->get();
        if ($supplier_id == "%")
            $supplier_id = "all";
        return view('pages.reports.supplier_ledger_analysis.print_creditor_note_report', compact('sales', 'from_date', 'to_date', 'supplier_id'));
    }

    public function supplierPaymentReport()
    {
        return view('pages.reports.purchase_analysis.supplier_payment_report', [
            'suppliers' => Supplier::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),
        ]);
    }

    public function loadSupplierPaymentReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $supplier_id = $request->supplier_id;

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        $sales = DB::table('suppliers')
            ->select('suppliers.name AS supplier', 'suppliers.id AS supplier_id', 'supplier_ledgers.id', 'supplier_ledgers.dr', 'supplier_ledgers.cr', 'teller_no', 'cr', 'account_name', 'date', 'payment_mode', 'supplier_ledgers.created_at')
            ->join('supplier_ledgers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->join('bank_accounts', 'bank_accounts.id', 'supplier_ledgers.bank_account_id')
            ->where('supplier_ledgers.supplier_id', 'LIKE', $supplier_id)
            ->where('payment_mode', '<>', 'Credit')
            ->where('suppliers.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(supplier_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('supplier_ledgers.date')
            ->get();
        if ($supplier_id == "%")
            $supplier_id = "all";
        return view('pages.reports.purchase_analysis.load_supplier_payment_report', compact('sales', 'from_date', 'to_date', 'supplier_id'));
    }

    public function printSupplierPaymentReport($from_date, $to_date, $supplier_id)
    {

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        $sales = DB::table('suppliers')
            ->select('suppliers.name AS supplier', 'suppliers.id AS supplier_id', 'supplier_ledgers.id', 'supplier_ledgers.dr', 'supplier_ledgers.cr', 'teller_no', 'cr', 'account_name', 'date', 'payment_mode', 'supplier_ledgers.created_at')
            ->join('supplier_ledgers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->join('bank_accounts', 'bank_accounts.id', 'supplier_ledgers.bank_account_id')
            ->where('supplier_ledgers.supplier_id', 'LIKE', $supplier_id)
            ->where('payment_mode', '<>', 'Credit')
            ->where('suppliers.branch_id', 'LIKE', User::userBranchAction())
            ->whereBetween(DB::raw("DATE(supplier_ledgers.date)"), [$from_date, $to_date])
            ->orderBy('supplier_ledgers.date')
            ->get();
        if ($supplier_id == "%")
            $supplier_id = "all";
        return view('pages.reports.purchase_analysis.print_supplier_payment_report', compact('sales', 'from_date', 'to_date', 'supplier_id'));
    }

    public function purchasesReport()
    {
        return view('pages.reports.purchase_analysis.supplier_transaction', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::where('branch_id', 'LIKE', User::userBranchAction())->get(),
        ]);
    }

    public function loadPurchasesReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $supplier_id = $request->supplier_id;
        $purchase_mode = $request->purchase_mode;

        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        if ($purchase_mode == 'all') {
            $purchase_mode = '%';
        }

        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'invoice', 'products.name AS product', 'stores.name AS store', 'purchase_products.qty_supplied AS quantity', 'unit_price', 'purchase_products.updated_at', 'purchases.purchase_date', 'purchase_mode')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchases.source_store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('purchase_products.product_id', 'LIKE', $product_id)
            ->where('purchases.source_store_id', 'LIKE', $store_id)
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.purchase_mode', 'LIKE', $purchase_mode)
            ->where('purchase_products.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($supplier_id == "%")
            $supplier_id = "all";
        if ($purchase_mode == "%")
            $purchase_mode = "all";


        return view('pages.reports.purchase_analysis.load_supplier_transaction_report', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'supplier_id', 'purchase_mode'));
    }

    public function printPurchasesReport($from_date, $to_date, $store_id, $category_id, $product_id, $supplier_id, $purchase_mode)
    {
        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        if ($purchase_mode == 'all') {
            $purchase_mode = '%';
        }

        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'invoice', 'products.name AS product', 'stores.name AS store', 'purchase_products.qty_supplied AS quantity', 'unit_price', 'purchase_products.updated_at', 'purchases.purchase_date', 'purchase_mode')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchases.source_store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('purchase_products.product_id', 'LIKE', $product_id)
            ->where('purchases.source_store_id', 'LIKE', $store_id)
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.purchase_mode', 'LIKE', $purchase_mode)
            ->where('purchase_products.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        return view('pages.reports.purchase_analysis.print_supplier_transaction_report', compact('sales', 'from_date', 'to_date', 'purchase_mode'));
    }

    public function purchaseCheckReport()
    {
        return view('pages.reports.purchase_analysis.purchase_transaction_check', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::where('branch_id', 'LIKE', User::userBranchAction())->get(),
        ]);
    }

    public function loadPurchaseCheckReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $supplier_id = $request->supplier_id;
        $purchase_mode = $request->purchase_mode;

        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        if ($purchase_mode == 'all') {
            $purchase_mode = '%';
        }

        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'invoice', 'products.name AS product', 'stores.name AS store', 'purchase_products.qty_supplied AS quantity', 'unit_price', 'purchase_products.updated_at', 'purchases.purchase_date', 'purchase_mode', 'purchases.source_store_id', 'purchase_products.product_id')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchases.source_store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('purchase_products.product_id', 'LIKE', $product_id)
            ->where('purchases.source_store_id', 'LIKE', $store_id)
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.purchase_mode', 'LIKE', $purchase_mode)
            ->where('purchase_products.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($supplier_id == "%")
            $supplier_id = "all";
        if ($purchase_mode == "%")
            $purchase_mode = "all";


        return view('pages.reports.purchase_analysis.load_purchase_transaction_check_report', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'supplier_id', 'purchase_mode'));
    }

    public function printPurchaseCheckReport($from_date, $to_date, $store_id, $category_id, $product_id, $supplier_id, $purchase_mode)
    {
        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        if ($purchase_mode == 'all') {
            $purchase_mode = '%';
        }

        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'invoice', 'products.name AS product', 'stores.name AS store', 'purchase_products.qty_supplied AS quantity', 'unit_price', 'purchase_products.updated_at', 'purchases.purchase_date', 'purchase_mode', 'purchases.source_store_id', 'purchase_products.product_id')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchases.source_store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('purchase_products.product_id', 'LIKE', $product_id)
            ->where('purchases.source_store_id', 'LIKE', $store_id)
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.purchase_mode', 'LIKE', $purchase_mode)
            ->where('purchase_products.status', 1)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        return view('pages.reports.purchase_analysis.print_purchase_transaction_check_report', compact('sales', 'from_date', 'to_date', 'purchase_mode'));
    }

    public function totalPurchaseItemReport()
    {
        return view('pages.reports.purchase_analysis.total_purchase_item_report', [
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('id')->get()
        ]);
    }

    public function loadTotalPurchaseItemReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $store_id = $request->store_id;

        if ($store_id == 'all') {
            $store_id = '%';
        }

        $sales = DB::table('purchases')
            ->select('products.name AS product', DB::raw("SUM(qty_supplied) AS quantity"))
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('stores', 'stores.id', 'purchases.source_store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('purchases.source_store_id', 'LIKE', $store_id)
            ->where('purchase_products.status', 1)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('products.name')
            ->groupBy('purchase_products.qty_supplied');
        if ($store_id != '%')
            $sales = $sales->groupBy('purchases.source_store_id');
        $sales = $sales->get();


        if ($store_id == "%")
            $store_id = "all";

        return view('pages.reports.purchase_analysis.load_total_purchase_item_report', compact('sales', 'from_date', 'to_date', 'store_id'));
    }

    public function printTotalPurchaseItemReport($from_date, $to_date, $store_id)
    {

        if ($store_id == 'all') {
            $store_id = '%';
        }
        $sales = DB::table('purchases')
            ->select('products.name AS product', DB::raw("SUM(qty_supplied) AS quantity"))
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('stores', 'stores.id', 'purchases.source_store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('purchases.source_store_id', 'LIKE', $store_id)
            ->where('purchase_products.status', 1)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('products.name')
            ->groupBy('purchase_products.qty_supplied');
        if ($store_id != '%')
            $sales = $sales->groupBy('purchases.source_store_id');
        $sales = $sales->get();
        if ($store_id == "%")
            $store_id = "all";
        return view('pages.reports.purchase_analysis.print_total_purchase_item_report', compact('sales', 'from_date', 'to_date', 'store_id'));
    }

    public function logs(User $user)
    {
        return view('pages.users.logs', [
            'user' => $user,
            'users' => User::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get(),
        ]);
    }

    public function viewLogs(Request $request)
    {
        $records = AuditLog::whereDate(DB::raw('DATE(created_at)'), '>=', $request->from_date)->whereDate(DB::raw('DATE(created_at)'), '<=', $request->to_date)->where('user_id', $request->user_id)->latest()->get();
        return view('pages.users.load_user_logs', [
            'records' => $records,
            'user' => User::find($request->user_id),
            'from_date' => $request->from_date,
            'to_date' => $request->to_date
        ]);
    }

    public function printLogs($from_date, $to_date, $user_id)
    {
        $records = AuditLog::whereDate(DB::raw('DATE(created_at)'), '>=', $from_date)->whereDate(DB::raw('DATE(created_at)'), '<=', $to_date)->where('user_id', $user_id)->latest()->get();
        return view('pages.users.user_logs_print', [
            'records' => $records,
            'user' => User::find($user_id),
            'from_date' => $from_date,
            'to_date' => $to_date
        ]);
    }

    public function loanBalance()
    {
        return view('pages.reports.user_ledger_and_loan.user_loan_balance', ['users' => LoanCollector::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get()]);
    }

    public function loadLoanBalance(Request $request)
    {
        $collector_id = $request->user_id;
        if ($collector_id == "all")
            $collector_id = '%';
        $records = Loan::select('loan_collectors.name', DB::raw('SUM(balance) AS balance'))
            ->where('loan_collector_id', 'LIKE', $collector_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('balance', '>', 0)
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->groupBy('loans.loan_collector_id')
            ->get();
        if ($collector_id == "%")
            $collector_id = "all";
        return view('pages.reports.user_ledger_and_loan.load_user_loan_balance', ['records' => $records, 'user_id' => $collector_id]);
    }

    public function printLoanBalance($collector_id)
    {
        if ($collector_id == "all")
            $collector_id = '%';
        $records = Loan::select('loan_collectors.name', DB::raw('SUM(balance) AS balance'))
            ->where('loan_collector_id', 'LIKE', $collector_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('balance', '>', 0)
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->groupBy('loans.loan_collector_id')
            ->get();
        if ($collector_id == "%")
            $collector_id = "all";
        return view('pages.reports.user_ledger_and_loan.print_user_loan_balance', ['records' => $records, 'user_id' => $collector_id]);
    }

    public function loanHistory()
    {
        return view('pages.reports.user_ledger_and_loan.user_loan_history', ['users' => LoanCollector::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get()]);
    }

    public function loadLoanHistory(Request $request)
    {
        $collector_id = $request->user_id;
        if ($collector_id == "all")
            $collector_id = '%';
        $records = Loan::select('loans.amount AS amount_collected', 'loans.date', 'loans.receipt_no AS c_receipt_no', 'loan_payments.receipt_no AS p_receipt_no', DB::raw('SUM(loan_payments.amount) AS amount_paid'), 'due_date', 'account_name')
            ->where('loan_collector_id', 'LIKE', $collector_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->leftJoin('loan_payments', 'loan_payments.loan_id', 'loans.id')
            ->leftJoin('bank_accounts', 'bank_accounts.id', 'loan_payments.bank_account_id')
            ->groupBy('loan_payments.loan_id')
            ->get();
        if ($collector_id == "%")
            $collector_id = "all";
        $collector = LoanCollector::find($collector_id);
        return view('pages.reports.user_ledger_and_loan.load_user_loan_history', ['records' => $records, 'collector' => $collector]);
    }

    public function printLoanHistory($collector_id)
    {
        if ($collector_id == "all")
            $collector_id = '%';
        $records = Loan::select('loans.amount AS amount_collected', 'loans.date', 'loans.receipt_no AS c_receipt_no', 'loan_payments.receipt_no AS p_receipt_no', DB::raw('SUM(loan_payments.amount) AS amount_paid'), 'due_date', 'account_name')
            ->where('loan_collector_id', 'LIKE', $collector_id)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->join('loan_collectors', 'loan_collectors.id', 'loans.loan_collector_id')
            ->leftJoin('loan_payments', 'loan_payments.loan_id', 'loans.id')
            ->leftJoin('bank_accounts', 'bank_accounts.id', 'loan_payments.bank_account_id')
            ->groupBy('loan_payments.loan_id')
            ->get();
        if ($collector_id == "%")
            $collector_id = "all";
        $collector = LoanCollector::find($collector_id);
        return view('pages.reports.user_ledger_and_loan.print_user_loan_history', ['records' => $records, 'collector' => $collector]);
    }

    public function printAvailableStock(Request $request)
    {
        $categor_id = $request->categor_id;
        $store = $request->store;
        $number = $request->number;
        if ($categor_id == "all")
            $categor_id = "%";
        if ($store == "all")
            $store = "%";
        $stores = DB::table('store_products')
            ->select('products.name', 'stores.name as store', 'store_products.qty_available', 'selling_price', 'cost_price')
            ->distinct()
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'store_products.product_id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $categor_id)
            ->where('store_products.store_id', 'LIKE', $store)
            ->where('store_products.qty_available', '>=', $number)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('products.name');
        $stores = $stores->get();
        return view('pages.reports.stock_control.print_available_stock', ['stores' => $stores]);
    }

    public function accountBalance(Request $request)
    {
        //return view('pages.reports.ap_ar.account_balances', ['model' => null]);

        $user_branch = User::userBranchAction();
        $accounts = GeneralAccount::whereIn('class', ['A11', 'A12', 'A13'])->orderBy('number')->get();
        $customers = Customer::whereIn('type', ['Retail', 'Wholesale'])->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $model = new GeneralAccountLedger();
        return view('pages.reports.ap_ar.account_balances', compact('accounts', 'customers', 'model'));
    }

    public function trialBalance()
    {
        $branches = Branch::select(['id', 'name', 'code'])->orderBy('name')->get();

        return view('pages.reports.ap_ar.trial_balance.index', compact('branches'));
    }

    public function loadTrialBalance(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;

        $query = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id);
        $ledgers = $query->orderBy('date')->orderBy('general_account_ledgers.id')->get();

        $credit_sum = $query->sum('credit');
        $debit_sum = $query->sum('debit');
        $balance = $credit_sum - $debit_sum;
        $branch = Branch::find($branch_id);

        return view('pages.reports.ap_ar.trial_balance.load', compact('ledgers', 'branch', 'from_date', 'to_date', 'balance', 'credit_sum', 'debit_sum'));
    }

    public function printTrialBalance($from, $to, Branch $branch)
    {
        $query = $this->generalAccountLedgerBy($from, $to, $branch->id);
        $ledgers = $query->orderBy('date')->orderBy('general_account_ledgers.id')->get();

        $credit_sum = $query->sum('credit');
        $debit_sum = $query->sum('debit');
        $balance = $credit_sum - $debit_sum;

        return view('pages.reports.ap_ar.trial_balance.print', compact('ledgers', 'branch', 'from', 'to', 'balance', 'credit_sum', 'debit_sum'));
    }

    private function generalAccountLedgerBy($from_date, $to_date, $branch_id)
    {
        return GeneralAccountLedger::join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->where('general_account_ledgers.branch_id', 'like', $branch_id)
            ->whereBetween('date', [$from_date, $to_date]);
    }

}
