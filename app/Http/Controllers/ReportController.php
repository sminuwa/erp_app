<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ChartOfAccount;
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
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\InterBank;
use App\Models\Journal;





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


    public function stockHistory()
    {
        return view('pages.reports.stock_control.stock_history_report');
    }

    public function loadStockHistoryReport(Request $request)
    {
        //return $request->stock_in_out;
        $type = $request->type;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $category_id = $request->category_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        if ($type == "all" || $type == "")
            $type = "%";
        if ($branch_id == "all" || $branch_id == "")
            $branch_id = "%";
        if ($store_id == "all" || $store_id == "")
            $store_id = "%";
        if ($category_id == "all" || $category_id == "")
            $category_id = "%";
        if ($product_id == "all" || $product_id == "")
            $product_id = "%";

        $records = StockCard::select('date', 'branch_id', 'cr', 'dr', 'products.name AS product_name', 'products.code AS product_code', 'branches.code AS branch_code', 'refno', 'stores.code AS store_code')
            ->join('products', 'products.id', 'stock_cards.product_id')
            ->join('stores', 'stores.id', 'stock_cards.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stock_cards.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('stock_cards.store_id', 'LIKE', $store_id)
            ->where('stock_cards.type', 'LIKE', $type)
            ->whereBetween('date', [$from_date, $to_date])->get();

        if ($type == "%" || $type == "")
            $type = "all";
        if ($branch_id == "%" || $branch_id == "")
            $branch_id = "all";
        if ($store_id == "%" || $store_id == "")
            $store_id = "all";
        if ($category_id == "%" || $category_id == "")
            $category_id = "all";
        if ($product_id == "%" || $product_id == "")
            $product_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.load_stock_history_report', compact('records', 'from_date', 'to_date', 'store_id', 'product_id', 'branch_id', 'category_id', 'branch', 'type'));
    }

    public function printStockHistory($from_date, $to_date, $type, $branch_id, $store_id, $category_id, $product_id)
    {
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($branch_id == 'all') {
            $branch_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }
        if ($type == 'all') {
            $type = '%';

        }

        $records = StockCard::select('date', 'branch_id', 'cr', 'dr', 'products.name AS product_name', 'products.code AS product_code', 'branches.code AS branch_code', 'refno', 'stores.code AS store_code')
            ->join('products', 'products.id', 'stock_cards.product_id')
            ->join('stores', 'stores.id', 'stock_cards.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stock_cards.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('stock_cards.store_id', 'LIKE', $store_id)
            ->where('stock_cards.type', 'LIKE', $type)
            ->whereBetween('date', [$from_date, $to_date])->get();

        return view('pages.reports.stock_control.print_stock_history', compact('records', 'from_date', 'to_date'));
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
        $branch_id = $request->branch_id;
        $category_id = $request->category_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        if ($branch_id == "all" || $branch_id == "")
            $branch_id = "%";
        if ($store_id == "all" || $store_id == "")
            $store_id = "%";
        if ($category_id == "all" || $category_id == "")
            $category_id = "%";
        if ($product_id == "all" || $product_id == "")
            $product_id = "%";

        $stores = DB::table('store_products')
            ->select('products.name', 'products.code AS product_code', 'stores.code as store_code', 'store_products.qty_available', 'retail_selling_price', 'whole_selling_price', 'cost_price', 'store_products.id')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('branch_product_prices.branch_id', 'LIKE', $branch_id)
            ->orderBy('products.name')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.load_stock', ['stores' => $stores, 'branch_id' => $branch_id, 'store_id' => $store_id, 'product_id' => $product_id, 'category_id' => $category_id, 'branch' => $branch]);
    }

    public function printCurrentStock($branch_id, $store_id, $category_id, $product_id)
    {
        if ($branch_id == "all" || $branch_id == "")
            $branch_id = "%";
        if ($store_id == "all" || $store_id == "")
            $store_id = "%";
        if ($category_id == "all" || $category_id == "")
            $category_id = "%";
        if ($product_id == "all" || $product_id == "")
            $product_id = "%";

        $stores = DB::table('store_products')
            ->select('products.name', 'products.code AS product_code', 'stores.code as store_code', 'store_products.qty_available', 'retail_selling_price', 'whole_selling_price', 'cost_price', 'store_products.id')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('branch_product_prices.branch_id', 'LIKE', $branch_id)
            ->orderBy('products.name')
            ->get();
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.print_current_stock', ['stores' => $stores, 'branch' => $branch]);
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
        return view('pages.reports.stock_control.store_ledger_report');
    }

    public function loadStoreLedger(Request $request)
    {
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $branch_id = $request->branch_id;
        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';

        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';

        }
        if ($store_id == 'all' || $store_id == '') {
            $store_id = '%';

        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';

        }
        $stores = DB::table('store_products')
            ->select('products.name', 'products.code', 'stores.code as store', 'store_products.qty_available', 'retail_selling_price', 'whole_selling_price', 'cost_price', 'store_products.id', 'categories.name AS category')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', 'products.category_id')
            // ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.load_store_ledger', ['stores' => $stores, 'branch_id' => $branch_id, 'branch' => $branch, 'product_id' => $product_id, 'category_id' => $category_id, 'store_id' => $store_id]);
    }

    public function printstoreLedger($branch_id, $store_id, $category_id, $product_id)
    {

        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($store_id == 'all') {
            $store_id = '%';

        }
        $stores = DB::table('store_products')
            ->select('products.name', 'products.code', 'stores.code as store', 'store_products.qty_available', 'retail_selling_price', 'whole_selling_price', 'cost_price', 'store_products.id', 'categories.name AS category')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', 'products.category_id')
            // ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->get();
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.print_store_ledger', ['stores' => $stores, 'branch' => $branch]);
    }

    public function stockAdjustment()
    {
        return view('pages.reports.stock_control.stock_adjustment_report');
    }

    public function loadStockAdjustment(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $branch_id = $request->branch_id;
        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';

        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';

        }
        if ($store_id == 'all' || $store_id == '') {
            $store_id = '%';

        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';

        }

        $stores = DB::table('stock_adjustments')
            ->select('products.name', 'products.code', 'stores.name as store', 'quantity', 'date', 'reference')
            ->join('stock_adjustment_details', 'stock_adjustment_details.stock_adjustment_id', 'stock_adjustments.id')
            ->join('products', 'products.id', '=', 'stock_adjustment_details.product_id')
            ->join('stores', 'stores.id', '=', 'stock_adjustment_details.store_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stock_adjustment_details.product_id', 'LIKE', $product_id)
            ->where('stock_adjustment_details.store_id', 'LIKE', $store_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->whereBetween('date', [$from_date, $to_date])
            ->orderBy('date', 'DESC')
            ->orderBy('reference', 'ASC')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        $branch = null;
        if ($branch_id != '%')
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.load_stock_adjustment', compact('stores', 'from_date', 'to_date', 'branch', 'branch_id', 'product_id', 'store_id', 'category_id'));
    }

    public function printStockAdjustment($from_date, $to_date, $branch_id, $store_id, $category_id, $product_id)
    {
        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';

        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';

        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';

        }
        if ($store_id == 'all' || $store_id == '') {
            $store_id = '%';
        }

        $stores = DB::table('stock_adjustments')
            ->select('products.name', 'products.code', 'stores.name as store', 'quantity', 'date', 'reference')
            ->join('stock_adjustment_details', 'stock_adjustment_details.stock_adjustment_id', 'stock_adjustments.id')
            ->join('products', 'products.id', '=', 'stock_adjustment_details.product_id')
            ->join('stores', 'stores.id', '=', 'stock_adjustment_details.store_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stock_adjustment_details.product_id', 'LIKE', $product_id)
            ->where('stock_adjustment_details.store_id', 'LIKE', $store_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->whereBetween('date', [$from_date, $to_date])
            ->orderBy('date', 'DESC')
            ->orderBy('reference', 'ASC')
            ->get();
        $branch = null;
        if ($branch_id != '%')
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.print_stock_adjutment', compact('stores', 'from_date', 'to_date', 'branch'));
    }

    //Sales and Cash Analysis
    public function generalSaleReport()
    {
        return view('pages.reports.sales_and_cash_analysis.general_sale');
    }

    public function loadGeneralSaleReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $customer_id = $request->customer_id;
        $type = $request->type;

        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';
        }
        if ($store_id == 'all' || $store_id == '') {
            $store_id = '%';
        }
        if ($customer_id == 'all' || $customer_id == '') {
            $customer_id = '%';
        }

        if ($type == 'all' || $type == '') {
            $type = '%';
        }

        $sales = DB::table('orders')
            ->select(
                'orders.id AS order_id',
                'customers.code AS customer',
                'orders.reference',
                'products.name AS product_name',
                'products.code AS product_code',
                'stores.code AS store_code',
                'order_details.quantity',
                'sold_price',
                'cost_price',
                'order_date',
                'store_products.id AS store_product_id'
            )
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('customers.type', 'LIKE', $type)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('order_details.status', 1)
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
        if ($type == "%")
            $type = "all";

        $branch = null;
        if ($branch_id != 'all' && $branch_id != '')
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.load_general_sale_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'product_id', 'store_id', 'category_id', 'customer_id', 'type', 'branch'));
    }

    public function printGeneralSaleReport($from_date, $to_date, $branch_id, $store_id, $category_id, $product_id, $customer_id, $type)
    {
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';
        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';
        }
        if ($store_id == 'all' | $store_id == '') {
            $store_id = '%';
        }
        if ($customer_id == 'all' || $customer_id == '') {
            $customer_id = '%';
        }
        if ($type == 'all' || $type == '') {
            $type = '%';
        }

        $sales = DB::table('orders')
            ->select(
                'orders.id AS order_id',
                'customers.code AS customer',
                'orders.reference',
                'products.name AS product_name',
                'products.code AS product_code',
                'stores.code AS store_code',
                'order_details.quantity',
                'sold_price',
                'cost_price',
                'order_date'
            )
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('customers.type', 'LIKE', $type)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('order_details.status', 1)
            ->where(DB::raw("DATE(order_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(order_date)"), '<=', $to_date)
            ->orderBy('order_date')
            ->get();
        $branch = null;
        if ($branch_id != 'all' && $branch_id != '')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_general_sale_report', compact('sales', 'from_date', 'to_date', 'type', 'branch'));
    }

    public function categorySaleReport()
    {
        return view('pages.reports.sales_and_cash_analysis.sales_by_category');
    }
    public function loadCategorySaleReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $category_id1 = $request->category_id1;
        $category_id2 = $request->category_id2;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($category_id1 == 'all' || $category_id1 == '') {
            $category_id1 = '%';
        }
        if ($category_id2 == 'all' || $category_id2 == '') {
            $category_id2 = '%';
        }

        $sales = DB::table('orders')
            ->select(
                'categories.name AS category',
                DB::raw('SUM(order_details.quantity) AS quantity'),
                DB::raw('SUM(order_details.total) AS amount'),
                DB::raw('SUM(order_details.cost_price * order_details.quantity) AS cost')
            )
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('order_details.status', 1)
            ->where(DB::raw("DATE(order_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(order_date)"), '<=', $to_date);
        if ($category_id2 == '%' && $category_id1 != '%') {
            $sales = $sales->where('products.category_id', 'LIKE', $category_id1);
        } elseif ($category_id2 != '%') {
            $sales = $sales->where('products.category_id', '>=', $category_id1)
                ->where('products.category_id', '<=', $category_id2);
        }
        $sales = $sales->orderBy('order_date')
            ->groupBy('products.category_id')
            ->get();


        if ($category_id1 == "%")
            $category_id1 = "all";
        if ($category_id2 == "%")
            $category_id2 = "all";
        if ($branch_id == '%' || $branch_id == '')
            $branch_id = 'all';

        $branch = null;
        if ($branch_id != 'all' && $branch_id != '')
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.load_sale_by_category_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'category_id1', 'category_id2', 'branch'));
    }
    public function printCategorySaleReport($from_date, $to_date, $branch_id, $category_id1, $category_id2)
    {
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($category_id1 == 'all' || $category_id1 == '') {
            $category_id1 = '%';
        }
        if ($category_id2 == 'all' || $category_id2 == '') {
            $category_id2 = '%';
        }
        $sales = DB::table('orders')
            ->select(
                'categories.name AS category',
                DB::raw('SUM(order_details.quantity) AS quantity'),
                DB::raw('SUM(order_details.total) AS amount'),
                DB::raw('SUM(order_details.cost_price * order_details.quantity) AS cost')
            )
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('order_details.status', 1)
            ->where(DB::raw("DATE(order_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(order_date)"), '<=', $to_date);
        if ($category_id2 == '' || $category_id2 == 'all') {
            $sales = $sales->where('products.category_id', 'LIKE', $category_id1);
        } elseif ($category_id2 != '' || $category_id2 != 'all') {
            $sales = $sales->where('products.category_id', '>=', $category_id1)
                ->where('products.category_id', '<=', $category_id2);
        }
        $sales = $sales->orderBy('order_date')
            ->groupBy('products.category_id')
            ->get();

        $branch = null;
        if ($branch_id != 'all' && $branch_id != '')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_category_sale_report', compact('sales', 'from_date', 'to_date', 'branch'));
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
        return view('pages.reports.stock_control.stock_ledger_report');
    }

    public function loadStockLedger(Request $request)
    { //return $request;

        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;

        if ($branch_id == "all" || $branch_id == "")
            $branch_id = "%";
        if ($store_id == "all" || $store_id == "")
            $store_id = "%";

        if ($product_id == "all" || $product_id == "")
            $product_id = "%";

        $records = StockCard::select(
            'stock_cards.date',
            'cr',
            'dr',
            'products.name AS product_name',
            'products.code AS product_code',
            'branches.code AS branch_code',
            'refno',
            'stores.code AS store_code',
            'qty_before',
            'qty_after',
            'model_name',
            'model_id'
        )
            ->join('products', 'products.id', 'stock_cards.product_id')
            ->join('stores', 'stores.id', 'stock_cards.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->join('general_account_ledgers', 'general_account_ledgers.reference', 'stock_cards.refno')
            ->where('stock_cards.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('stock_cards.store_id', 'LIKE', $store_id)
            ->where('general_account_ledgers.model_name', '<>', 'GeneralAccount')
            ->whereBetween('stock_cards.date', [$from_date, $to_date])
            ->orderBy('date', 'DESC')
            ->get();
        $qty_available = 0;
        if ($store_id > 0)
            $qty_available = DB::table('store_products')->WHERE('store_id', $store_id)->where('product_id', $product_id)->sum('qty_available');
        else
            $qty_available = DB::table('store_products')->where('product_id', $product_id)->groupBy('store_id')->sum('qty_available');

        if ($branch_id == "%" || $branch_id == "")
            $branch_id = "all";
        if ($store_id == "%" || $store_id == "")
            $store_id = "all";
        if ($product_id == "%" || $product_id == "")
            $product_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);

        return view('pages.reports.stock_control.load_stock_ledger_report', [
            'records' => $records,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'branch_id' => $branch_id,
            'store_id' => $store_id,
            'product_id' => $product_id,
            'qty_available' => $qty_available,
        ]);
    }

    public function printStockLedger($from_date, $to_date, $branch_id, $store_id, $product_id)
    {

        if ($branch_id == "all" || $branch_id == "")
            $branch_id = "%";
        if ($store_id == "all" || $store_id == "")
            $store_id = "%";

        if ($product_id == "all" || $product_id == "")
            $product_id = "%";

        $records = StockCard::select(
            'stock_cards.date',
            'cr',
            'dr',
            'products.name AS product_name',
            'products.code AS product_code',
            'branches.code AS branch_code',
            'refno',
            'stores.code AS store_code',
            'qty_before',
            'qty_after',
            'model_name',
            'model_id'
        )
            ->join('products', 'products.id', 'stock_cards.product_id')
            ->join('stores', 'stores.id', 'stock_cards.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->join('general_account_ledgers', 'general_account_ledgers.reference', 'stock_cards.refno')
            ->where('stock_cards.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('stock_cards.store_id', 'LIKE', $store_id)
            ->where('general_account_ledgers.model_name', '<>', 'GeneralAccount')
            ->whereBetween('stock_cards.date', [$from_date, $to_date])
            ->orderBy('date', 'DESC')
            ->get();
        $qty_available = 0;
        if ($store_id > 0)
            $qty_available = DB::table('store_products')->WHERE('store_id', $store_id)->where('product_id', $product_id)->sum('qty_available');
        else
            $qty_available = DB::table('store_products')->where('product_id', $product_id)->groupBy('store_id')->sum('qty_available');

        if ($branch_id == "%" || $branch_id == "")
            $branch_id = "all";
        if ($store_id == "%" || $store_id == "")
            $store_id = "all";
        if ($product_id == "%" || $product_id == "")
            $product_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);

        return view('pages.reports.stock_control.print_stock_ledger_report', [
            'records' => $records,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'branch_id' => $branch_id,
            'store_id' => $store_id,
            'product_id' => $product_id,
            'qty_available' => $qty_available,
            'branch' => $branch,
        ]);
    }

    public function customerSaleReport()
    {
        return view('pages.reports.sales_and_cash_analysis.sale_report_with_common_name');
    }

    public function loadCustomerSaleReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $customer = $request->customer;
        $payment_mode = $request->payment_mode;
        $credit_walkedin = $request->credit_walkedin;
        $matching = $request->matching;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';
        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';
        }
        if ($store_id == 'all' || $store_id == '') {
            $store_id = '%';
        }

        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'customers.code', DB::raw("SUM(orders.total) AS total"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->groupBy('orders.customer_id')
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date]);
        if ($matching == "similar")
            $sales = $sales->where('customers.name', 'LIKE', "%$customer%");
        if ($matching == "exact")
            $sales = $sales->where('customers.name', '=', $customer);
        $sales = $sales->orderBy('order_date')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.load_report_with_common_name', compact('sales', 'from_date', 'to_date', 'branch', 'branch_id', 'product_id', 'store_id', 'category_id', 'customer', 'matching'));
    }

    public function printCustomerSaleReport($from_date, $to_date, $branch_id, $store_id, $category_id, $product_id, $customer, $matching)
    {

        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }


        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'customers.code', DB::raw("SUM(orders.total) AS total"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', $branch_id)
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
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_report_with_common_name', compact('sales', 'from_date', 'to_date', 'branch', 'product_id', 'store_id', 'category_id', 'customer'));
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
        $type = $request->type;
        $branch_id = $request->branch_id;

        if ($branch_id == '' || $branch_id == 'all')
            $branch_id = '%';

        $sales = DB::table('orders')
            ->select('products.name AS item', 'products.code AS code', DB::raw("SUM(order_details.quantity) AS quantity"), DB::raw("SUM(order_details.total) AS total"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date]);
        if ($type == 'qty')
            $sales = $sales->orderBy(DB::raw("SUM(order_details.quantity)"), 'DESC');
        if ($type == 'amt')
            $sales = $sales->orderBy(DB::raw("SUM(order_details.total)"), 'DESC');
        $sales = $sales->groupBy('store_products.product_id')
            ->take($number_limit)
            ->get();
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_most_sold_item_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'branch', 'type', 'number_limit'));
    }

    public function printMostSoldItemReport($from_date, $to_date, $branch_id, $type, $number_limit)
    {
        if ($branch_id == '' || $branch_id == 'all')
            $branch_id = '%';

        $sales = DB::table('orders')
            ->select('products.name AS item', 'products.code AS code', DB::raw("SUM(order_details.quantity) AS quantity"), DB::raw("SUM(order_details.total) AS total"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date]);
        if ($type == 'qty')
            $sales = $sales->orderBy(DB::raw("SUM(order_details.quantity)"), 'DESC');
        if ($type == 'amt')
            $sales = $sales->orderBy(DB::raw("SUM(order_details.total)"), 'DESC');
        $sales = $sales->groupBy('store_products.product_id')
            ->take($number_limit)
            ->get();
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_most_sold_item_report', compact('sales', 'from_date', 'to_date', 'branch', 'type', 'number_limit'));
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

    public function accountStatement(Request $request)
    {
        //return view('pages.reports.ap_ar.account_balances', ['model' => null]);

        $user_branch = User::userBranchAction();
        return view('pages.reports.ap_ar.statements.account_statement');
    }

    public function loadAccountStatement(Request $request)
    {
        $type = $request->type;
        $payer_id = $request->payer_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        if ($type == 'all')
            $type = '%';
        if ($payer_id == 'all')
            $payer_id = '%';
        if ($branch_id == 'all')
            $branch_id = '%';

        $query = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id);
        $ledgers = $query->orderBy('date')->orderBy('general_account_ledgers.id')->get();
        $ledgers = $query->where('model_name', 'LIKE', $type)
            ->where('model_id', 'LIKE', $payer_id)->get();

        $credit_sum = $query->sum('credit');
        $debit_sum = $query->sum('debit');


        $sum_cr_b_d = $this->generalAccountLedgerB4D($from_date, $branch_id)->sum('credit');
        $sum_dr_b_d = $this->generalAccountLedgerB4D($from_date, $branch_id)->sum('debit');
        $balance = $credit_sum - $debit_sum;
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d;
        if ($branch_id == '%')
            $branch_id = 'all';
        if ($payer_id == '%')
            $payer_id = 'all';
        if ($type == '%')
            $type = 'all';
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.statements.load_account_statement', compact('ledgers', 'branch', 'from_date', 'to_date', 'balance', 'branch_id', 'payer_id','type','credit_sum', 'debit_sum', 'sum_cr_b_d', 'sum_dr_b_d', 'balance_b_d'));
    }
    public function accountBalance(Request $request)
    {

        return view('pages.reports.ap_ar.statements.account_balance');
    }

    public function loadAccountBalance(Request $request)
    {
        $type = $request->account_type;

        $date = $request->date;
        $branch_id = $request->branch_id;
        if ($type == 'all' || $type == '')
            $type = '%';

        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($type == "GeneralAccount") {
            $query = GeneralAccountLedger::select(DB::raw('SUM(credit) AS credit'), DB::raw('SUM(debit) AS debit'), 'number', 'general_accounts.description', 'general_account_ledgers.id')
                ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->whereDate('date', '<=', $date)
                ->where('model_name', 'LIKE', 'GeneralAccount')
                ->orderBy('number')
                ->groupBy('model_id');
        }
        if ($type == "Customer") {
            $query = GeneralAccountLedger::select(DB::raw('SUM(credit) AS credit'), DB::raw('SUM(debit) AS debit'), 'code AS number', 'customers.name AS description', 'general_account_ledgers.id')
                ->join('customers', 'customers.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->whereDate('date', '<=', $date)
                ->where('model_name', 'LIKE', 'Customer')
                ->orderBy('code')
                ->groupBy('model_id');
        }
        if ($type == "Supplier") {
            $query = GeneralAccountLedger::select(DB::raw('SUM(credit) AS credit'), DB::raw('SUM(debit) AS debit'), 'code AS number', 'suppliers.name AS description', 'general_account_ledgers.id')
                ->join('suppliers', 'suppliers.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->whereDate('date', '<=', $date)
                ->where('model_name', 'LIKE', 'Supplier')
                ->orderBy('code')
                ->groupBy('model_id');
        }

        $ledgers = $query;
        $ledgers = $query->where('model_name', 'LIKE', $type)->get();

        $credit_sum = $query->sum('credit');
        $debit_sum = $query->sum('debit');

        $branch = Branch::find($branch_id);

        $balance = $credit_sum - $debit_sum;
        if ($type == '%' || $type == '')
            $type = 'all';

        if ($branch_id == '%' || $branch_id == '')
            $branch_id = 'all';
        return view('pages.reports.ap_ar.statements.load_account_balances', compact('ledgers', 'branch', 'type', 'date', 'branch_id', 'balance', 'credit_sum', 'debit_sum'));
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
        $ledgers = $query->select(
            DB::raw('SUM(credit) AS credit'),
            DB::raw('SUM(debit) AS debit'),
            'number',
            'general_accounts.description',
            'general_account_ledgers.id'
        )
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->orderBy('number')
            ->groupBy('number')
            ->get();


        $credit_sum = $query->sum('credit');
        $debit_sum = $query->sum('debit');
        $balance = $credit_sum - $debit_sum;
        $branch = null;

        if ($branch_id == '' || $branch_id == '%')
            $branch_id = 'all';
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.trial_balance.load', compact('ledgers', 'branch', 'from_date', 'to_date', 'branch_id', 'balance', 'credit_sum', 'debit_sum'));
    }

    public function printTrialBalance($from, $to, $branch_id)
    {
        $query = $this->generalAccountLedgerBy($from, $to, $branch_id);
        $ledgers = $query->select(
            DB::raw('SUM(credit) AS credit'),
            DB::raw('SUM(debit) AS debit'),
            'number',
            'general_accounts.description',
            'general_account_ledgers.id'
        )
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->orderBy('number')
            ->groupBy('number')
            ->get();

        $credit_sum = $query->sum('credit');
        $debit_sum = $query->sum('debit');
        $balance = $credit_sum - $debit_sum;
        $balance = $credit_sum - $debit_sum;
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.trial_balance.print', compact('ledgers', 'branch', 'from', 'to', 'balance', 'credit_sum', 'debit_sum'));
    }

    private function generalAccountLedgerBy($from_date, $to_date, $branch_id)
    {
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        return GeneralAccountLedger::join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->where('general_account_ledgers.branch_id', 'like', $branch_id)
            ->whereDate('date', '>=', $from_date)
            ->whereDate('date', '<=', $to_date);
    }

    private function generalAccountLedgerB4D($from_date, $branch_id)
    {
        //To get account balance before start date 
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        return GeneralAccountLedger::join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->whereDate('date', '<', $from_date);
    }
    public function incomeStatement(Request $request)
    {
        return view('pages.reports.ap_ar.statements.income');
    }
    public function loadIncomeStatement(Request $request)
    {
        $branch_id = $request->branch_id;
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $income_year = $request->income_year;
        $from_month = $request->from_month;
        $to_month = $request->to_month;
        $revenue_class = ['R40'];
        $cost_of_sale_class = ['C50'];
        $expense_class = ['C51', 'C52', 'C53', 'C54', 'C55', 'C56', 'C57', 'C58', 'C59', 'C60', 'C61', 'C62', 'C63'];
        $query = ChartOfAccount::select(DB::raw('SUM(credit) AS credit'), DB::raw('SUM(debit) AS debit'), 'number', 'general_accounts.description')
            ->whereYear('date', $income_year)
            ->join('general_accounts', 'general_accounts.class', 'chart_of_accounts.class')
            ->join('general_account_ledgers', 'model_id', 'general_accounts.id')
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('model_name', 'GeneralAccount')
            ->groupBy('number');

        if ($from_month == '' || $to_month == '') {
            $query->whereMonth('date', '<=', 12);
        }

        if ($from_month != '') {
            $query->whereMonth('date', '>=', $from_month);
        }

        if ($to_month != '') {
            $query->whereMonth('date', '<=', $to_month);
        }

        $query->orderBy('number');

        $expenses = clone $query;
        $expenses = $expenses->whereIn('chart_of_accounts.class', $expense_class)->get();

        $cost_of_sales = clone $query;
        $cost_of_sales = $cost_of_sales->whereIn('chart_of_accounts.class', $cost_of_sale_class)->get();

        $revenues = clone $query;
        $revenues = $revenues->whereIn('chart_of_accounts.class', $revenue_class)->get();

        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        if ($from_month == '')
            $from_month = 'all';

        if ($to_month == '')
            $to_month = 'all';
        if ($branch_id == '')
            $branch_id = 'all';
        return view('pages.reports.ap_ar.statements.load_income_statement', [
            'revenues' => $revenues,
            'cost_of_sales' => $cost_of_sales,
            'expenses' => $expenses,
            'from_month' => $from_month,
            'to_month' => $to_month,
            'income_year' => $income_year,
            'branch' => $branch,
            'branch_id' => $branch_id,
        ]);
    }
    public function printIncomeStatement($from_month, $to_month, $income_year, $branch_id)
    {
        $revenue_class = ['R40'];
        $cost_of_sale_class = ['C50'];
        $expense_class = ['C51', 'C52', 'C53', 'C54', 'C55', 'C56', 'C57', 'C58', 'C59', 'C60', 'C61', 'C62', 'C63'];
        $query = ChartOfAccount::select(DB::raw('SUM(credit) AS credit'), DB::raw('SUM(debit) AS debit'), 'number', 'general_accounts.description')
            ->whereYear('date', $income_year)
            ->join('general_accounts', 'general_accounts.class', 'chart_of_accounts.class')
            ->join('general_account_ledgers', 'model_id', 'general_accounts.id')
            ->where('general_account_ledgers.branch_id', $branch_id)
            ->groupBy('number');

        if ($from_month == '' || $to_month == '') {
            $query->whereMonth('date', '<=', 12);
        }

        if ($from_month != '') {
            $query->whereMonth('date', '>=', $from_month);
        }

        if ($to_month != '') {
            $query->whereMonth('date', '<=', $to_month);
        }

        $query->orderBy('number');

        $expenses = clone $query;
        $expenses = $expenses->whereIn('chart_of_accounts.class', $expense_class)->get();

        $cost_of_sales = clone $query;
        $cost_of_sales = $cost_of_sales->whereIn('chart_of_accounts.class', $cost_of_sale_class)->get();

        $revenues = clone $query;
        $revenues = $revenues->whereIn('chart_of_accounts.class', $revenue_class)->get();

        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        if ($from_month == '')
            $from_month = 'all';

        if ($to_month == '')
            $to_month = 'all';
        if ($branch_id == '')
            $branch_id = 'all';
        return view('pages.reports.ap_ar.statements.print_income_statement', [
            'revenues' => $revenues,
            'cost_of_sales' => $cost_of_sales,
            'expenses' => $expenses,
            'from_month' => $from_month,
            'to_month' => $to_month,
            'income_year' => $income_year,
            'branch' => $branch,
            'branch_id' => $branch_id,
        ]);
    }
    public function remittance()
    {
        $users = User::orderBy('user_code')->get();
        return view('pages.reports.ap_ar.remittance.index', compact('users'));
    }
    public function loadRemittance(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $payee_id = $request->payee_id;
        $user_id = $request->user_id;
        if ($user_id == 'all' || $user_id == '')
            $user_id = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($payee_id == 'all' || $payee_id == '')
            $payee_id = '%';
        $query = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id);
        $ledgers = $query->select(
            DB::raw('SUM(credit) AS credit'),
            DB::raw('SUM(debit) AS debit'),
            'number',
            'general_accounts.description',
            'general_account_ledgers.id',
            'users.name'
        )->join('users', 'users.id', 'general_account_ledgers.user_id')
            ->where('model_id', 'LIKE', $payee_id)
            ->where('user_id', 'LIKE', $user_id)
            ->where(function ($ledgers) {
                $ledgers->where('class', 'LIKE', 'A11%')
                    ->orWhere('class', 'LIKE', 'A12%')
                    ->orWhere('class', 'LIKE', 'A13%');
            })
            ->orderBy('number')
            ->groupBy('user_id')
            ->groupBy('number')
            ->get();


        $credit_sum = $query->sum('credit');
        $debit_sum = $query->sum('debit');
        $balance = $credit_sum - $debit_sum;
        $branch = Branch::find($branch_id);
        if ($user_id == '%')
            $user_id = 'all';
        if ($branch_id == '%')
            $branch_id = 'all';
        if ($payee_id == '%')
            $payee_id = 'all';
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.remittance.load', compact('ledgers', 'branch', 'from_date', 'to_date', 'balance', 'branch_id', 'payee_id', 'user_id', 'credit_sum', 'debit_sum'));
    }

    public function printRemittance($from, $to, $branch_id, $payee_id, $user_id)
    {
        if ($user_id == 'all' || $user_id == '')
            $user_id = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($payee_id == 'all' || $payee_id == '')
            $payee_id = '%';
        $query = $this->generalAccountLedgerBy($from, $to, $branch_id);
        $ledgers = $query->select(
            DB::raw('SUM(credit) AS credit'),
            DB::raw('SUM(debit) AS debit'),
            'number',
            'general_accounts.description',
            'general_account_ledgers.id',
            'users.name'
        )->join('users', 'users.id', 'general_account_ledgers.user_id')
            ->where('model_id', 'LIKE', $payee_id)
            ->where('user_id', 'LIKE', $user_id)
            ->orderBy('number')
            ->groupBy('user_id')
            ->groupBy('number')
            ->get();

        $credit_sum = $query->sum('credit');
        $debit_sum = $query->sum('debit');
        $balance = $credit_sum - $debit_sum;
        $balance = $credit_sum - $debit_sum;
        if ($branch_id != '%')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.remittance.print', compact('ledgers', 'branch', 'from', 'to', 'balance', 'credit_sum', 'debit_sum'));
    }
    public function documentStatus()
    {
        return view('pages.reports.ap_ar.document_status.index');
    }
    public function loadDocumentStatus(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $type = $request->type;
        $status = $request->status;
        if ($status == 'all' || $status == '')
            $status = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';

        $query = null;
        if ($type == "Invoice")
            $query = Order::where('branch_id', 'LIKE', $branch_id)
                ->where('status', 'LIKE', $status)->orderBy('order_date', "DESC");
        if ($type == "Payment")
            $query = Payment::where('branch_id', 'LIKE', $branch_id)
                ->where('status', 'LIKE', $status)->orderBy('date', "DESC");
        if ($type == "Receipt")
            $query = Receipt::where('branch_id', 'LIKE', $branch_id)
                ->where('status', 'LIKE', $status)->orderBy('date', "DESC");
        if ($type == "Journal")
            $query = Journal::where('branch_id', 'LIKE', $branch_id)
                ->where('status', 'LIKE', $status)->orderBy('date', "DESC");
        if ($type == "Interbank")
            $query = InterBank::where('branch_id', 'LIKE', $branch_id)
                ->where('status', 'LIKE', $status)->orderBy('date', "DESC");

        $payments = $query->get();

        if ($status == '%')
            $status = 'all';
        $branch = null;
        if ($branch_id != 'all' || $status != '%')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.document_status.load', compact('payments', 'branch', 'from_date', 'to_date', 'branch_id', 'type', 'status'));
    }

}
