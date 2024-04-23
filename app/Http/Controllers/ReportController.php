<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\CreditNote;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountLedger;
use App\Models\OrderInvoice;
use App\Models\ReturnDebit;
use App\Models\StoreProductBatch;
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
use App\Models\Company;





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
            ->selectRaw(
                "products.name,
                products.code AS product_code,
                stores.code as store_code,
                store_products.qty_available,
                branch_product_prices.retail_selling_price,
                branch_product_prices.whole_selling_price,

                branch_product_prices.cost_price,
                store_products.id"
            )
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', $branch_id)
            ->where('branch_product_prices.branch_id', $branch_id)
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
            ->where('stores.branch_id', $branch_id)
            ->where('branch_product_prices.branch_id', $branch_id)
            ->orderBy('products.name')
            ->get();
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.print_current_stock', ['stores' => $stores, 'branch' => $branch]);
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
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->groupBy('stores.branch_id')
            ->groupBy('store_products.product_id')
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
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->groupBy('stores.branch_id')
            ->groupBy('store_products.product_id')
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
            ->select('products.name', 'products.code', 'stores.name as store', 'quantity', 'date', 'reference', 'operation')
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
            ->select('products.name', 'products.code', 'stores.name as store', 'quantity', 'date', 'reference', 'operation')
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

        // $sales = DB::table('orders')
        //     ->select(
        //         'categories.name AS category',
        //         'categories.code AS code',
        //         DB::raw('SUM(order_details.quantity) AS quantity'),
        //         DB::raw('SUM(order_details.total) AS amount'),
        //         DB::raw('SUM(order_details.cost_price * order_details.quantity) AS cost'),
        //         DB::raw('SUM(store_products.qty_available) AS qty_available'),
        //     )
        //     ->join('order_details', 'order_details.order_id', 'orders.id')
        //     ->join('store_products', 'store_products.id', 'order_details.store_product_id')
        //     ->join('stores', 'stores.id', 'store_products.store_id')
        //     ->join('products', 'products.id', 'store_products.product_id')
        //     ->join('categories', 'categories.id', 'products.category_id')
        //     ->where('stores.branch_id', 'LIKE', $branch_id)
        //     ->where('order_details.status', 1)
        //     ->where(DB::raw("DATE(order_date)"), '>=', $from_date)
        //     ->where(DB::raw("DATE(order_date)"), '<=', $to_date);
        // if ($category_id2 == '%' && $category_id1 != '%') {
        //     $sales = $sales->where('products.category_id', 'LIKE', $category_id1);
        // } elseif ($category_id2 != '%') {
        //     $sales = $sales->where('products.category_id', '>=', $category_id1)
        //         ->where('products.category_id', '<=', $category_id2);
        // }
        // $sales = $sales->orderBy('code')->orderBy('order_date')
        //     ->groupBy('products.category_id')
        //     ->get();

        $data = DB::table('orders')
            ->select(
                'categories.name as category',
                'categories.code as code',
                DB::raw('SUM(order_details.quantity) as quantity'),
                DB::raw('SUM(order_details.total) as amount'),
                DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
                DB::raw('(SELECT SUM(store_products.qty_available) FROM store_products
                    JOIN stores ON store_products.store_id = stores.id
                    JOIN products ON store_products.product_id = products.id
                    WHERE stores.branch_id LIKE ' . $branch_id . '
                        AND products.category_id = categories.id) as qty_available')
            )
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
            ->join('stores', 'store_products.store_id', '=', 'stores.id')
            ->join('products', 'store_products.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('order_details.status', '=', 1)
            ->whereDate('order_date', '>=', $from_date)
            ->whereDate('order_date', '<=', $to_date);
        if ($category_id2 == '%' && $category_id1 != '%') {
            $sales = $data->where('products.category_id', 'LIKE', $category_id1);
        } elseif ($category_id2 != '%') {
            $sales = $data->where('products.category_id', '>=', $category_id1)
                ->where('products.category_id', '<=', $category_id2);
        }
        $sales = $data->groupBy('products.category_id')
            ->orderBy('code', 'ASC')->get();



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
        $data = DB::table('orders')
            ->select(
                'categories.name as category',
                'categories.code as code',
                DB::raw('SUM(order_details.quantity) as quantity'),
                DB::raw('SUM(order_details.total) as amount'),
                DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
                DB::raw('(SELECT SUM(store_products.qty_available) FROM store_products
                JOIN stores ON store_products.store_id = stores.id
                JOIN products ON store_products.product_id = products.id
                WHERE stores.branch_id LIKE ' . $branch_id . '
                    AND products.category_id = categories.id) as qty_available')
            )
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
            ->join('stores', 'store_products.store_id', '=', 'stores.id')
            ->join('products', 'store_products.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('order_details.status', '=', 1)
            ->whereDate('order_date', '>=', $from_date)
            ->whereDate('order_date', '<=', $to_date);
        if ($category_id2 == '%' && $category_id1 != '%') {
            $sales = $data->where('products.category_id', 'LIKE', $category_id1);
        } elseif ($category_id2 != '%') {
            $sales = $data->where('products.category_id', '>=', $category_id1)
                ->where('products.category_id', '<=', $category_id2);
        }
        $sales = $data->groupBy('products.category_id')
            ->orderBy('code', 'ASC')->get();


        $branch = null;
        if ($branch_id != 'all' && $branch_id != '')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_category_sale_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }
    public function staffSaleReport()
    {
        return view('pages.reports.sales_and_cash_analysis.staff_sale_report');
    }

    public function loadStaffSaleReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $branch_id = $request->branch_id;
        $category_id = $request->category_id;
        $staff_id = $request->staff_id;

        if ($product_id == '') {
            $product_id = '%';
        }
        if ($branch_id == '') {
            $branch_id = '%';
        }
        if ($category_id == '') {
            $category_id = '%';
        }
        if ($store_id == '') {
            $store_id = '%';
        }
        if ($staff_id == '') {
            $staff_id = '%';
        }
        $sales = DB::table('orders')
            ->select('customers.code AS customer', 'orders.reference', 'products.name AS product', 'stores.code AS store', 'order_details.quantity', 'sold_price', 'cost_price', 'users.user_code AS user', 'users.name AS name', 'order_date')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.sold_by', 'LIKE', $staff_id)
            ->where('order_details.status', 1)
            ->where('orders.status', 1)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->join('users', 'users.id', 'orders.sold_by')
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy('order_date')
            ->get();
        $total_cash = Order::where('sold_by', 'LIKE', $staff_id)->where('status', 1)->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])->sum('total');

        if ($branch_id == "%")
            $branch_id = "all";
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($staff_id == "%")
            $staff_id = "all";
        $user = User::find($staff_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_staff_sale_report', compact('sales', 'from_date', 'to_date', 'branch', 'branch_id', 'product_id', 'store_id', 'category_id', 'staff_id', 'total_cash', 'user'));
    }

    public function printStaffSaleReport($from_date, $to_date, $branch_id, $store_id, $category_id, $product_id, $staff_id)
    {
        if ($branch_id == 'all') {
            $product_id = '%';
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
        if ($staff_id == 'all') {
            $staff_id = '%';
        }


        $sales = DB::table('orders')
            ->select('customers.code AS customer', 'orders.reference', 'products.name AS product', 'stores.code AS store', 'order_details.quantity', 'sold_price', 'cost_price', 'users.user_code AS user', 'users.name AS name', 'order_date')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.sold_by', 'LIKE', $staff_id)
            ->where('order_details.status', 1)
            ->where('orders.status', 1)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->join('users', 'users.id', 'orders.sold_by')
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->orderBy('order_date')
            ->get();
        $total_cash = Order::where('sold_by', 'LIKE', $staff_id)->where('status', 1)->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])->sum('total');
        $user = User::find($staff_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_staff_sale_report', compact('sales', 'from_date', 'to_date', 'branch', 'total_cash', 'user'));
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
            'charged_account'
        )
            ->join('products', 'products.id', 'stock_cards.product_id')
            ->join('stores', 'stores.id', 'stock_cards.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stock_cards.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('stock_cards.store_id', 'LIKE', $store_id)
            ->whereBetween('stock_cards.date', [$from_date, $to_date])
            ->orderBy('stock_cards.date', 'DESC')
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
            'charged_account'
        )
            ->join('products', 'products.id', 'stock_cards.product_id')
            ->join('stores', 'stores.id', 'stock_cards.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stock_cards.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('stock_cards.store_id', 'LIKE', $store_id)
            ->whereBetween('stock_cards.date', [$from_date, $to_date])
            ->orderBy('stock_cards.updated_at', 'DESC')
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
            ->select('products.name AS item', 'products.code AS code', DB::raw("SUM(order_details.quantity) AS quantity"), DB::raw("SUM(order_details.quantity * cost_price) AS total_cost"), DB::raw("SUM(order_details.total) AS total"), DB::raw("SUM(order_details.quantity * cost_price) - SUM(order_details.total) AS margin"))
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
        if ($type == 'mgn')
            $sales = $sales->orderBy('margin', 'ASC');

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
            ->select('products.name AS item', 'products.code AS code', DB::raw("SUM(order_details.quantity) AS quantity"), DB::raw("SUM(order_details.quantity * cost_price) AS total_cost"), DB::raw("SUM(order_details.total) AS total"), DB::raw("SUM(order_details.quantity * cost_price) - SUM(order_details.total) AS margin"))
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
        if ($type == 'mgn')
            $sales = $sales->orderBy('margin', 'ASC');
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
        return view('pages.reports.sales_and_cash_analysis.item_sold_report');
    }

    public function loadItemSoldReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $product_id = $request->product_id;
        $category_id = $request->category_id;
        $customer_id = $request->customer_id;
        $credit_walkedin = $request->credit_walkedin;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';
        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';
        }

        if ($customer_id == 'all' || $customer_id == '') {
            $customer_id = '%';
        }

        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'products.name AS product', 'products.code', 'stores.code AS store', DB::raw("SUM(order_details.quantity) AS quantity"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->groupBy('store_products.product_id')
            ->groupBy('orders.customer_id')
            ->orderBy('quantity', "ASC")
            ->orderBy('products.code')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($customer_id == "%")
            $customer_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.load_item_sold_report', compact('sales', 'from_date', 'to_date', 'branch', 'branch_id', 'product_id', 'category_id', 'customer_id'));
    }

    public function printItemSoldReport($from_date, $to_date, $branch_id, $category_id, $product_id, $customer_id)
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

        if ($customer_id == 'all' || $customer_id == '') {
            $customer_id = '%';
        }

        $sales = DB::table('orders')
            ->select('customers.name AS customer', 'products.name AS product', 'products.code', 'stores.code AS store', DB::raw("SUM(order_details.quantity) AS quantity"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->groupBy('store_products.product_id')
            ->groupBy('orders.customer_id')
            ->orderBy('quantity', "ASC")
            ->orderBy('products.code')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($product_id == "%")
            $product_id = "all";
        $branch = null;
        if ($branch_id != "%")
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_item_sold_report', compact('sales', 'from_date', 'branch', 'to_date', 'category_id', 'product_id'));
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

        if ($customer_id == 'all' || $customer_id == '') {
            $customer_id = '%';
        }

        $sales = GeneralAccountLedger::where('model_id', 'LIKE', $customer_id)
            ->join('customers', 'customers.id', '=', 'general_account_ledgers.model_id')
            //->whereBetween('date', [$from_date, $to_date])
            ->whereDate('date', '<=', $to_date)
            ->where('customers.branch_id', 'LIKE', User::userBranchAction())
            ->select(
                'customers.name AS customer',
                'customers.code AS code',
                DB::raw('SUM(credit) AS total'),
                DB::raw('SUM(debit) AS pay'),
                DB::raw('SUM(credit) - SUM(debit) AS due')
            )
            ->havingRaw('SUM(credit) - SUM(debit) < 0')
            ->orderBy('customers.name')
            ->groupBy('general_account_ledgers.model_id')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.load_customer_total_debt_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }

    public function printCustomerDebtReport($from_date, $to_date, $customer_id)
    {

        if ($customer_id == 'all' || $customer_id == 'all') {
            $customer_id = '%';
        }
        $sales = GeneralAccountLedger::where('model_id', 'LIKE', $customer_id)
            ->join('customers', 'customers.id', '=', 'general_account_ledgers.model_id')
            //->whereBetween('date', [$from_date, $to_date])
            ->whereDate('date', '<=', $to_date)
            ->where('customers.branch_id', 'LIKE', User::userBranchAction())
            ->select(
                'customers.name AS customer',
                'customers.code AS code',
                DB::raw('SUM(credit) AS total'),
                DB::raw('SUM(debit) AS pay'),
                DB::raw('SUM(credit) - SUM(debit) AS due')
            )
            ->havingRaw('SUM(credit) - SUM(debit) < 0')
            ->orderBy('customers.name')
            ->groupBy('general_account_ledgers.model_id')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        return view('pages.reports.customer_ledger_analysis.print_customer_total_debt_report', compact('sales', 'from_date', 'to_date', 'customer_id'));
    }


    public function ageingReport()
    {
        return view('pages.reports.customer_ledger_analysis.ageing_report');
    }

    public function loadAgeingReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $customer_id = $request->customer_id;
        $branch_id = $request->branch_id;

        if ($customer_id == 'all' || $customer_id == '') {
            $customer_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        $sales = DB::table('customers')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit)-SUM(general_account_ledgers.debit) AS balance'),
                'reference',
                'description',
                'date',
                'customers.name',
                'customers.code',
                'model_id AS customer_id',
                'users.name AS relation_officer',
                DB::raw('DATEDIFF(NOW(), general_account_ledgers.date) AS age')
            )
            ->join('general_account_ledgers', 'general_account_ledgers.model_id', '=', 'customers.id')
            ->leftJoin('users', 'users.id', '=', 'customers.relation_officer')
            ->where('general_account_ledgers.model_id', 'LIKE', $customer_id)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('general_account_ledgers.model_name', 'LIKE', 'Customer')
            ->groupBy('model_id')
            ->having(DB::raw('SUM(general_account_ledgers.credit) - SUM(general_account_ledgers.debit)'), '<', 0);

        switch ($to_date) {
            case 1:
                $lowerdays = 0;
                $upperdays = 7;
                break;
            case 2:
                $lowerdays = 8;
                $upperdays = 14;
                break;
            case 3:
                $lowerdays = 15;
                $upperdays = 30;
                break;
            case 4:
                $lowerdays = 31;
                $upperdays = 60;
                break;
            case 5:
                $lowerdays = 61;
                $upperdays = 90;
                break;
            case 6:
                $lowerdays = 91;
                $upperdays = 120;
                break;
            case 7:
                $lowerdays = 121;
                $upperdays = 180;
                break;
            case 8:
                $lowerdays = 181;
                $upperdays = null; // No upper limit
                break;
            default:
                $lowerdays = 1;
                $upperdays = null; // Default to all data
                break;
        }

        if ($lowerdays != null) {
            $sales = $sales->whereRaw('DATEDIFF(NOW(), general_account_ledgers.date) >= ?', [$lowerdays]);
        }

        if ($upperdays != null) {
            $sales = $sales->whereRaw('DATEDIFF(NOW(), general_account_ledgers.date) <= ?', [$upperdays]);
        }
        // if ($upperdays == null && $lowerdays == null) {
        //     $sales = $sales->whereRaw('DATEDIFF(NOW(), general_account_ledgers.date) > ?', [0]);
        // }

        $sales = $sales->orderBy('age', 'DESC')->get();




        if ($customer_id == "%")
            $customer_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        if ($from_date == null)
            $from_date = "all";
        if ($to_date == null)
            $to_date = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.load_ageing_report', compact('sales', 'from_date', 'branch', 'to_date', 'branch_id', 'customer_id'));
    }

    public function printAgeingReport($from_date, $to_date, $branch_id, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        $sales = DB::table('customers')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit) - SUM(general_account_ledgers.debit) AS balance'),
                'reference',
                'description',
                'date',
                'customers.name',
                'customers.code',
                'model_id AS customer_id',
                'users.name AS relation_officer',
                DB::raw('DATEDIFF(NOW(), general_account_ledgers.date) AS age')
            )
            ->join('general_account_ledgers', 'general_account_ledgers.model_id', '=', 'customers.id')
            ->leftJoin('users', 'users.id', '=', 'customers.relation_officer')
            ->where('general_account_ledgers.model_id', 'LIKE', $customer_id)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('general_account_ledgers.model_name', 'LIKE', 'Customer')
            ->groupBy('model_id')
            ->having(DB::raw('SUM(general_account_ledgers.credit)-SUM(general_account_ledgers.debit)'), '<', 0);

        switch ($to_date) {
            case 1:
                $lowerdays = 0;
                $upperdays = 7;
                break;
            case 2:
                $lowerdays = 8;
                $upperdays = 14;
                break;
            case 3:
                $lowerdays = 15;
                $upperdays = 30;
                break;
            case 4:
                $lowerdays = 31;
                $upperdays = 60;
                break;
            case 5:
                $lowerdays = 61;
                $upperdays = 90;
                break;
            case 6:
                $lowerdays = 91;
                $upperdays = 120;
                break;
            case 7:
                $lowerdays = 121;
                $upperdays = 180;
                break;
            case 8:
                $lowerdays = 181;
                $upperdays = null; // No upper limit
                break;
            default:
                $lowerdays = 1;
                $upperdays = null; // Default to all data
                break;
        }

        if ($lowerdays != null) {
            $sales = $sales->whereRaw('DATEDIFF(NOW(), general_account_ledgers.date) >= ?', [$lowerdays]);
        }

        if ($upperdays != null) {
            $sales = $sales->whereRaw('DATEDIFF(NOW(), general_account_ledgers.date) <= ?', [$upperdays]);
        }

        // if ($upperdays == null && $lowerdays == null) {
        //     $sales = $sales->whereRaw('DATEDIFF(NOW(), general_account_ledgers.date) > ?', [0]);
        // }

        $sales = $sales->orderBy('age', 'DESC')->get();
        if ($customer_id == "%")
            $customer_id = "all";
        if ($from_date == null)
            $from_date = "all";
        if ($to_date == null)
            $to_date = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.print_ageing_report', compact('sales', 'from_date', 'to_date', 'branch', 'branch_id', 'customer_id'));
    }

    public function lastTransaction()
    {
        return view('pages.reports.customer_ledger_analysis.customer_last_transaction_report');
    }

    public function loadLastTransaction(Request $request)
    {
        $branch_id = $request->branch_id;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all' || $customer_id == '') {
            $customer_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        $sales = DB::table('customers')
            ->select('customers.name AS customer', DB::raw('SUM(general_account_ledgers.credit) - SUM(general_account_ledgers.debit) AS balance'), 'customers.code', 'customers.id AS customer_id')
            ->join('general_account_ledgers', 'general_account_ledgers.model_id', 'customers.id')
            ->where('general_account_ledgers.model_id', 'LIKE', $customer_id)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('model_name', 'Customer')
            ->groupBy('general_account_ledgers.model_id')
            ->orderBy('general_account_ledgers.date')
            ->get();



        if ($customer_id == "%")
            $customer_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.load_customer_last_transaction_report', compact('sales', 'branch', 'branch_id', 'customer_id'));
    }

    public function printLastTransaction($branch_id, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        $sales = DB::table('customers')
            ->select('customers.name AS customer', DB::raw('SUM(general_account_ledgers.credit) - SUM(general_account_ledgers.debit) AS balance'), 'customers.code', 'customers.id AS customer_id')
            ->join('general_account_ledgers', 'general_account_ledgers.model_id', 'customers.id')
            ->where('general_account_ledgers.model_id', 'LIKE', $customer_id)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('model_name', 'Customer')
            ->groupBy('general_account_ledgers.model_id')
            ->orderBy('general_account_ledgers.date')
            ->get();
        if ($customer_id == "%")
            $customer_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.print_customer_last_transaction_report', compact('sales', 'branch', 'customer_id'));
    }


    public function creditNoteReport()
    {
        return view('pages.reports.sales_and_cash_analysis.credit_notes_report');
    }

    public function loadCreditNoteReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $status = $request->status;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = CreditNote::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_credit_notes_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'status', 'branch'));
    }

    public function printCreditNoteReport($from_date, $to_date, $branch_id, $status)
    {

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = CreditNote::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_credit_notes_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }
    public function invoiceReport()
    {
        return view('pages.reports.sales_and_cash_analysis.list_of_invoices_report');
    }

    public function loadInvoiceReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $status = $request->status;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = Order::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_list_of_invoices_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'status', 'branch'));
    }
    public function invoiceLinesReport()
    {
        return view('pages.reports.sales_and_cash_analysis.invoice_lines_report');
    }

    public function loadInvoiceLinesReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $status = $request->status;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = Order::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_invoice_lines_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'status', 'branch'));
    }
    public function printInvoiceLinesReport($from_date, $to_date, $branch_id, $status)
    {

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = Order::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_invoice_lines_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }

    public function orderReport()
    {
        return view('pages.reports.sales_and_cash_analysis.orders_report');
    }

    public function loadOrderReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $status = $request->status;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = OrderInvoice::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_orders_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'status', 'branch'));
    }

    public function printOrderReport($from_date, $to_date, $branch_id, $status)
    {

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = OrderInvoice::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_orders_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }
    public function orderLinesReport()
    {
        return view('pages.reports.sales_and_cash_analysis.order_lines_report');
    }

    public function loadOrderLinesReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $status = $request->status;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = OrderInvoice::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_order_lines_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'status', 'branch'));
    }

    public function printOrderLinesReport($from_date, $to_date, $branch_id, $status)
    {

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = OrderInvoice::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_order_lines_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }

    public function purchaseInvoiceLinesReport()
    {
        return view('pages.reports.inventory.purchase_invoice_lines_report');
    }

    public function loadPurchaseInvoiceLinesReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $supplier_id = $request->supplier_id;
        $branch_id = $request->branch_id;
        $status = $request->status;

        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';
        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';
        }
        if ($store_id == 'all' || $store_id == '') {
            $store_id = '%';
        }
        if ($supplier_id == 'all' || $supplier_id == '') {
            $supplier_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }


        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'stores.code AS store', 'purchase_products.quantity AS quantity', 'unit_price', 'purchases.purchase_date', 'wbno')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('purchase_products.product_id', 'LIKE', $product_id)
            ->where('purchase_products.store_id', 'LIKE', $store_id)
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.status', 'LIKE', $status)
            ->where('purchases.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($status == "%")
            $status = "all";
        if ($product_id == "%")
            $product_id = "all";
        if ($store_id == "%")
            $store_id = "all";
        if ($supplier_id == "%")
            $supplier_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.load_purchase_invoice_lines_report', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'supplier_id', 'branch_id', 'branch', 'status'));
    }

    public function printPurchaseInvoiceLinesReport($from_date, $to_date, $branch_id, $store_id, $category_id, $product_id, $supplier_id, $status)
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
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($status == 'all') {
            $status = '%';
        }
        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'stores.code AS store', 'purchase_products.quantity AS quantity', 'unit_price', 'purchases.purchase_date', 'wbno')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('purchase_products.product_id', 'LIKE', $product_id)
            ->where('purchase_products.store_id', 'LIKE', $store_id)
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.status', 'LIKE', $status)
            ->where('purchases.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.print_purchase_invoice_lines_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }

    public function returnDebitReport()
    {
        return view('pages.reports.inventory.return_debit_report');
    }

    public function loadReturnDebitReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $status = $request->status;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = ReturnDebit::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.load_return_debit_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'status', 'branch'));
    }

    public function printReturnDebitReport($from_date, $to_date, $branch_id, $status)
    {

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = ReturnDebit::where('branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.print_return_debit_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }

    public function expiryReport()
    {
        return view('pages.reports.inventory.expiry_date_report');
    }

    public function loadExpiryReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;


        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        $store_ids = Store::where('branch_id', 'LIKE', $branch_id)->get()->pluck('id')->toArray();
        $sales = StoreProductBatch::whereIn('store_id', $store_ids)
            ->whereBetween(DB::raw("DATE(expiry_date)"), [$from_date, $to_date])
            ->orderBy('expiry_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.load_expiry_date_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'branch'));
    }

    public function printExpiryReport($from_date, $to_date, $branch_id)
    {

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }

        $store_ids = Store::where('branch_id', 'LIKE', $branch_id)->get()->pluck('id')->toArray();
        $sales = StoreProductBatch::whereIn('store_id', $store_ids)
            ->whereBetween(DB::raw("DATE(expiry_date)"), [$from_date, $to_date])
            ->orderBy('expiry_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.print_expiry_date_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }

    public function additionalInvoiceReport()
    {
        return view('pages.reports.inventory.additional_invoice_report');
    }

    public function loadAdditionalInvoiceReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $supplier_id = $request->supplier_id;
        $branch_id = $request->branch_id;
        $status = $request->status;


        if ($supplier_id == 'all' || $supplier_id == '') {
            $supplier_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }

        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'purchases.reference', 'purchase_expenses.reference AS ref', 'stores.code AS store', 'description', 'purchase_expenses.name', 'purchases.purchase_date', 'wbno', 'amount', 'purchase_expenses.status')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('purchase_expenses', 'purchase_expenses.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.status', 'LIKE', $status)
            ->where('purchases.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        if ($supplier_id == "%")
            $supplier_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.load_additional_invoice_report', compact('sales', 'from_date', 'to_date', 'supplier_id', 'branch_id', 'branch', 'status'));
    }

    public function printAdditionalInvoiceReport($from_date, $to_date, $branch_id, $supplier_id, $status)
    {

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($status == 'all') {
            $status = '%';
        }
        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'purchases.reference', 'purchase_expenses.reference AS ref', 'stores.code AS store', 'description', 'purchase_expenses.name', 'purchases.purchase_date', 'wbno', 'amount', 'purchase_expenses.status')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('purchase_expenses', 'purchase_expenses.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.status', 'LIKE', $status)
            ->where('purchases.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.print_additional_invoice_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }


    public function purchaseInvoiceReport()
    {
        return view('pages.reports.inventory.purchase_invoice_report');
    }

    public function loadPurchaseInvoiceReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $supplier_id = $request->supplier_id;
        $branch_id = $request->branch_id;
        $status = $request->status;


        if ($supplier_id == 'all' || $supplier_id == '') {
            $supplier_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }


        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'stores.code AS store', 'purchase_products.quantity AS quantity', 'unit_price', 'purchases.purchase_date', 'wbno', DB::raw('SUM(quantity * unit_price) AS total'), 'purchases.status')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.status', 'LIKE', $status)
            ->where('purchases.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->groupBy('purchase_products.purchase_id')
            ->get();


        if ($status == "%")
            $status = "all";
        if ($supplier_id == "%")
            $supplier_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.load_purchase_invoice_report', compact('sales', 'from_date', 'to_date', 'supplier_id', 'branch_id', 'branch', 'status'));
    }

    public function printPurchaseInvoiceReport($from_date, $to_date, $branch_id, $supplier_id, $status)
    {

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($status == 'all') {
            $status = '%';
        }
        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'stores.code AS store', 'purchase_products.quantity AS quantity', 'unit_price', 'purchases.purchase_date', 'wbno', DB::raw('SUM(quantity * unit_price) AS total'), 'purchases.status')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.status', 'LIKE', $status)
            ->where('purchases.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->groupBy('purchase_products.purchase_id')
            ->get();

        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.print_purchase_invoice_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }
    public function purchaseRequestReport()
    {
        return view('pages.reports.inventory.purchase_request_report');
    }

    public function loadPurchaseRequestReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $category_id = $request->category_id;
        $supplier_id = $request->supplier_id;
        $branch_id = $request->branch_id;
        $status = $request->status;

        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';
        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';
        }

        if ($supplier_id == 'all' || $supplier_id == '') {
            $supplier_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }


        $sales = DB::table('purchase_requests')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'purchase_product_requests.quantity', 'unit_price', 'purchase_requests.purchase_date', 'wbno', 'purchase_requests.status')
            ->join('purchase_product_requests', 'purchase_product_requests.purchase_id', 'purchase_requests.id')
            ->join('suppliers', 'suppliers.id', 'purchase_requests.supplier_id')
            ->join('products', 'products.id', 'purchase_product_requests.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('purchase_product_requests.product_id', 'LIKE', $product_id)
            ->where('purchase_requests.supplier_id', 'LIKE', $supplier_id)
            ->where('purchase_requests.status', 'LIKE', $status)
            ->where('purchase_requests.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        if ($category_id == "%")
            $category_id = "all";
        if ($status == "%")
            $status = "all";
        if ($product_id == "%")
            $product_id = "all";

        if ($supplier_id == "%")
            $supplier_id = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.load_purchase_request_report', compact('sales', 'from_date', 'to_date', 'supplier_id', 'branch_id', 'category_id', 'product_id', 'branch', 'status'));
    }

    public function printPurchaseRequestReport($from_date, $to_date, $branch_id, $category_id, $product_id, $supplier_id, $status)
    {
        if ($product_id == 'all') {
            $product_id = '%';
        }
        if ($category_id == 'all') {
            $category_id = '%';
        }

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($status == 'all') {
            $status = '%';
        }
        $sales = DB::table('purchase_requests')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'purchase_product_requests.quantity', 'unit_price', 'purchase_requests.purchase_date', 'wbno', 'purchase_requests.status')
            ->join('purchase_product_requests', 'purchase_product_requests.purchase_id', 'purchase_requests.id')
            ->join('suppliers', 'suppliers.id', 'purchase_requests.supplier_id')
            ->join('products', 'products.id', 'purchase_product_requests.product_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('purchase_product_requests.product_id', 'LIKE', $product_id)
            ->where('purchase_requests.supplier_id', 'LIKE', $supplier_id)
            ->where('purchase_requests.status', 'LIKE', $status)
            ->where('purchase_requests.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.print_purchase_request_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }
    public function goodsInTransitReport()
    {
        return view('pages.reports.inventory.goods_in_transit_report');
    }

    public function loadGoodsInTransitReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $branch_id = $request->branch_id;
        $status = $request->status;


        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }


        $sales = DB::table('intersite_transfers')
            ->select('vehicle_no', 'description', 'reference', 'products.name AS product', 'products.code AS code', 'intersite_transfer_products.quantity', 'cost_price', 'intersite_transfers.date', 'intersite_transfers.status', 'source_branch.code AS source', 'destination_branch.code AS destination')
            ->join('intersite_transfer_products', 'intersite_transfer_products.intersite_transfer_id', 'intersite_transfers.id')
            ->join('branches as source_branch', 'source_branch.id', '=', 'intersite_transfers.source_branch_id')
            ->join('branches as destination_branch', 'destination_branch.id', '=', 'intersite_transfers.destination_branch_id')
            ->join('products', 'products.id', 'intersite_transfer_products.product_id')
            ->where('intersite_transfers.status', 'LIKE', $status)
            ->where('intersite_transfers.destination_branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(date)"), '>=', $from_date)
            ->where(DB::raw("DATE(date)"), '<=', $to_date)
            ->orderBy('date')
            ->get();

        if ($status == "%")
            $status = "all";
        if ($branch_id == "%")
            $branch_id = "all";
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.load_goods_in_transit_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'branch', 'status'));
    }

    public function printGoodsInTransitReport($from_date, $to_date, $branch_id, $status)
    {

        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($status == 'all') {
            $status = '%';
        }
        $sales = DB::table('intersite_transfers')
            ->select('vehicle_no', 'description', 'reference', 'products.name AS product', 'products.code AS code', 'intersite_transfer_products.quantity', 'cost_price', 'intersite_transfers.date', 'intersite_transfers.status', 'source_branch.code AS source', 'destination_branch.code AS destination')
            ->join('intersite_transfer_products', 'intersite_transfer_products.intersite_transfer_id', 'intersite_transfers.id')
            ->join('branches as source_branch', 'source_branch.id', '=', 'intersite_transfers.source_branch_id')
            ->join('branches as destination_branch', 'destination_branch.id', '=', 'intersite_transfers.destination_branch_id')
            ->join('products', 'products.id', 'intersite_transfer_products.product_id')
            ->where('intersite_transfers.status', 'LIKE', $status)
            ->where('intersite_transfers.destination_branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(date)"), '>=', $from_date)
            ->where(DB::raw("DATE(date)"), '<=', $to_date)
            ->orderBy('date')
            ->get();
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.print_goods_in_transit_report', compact('sales', 'from_date', 'to_date', 'branch'));
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

    public function productValuation()
    {
        return view('pages.reports.inventory.product_valuation.product_valuation_report');
    }

    public function loadProductValuationReport(Request $request)
    {

        $date = $request->date;
        $yesterday = date('Y-m-d', strtotime($date . '-1 days'));
        $product_id = $request->product_id;
        $category_id = $request->category_id;
        $branch_id = $request->branch_id;
        $store_id = $request->store_id;
        $store_group = $request->store_group;
        $stock_cards = StockCard::selectRaw("
            store_id,
            product_id,
            sum(cr) as credit,
            sum(dr) as debit,
            (sum(cr) - sum(dr)) as quantity,
            (
                SELECT cost
                FROM stock_cards sc
                WHERE sc.product_id = stock_cards.product_id
                AND sc.date <= '$date'
                ORDER BY sc.id DESC
                LIMIT 1
            ) as cost
            ")
            ->groupBy('product_id')
            ->where('date', '<=', $date)
            ->with('store', 'product')
            ->havingRaw("(credit - debit) > 0")
            ->orderBy('created_at', 'desc');
        if (!is_null($branch_id)) {
            $store_ids = Store::where('branch_id', $branch_id)->get('id')->toArray();
            $stock_cards->whereIn('store_id', $store_ids);
        }
        if ($store_id != '') {
            $stock_cards->where('store_id', $store_id);
        }
        if ($store_group != '') {
            $stock_cards->groupBy('store_id');
        }
        if ($category_id != '') {
            $product_ids = Product::where('category_id', $category_id)->get('id')->toArray();
            $stock_cards->whereIn('product_id', $product_ids);
        }
        if ($product_id != '') {
            $stock_cards->where('product_id', $product_id);
        }
        $stock_cards = $stock_cards->get();

        /*$sales = DB::table('product_valuations')
            ->select('products.code', 'reference', 'products.name AS product', 'quantity', 'cost_price', 'product_valuations.date', 'stores.code AS store_code')
            ->join('products', 'products.id', 'product_valuations.product_id')
            ->join('stores', 'stores.id', 'product_valuations.store_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('product_valuations.product_id', 'LIKE', $product_id)
            ->where('product_valuations.branch_id', 'LIKE', $branch_id)
            ->where('product_valuations.store_id', 'LIKE', $store_id)
            ->where(DB::raw("DATE(date)"), '>=', $date)

            ->orderBy('date')
            ->get();*/

        if ($category_id == "%")
            $category_id = "";
        if ($product_id == "%")
            $product_id = "";

        if ($branch_id == "%")
            $branch_id = "";
        if ($store_id == "%")
            $store_id = "";
        $branch = null;
        if ($branch_id != "")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.product_valuation.load_product_valuation_report', compact('stock_cards', 'date', 'branch_id', 'store_id', 'category_id', 'product_id', 'branch'));
    }

    public function printProductValuationReport(Request $request)
    {

        $date = $request->date;
        $yesterday = date('Y-m-d', strtotime($date . '-1 days'));
        $product_id = $request->product_id;
        $category_id = $request->category_id;
        $branch_id = $request->branch_id;
        $store_id = $request->store_id;
        $stock_cards = StockCard::selectRaw("
            store_id,
            product_id,
            sum(cr) as credit,
            sum(dr) as debit,
            (sum(cr) - sum(dr)) as quantity,
            (
                SELECT cost
                FROM stock_cards sc
                WHERE sc.product_id = stock_cards.product_id
                AND sc.date <= '$date'
                ORDER BY sc.id DESC
                LIMIT 1
            ) as cost
            ")
            ->groupBy('product_id')
            ->where('date', '<=', $date)
            ->with('store', 'product')
            ->havingRaw("(credit - debit) > 0")
            ->orderBy('created_at', 'desc');
        if (!is_null($branch_id)) {
            $store_ids = Store::where('branch_id', $branch_id)->get('id')->toArray();
            $stock_cards->whereIn('store_id', $store_ids);
        }
        if ($store_id != '') {
            $stock_cards->where('store_id', $store_id);
        }
        if ($category_id != '') {
            $product_ids = Product::where('category_id', $category_id)->get('id')->toArray();
            $stock_cards->whereIn('product_id', $product_ids);
        }
        if ($product_id != '') {
            $stock_cards->where('product_id', $product_id);
        }
        $stock_cards = $stock_cards->get();

        $branch = null;
        if ($branch_id != "")
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.product_valuation.print_product_valuation_report', compact('stock_cards', 'date', 'branch'));
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
            ->select('products.name', 'stores.name as store', 'store_products.qty_available', 'retail_selling_price', 'whole_selling_price', 'cost_price')
            ->distinct()
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'store_products.product_id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $categor_id)
            ->where('store_products.store_id', 'LIKE', $store)
            ->where('store_products.qty_available', '>=', $number)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
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

        $query = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)
            ->orderBy('date')
            ->orderBy('general_account_ledgers.id');
        $ledgers = $query->get();


        // $credit_sum = $query->sum('credit');
        // $debit_sum = $query->sum('debit');
        $credit_sum = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)->sum('credit');
        $debit_sum = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)->sum('debit');


        $sum_cr_b_d = $this->generalAccountLedgerB4D($from_date, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)
            ->where('model_name', 'LIKE', $type)->sum('credit');
        $sum_dr_b_d = $this->generalAccountLedgerB4D($from_date, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)
            ->where('model_name', 'LIKE', $type)->sum('debit');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d;
        $balance = $credit_sum - $debit_sum + $balance_b_d;

        if ($branch_id == '%')
            $branch_id = 'all';
        if ($payer_id == '%')
            $payer_id = 'all';
        if ($type == '%')
            $type = 'all';
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.statements.load_account_statement', compact('ledgers', 'branch', 'from_date', 'to_date', 'balance', 'branch_id', 'payer_id', 'type', 'credit_sum', 'debit_sum', 'sum_cr_b_d', 'sum_dr_b_d', 'balance_b_d'));
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
            $query = GeneralAccountLedger::select(DB::raw('SUM(credit) AS credit'), DB::raw('SUM(debit) AS debit'), 'code AS number', 'customers.name AS description', 'general_account_ledgers.id', 'users.name AS relation_officer')
                ->join('customers', 'customers.id', '=', 'general_account_ledgers.model_id')
                ->leftJoin('users', 'users.id', '=', 'customers.relation_officer')
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

        $query1 = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->whereIn(DB::raw('SUBSTR(general_accounts.number, 1, 1)'), ['R', 'C']);
        $query2 = $this->generalAccountLedgerBy(null, $to_date, $branch_id, 'GeneralAccount')
            ->whereNotIn(DB::raw('SUBSTR(general_accounts.number, 1, 1)'), ['R', 'C']);
        // $query3 = $this->generalAccountLedgerBy(null, $to_date, $branch_id, 'GeneralAccount')
        //     ->where('general_accounts.number', 'A150001');
        // $query4 = $this->generalAccountLedgerBy(null, $to_date, $branch_id, 'GeneralAccount')
        //     ->where('general_accounts.number', 'L220010');
        $ledger1 = $query1->select(
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

        $ledger2 = $query2->select(
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
        $ledger3 = DB::table('general_account_ledgers')
            ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit) AS credit'),
                DB::raw('SUM(general_account_ledgers.debit) AS debit'),
                'general_accounts.description',
                'general_account_ledgers.id'
            )
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->whereDate('general_account_ledgers.date', '<=', $to_date)
            ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Supplier'])
            ->groupBy('model_name')
            ->get();
        $ledger4 = DB::table('general_account_ledgers')
            ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit) AS credit'),
                DB::raw('SUM(general_account_ledgers.debit) AS debit'),
                'general_accounts.description',
                'general_account_ledgers.id'
            )
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->whereDate('general_account_ledgers.date', '<=', $to_date)
            ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Customer'])
            ->groupBy('model_name')
            ->get();

        $credit_sum1 = $query1->sum('credit');
        $debit_sum1 = $query1->sum('debit');
        $balance1 = $credit_sum1 - $debit_sum1;

        $credit_sum2 = $query2->sum('credit');
        $debit_sum2 = $query2->sum('debit');
        $balance2 = $credit_sum2 - $debit_sum2;

        $credit_sum3 = $ledger3->sum('credit');
        $debit_sum3 = $ledger3->sum('debit');
        $balance3 = $credit_sum3 - $debit_sum3;

        $credit_sum4 = $ledger4->sum('credit');
        $debit_sum4 = $ledger4->sum('debit');
        $balance4 = $credit_sum4 - $debit_sum4;

        $branch = null;

        if ($branch_id == '' || $branch_id == '%')
            $branch_id = 'all';
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.trial_balance.load', compact('ledger1', 'ledger2', 'ledger3', 'ledger4', 'branch', 'from_date', 'to_date', 'branch_id', 'balance1', 'credit_sum1', 'debit_sum1', 'balance2', 'credit_sum2', 'debit_sum2', 'balance3', 'credit_sum3', 'debit_sum3', 'balance4', 'credit_sum4', 'debit_sum4'));
    }

    public function printTrialBalance($from, $to, $branch_id)
    {
        $query1 = $this->generalAccountLedgerBy($from, $to, $branch_id, 'GeneralAccount')
            ->whereIn(DB::raw('SUBSTR(general_accounts.number, 1, 1)'), ['R', 'C']);
        $query2 = $this->generalAccountLedgerBy(null, $to, $branch_id, 'GeneralAccount')
            ->whereNotIn(DB::raw('SUBSTR(general_accounts.number, 1, 1)'), ['R', 'C']);
        // $query3 = $this->generalAccountLedgerBy(null, $to_date, $branch_id, 'GeneralAccount')
        //     ->where('general_accounts.number', 'A150001');
        // $query4 = $this->generalAccountLedgerBy(null, $to_date, $branch_id, 'GeneralAccount')
        //     ->where('general_accounts.number', 'L220010');
        $ledger1 = $query1->select(
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

        $ledger2 = $query2->select(
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
        $ledger3 = DB::table('general_account_ledgers')
            ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit) AS credit'),
                DB::raw('SUM(general_account_ledgers.debit) AS debit'),
                'general_accounts.description',
                'general_account_ledgers.id'
            )
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->whereDate('general_account_ledgers.date', '<=', $to)
            ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Supplier'])
            ->groupBy('model_name')
            ->get();
        $ledger4 = DB::table('general_account_ledgers')
            ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit) AS credit'),
                DB::raw('SUM(general_account_ledgers.debit) AS debit'),
                'general_accounts.description',
                'general_account_ledgers.id'
            )
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->whereDate('general_account_ledgers.date', '<=', $to)
            ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Customer'])
            ->groupBy('model_name')
            ->get();

        $credit_sum1 = $query1->sum('credit');
        $debit_sum1 = $query1->sum('debit');
        $balance1 = $credit_sum1 - $debit_sum1;

        $credit_sum2 = $query2->sum('credit');
        $debit_sum2 = $query2->sum('debit');
        $balance2 = $credit_sum2 - $debit_sum2;

        $credit_sum3 = $ledger3->sum('credit');
        $debit_sum3 = $ledger3->sum('debit');
        $balance3 = $credit_sum3 - $debit_sum3;

        $credit_sum4 = $ledger4->sum('credit');
        $debit_sum4 = $ledger4->sum('debit');
        $balance4 = $credit_sum4 - $debit_sum4;

        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.trial_balance.print', compact('ledger1', 'ledger2', 'ledger3', 'ledger4', 'branch', 'from', 'to', 'branch_id', 'balance1', 'credit_sum1', 'debit_sum1', 'balance2', 'credit_sum2', 'debit_sum2', 'balance3', 'credit_sum3', 'debit_sum3', 'balance4', 'credit_sum4', 'debit_sum4'));
    }
    public function balanceSheet()
    {
        $branches = Branch::select(['id', 'name', 'code'])->orderBy('name')->get();

        return view('pages.reports.ap_ar.balance_sheet.index', compact('branches'));
    }
    public function loadBalanceSheet(Request $request)
    {
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;

        $query1 = $this->generalAccountLedgerBy(null, $to_date, $branch_id, 'GeneralAccount');
        $ledger1 = $query1->select(
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
        $ledger2 = DB::table('general_account_ledgers')
            ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit) AS credit'),
                DB::raw('SUM(general_account_ledgers.debit) AS debit'),
                'general_accounts.description',
                'general_account_ledgers.id'
            )
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->whereDate('general_account_ledgers.date', '<=', $to_date)
            ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Supplier'])
            ->groupBy('model_name')
            ->get();
        $ledger3 = DB::table('general_account_ledgers')
            ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit) AS credit'),
                DB::raw('SUM(general_account_ledgers.debit) AS debit'),
                'general_accounts.description',
                'general_account_ledgers.id'
            )
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->whereDate('general_account_ledgers.date', '<=', $to_date)
            ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Customer'])
            ->groupBy('model_name')
            ->get();



        $credit_sum1 = $query1->sum('credit');
        $debit_sum1 = $query1->sum('debit');
        $balance1 = $credit_sum1 - $debit_sum1;


        $branch = null;

        if ($branch_id == '' || $branch_id == '%')
            $branch_id = 'all';
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.balance_sheet.load', compact('ledger1','ledger2','ledger3', 'branch', 'to_date', 'branch_id', 'balance1', 'credit_sum1', 'debit_sum1'));
    }

    public function printBalanceSheet($to, $branch_id)
    {
        $query1 = $this->generalAccountLedgerBy(null, $to, $branch_id, 'GeneralAccount');
        $query1 = $this->generalAccountLedgerBy(null, $to, $branch_id, 'GeneralAccount');
        $ledger1 = $query1->select(
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
        $ledger2 = DB::table('general_account_ledgers')
            ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit) AS credit'),
                DB::raw('SUM(general_account_ledgers.debit) AS debit'),
                'general_accounts.description',
                'general_account_ledgers.id'
            )
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->whereDate('general_account_ledgers.date', '<=', $to)
            ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Supplier'])
            ->groupBy('model_name')
            ->get();
        $ledger3 = DB::table('general_account_ledgers')
            ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->select(
                DB::raw('SUM(general_account_ledgers.credit) AS credit'),
                DB::raw('SUM(general_account_ledgers.debit) AS debit'),
                'general_accounts.description',
                'general_account_ledgers.id'
            )
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->whereDate('general_account_ledgers.date', '<=', $to)
            ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Customer'])
            ->groupBy('model_name')
            ->get();


        $credit_sum1 = $query1->sum('credit');
        $debit_sum1 = $query1->sum('debit');
        $balance1 = $credit_sum1 - $debit_sum1;


        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.balance_sheet.print', compact('ledger1', 'branch', 'to', 'branch_id', 'balance1', 'credit_sum1', 'debit_sum1'));
    }

    public function cashFlow()
    {
        return view('pages.reports.ap_ar.cash_flow.index');
    }
    public function loadCashFlow(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $branch_id = $request->branch_id;
        $company_id = $request->company_id;
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';

        $total_generated = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->whereIn('general_accounts.class', ['A12'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->sum('debit');

        $total_bank_transfer = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->whereIn('general_accounts.class', ['A11'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->sum('debit');

        $total_at_hand = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS total_at_hand')
            ->whereIn('general_accounts.class', ['A12'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->total_at_hand;

        $total_cash_in_bank = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS total_in_bank')
            ->whereIn('general_accounts.class', ['A11'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->total_in_bank;

        $total_amount_expended = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS expended')
            ->whereIn('general_accounts.class', ['C51', 'C52', 'C53', 'C54', 'C55', 'C56', 'C57', 'C58', 'C59', 'C60', 'C61', 'C62', 'C63'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->expended;

        $branch = $company = null;

        if ($branch_id == '' || $branch_id == '%')
            $branch_id = 'all';
        if ($company_id == '' || $company_id == '%')
            $company_id = 'all';
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        if ($company_id != 'all')
            $company = Company::find($company_id);
        return view('pages.reports.ap_ar.cash_flow.load', compact('total_generated', 'total_bank_transfer', 'total_at_hand', 'total_cash_in_bank', 'total_amount_expended', 'branch', 'company', 'from_date', 'to_date', 'branch_id', 'company_id'));
    }

    public function printCashFlow($from_date, $to_date, $branch_id, $company_id)
    {
        $total_generated = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->whereIn('general_accounts.class', ['A12'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->sum('debit');

        $total_bank_transfer = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->whereIn('general_accounts.class', ['A11'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->sum('debit');

        $total_at_hand = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS total_at_hand')
            ->whereIn('general_accounts.class', ['A12'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->total_at_hand;

        $total_cash_in_bank = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS total_in_bank')
            ->whereIn('general_accounts.class', ['A11'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->total_in_bank;

        $total_amount_expended = $this->generalAccountLedgerBy($from_date, $to_date, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS expended')
            ->whereIn('general_accounts.class', ['C51', 'C52', 'C53', 'C54', 'C55', 'C56', 'C57', 'C58', 'C59', 'C60', 'C61', 'C62', 'C63'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->expended;

        $branch = $company = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        if ($company_id != 'all')
            $company = Company::find($company_id);
        return view('pages.reports.ap_ar.cash_flow.print', compact('total_generated', 'total_bank_transfer', 'total_at_hand', 'total_cash_in_bank', 'total_amount_expended', 'branch', 'company', 'from_date', 'to_date'));
    }


    private function generalAccountLedgerBy($from_date, $to_date, $branch_id, $type = null)
    {
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($type != null && $type == "Customer") {
            $query = GeneralAccountLedger::join('customers', 'customers.id', '=', 'general_account_ledgers.model_id')
                ->where('customers.branch_id', 'like', $branch_id)
                ->whereDate('date', '<=', $to_date)
                ->where('model_name', 'Customer');
            if ($from_date != null)
                $query = $query->whereDate('date', '>=', $from_date);
            return $query;
        }
        if ($type != null && $type == "Supplier") {
            $query = GeneralAccountLedger::join('suppliers', 'suppliers.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->whereDate('date', '<=', $to_date)
                ->where('model_name', 'Supplier');
            if ($from_date != null)
                $query = $query->whereDate('date', '>=', $from_date);
            return $query;
        }
        if ($type != null && $type == "GeneralAccount") {
            $query = GeneralAccountLedger::leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->whereDate('date', '<=', $to_date)
                ->where('model_name', 'GeneralAccount');
            if ($from_date != null)
                $query = $query->whereDate('date', '>=', $from_date);
            return $query;
        }
        return GeneralAccountLedger::leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->where('general_account_ledgers.branch_id', 'like', $branch_id)
            ->whereDate('date', '>=', $from_date)
            ->whereDate('date', '<=', $to_date);

    }

    private function generalAccountLedgerB4D($from_date, $branch_id, $type = null)
    {
        //To get account balance before start date
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($type != null && $type == "Customer") {
            return GeneralAccountLedger::join('customers', 'customers.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
                ->whereDate('date', '<', $from_date)
                ->where('model_name', 'Customer');
        }
        if ($type != null && $type == "Supplier") {
            return GeneralAccountLedger::join('suppliers', 'suppliers.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
                ->whereDate('date', '<', $from_date)
                ->where('model_name', 'Supplier');
        }
        if ($type != null && $type == "GeneralAccount") {
            return GeneralAccountLedger::join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
                ->whereDate('date', '<', $from_date)
                ->where('model_name', 'GeneralAccount');
        }

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
        $category_id1 = $request->category_id1;
        $category_id2 = $request->category_id2;

        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($category_id1 == 'all' || $category_id1 == '')
            $category_id1 = '%';
        if ($category_id2 == 'all' || $category_id2 == '')
            $category_id2 = '%';
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
            ->where('model_name', 'GeneralAccount')->groupBy('number');

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
        $cost_of_sales = $cost_of_sales->whereIn('chart_of_accounts.class', $cost_of_sale_class)
            ->leftjoin('categories', 'categories.cost_account', 'general_accounts.id');
        if ($category_id2 == '%' && $category_id1 != '%') {
            $cost_of_sales = $cost_of_sales->where('categories.id', 'LIKE', $category_id1);
        } elseif ($category_id2 != '%') {
            $cost_of_sales = $cost_of_sales->where('categories.id', '>=', $category_id1)
                ->where('categories.id', '<=', $category_id2);
        }
        $cost_of_sales = $cost_of_sales->get();

        $revenues = clone $query;
        $revenues = $revenues->whereIn('chart_of_accounts.class', $revenue_class)
            ->leftjoin('categories', 'categories.revenue_account', 'general_accounts.id');
        if ($category_id2 == '%' && $category_id1 != '%') {
            $revenues = $revenues->where('categories.id', 'LIKE', $category_id1);
        } elseif ($category_id2 != '%') {
            $revenues = $revenues->where('categories.id', '>=', $category_id1)
                ->where('categories.id', '<=', $category_id2);
        }
        $revenues = $revenues->get();

        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        if ($from_month == '')
            $from_month = 'all';

        if ($to_month == '')
            $to_month = 'all';
        if ($branch_id == '' || $branch_id == '%')
            $branch_id = 'all';
        if ($category_id1 == '' || $category_id1 == '%')
            $category_id1 = 'all';
        if ($category_id1 == '' || $category_id2 == '%')
            $category_id1 = 'all';
        return view('pages.reports.ap_ar.statements.load_income_statement', [
            'revenues' => $revenues,
            'cost_of_sales' => $cost_of_sales,
            'expenses' => $expenses,
            'from_month' => $from_month,
            'to_month' => $to_month,
            'income_year' => $income_year,
            'branch' => $branch,
            'branch_id' => $branch_id,
            'category_id1' => $category_id1,
            'category_id2' => $category_id2
        ]);
    }
    public function printIncomeStatement($from_month, $to_month, $income_year, $branch_id, $category_id1, $category_id2)
    {
        if ($branch_id == 'all')
            $branch_id = '%';
        if ($category_id1 == 'all')
            $category_id1 = '%';
        if ($category_id2 == 'all')
            $category_id2 = '%';
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

        if ($from_month == 'all' || $to_month == 'all') {
            $query->whereMonth('date', '<=', 12);
        }

        if ($from_month != 'all') {
            $query->whereMonth('date', '>=', $from_month);
        }

        if ($to_month != 'all') {
            $query->whereMonth('date', '<=', $to_month);
        }

        $query->orderBy('number');

        $expenses = clone $query;
        $expenses = $expenses->whereIn('chart_of_accounts.class', $expense_class)->get();

        $cost_of_sales = clone $query;
        $cost_of_sales = $cost_of_sales->whereIn('chart_of_accounts.class', $cost_of_sale_class)
            ->leftjoin('categories', 'categories.cost_account', 'general_accounts.id');
        if ($category_id2 == '%' && $category_id1 != '%') {
            $cost_of_sales = $cost_of_sales->where('categories.id', 'LIKE', $category_id1);
        } elseif ($category_id2 != '%') {
            $cost_of_sales = $cost_of_sales->where('categories.id', '>=', $category_id1)
                ->where('categories.id', '<=', $category_id2);
        }
        $cost_of_sales = $cost_of_sales->get();

        $revenues = clone $query;
        $revenues = $revenues->whereIn('chart_of_accounts.class', $revenue_class)
            ->leftjoin('categories', 'categories.revenue_account', 'general_accounts.id');
        if ($category_id2 == '%' && $category_id1 != '%') {
            $revenues = $revenues->where('categories.id', 'LIKE', $category_id1);
        } elseif ($category_id2 != '%') {
            $revenues = $revenues->where('categories.id', '>=', $category_id1)
                ->where('categories.id', '<=', $category_id2);
        }
        $revenues = $revenues->get();


        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        if ($from_month == '')
            $from_month = 'all';

        if ($to_month == '')
            $to_month = 'all';
        if ($branch_id == '')
            $branch_id = 'all';
        if ($category_id1 == '')
            $category_id1 = 'all';
        if ($category_id2 == '')
            $category_id2 = 'all';
        return view('pages.reports.ap_ar.statements.print_income_statement', [
            'revenues' => $revenues,
            'cost_of_sales' => $cost_of_sales,
            'expenses' => $expenses,
            'from_month' => $from_month,
            'to_month' => $to_month,
            'income_year' => $income_year,
            'branch' => $branch,
            'branch_id' => $branch_id,
            'category_id1' => $category_id1,
            'category_id2' => $category_id2
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
                ->where('status', 'LIKE', $status)->orderBy('order_date', "DESC")
                ->whereDate('order_date', '>=', $from_date)
                ->whereDate('order_date', '<=', $to_date);
        if ($type == "Payment")
            $query = Payment::where('branch_id', 'LIKE', $branch_id)
                ->where('status', 'LIKE', $status)->orderBy('date', "DESC")
                ->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date);
        if ($type == "Receipt")
            $query = Receipt::where('branch_id', 'LIKE', $branch_id)
                ->where('status', 'LIKE', $status)->orderBy('date', "DESC")
                ->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date);
        if ($type == "Journal")
            $query = Journal::where('branch_id', 'LIKE', $branch_id)
                ->where('status', 'LIKE', $status)->orderBy('date', "DESC")
                ->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date);
        if ($type == "Interbank")
            $query = InterBank::where('branch_id', 'LIKE', $branch_id)
                ->where('status', 'LIKE', $status)->orderBy('date', "DESC")
                ->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date);

        $payments = $query->get();

        if ($status == '%')
            $status = 'all';
        $branch = null;
        if ($branch_id != 'all' || $branch_id != '%')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.document_status.load', compact('payments', 'branch', 'from_date', 'to_date', 'branch_id', 'type', 'status'));
    }
    public function customerList(Request $request)
    {
        return view('pages.reports.customer_ledger_analysis.customer_list_report');
    }
    public function loadCustomerListReport(Request $request)
    {
        $branch_id = $request->branch_id;
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::where('branch_id', 'LIKE', $branch_id)
            ->orderBy('code')
            ->orderBy('name')
            ->get();
        if ($branch_id == '%')
            $branch_id = 'all';
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.load_customer_list_report', compact('customers', 'branch', 'branch_id'));
    }
    public function printCustomerListReport($branch_id)
    {
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::where('branch_id', 'LIKE', $branch_id)
            ->orderBy('code')
            ->orderBy('name')
            ->get();
        if ($branch_id == '%')
            $branch_id = 'all';
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.print_customer_list_report', compact('customers', 'branch', 'branch_id'));
    }
    public function customerCreditLimit(Request $request)
    {
        return view('pages.reports.customer_ledger_analysis.customer_with_credit_limit_report');
    }
    public function loadCustomerCreditLimitReport(Request $request)
    {
        $branch_id = $request->branch_id;
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::where('branch_id', 'LIKE', $branch_id)
            ->where('credit_limit', '>', 0)
            ->orderBy('code')
            ->orderBy('name')
            ->get();
        if ($branch_id == '%')
            $branch_id = 'all';
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.load_customer_with_credit_limit_report', compact('customers', 'branch', 'branch_id'));
    }
    public function printCustomerCreditLimitReport($branch_id)
    {
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::where('branch_id', 'LIKE', $branch_id)
            ->where('credit_limit', '>', 0)
            ->orderBy('code')
            ->orderBy('name')
            ->get();
        if ($branch_id == '%')
            $branch_id = 'all';
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.print_customer_with_credit_limit_report', compact('customers', 'branch', 'branch_id'));
    }
    public function customerExceededCreditLimit(Request $request)
    {
        return view('pages.reports.customer_ledger_analysis.customer_exceed_credit_limit_report');
    }
    public function loadCustomerExceededCreditLimitReport(Request $request)
    {
        $branch_id = $request->branch_id;
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::select(DB::raw("ABS(SUM(credit) - SUM(debit)) as balance"), 'customers.*')
            ->where('customers.branch_id', 'LIKE', $branch_id)
            ->where('credit_limit', '>', 0)
            ->join('general_account_ledgers', 'general_account_ledgers.model_id', '=', 'customers.id')
            ->groupBy('model_id')
            ->havingRaw('ABS(SUM(credit) - SUM(debit)) > credit_limit')
            ->orderBy('code')
            ->orderBy('name')
            ->get();

        if ($branch_id == '%')
            $branch_id = 'all';
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.load_customer_exceed_credit_limit_report', compact('customers', 'branch', 'branch_id'));
    }
    public function printCustomerExceededCreditLimitReport($branch_id)
    {
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::select(DB::raw("ABS(SUM(credit) - SUM(debit)) as balance"), 'customers.*')
            ->where('customers.branch_id', 'LIKE', $branch_id)
            ->where('credit_limit', '>', 0)
            ->join('general_account_ledgers', 'general_account_ledgers.model_id', '=', 'customers.id')
            ->groupBy('model_id')
            ->havingRaw('ABS(SUM(credit) - SUM(debit)) > credit_limit')
            ->orderBy('code')
            ->orderBy('name')
            ->get();
        if ($branch_id == '%')
            $branch_id = 'all';
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.print_customer_exceed_credit_limit_report', compact('customers', 'branch', 'branch_id'));
    }

}
