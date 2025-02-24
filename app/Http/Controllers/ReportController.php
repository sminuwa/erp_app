<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\CreditNote;
use App\Models\GeneralAccountLedger;
use App\Models\GeneralAccount;
use App\Models\IntersiteTransfer;
use App\Models\InterstoreTransfer;
use App\Models\OrderInvoice;
use App\Models\ReturnDebit;
use App\Models\StoreProductBatch;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Order;
use App\Models\Loan;
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
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Exports\SalesBySiteReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    public function intersiteTransfer()
    {
        return view('pages.reports.stock_control.intersite_stock_transfer_report', [
            'branches' => Branch::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadIntersiteTransferReport(Request $request)
    {
        //return $request->stock_in_out;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $source_branch_id = $request->source_branch_id;
        $destination_branch_id = $request->destination_branch_id;
        $category_id = $request->category_id;
        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';

        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';
        }
        if ($source_branch_id == 'all' || $source_branch_id == '') {
            $source_branch_id = '%';
        }
        if ($destination_branch_id == 'all' || $destination_branch_id == '') {
            $destination_branch_id = '%';
        }

        $query = IntersiteTransfer::select(
            'products.name AS product_name',
            'products.code AS product_code',
            'products.unit AS product_unit',
            'quantity',
            'cost_price',
            'reference',
            'vehicle_no',
            'source_branch_id',
            'destination_branch_id',
            'users.name AS created_by',
            'intersite_transfers.created_at'
        )
            ->where('intersite_transfer_products.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('source_branch_id', 'LIKE', $source_branch_id)
            ->where('destination_branch_id', 'LIKE', $destination_branch_id)
            ->join('intersite_transfer_products', 'intersite_transfer_products.intersite_transfer_id', 'intersite_transfers.id')
            ->join('stores', 'stores.id', 'intersite_transfers.source_branch_id')
            ->join('products', 'products.id', 'intersite_transfer_products.product_id')
            ->join('users', 'users.id', 'intersite_transfers.created_by')
            ->whereBetween('intersite_transfers.date', [$from_date, $to_date]);

        $transfers = $query->get();
        if ($product_id == '%') {
            $product_id = 'all';

        }
        if ($category_id == '%') {
            $category_id = 'all';

        }
        if ($source_branch_id == '%') {
            $source_branch_id = 'all';

        }
        if ($destination_branch_id == '%') {
            $destination_branch_id = 'all';

        }

        return view('pages.reports.stock_control.load_intersite_stock_transfer_report', compact('transfers', 'from_date', 'to_date', 'product_id', 'source_branch_id', 'destination_branch_id', 'category_id'));
    }

    public function printIntersiteTransfer($from_date, $to_date, $source_branch_id, $destination_branch_id, $category_id, $product_id)
    {
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($source_branch_id == 'all') {
            $source_branch_id = '%';

        }
        if ($destination_branch_id == 'all') {
            $destination_branch_id = '%';

        }

        $query = IntersiteTransfer::select(
            'products.name AS product_name',
            'products.code AS product_code',
            'quantity',
            'cost_price',
            'reference',
            'vehicle_no',
            'source_branch_id',
            'destination_branch_id',
            'users.name AS created_by',
            'intersite_transfers.created_at'
        )
            ->where('intersite_transfer_products.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('source_branch_id', 'LIKE', $source_branch_id)
            ->where('destination_branch_id', 'LIKE', $destination_branch_id)
            ->join('intersite_transfer_products', 'intersite_transfer_products.intersite_transfer_id', 'intersite_transfers.id')
            ->join('stores', 'stores.id', 'intersite_transfers.source_branch_id')
            ->join('products', 'products.id', 'intersite_transfer_products.product_id')
            ->join('users', 'users.id', 'intersite_transfers.created_by')
            ->whereBetween('intersite_transfers.date', [$from_date, $to_date]);
        $transfers = $query->get();
        //$query2 = $query;
        return view('pages.reports.stock_control.print_intersite_stock_transfer', compact('transfers', 'from_date', 'to_date'));
    }

    public function interstoreTransfer()
    {
        return view('pages.reports.stock_control.interstore_stock_transfer_report', [
            'branches' => Branch::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function loadInterstoreTransferReport(Request $request)
    {
        //return $request->stock_in_out;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $product_id = $request->product_id;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $source_store_id = $request->source_store_id;
        $destination_store_id = $request->destination_store_id;
        $category_id = $request->category_id;
        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';

        }
        if ($category_id == 'all' || $category_id == '') {
            $category_id = '%';
        }
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($source_store_id == 'all' || $source_store_id == '') {
            $source_store_id = '%';
        }
        if ($destination_store_id == 'all' || $destination_store_id == '') {
            $destination_store_id = '%';
        }

        $query = InterstoreTransfer::select(
            'products.name AS product_name',
            'products.code AS product_code',
            'products.unit AS product_unit',
            'quantity',
            'reference',
            'source_store_id',
            'destination_store_id',
            'interstore_transfers.branch_id',
            'interstore_transfers.created_at',
            'users.name AS created_by'
        )
            ->where('interstore_transfer_details.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('interstore_transfers.branch_id', 'LIKE', $branch_id)
            ->where('source_store_id', 'LIKE', $destination_store_id)
            ->where('destination_store_id', 'LIKE', $destination_store_id)
            ->join('interstore_transfer_details', 'interstore_transfer_details.interstore_transfer_id', 'interstore_transfers.id')
            ->join('stores', 'stores.id', 'interstore_transfer_details.source_store_id')
            ->join('products', 'products.id', 'interstore_transfer_details.product_id')
            ->join('users', 'users.id', 'interstore_transfers.created_by')
            ->join('branches', 'stores.branch_id', 'branches.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween('interstore_transfers.date', [$from_date, $to_date]);

        $transfers = $query->get();
        if ($product_id == '%') {
            $product_id = 'all';

        }
        if ($category_id == '%') {
            $category_id = 'all';

        }
        if ($company_id == '%') {
            $company_id = 'all';
        }
        if ($branch_id == '%') {
            $branch_id = 'all';

        }
        if ($source_store_id == '%') {
            $source_store_id = 'all';

        }
        if ($destination_store_id == '%') {
            $destination_store_id = 'all';

        }

        return view('pages.reports.stock_control.load_interstore_stock_transfer_report', compact('transfers', 'from_date', 'to_date', 'product_id', 'company_id', 'branch_id', 'source_store_id', 'destination_store_id', 'category_id'));
    }

    public function printInterstoreTransfer($from_date, $to_date, $company_id, $branch_id, $source_store_id, $destination_store_id, $category_id, $product_id)
    {
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($company_id == 'all') {
            $company_id = '%';

        }

        if ($branch_id == 'all') {
            $branch_id = '%';

        }
        if ($source_store_id == 'all') {
            $source_store_id = '%';

        }
        if ($destination_store_id == 'all') {
            $destination_store_id = '%';

        }

        $query = InterstoreTransfer::select(
            'products.name AS product_name',
            'products.code AS product_code',
            'quantity',
            'reference',
            'source_store_id',
            'destination_store_id',
            'interstore_transfers.branch_id',
            'interstore_transfers.created_at',
            'users.name AS created_by'
        )
            ->where('interstore_transfer_details.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('interstore_transfers.branch_id', 'LIKE', $branch_id)
            ->where('source_store_id', 'LIKE', $destination_store_id)
            ->where('destination_store_id', 'LIKE', $destination_store_id)
            ->join('interstore_transfer_details', 'interstore_transfer_details.interstore_transfer_id', 'interstore_transfers.id')
            ->join('stores', 'stores.id', 'interstore_transfer_details.source_store_id')
            ->join('products', 'products.id', 'interstore_transfer_details.product_id')
            ->join('users', 'users.id', 'interstore_transfers.created_by')
            ->join('branches', 'stores.branch_id', 'branches.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween('interstore_transfers.date', [$from_date, $to_date]);
        $transfers = $query->get();
        return view('pages.reports.stock_control.print_intersite_stock_transfer', compact('transfers', 'from_date', 'to_date'));
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $category_id = $request->category_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        if ($type == "all" || $type == "")
            $type = "%";
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == "all" || $branch_id == "")
            $branch_id = "%";
        if ($store_id == "all" || $store_id == "")
            $store_id = "%";
        if ($category_id == "all" || $category_id == "")
            $category_id = "%";
        if ($product_id == "all" || $product_id == "")
            $product_id = "%";

        $records = StockCard::select('date', 'branch_id', 'cr', 'dr', 'products.name AS product_name', 'products.code AS product_code', 'products.unit AS product_unit', 'branches.code AS branch_code', 'refno', 'stores.code AS store_code')
            ->join('products', 'products.id', 'stock_cards.product_id')
            ->join('stores', 'stores.id', 'stock_cards.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stock_cards.product_id', 'LIKE', $product_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('stock_cards.store_id', 'LIKE', $store_id)
            ->where('stock_cards.type', 'LIKE', $type)
            ->where('branches.company_id', 'LIKE', $company_id)
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

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.load_stock_history_report', compact('records', 'from_date', 'to_date', 'store_id', 'product_id', 'branch_id', 'category_id', 'branch', 'type', 'company_id', 'company'));
    }

    public function printStockHistory($from_date, $to_date, $type, $company_id, $branch_id, $store_id, $category_id, $product_id)
    {
        if ($product_id == 'all') {
            $product_id = '%';

        }
        if ($category_id == 'all') {
            $category_id = '%';

        }
        if ($company_id == 'all') {
            $company_id = '%';

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
            ->where('branches.company_id', 'LIKE', $company_id)
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

    // public function loadCurrentStock(Request $request)
    // {
    //     $branch_id = $request->branch_id;
    //     $category_id = $request->category_id;
    //     $product_id = $request->product_id;
    //     $store_id = $request->store_id;
    //     if ($branch_id == "all" || $branch_id == "")
    //         $branch_id = "%";
    //     if ($store_id == "all" || $store_id == "")
    //         $store_id = "%";
    //     if ($category_id == "all" || $category_id == "")
    //         $category_id = "%";
    //     if ($product_id == "all" || $product_id == "")
    //         $product_id = "%";

    //     $stores = DB::table('store_products')
    //         ->selectRaw(
    //             "products.name,
    //             products.code AS product_code,
    //             stores.code as store_code,
    //             store_products.qty_available,
    //             (SELECT  bpp.branch_id
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as branch_id,
    //                 branches.id as branch_id2,
    //             (SELECT  bpp.retail_selling_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as retail_selling_price,
    //             (SELECT  bpp.whole_selling_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as whole_selling_price,
    //             (SELECT  bpp.cost_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as cost_price,
    //             (SELECT  b.code
    //                 FROM branches b
    //                 WHERE b.id = stores.branch_id
    //                 LIMIT 1) as branch_code,
    //             store_products.id"
    //         )
    //         ->join('products', 'products.id', '=', 'store_products.product_id')
    //         ->join('stores', 'stores.id', '=', 'store_products.store_id')
    //         ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
    //         ->join('branches', 'branches.id', 'branch_product_prices.branch_id')
    //         ->where('store_products.qty_available', '>', 0)
    //         ->where('products.category_id', 'LIKE', $category_id)
    //         ->where('store_products.product_id', 'LIKE', $product_id)
    //         ->where('store_products.store_id', 'LIKE', $store_id)
    //         ->where('branch_product_prices.product_id', 'LIKE', $product_id)
    //         ->where('stores.branch_id', 'LIKE', $branch_id)
    //         ->where('branch_product_prices.branch_id', 'LIKE', $branch_id)
    //         ->orderBy('products.name')
    //         ->groupBy('store_products.store_id', 'branch_product_prices.product_id')
    //         ->get();


    //     if ($branch_id == "%")
    //         $branch_id = "all";
    //     if ($category_id == "%")
    //         $category_id = "all";
    //     if ($product_id == "%")
    //         $product_id = "all";
    //     if ($store_id == "%")
    //         $store_id = "all";
    //     $branch = null;
    //     if ($branch_id != "all")
    //         $branch = Branch::find($branch_id);
    //     return view('pages.reports.stock_control.load_stock', ['stores' => $stores, 'branch_id' => $branch_id, 'store_id' => $store_id, 'product_id' => $product_id, 'category_id' => $category_id, 'branch' => $branch]);
    // }


    public function loadCurrentStock(Request $request)
    {
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $category_ids = $request->category_id; // This is now an array for multiple selections
        $product_id = $request->product_id;
        $store_id = $request->store_id;

        // Handle "all" or empty selection for branch and store
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == "all" || $branch_id == "") {
            $branch_id = "%";
        }
        if ($store_id == "all" || $store_id == "") {
            $store_id = "%";
        }

        // Handle multiple categories, or default to "%"
        if (empty($category_ids)) {
            $category_ids = "%"; // No categories selected, match all
        }

        if ($product_id == "all" || $product_id == "") {
            $product_id = "%";
        }

        $stores = DB::table('store_products')
            ->selectRaw(
                "products.name,
            products.code AS product_code,
            products.unit AS product_unit,
            stores.code as store_code,
            store_products.qty_available,
            (SELECT bpp.branch_id
                FROM branch_product_prices bpp
                WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
                group by bpp.branch_id
                LIMIT 1) as branch_id,
            branches.id as branch_id2,
            (SELECT bpp.retail_selling_price
                FROM branch_product_prices bpp
                WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
                group by bpp.branch_id
                LIMIT 1) as retail_selling_price,
            (SELECT bpp.whole_selling_price
                FROM branch_product_prices bpp
                WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
                group by bpp.branch_id
                LIMIT 1) as whole_selling_price,
            (SELECT bpp.cost_price
                FROM branch_product_prices bpp
                WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
                group by bpp.branch_id
                LIMIT 1) as cost_price,
            (SELECT b.code
                FROM branches b
                WHERE b.id = stores.branch_id
                LIMIT 1) as branch_code,
            store_products.id"
            )
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('store_products.qty_available', '>', 0)
            ->where(function ($query) use ($category_ids) {
                // Handle multiple category selection
                if (is_array($category_ids)) {
                    $query->whereIn('products.category_id', $category_ids);
                } else {
                    $query->where('products.category_id', 'LIKE', $category_ids);
                }
            })
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('branch_product_prices.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->orderBy('products.name')
            ->groupBy('store_products.store_id', 'branch_product_prices.product_id')
            ->get();

        // Adjust return values for "all" or empty selections
        if ($company_id == "%") {
            $company_id = "all";
        }
        if ($branch_id == "%") {
            $branch_id = "all";
        }
        if ($category_ids == "%") {
            $category_ids = "all";
        }
        if ($product_id == "%") {
            $product_id = "all";
        }
        if ($store_id == "%") {
            $store_id = "all";
        }

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);

        // Fetch branch details if a specific branch is selected
        $branch = null;
        if ($branch_id != "all") {
            $branch = Branch::find($branch_id);
        }
        $company = null;
        if ($company_id != "all") {
            $company = Branch::find($company_id);
        }

        return view('pages.reports.stock_control.load_stock', [
            'stores' => $stores,
            'branch_id' => $branch_id,
            'company_id' => $company_id,
            'store_id' => $store_id,
            'product_id' => $product_id,
            'category_id' => $category_ids,
            'branch' => $branch,
            'company' => $company,
        ]);
    }

    // public function printCurrentStock($branch_id, $store_id, $category_id, $product_id)
    // {
    //     if ($branch_id == "all" || $branch_id == "")
    //         $branch_id = "%";
    //     if ($store_id == "all" || $store_id == "")
    //         $store_id = "%";
    //     if ($category_id == "all" || $category_id == "")
    //         $category_id = "%";
    //     if ($product_id == "all" || $product_id == "")
    //         $product_id = "%";

    //     $stores = DB::table('store_products')
    //         ->selectRaw(
    //             "products.name,
    //             products.code AS product_code,
    //             stores.code as store_code,
    //             store_products.qty_available,
    //             (SELECT  bpp.branch_id
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as branch_id,
    //                 branches.id as branch_id2,
    //             (SELECT  bpp.retail_selling_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as retail_selling_price,
    //             (SELECT  bpp.whole_selling_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as whole_selling_price,
    //             (SELECT  bpp.cost_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as cost_price,
    //             (SELECT  b.code
    //                 FROM branches b
    //                 WHERE b.id = stores.branch_id
    //                 LIMIT 1) as branch_code,
    //             store_products.id"
    //         )
    //         ->join('products', 'products.id', '=', 'store_products.product_id')
    //         ->join('stores', 'stores.id', '=', 'store_products.store_id')
    //         ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
    //         ->join('branches', 'branches.id', 'branch_product_prices.branch_id')
    //         ->where('store_products.qty_available', '>', 0)
    //         ->where('products.category_id', 'LIKE', $category_id)
    //         ->where('store_products.product_id', 'LIKE', $product_id)
    //         ->where('store_products.store_id', 'LIKE', $store_id)
    //         ->where('branch_product_prices.product_id', 'LIKE', $product_id)
    //         ->where('stores.branch_id', 'LIKE', $branch_id)
    //         ->where('branch_product_prices.branch_id', 'LIKE', $branch_id)
    //         ->orderBy('products.name')
    //         ->groupBy('store_products.store_id', 'branch_product_prices.product_id')
    //         ->get();
    //     $branch = null;
    //     if ($branch_id != "all")
    //         $branch = Branch::find($branch_id);
    //     return view('pages.reports.stock_control.print_current_stock', ['stores' => $stores, 'branch' => $branch]);
    // }
    public function printCurrentStock($company_id, $branch_id, $store_id, $category_id, $product_id)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
                (SELECT  bpp.branch_id
                    FROM branch_product_prices bpp
                    WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
                    group by bpp.branch_id
                    LIMIT 1) as branch_id,
                    branches.id as branch_id2,
                (SELECT  bpp.retail_selling_price
                    FROM branch_product_prices bpp
                    WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
                    group by bpp.branch_id
                    LIMIT 1) as retail_selling_price,
                (SELECT  bpp.whole_selling_price
                    FROM branch_product_prices bpp
                    WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
                    group by bpp.branch_id
                    LIMIT 1) as whole_selling_price,
                (SELECT  bpp.cost_price
                    FROM branch_product_prices bpp
                    WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
                    group by bpp.branch_id
                    LIMIT 1) as cost_price,
                (SELECT  b.code
                    FROM branches b
                    WHERE b.id = stores.branch_id
                    LIMIT 1) as branch_code,
                store_products.id"
            )
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('store_products.qty_available', '>', 0)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('branch_product_prices.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->orderBy('products.name')
            ->groupBy('store_products.store_id', 'branch_product_prices.product_id')
            ->get();
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.print_current_stock', ['stores' => $stores, 'branch' => $branch]);
    }

    public function storeLedger()
    {
        return view('pages.reports.stock_control.store_ledger_report');
    }

    // public function loadStoreLedger(Request $request)
    // {
    //     $product_id = $request->product_id;
    //     $store_id = $request->store_id;
    //     $category_id = $request->category_id;
    //     $branch_id = $request->branch_id;
    //     if ($product_id == 'all' || $product_id == '') {
    //         $product_id = '%';

    //     }
    //     if ($category_id == 'all' || $category_id == '') {
    //         $category_id = '%';

    //     }
    //     if ($store_id == 'all' || $store_id == '') {
    //         $store_id = '%';

    //     }
    //     if ($branch_id == 'all' || $branch_id == '') {
    //         $branch_id = '%';

    //     }

    //     $stores = DB::table('store_products')
    //         ->selectRaw("
    //             products.name,
    //             products.code,
    //             stores.code as store,
    //             store_products.qty_available,
    //             (SELECT  bpp.retail_selling_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as retail_selling_price,
    //             (SELECT  bpp.whole_selling_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as whole_selling_price,
    //            (SELECT  bpp.cost_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as cost_price,
    //            (SELECT  b.code
    //                 FROM branches b
    //                 WHERE b.id = stores.branch_id
    //                 LIMIT 1) as branch_code,
    //             store_products.id,
    //             categories.name AS category")
    //         ->join('products', 'products.id', '=', 'store_products.product_id')
    //         ->join('stores', 'stores.id', '=', 'store_products.store_id')
    //         ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
    //         ->join('categories', 'categories.id', 'products.category_id')
    //         ->where('store_products.qty_available', '>', 0)
    //         ->where('products.category_id', 'LIKE', $category_id)
    //         ->where('store_products.product_id', 'LIKE', $product_id)
    //         ->where('store_products.store_id', 'LIKE', $store_id)
    //         ->where('branch_product_prices.product_id', 'LIKE', $product_id)
    //         ->where('stores.branch_id', 'LIKE', $branch_id)
    //         ->groupBy('stores.branch_id')
    //         ->groupBy('store_products.store_id', 'branch_product_prices.product_id')
    //         ->get();
    //     if ($category_id == "%")
    //         $category_id = "all";
    //     if ($branch_id == "%")
    //         $branch_id = "all";
    //     if ($product_id == "%")
    //         $product_id = "all";
    //     if ($store_id == "%")
    //         $store_id = "all";
    //     $branch = null;
    //     if ($branch_id != 'all')
    //         $branch = Branch::find($branch_id);
    //     return view('pages.reports.stock_control.load_store_ledger', ['stores' => $stores, 'branch_id' => $branch_id, 'branch' => $branch, 'product_id' => $product_id, 'category_id' => $category_id, 'store_id' => $store_id]);
    // }

    public function loadStoreLedger(Request $request)
    {
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_ids = $request->category_id;  // Array of selected category IDs or "all"
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;

        if ($category_ids == 'all' || $category_ids == '') {
            $category_ids = 'all';
        }

        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';
        }
        if ($store_id == 'all' || $store_id == '') {
            $store_id = '%';
        }
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }

        // Begin query
        $stores = DB::table('store_products')
            ->selectRaw("
            products.name,
            products.code,
            products.unit as product_unit,
            stores.code as store,
            store_products.qty_available,
            (SELECT bpp.retail_selling_price
                FROM branch_product_prices bpp
                WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
                group by bpp.branch_id
                LIMIT 1) as retail_selling_price,
            (SELECT bpp.whole_selling_price
                FROM branch_product_prices bpp
                WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
                group by bpp.branch_id
                LIMIT 1) as whole_selling_price,
            (SELECT bpp.cost_price
                FROM branch_product_prices bpp
                WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
                group by bpp.branch_id
                LIMIT 1) as cost_price,
            (SELECT b.code
                FROM branches b
                WHERE b.id = stores.branch_id
                LIMIT 1) as branch_code,
            store_products.id,
            categories.name AS category")
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('store_products.qty_available', '>', 0)
            // Category filter, only apply if category_id is not 'all'
            ->when($category_ids != 'all', function ($query) use ($category_ids) {
                return $query->whereIn('products.category_id', $category_ids);
            })
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->groupBy('stores.branch_id')
            ->groupBy('store_products.store_id', 'branch_product_prices.product_id')
            ->get();

        // Reset to "all" for display purposes if necessary
        if ($category_ids == 'all') {
            $category_ids = "all";
        }
        if ($branch_id == "%") {
            $branch_id = "all";
        }
        if ($product_id == "%") {
            $product_id = "all";
        }
        if ($store_id == "%") {
            $store_id = "all";
        }

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        // Find the branch for display if a specific branch is selected
        $branch = null;
        if ($branch_id != 'all') {
            $branch = Branch::find($branch_id);
        }

        // Return view with data
        return view('pages.reports.stock_control.load_store_ledger', [
            'stores' => $stores,
            'company_id' => $company_id,
            'company' => $company,
            'branch_id' => $branch_id,
            'branch' => $branch,
            'product_id' => $product_id,
            'category_id' => $category_ids,
            'store_id' => $store_id
        ]);
    }

    // public function printstoreLedger($branch_id, $store_id, $category_id, $product_id)
    // {

    //     if ($product_id == 'all') {
    //         $product_id = '%';

    //     }
    //     if ($branch_id == 'all') {
    //         $branch_id = '%';
    //     }
    //     if ($category_id == 'all') {
    //         $category_id = '%';

    //     }
    //     if ($store_id == 'all') {
    //         $store_id = '%';

    //     }
    //     $stores = DB::table('store_products')
    //         ->selectRaw("
    //             products.name,
    //             products.code,
    //             stores.code as store,
    //             store_products.qty_available,
    //             (SELECT  bpp.retail_selling_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as retail_selling_price,
    //             (SELECT  bpp.whole_selling_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as whole_selling_price,
    //            (SELECT  bpp.cost_price
    //                 FROM branch_product_prices bpp
    //                 WHERE bpp.branch_id =  stores.branch_id AND bpp.product_id = products.id
    //                 group by bpp.branch_id
    //                 LIMIT 1) as cost_price,
    //             (SELECT  b.code
    //                 FROM branches b
    //                 WHERE b.id = stores.branch_id
    //                 LIMIT 1) as branch_code,
    //             store_products.id,
    //             branches.code as branch_code,
    //             categories.name AS category")
    //         ->join('products', 'products.id', '=', 'store_products.product_id')
    //         ->join('stores', 'stores.id', '=', 'store_products.store_id')
    //         ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
    //         ->join('categories', 'categories.id', 'products.category_id')
    //         ->where('store_products.qty_available', '>', 0)
    //         ->where('products.category_id', 'LIKE', $category_id)
    //         ->where('store_products.product_id', 'LIKE', $product_id)
    //         ->where('store_products.store_id', 'LIKE', $store_id)
    //         ->where('branch_product_prices.product_id', 'LIKE', $product_id)
    //         ->where('stores.branch_id', 'LIKE', $branch_id)
    //         ->groupBy('stores.branch_id')
    //         ->groupBy('store_products.store_id', 'branch_product_prices.product_id')
    //         ->get();
    //     $branch = null;
    //     if ($branch_id != 'all')
    //         $branch = Branch::find($branch_id);
    //     return view('pages.reports.stock_control.print_store_ledger', ['stores' => $stores, 'branch' => $branch,'category_id'=>$category_id]);
    // }
    public function printstoreLedger($company_id, $branch_id, $store_id, $category_ids, $product_id)
    {
        // Handle "all" for branch, store, category, and product
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($store_id == 'all') {
            $store_id = '%';
        }
        if ($product_id == 'all') {
            $product_id = '%';
        }

        // Convert comma-separated category IDs back to an array, handle 'all'
        if ($category_ids == 'all' || empty($category_ids)) {
            $category_ids = '%'; // Match all categories if 'all' is selected
        } else {
            $category_ids = explode(',', $category_ids); // Convert string back to array
        }

        // Build query
        $stores = DB::table('store_products')
            ->selectRaw("
        products.name,
        products.code,
        stores.code as store,
        store_products.qty_available,
        (SELECT bpp.retail_selling_price
            FROM branch_product_prices bpp
            WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
            GROUP BY bpp.branch_id
            LIMIT 1) as retail_selling_price,
        (SELECT bpp.whole_selling_price
            FROM branch_product_prices bpp
            WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
            GROUP BY bpp.branch_id
            LIMIT 1) as whole_selling_price,
       (SELECT bpp.cost_price
            FROM branch_product_prices bpp
            WHERE bpp.branch_id = stores.branch_id AND bpp.product_id = products.id
            GROUP BY bpp.branch_id
            LIMIT 1) as cost_price,
        branches.code as branch_code,  -- Make sure 'branches.code' exists
        categories.name AS category"
            )
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')  // Ensure proper join on branches
            ->where('store_products.qty_available', '>', 0)
            ->where(function ($query) use ($category_ids) {
                if (is_array($category_ids)) {
                    $query->whereIn('products.category_id', $category_ids);  // Handle multiple categories
                } else {
                    $query->where('products.category_id', 'LIKE', $category_ids);  // Single category case
                }
            })
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('branch_product_prices.product_id', 'LIKE', $product_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->groupBy('stores.branch_id', 'store_products.store_id', 'branch_product_prices.product_id')
            ->get();

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        // Fetch branch details if a specific branch is selected
        $branch = null;
        if ($branch_id != 'all') {
            $branch = Branch::find($branch_id);
        }

        return view('pages.reports.stock_control.print_store_ledger', [
            'stores' => $stores,
            'branch' => $branch,
            'category_id' => $category_ids
        ]);
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
        $company_id = $request->company_id;
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
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';

        }

        $stores = DB::table('stock_adjustments')
            ->selectRaw("products.name, products.code, products.unit as product_unit, stores.name as store, quantity, date, reference, operation, stock_adjustments.created_at,
                (SELECT name FROM users WHERE id = stock_adjustments.created_by LIMIT 1) as created_by,
                (SELECT name FROM users WHERE id = stock_adjustments.posted_by LIMIT 1) as posted_by")
            ->join('stock_adjustment_details', 'stock_adjustment_details.stock_adjustment_id', 'stock_adjustments.id')
            ->join('products', 'products.id', '=', 'stock_adjustment_details.product_id')
            ->join('stores', 'stores.id', '=', 'stock_adjustment_details.store_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != '%')
            $branch = Branch::find($branch_id);
        return view('pages.reports.stock_control.load_stock_adjustment', compact('stores', 'from_date', 'to_date', 'branch', 'company_id', 'branch_id', 'product_id', 'store_id', 'category_id'));
    }

    public function printStockAdjustment($from_date, $to_date, $company_id, $branch_id, $store_id, $category_id, $product_id)
    {
        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';

        }
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
            ->selectRaw("products.name, products.code, stores.name as store, quantity, date, reference, operation, stock_adjustments.created_at,
                (SELECT name FROM users WHERE id = stock_adjustments.created_by LIMIT 1) as created_by,
                (SELECT name FROM users WHERE id = stock_adjustments.posted_by LIMIT 1) as posted_by")
            ->join('stock_adjustment_details', 'stock_adjustment_details.stock_adjustment_id', 'stock_adjustments.id')
            ->join('products', 'products.id', '=', 'stock_adjustment_details.product_id')
            ->join('stores', 'stores.id', '=', 'stock_adjustment_details.store_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('stock_adjustment_details.product_id', 'LIKE', $product_id)
            ->where('stock_adjustment_details.store_id', 'LIKE', $store_id)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->whereBetween('date', [$from_date, $to_date])
            ->orderBy('date', 'DESC')
            ->orderBy('reference', 'ASC')
            ->get();
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $customer_id = $request->customer_id;
        $type = $request->type;

        if ($product_id == 'all' || $product_id == '') {
            $product_id = '%';
        }
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
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
                'order_details.unit AS product_unit',
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
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('customers.type', 'LIKE', $type)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all' && $branch_id != '')
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.load_general_sale_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'product_id', 'store_id', 'category_id', 'customer_id', 'type', 'branch'));
    }

    public function printGeneralSaleReport($from_date, $to_date, $company_id, $branch_id, $store_id, $category_id, $product_id, $customer_id, $type)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('store_products.product_id', 'LIKE', $product_id)
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('orders.customer_id', 'LIKE', $customer_id)
            ->where('customers.type', 'LIKE', $type)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('order_details.status', 1)
            ->where(DB::raw("DATE(order_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(order_date)"), '<=', $to_date)
            ->orderBy('order_date')
            ->get();
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all' && $branch_id != '')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_general_sale_report', compact('sales', 'from_date', 'to_date', 'type', 'branch'));
    }

    public function categorySaleReport()
    {
        return view('pages.reports.sales_and_cash_analysis.sales_by_category');
    }

    // public function loadCategorySaleReport(Request $request)
    // {
    //     $from_date = date('Y-m-d', strtotime($request->from_date));
    //     $to_date = date('Y-m-d', strtotime($request->to_date));
    //     $company_id = $request->company_id;
    //     $branch_id = $request->branch_id;
    //     $category_id1 = $request->category_id1;  // Now an array

    //     // Handle 'all' for company and branch
    //     if ($company_id == 'all' || $company_id == '') {
    //         $company_id = '%';
    //     }
    //     if ($branch_id == 'all' || $branch_id == '') {
    //         $branch_id = '%';  // wildcard for all branches
    //     }

    //     // Check if any categories are selected, or treat as "all"
    //     $category_id1 = is_array($category_id1) ? $category_id1 : ['%'];  // Convert to array if not already

    //     // Build the query
    //     $data = DB::table('orders')
    //         ->select(
    //             'categories.name as category',
    //             'categories.code as code',
    //             DB::raw('SUM(order_details.quantity) as quantity'),
    //             DB::raw('SUM(order_details.total) as amount'),
    //             DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
    //             // Calculate qty_available based on whether a branch filter is applied
    //             $branch_id != '%' ? DB::raw('(SELECT SUM(store_products.qty_available)
    //             FROM store_products
    //             JOIN stores s ON store_products.store_id = s.id
    //             JOIN products p ON store_products.product_id = p.id
    //             WHERE s.branch_id LIKE stores.branch_id
    //             AND p.category_id = categories.id) as qty_available')
    //             :
    //             DB::raw('(SELECT SUM(store_products.qty_available)
    //             FROM store_products
    //             JOIN products p ON store_products.product_id = p.id
    //             WHERE p.category_id = categories.id) as qty_available')
    //         )
    //         ->join('order_details', 'orders.id', '=', 'order_details.order_id')
    //         ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
    //         ->join('stores', 'store_products.store_id', '=', 'stores.id')
    //         ->join('branches', 'stores.branch_id', '=', 'branches.id')  // Link stores to branches
    //         ->join('companies', 'branches.company_id', '=', 'companies.id')  // Link branches to companies
    //         ->join('products', 'store_products.product_id', '=', 'products.id')
    //         ->join('categories', 'products.category_id', '=', 'categories.id')
    //         ->where('branches.company_id', 'LIKE', $company_id)  // Filter by company_id
    //         ->where('stores.branch_id', 'LIKE', $branch_id)
    //         ->where('order_details.status', '=', 1)  // assuming status 1 means confirmed sales
    //         ->whereBetween('order_date', [$from_date, $to_date]);
    //     if (!in_array('%', $category_id1))
    //         $data = $data->whereIn('products.category_id', $category_id1);

    //     // Group the results by category and sort by category code
    //     $sales = $data->groupBy('products.category_id')
    //         ->orderBy('code', 'ASC')
    //         ->get();

    //     // Reset 'all' to make it more readable in the UI
    //     if (in_array('%', $category_id1)) {
    //         $category_id1 = "all";
    //     }
    //     if ($branch_id == '%') {
    //         $branch_id = 'all';
    //     }
    //     if ($company_id == '%') {
    //         $company_id = 'all';
    //     }
    //     $company = null;
    //     if ($company_id != 'all')
    //         $company = Company::find($company_id);
    //     // Fetch branch info if a specific branch is selected
    //     $branch = null;
    //     if ($branch_id != 'all') {
    //         $branch = Branch::find($branch_id);
    //     }
    //     $company = null;
    //     if ($company_id != 'all') {
    //         $company = Branch::find($company_id);
    //     }

    //     // Return the view with the data
    //     return view('pages.reports.sales_and_cash_analysis.load_sale_by_category_report', compact('sales', 'from_date', 'to_date', 'branch_id', 'category_id1', 'branch', 'company', 'company_id'));
    // }


    public function loadCategorySaleReport(Request $request)
    {
        $from_date = date('Y-m-d', strtotime($request->from_date));
        $to_date = date('Y-m-d', strtotime($request->to_date));
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $category_id1 = $request->category_id1; // Now an array

        // Handle 'all' for company and branch
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }

        // Convert category selection to array if not already
        $category_id1 = is_array($category_id1) ? $category_id1 : ['%'];

        // Determine Grouping Options
        $group_by_category = $request->group_by_category ?? 0;
        $group_by_product = $request->group_by_product ?? 0;

        // Define Group Fields Dynamically
        $groupFields = [];
        if ($group_by_category) {
            $groupFields[] = 'categories.id';
        }
        if ($group_by_product) {
            $groupFields[] = 'products.id';
        }

        // Default to category grouping if no option is selected
        if (empty($groupFields)) {
            $groupFields[] = 'categories.id';
        }

        // Build the query
        $data = DB::table('orders')
            ->select(
                'categories.name as category',
                'categories.code as category_code',
                'order_details.unit as product_unit',
                DB::raw('SUM(order_details.quantity) as quantity'),
                DB::raw('SUM(order_details.total) as amount'),
                DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
                DB::raw("
                (SELECT SUM(sp.qty_available)
                FROM store_products sp
                JOIN products p ON sp.product_id = p.id
                WHERE sp.store_id IN (SELECT id FROM stores WHERE branch_id = stores.branch_id)
                AND p.category_id = categories.id
            ) as qty_available")
            )
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
            ->join('stores', 'store_products.store_id', '=', 'stores.id')
            ->join('branches', 'stores.branch_id', '=', 'branches.id')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->join('products', 'store_products.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('order_details.status', '=', 1)
            ->whereBetween('orders.order_date', [$from_date, $to_date]);

        // Apply category and branch filters if not 'all'
        if (!in_array('%', $category_id1)) {
            $data->whereIn('products.category_id', $category_id1);
        }
        if ($branch_id !== '%') {
            $data->where('stores.branch_id', 'LIKE', $branch_id);
        }

        // Add product details if grouping by Product
        if ($group_by_product) {
            $data->addSelect(
                'products.name as product_name',
                'products.code as product_code'
            );
        }

        // Apply dynamic grouping fields
        $sales = $data->groupBy($groupFields)
            ->orderBy('categories.name', 'ASC')
            ->when($group_by_product, function ($query) {
                return $query->orderBy('products.name', 'ASC');
            })
            ->get();

        // Reset 'all' for readability in UI
        $category_id1 = in_array('%', $category_id1) ? "all" : $category_id1;
        $branch_id = $branch_id === '%' ? "all" : $branch_id;
        $company_id = $company_id === '%' ? "all" : $company_id;

        // Fetch additional company & branch info
        $company = $company_id !== 'all' ? Company::find($company_id) : null;
        $branch = $branch_id !== 'all' ? Branch::find($branch_id) : null;

        // Return view with data
        return view('pages.reports.sales_and_cash_analysis.load_sale_by_category_report', compact(
            'sales',
            'from_date',
            'to_date',
            'category_id1',
            'company',
            'company_id',
            'branch',
            'branch_id',
            'group_by_category',
            'group_by_product'
        ));
    }

    public function printCategorySaleReport($from_date, $to_date, $company_id, $branch_id, $category_id1)
    {
        $from_date = date('Y-m-d', strtotime($from_date));
        $to_date = date('Y-m-d', strtotime($to_date));

        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }


        // Handle category_id1 and category_id2 as arrays
        $category_id1 = is_array($category_id1) && count($category_id1) > 0 ? $category_id1 : ['%'];
        //$category_id2 = is_array($category_id2) && count($category_id2) > 0 ? $category_id2 : ['%'];

        $data = DB::table('orders')
            ->select(
                'categories.name as category',
                'categories.code as code',
                DB::raw('SUM(order_details.quantity) as quantity'),
                DB::raw('SUM(order_details.total) as amount'),
                DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
                // Calculate qty_available based on whether a branch filter is applied
                $branch_id != '%' ? DB::raw('(SELECT SUM(store_products.qty_available)
            FROM store_products
            JOIN stores s ON store_products.store_id = s.id
            JOIN products p ON store_products.product_id = p.id
            WHERE s.branch_id LIKE stores.branch_id
            AND p.category_id = categories.id) as qty_available')
                    :
                    DB::raw('(SELECT SUM(store_products.qty_available)
            FROM store_products
            JOIN products p ON store_products.product_id = p.id
            WHERE p.category_id = categories.id) as qty_available')
            )
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
            ->join('stores', 'store_products.store_id', '=', 'stores.id')
            ->join('branches', 'stores.branch_id', '=', 'branches.id')  // Link stores to branches
            ->join('companies', 'branches.company_id', '=', 'companies.id')  // Link branches to companies
            ->join('products', 'store_products.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('branches.company_id', 'LIKE', $company_id)  // Filter by company_id
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->where('order_details.status', '=', 1)  // assuming status 1 means confirmed sales
            ->whereBetween('order_date', [$from_date, $to_date]);
        // Check if category_id1 or category_id2 has values and apply filter
        if ($category_id1 != ['%']) {
            $data = $data->whereIn('products.category_id', $category_id1);
        }

        // Group the results by category and sort by category code
        $sales = $data->groupBy('products.category_id')
            ->orderBy('code', 'ASC')
            ->get();

        if ($category_id1 == ['%'])
            $category_id1 = "all";

        if ($branch_id == '%' || $branch_id == '')
            $branch_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all' && $branch_id != '')
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.print_category_sale_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }

    public function categorySaleBySiteReport()
    {
        return view('pages.reports.sales_and_cash_analysis.sales_by_category_by_site');
    }

    // public function loadCategorySaleBySiteReport(Request $request)
    // {
    //     $from_date = date('Y-m-d', strtotime($request->from_date));
    //     $to_date = date('Y-m-d', strtotime($request->to_date));
    //     $company_id = $request->company_id;
    //     $branch_id = $request->branch_id;
    //     $category_id1 = $request->category_id1;  // Now an array

    //     // Handle 'all' for company and branch
    //     if ($company_id == 'all' || $company_id == '') {
    //         $company_id = '%';
    //     }
    //     // if ($branch_id == 'all' || $branch_id == '') {
    //     //     $branch_id = '%';  // wildcard for all branches
    //     // }

    //     // Check if any categories are selected, or treat as "all"
    //     $category_id1 = is_array($category_id1) ? $category_id1 : ['%'];  // Convert to array if not already
    //     $branch_id = is_array($branch_id) ? $branch_id : ['%'];  // Convert to array if not already

    //     // Build the query
    //     $data = DB::table('orders')
    //         ->select(
    //             'branches.id as branch_id',
    //             'branches.name as branch_name',
    //             'branches.code as branch_code',
    //             'categories.name as category',
    //             'categories.code as code',
    //             DB::raw('SUM(order_details.quantity) as quantity'),
    //             DB::raw('SUM(order_details.total) as amount'),
    //             DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
    //             DB::raw('(SELECT SUM(store_products.qty_available)
    //             FROM store_products
    //             JOIN products p ON store_products.product_id = p.id
    //             WHERE store_products.store_id IN (SELECT id FROM stores WHERE branch_id = branches.id)
    //             AND p.category_id = categories.id) as qty_available')
    //         )
    //         ->join('order_details', 'orders.id', '=', 'order_details.order_id')
    //         ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
    //         ->join('stores', 'store_products.store_id', '=', 'stores.id')
    //         ->join('branches', 'stores.branch_id', '=', 'branches.id')
    //         ->join('companies', 'branches.company_id', '=', 'companies.id')
    //         ->join('products', 'store_products.product_id', '=', 'products.id')
    //         ->join('categories', 'products.category_id', '=', 'categories.id')
    //         ->where('branches.company_id', 'LIKE', $company_id)
    //         ->where('order_details.status', '=', 1)
    //         ->whereBetween('order_date', [$from_date, $to_date]);

    //     if (!in_array('%', $category_id1)) {
    //         $data = $data->whereIn('products.category_id', $category_id1);
    //     }
    //     if (!in_array('%', $branch_id)) {
    //         $data = $data->whereIn('stores.branch_id', $branch_id);
    //     }

    //     $sales = $data->groupBy('branches.id', 'products.category_id')
    //         ->orderBy('branches.name', 'ASC')
    //         ->orderBy('categories.code', 'ASC')
    //         ->get();

    //     // Group sales by branch
    //     $salesByBranch = $sales->groupBy('branch_id');

    //     // Reset 'all' to make it more readable in the UI
    //     if (in_array('%', $category_id1)) {
    //         $category_id1 = "all";
    //     }
    //     if (in_array('%', $branch_id)) {
    //         $branch_id = "all";
    //     }
    //     if ($company_id == '%') {
    //         $company_id = 'all';
    //     }

    //     // Fetch branch info if a specific branch is selected
    //     $branch = null;
    //     if ($branch_id != 'all') {
    //         $branch = Branch::find($branch_id);
    //     }
    //     $company = null;
    //     if ($company_id != 'all') {
    //         $company = Company::find($company_id);
    //     }

    //     // Return the view with the data
    //     return view(
    //         'pages.reports.sales_and_cash_analysis.load_sale_by_category_by_site_report',
    //         compact('salesByBranch', 'from_date', 'to_date', 'branch_id', 'category_id1', 'branch', 'company', 'company_id')
    //     );
    // }


    public function loadCategorySaleBySiteReport(Request $request): View
    {
        $from_date = date('Y-m-d', strtotime($request->from_date));
        $to_date = date('Y-m-d', strtotime($request->to_date));
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $category_id1 = $request->category_id1;

        // Grouping flags
        $group_by_category = $request->group_by_category == 1 ? true : false;
        $group_by_product = $request->group_by_product == 1 ? true : false;


        // Handle 'all' selections
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        $category_id1 = is_array($category_id1) ? $category_id1 : ['%'];
        $branch_id = is_array($branch_id) ? $branch_id : ['%'];

        // Base Query
        $data = DB::table('orders')
            ->select(
                'branches.id as branch_id',
                'branches.name as branch_name',
                'branches.code as branch_code',
                'categories.id as category_id',
                'categories.name as category_name',
                'categories.code as category_code',
                'products.id as product_id',
                'products.name as product_name',
                'products.code as product_code',
                DB::raw('SUM(order_details.quantity) as quantity'),
                DB::raw('SUM(order_details.total) as amount'),
                DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
                DB::raw('(SELECT SUM(store_products.qty_available)
                FROM store_products
                WHERE store_products.store_id IN (SELECT id FROM stores WHERE branch_id = branches.id)
                AND store_products.product_id = products.id) as qty_available')
            )
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
            ->join('stores', 'store_products.store_id', '=', 'stores.id')
            ->join('branches', 'stores.branch_id', '=', 'branches.id')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->join('products', 'store_products.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('order_details.status', '=', 1)
            ->whereBetween('order_date', [$from_date, $to_date]);

        // Apply category and branch filters
        if (!in_array('%', $category_id1)) {
            $data = $data->whereIn('products.category_id', $category_id1);
        }
        if (!in_array('%', $branch_id)) {
            $data = $data->whereIn('stores.branch_id', $branch_id);
        }

        // **Apply Correct Grouping**
        // $groupByColumns = ['orders.branch_id']; // Default: Branch > Category > Product
        $groupByColumns = ['branches.id', 'categories.id']; // Default: Branch > Category > Product

        if ($group_by_category) {
            $groupByColumns = ['orders.branch_id', 'products.category_id']; // Branch > Category
        }

        if ($group_by_product) {
            $groupByColumns = ['orders.branch_id', 'store_products.product_id']; // Branch > Product
        }

        $data = $data->groupBy($groupByColumns)
            ->orderBy('branches.name', 'ASC')
            ->orderBy('categories.code', 'ASC')
            ->orderBy('products.name', 'ASC');

        $sales = $data->get();

        // Structure data properly for display
        $salesByGroup = $sales->groupBy($group_by_category ? 'category_id' : ($group_by_product ? 'product_id' : 'branch_id'));
        session([
            'salesByGroup' => $salesByGroup,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'group_by_category' => $request->has('group_by_category'),
            'group_by_product' => $request->has('group_by_product')
        ]);
        return view(
            'pages.reports.sales_and_cash_analysis.load_sale_by_category_by_site_report',
            compact('salesByGroup', 'from_date', 'to_date', 'branch_id', 'category_id1', 'group_by_category', 'group_by_product')
        );
    }


    public function exportBySiteExcel(Request $request)
    {
        $salesByGroup = session('salesByGroup'); // Retrieve data from session
        $from_date = session('from_date');
        $to_date = session('to_date');
        $group_by_category = session('group_by_category');
        $group_by_product = session('group_by_product');

        return Excel::download(new SalesBySiteReportExport($salesByGroup, $from_date, $to_date, $group_by_category, $group_by_product), 'sales_report.xlsx');
    }

    public function exportBySitePDF(Request $request)
    {
        $salesByGroup = session('salesByGroup'); // Retrieve data from session
        $from_date = session('from_date');
        $to_date = session('to_date');
        $group_by_category = session('group_by_category');
        $group_by_product = session('group_by_product');

        $pdf = Pdf::loadView('pages.reports.sales_and_cash_analysis.load_sale_by_category_by_site_report_pdf', compact('salesByGroup', 'from_date', 'to_date', 'group_by_category', 'group_by_product'))
            ->setPaper('a4', 'landscape')  // Set to A4 and Landscape mode
            ->setOptions([
                'defaultFont' => 'Helvetica',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);
        return $pdf->download('sales_by_site_by_category_report.pdf');
    }

    public function printCategorySaleBySiteReport($from_date, $to_date, $company_id, $branch_id, $category_id1)
    {
        $from_date = date('Y-m-d', strtotime($from_date));
        $to_date = date('Y-m-d', strtotime($to_date));

        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }


        // Handle category_id1 and category_id2 as arrays
        $category_id1 = is_array($category_id1) && count($category_id1) > 0 ? $category_id1 : ['%'];
        $branch_id = is_array($branch_id) ? $branch_id : ['%'];  // Convert to array if not already

        $data = DB::table('orders')
            ->select(
                'branches.id as branch_id',
                'branches.name as branch_name',
                'branches.code as branch_code',
                'categories.name as category',
                'categories.code as code',
                DB::raw('SUM(order_details.quantity) as quantity'),
                DB::raw('SUM(order_details.total) as amount'),
                DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
                DB::raw('(SELECT SUM(store_products.qty_available)
                FROM store_products
                JOIN products p ON store_products.product_id = p.id
                WHERE store_products.store_id IN (SELECT id FROM stores WHERE branch_id = branches.id)
                AND p.category_id = categories.id) as qty_available')
            )
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
            ->join('stores', 'store_products.store_id', '=', 'stores.id')
            ->join('branches', 'stores.branch_id', '=', 'branches.id')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->join('products', 'store_products.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('order_details.status', '=', 1)
            ->whereBetween('order_date', [$from_date, $to_date]);

        if (!in_array('%', $category_id1)) {
            $data = $data->whereIn('products.category_id', $category_id1);
        }
        if (!in_array('%', $branch_id)) {
            $data = $data->whereIn('stores.branch_id', $branch_id);
        }

        $sales = $data->groupBy('branches.id', 'products.category_id')
            ->orderBy('branches.name', 'ASC')
            ->orderBy('categories.code', 'ASC')
            ->get();

        // Group sales by branch
        $salesByBranch = $sales->groupBy('branch_id');

        // Reset 'all' to make it more readable in the UI
        if (in_array('%', $category_id1)) {
            $category_id1 = "all";
        }
        if (in_array('%', $branch_id)) {
            $branch_id = "all";
        }
        if ($company_id == '%') {
            $company_id = 'all';
        }

        // Fetch branch info if a specific branch is selected
        $branch = null;
        if ($branch_id != 'all') {
            $branch = Branch::find($branch_id);
        }
        $company = null;
        if ($company_id != 'all') {
            $company = Company::find($company_id);
        }

        return view('pages.reports.sales_and_cash_analysis.print_category_sale_site_report', compact('salesByBranch', 'from_date', 'to_date', 'branch', 'company'));
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $category_id = $request->category_id;
        $staff_id = $request->staff_id;

        if ($product_id == '') {
            $product_id = '%';
        }
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
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
            ->select('customers.code AS customer', 'orders.reference', 'products.name AS product', 'order_details.unit as product_unit', 'stores.code AS store', 'order_details.quantity', 'sold_price', 'cost_price', 'users.user_code AS user', 'users.name AS name', 'order_date', 'orders.id as order_id')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_staff_sale_report', compact('sales', 'from_date', 'to_date', 'branch', 'company_id', 'branch_id', 'product_id', 'store_id', 'category_id', 'staff_id', 'total_cash', 'user'));
    }

    public function printStaffSaleReport($from_date, $to_date, $company_id, $branch_id, $store_id, $category_id, $product_id, $staff_id)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_staff_sale_report', compact('sales', 'from_date', 'to_date', 'branch', 'total_cash', 'user'));
    }

    public function staffRelationOfficerReport()
    {
        $relation_officers = User::where('is_sale_representative', 1)->orderBy('name')->get();
        return view('pages.reports.sales_and_cash_analysis.relation_officer_report', compact('relation_officers'));
    }

    public function loadRelationOfficerReport(Request $request)
    {
        $is_summary = $request->is_summary;
        $user_id = $request->input('user_id', ['%']);

        // Remove any '%' values from the array if it's not the only value
        if (count($user_id) > 1) {
            $user_id = array_filter($user_id, function ($value) {
                return $value !== '%';
            });
        }
        // $branch_id = $request->input('branch_id', ['%']);

        // // Remove any '%' values from the array if it's not the only value
        // if (count($branch_id) > 1) {
        //     $branch_id = array_filter($branch_id, function ($value) {
        //         return $value !== '%';
        //     });
        // }
        $category_id1 = $request->input('category_id1', ['%']);

        // Remove any '%' values from the array if it's not the only value
        if (count($category_id1) > 1) {
            $category_id1 = array_filter($category_id1, function ($value) {
                return $value !== '%';
            });
        }


        $from_date = date('Y-m-d', strtotime($request->from_date));
        $to_date = date('Y-m-d', strtotime($request->to_date));
        $company_id = $request->company_id;
        // $branch_id = json_encode($request->branch_id);
        // $category_id1 = json_encode($request->category_id1);  // Now an array
        // $user_id = json_encode($request->user_id);
        // Handle 'all' for company and branch
        if ($company_id == 'all' || $company_id == '' || $company_id == null) {
            $company_id = '%';
        }

        // Check if any categories are selected, or treat as "all"
        // $category_id1 = is_array($category_id1) ? $category_id1 : ['%'];  // Convert to array if not already
        // $branch_id = is_array($branch_id) ? $branch_id : ['%'];  // Convert to array if not already
        // $user_id = is_array($user_id) ? $user_id : ['%'];  // Convert to array if not already

        // Build the query
        // $data = DB::table('orders')
        //     ->select(
        //         'branches.id as branch_id',
        //         'branches.name as branch_name',
        //         'branches.code as branch_code',
        //         'categories.name as category',
        //         'categories.code as code',
        //         'users.user_code as ro_code',
        //         'users.name as user_name',
        //         DB::raw('SUM(order_details.quantity) as quantity'),
        //         DB::raw('SUM(order_details.total) as amount'),
        //         DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
        //         DB::raw('(SELECT SUM(store_products.qty_available)
        //         FROM store_products
        //         JOIN products p ON store_products.product_id = p.id
        //         WHERE store_products.store_id IN (SELECT id FROM stores WHERE branch_id = branches.id)
        //         AND p.category_id = categories.id) as qty_available')
        //     )
        //     ->join('order_details', 'orders.id', '=', 'order_details.order_id')
        //     ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
        //     ->join('stores', 'store_products.store_id', '=', 'stores.id')
        //     ->join('branches', 'stores.branch_id', '=', 'branches.id')
        //     ->join('companies', 'branches.company_id', '=', 'companies.id')
        //     ->join('products', 'store_products.product_id', '=', 'products.id')
        //     ->join('categories', 'products.category_id', '=', 'categories.id')
        //     ->join('customers', 'customers.id', 'orders.customer_id')
        //     ->join('users', 'users.id', 'customers.relation_officer')
        //     ->where('branches.company_id', 'LIKE', $company_id)
        //     ->where('order_details.status', '=', 1)
        //     ->whereBetween('order_date', [$from_date, $to_date]);

        // if (!in_array('%', $category_id1)) {
        //     $data = $data->whereIn('products.category_id', $category_id1);
        // }
        // // if (!in_array('%', $branch_id)) {
        // //     $data = $data->whereIn('stores.branch_id', $branch_id);
        // // }
        // if (!in_array('%', $user_id)) {
        //     $data = $data->whereIn('customers.relation_officer', $user_id);
        // }


        // $salesByBranch = $data->groupBy('products.category_id')
        //     ->orderBy('branches.name', 'ASC')
        //     ->orderBy('categories.code', 'ASC')
        //     ->get();

        //        $data = DB::table('orders')
//            ->select(
//                'branches.id as branch_id',
//                'branches.name as branch_name',
//                'branches.code as branch_code',
//                'categories.name as category',
//                'categories.code as code',
//                'users.user_code as ro_code',
//                'users.id as ro_id',
//                'users.name as user_name',
//                DB::raw('SUM(order_details.quantity) as quantity'),
//                DB::raw('SUM(order_details.total) as amount'),
//                DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
//                DB::raw('(SELECT SUM(store_products.qty_available)
//            FROM store_products
//            JOIN products p ON store_products.product_id = p.id
//            WHERE store_products.store_id IN (SELECT id FROM stores WHERE branch_id = branches.id)
//            AND p.category_id = categories.id) as qty_available')
//            )
//            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
//            ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
//            ->join('stores', 'store_products.store_id', '=', 'stores.id')
//            ->join('branches', 'stores.branch_id', '=', 'branches.id')
//            ->join('companies', 'branches.company_id', '=', 'companies.id')
//            ->join('products', 'store_products.product_id', '=', 'products.id')
//            ->join('categories', 'products.category_id', '=', 'categories.id')
//            ->join('customers', 'customers.id', 'orders.customer_id')
//            ->join('users', 'users.id', 'customers.relation_officer')
//            ->where('branches.company_id', 'LIKE', $company_id)
//            ->where('order_details.status', '=', 1)
//            ->whereBetween('order_date', [$from_date, $to_date]);


        if ($is_summary)
            $data = DB::table('orders')
                ->select(
                    'users.id as ro_id',
                    'users.user_code as ro_code',
                    'users.name as ro_name',
                    'companies.id as company_id',
                    'companies.name as company_name',
                    'branches.id as branch_id',
                    'branches.name as branch_name',
                    DB::raw('SUM(order_details.quantity) as total_quantity'),
                    DB::raw('SUM(order_details.total) as amount'),
                    DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
                )
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
                ->join('stores', 'store_products.store_id', '=', 'stores.id')
                ->join('branches', 'stores.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->join('customers', 'customers.id', '=', 'orders.customer_id')
                ->join('users', 'users.id', '=', 'customers.relation_officer')
                ->where('branches.company_id', 'LIKE', $company_id)
                ->where('order_details.status', '=', 1)
                ->whereBetween('order_date', [$from_date, $to_date])
                ->groupBy('users.id', 'users.user_code', 'users.name', 'companies.id', 'companies.name', 'branches.id', 'branches.name')
                ->orderBy('users.name', 'ASC');
        else
            $data = DB::table('orders')
                ->select(
                    'branches.id as branch_id',
                    'branches.name as branch_name',
                    'branches.code as branch_code',
                    'categories.name as category',
                    'categories.code as code',
                    'users.user_code as ro_code',
                    'users.id as ro_id',
                    'users.name as user_name',
                    'order_details.unit as product_unit',
                    DB::raw('SUM(order_details.quantity) as quantity'),
                    DB::raw('SUM(order_details.total) as amount'),
                    DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
                    DB::raw('COALESCE(qty_data.qty_available, 0) as qty_available')
                )
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
                ->join('stores', 'store_products.store_id', '=', 'stores.id')
                ->join('branches', 'stores.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->join('products', 'store_products.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('customers', 'customers.id', '=', 'orders.customer_id')
                ->join('users', 'users.id', '=', 'customers.relation_officer')
                // Left join a pre-aggregated subquery for qty_available
                ->leftJoin(
                    DB::raw('(SELECT stores.branch_id, p.category_id, SUM(store_products.qty_available) AS qty_available
                 FROM store_products
                 JOIN products p ON store_products.product_id = p.id
                 JOIN stores ON store_products.store_id = stores.id
                 GROUP BY stores.branch_id, p.category_id) AS qty_data'),
                    function ($join) {
                        $join->on('qty_data.branch_id', '=', 'branches.id')
                            ->on('qty_data.category_id', '=', 'categories.id');
                    }
                )
                ->where('branches.company_id', 'LIKE', $company_id)
                ->where('order_details.status', '=', 1)
                ->whereBetween('order_date', [$from_date, $to_date]);


        if (!in_array('%', $category_id1)) {
            $data = $data->whereIn('products.category_id', $category_id1);
        }
        if (!in_array('%', $user_id)) {
            $data = $data->whereIn('customers.relation_officer', $user_id);
        }

        //        $salesByOfficer = $data
//            ->groupBy('customers.relation_officer', 'products.category_id')
//            ->orderBy('users.name', 'ASC')
//            ->orderBy('categories.code', 'ASC')
//            ->get()
//            ->groupBy('ro_id');

        $salesByOfficer = $is_summary ? $data->get() : $data
            ->groupBy('customers.relation_officer', 'products.category_id', 'branches.id', 'categories.id', 'users.id', 'users.name', 'branches.name', 'branches.code', 'categories.code', 'users.user_code', 'qty_data.qty_available')
            ->orderBy('users.name', 'ASC')
            ->orderBy('categories.code', 'ASC')
            ->get()
            ->groupBy('ro_id');

        //$salesByBranch = $data->get();

        // // Group sales by branch
        // $salesByBranch = $sales->groupBy('branch_id');

        // Reset 'all' to make it more readable in the UI
        if (in_array('%', $category_id1)) {
            $category_id1 = "all";
        }
        // if (in_array('%', $branch_id)) {
        //     $branch_id = "all";
        // }
        if (in_array('%', $user_id)) {
            $user_id = "all";
        }
        if ($company_id == '%') {
            $company_id = 'all';
        }

        // Fetch branch info if a specific branch is selected
        // $branch = null;
        // if ($branch_id != 'all') {
        //     $branch = Branch::find($branch_id);
        // }
        $company = null;
        if ($company_id != 'all') {
            $company = Company::find($company_id);
        }
        // Return the view with the data
        return view(
            'pages.reports.sales_and_cash_analysis.load_relation_officer_report',
            compact('salesByOfficer', 'from_date', 'to_date', 'category_id1', 'user_id', 'company', 'company_id', 'is_summary')
        );

    }

    public function printRelationOfficerReport($from_date, $to_date, $company_id, $branch_id, $store_id, $category_id, $product_id, $staff_id)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';

        if (count($staff_id) > 1) {
            $user_id = array_filter($staff_id, function ($value) {
                return $value !== '%';
            });
        }


        // Remove any '%' values from the array if it's not the only value
        if (count($branch_id) > 1) {
            $branch_id = array_filter($branch_id, function ($value) {
                return $value !== '%';
            });
        }

        // Remove any '%' values from the array if it's not the only value
        if (count($category_id) > 1) {
            $category_id = array_filter($category_id, function ($value) {
                return $value !== '%';
            });
        }


        $data = DB::table('orders')
            ->select(
                'branches.id as branch_id',
                'branches.name as branch_name',
                'branches.code as branch_code',
                'categories.name as category',
                'categories.code as code',
                'users.user_code as ro_code',
                'users.name as user_name',
                DB::raw('SUM(order_details.quantity) as quantity'),
                DB::raw('SUM(order_details.total) as amount'),
                DB::raw('SUM(order_details.cost_price * order_details.quantity) as cost'),
                DB::raw('(SELECT SUM(store_products.qty_available)
                FROM store_products
                JOIN products p ON store_products.product_id = p.id
                WHERE store_products.store_id IN (SELECT id FROM stores WHERE branch_id = branches.id)
                AND p.category_id = categories.id) as qty_available')
            )
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('store_products', 'order_details.store_product_id', '=', 'store_products.id')
            ->join('stores', 'store_products.store_id', '=', 'stores.id')
            ->join('branches', 'stores.branch_id', '=', 'branches.id')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->join('products', 'store_products.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('users', 'users.id', 'customers.relation_officer')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('order_details.status', '=', 1)
            ->whereBetween('order_date', [$from_date, $to_date]);

        if (!in_array('%', $category_id)) {
            $data = $data->whereIn('products.category_id', $category_id);
        }
        if (!in_array('%', $branch_id)) {
            $data = $data->whereIn('stores.branch_id', $branch_id);
        }
        if (!in_array('%', $staff_id)) {
            $data = $data->whereIn('customers.relation_officer', $staff_id);
        }


        $sales = $data->groupBy('branches.id', 'products.category_id')
            ->orderBy('branches.name', 'ASC')
            ->orderBy('categories.code', 'ASC')
            ->get();

        // Group sales by branch
        $salesByBranch = $sales->groupBy('branch_id');

        $total_cash = Order::where('sold_by', 'LIKE', $staff_id)->where('status', 1)->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])->sum('total');
        $user = User::find($staff_id);
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_relation_officer_report', compact('sales', 'from_date', 'to_date', 'branch', 'total_cash', 'user'));
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
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
            'products.unit AS product_unit',
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
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);

        return view('pages.reports.stock_control.load_stock_ledger_report', [
            'records' => $records,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'company_id' => $company_id,
            'company' => $company,
            'branch_id' => $branch_id,
            'store_id' => $store_id,
            'product_id' => $product_id,
            'qty_available' => $qty_available,
        ]);
    }

    public function printStockLedger($from_date, $to_date, $company_id, $branch_id, $store_id, $product_id)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);

        return view('pages.reports.stock_control.print_stock_ledger_report', [
            'records' => $records,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'company_id' => $company_id,
            'company' => $company,
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        $customer = $request->customer;
        $payment_mode = $request->payment_mode;
        $credit_walkedin = $request->credit_walkedin;
        $matching = $request->matching;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
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
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.load_report_with_common_name', compact('sales', 'from_date', 'to_date', 'branch', 'company_id', 'branch_id', 'product_id', 'store_id', 'category_id', 'customer', 'matching'));
    }

    public function printCustomerSaleReport($from_date, $to_date, $company_id, $branch_id, $store_id, $category_id, $product_id, $customer, $matching)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_report_with_common_name', compact('sales', 'from_date', 'to_date', 'company', 'branch', 'product_id', 'store_id', 'category_id', 'customer'));
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == '' || $branch_id == 'all')
            $branch_id = '%';

        $sales = DB::table('orders')
            ->select('products.name AS item', 'products.code AS code', 'order_details.unit as item_unit', DB::raw("SUM(order_details.quantity) AS quantity"), DB::raw("SUM(order_details.quantity * cost_price) AS total_cost"), DB::raw("SUM(order_details.total) AS total"), DB::raw("SUM(order_details.quantity * cost_price) - SUM(order_details.total) AS margin"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_most_sold_item_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'branch', 'type', 'number_limit'));
    }

    public function printMostSoldItemReport($from_date, $to_date, $company_id, $branch_id, $type, $number_limit)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == '' || $branch_id == 'all')
            $branch_id = '%';

        $sales = DB::table('orders')
            ->select('products.name AS item', 'products.code AS code', DB::raw("SUM(order_details.quantity) AS quantity"), DB::raw("SUM(order_details.quantity * cost_price) AS total_cost"), DB::raw("SUM(order_details.total) AS total"), DB::raw("SUM(order_details.quantity * cost_price) - SUM(order_details.total) AS margin"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_most_sold_item_report', compact('sales', 'from_date', 'to_date', 'branch', 'type', 'number_limit'));
    }

    public function bestPerformingCustomerReport()
    {
        return view('pages.reports.sales_and_cash_analysis.best_performing_customers_report');
    }

    public function loadBestPerformingCustomerReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $number_limit = $request->number_limit;
        $type = $request->type;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == '' || $branch_id == 'all')
            $branch_id = '%';

        $sales = DB::table('orders')
            ->select(
                'products.name AS item',
                'products.code AS code',
                'order_details.unit as item_unit',
                'customers.name AS customer_name',
                'customers.code AS customer_code',
                DB::raw("SUM(order_details.quantity) AS quantity"),
                DB::raw("SUM(order_details.quantity * cost_price) AS total_cost"),
                DB::raw("SUM(order_details.total) AS total"),
                DB::raw("SUM(order_details.quantity * cost_price) - SUM(order_details.total) AS margin")
            )
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('order_details.status', 1)
            ->where('stores.branch_id', 'LIKE', $branch_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date]);

        if ($type == 'qty')
            $sales = $sales->orderBy(DB::raw("SUM(order_details.quantity)"), 'DESC');
        if ($type == 'amt')
            $sales = $sales->orderBy(DB::raw("SUM(order_details.total)"), 'DESC');
        if ($type == 'mgn')
            $sales = $sales->orderBy('margin', 'ASC');

        $sales = $sales->groupBy('orders.customer_id')
            ->take($number_limit)
            ->get();
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_best_performing_customers_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'branch', 'type', 'number_limit'));
    }

    public function printBestPerformingCustomereport($from_date, $to_date, $company_id, $branch_id, $type, $number_limit)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == '' || $branch_id == 'all')
            $branch_id = '%';

        $sales = DB::table('orders')
            ->select('products.name AS item', 'products.code AS code', DB::raw("SUM(order_details.quantity) AS quantity"), DB::raw("SUM(order_details.quantity * cost_price) AS total_cost"), DB::raw("SUM(order_details.total) AS total"), DB::raw("SUM(order_details.quantity * cost_price) - SUM(order_details.total) AS margin"))
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('store_products', 'store_products.id', 'order_details.store_product_id')
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $product_id = $request->product_id;
        $category_id = $request->category_id;
        $customer_id = $request->customer_id;
        $credit_walkedin = $request->credit_walkedin;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
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
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.load_item_sold_report', compact('sales', 'from_date', 'to_date', 'branch', 'company_id', 'branch_id', 'product_id', 'category_id', 'customer_id'));
    }

    public function printItemSoldReport($from_date, $to_date, $company_id, $branch_id, $category_id, $product_id, $customer_id)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;

        if ($customer_id == 'all' || $customer_id == '') {
            $customer_id = '%';
        }
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }

        $sales = Customer::select(
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
            ->join('branches', 'branches.id', '=', 'general_account_ledgers.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);

        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.customer_ledger_analysis.load_ageing_report', compact('sales', 'from_date', 'branch', 'to_date', 'company_id', 'branch_id', 'customer_id'));
    }

    public function printAgeingReport($from_date, $to_date, $company_id, $branch_id, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
            ->join('branches', 'branches.id', '=', 'general_account_ledgers.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.print_ageing_report', compact('sales', 'from_date', 'to_date', 'branch', 'company_id', 'branch_id', 'customer_id'));
    }

    public function lastTransaction()
    {
        return view('pages.reports.customer_ledger_analysis.customer_last_transaction_report');
    }

    public function loadLastTransaction(Request $request)
    {
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $customer_id = $request->customer_id;

        if ($customer_id == 'all' || $customer_id == '' || $customer_id == null) {
            $customer_id = '%';
        }

        if ($company_id == 'all' || $company_id == '' || $company_id == null) {
            $company_id = '%';
        }

        if ($branch_id == 'all' || $branch_id == '' || $branch_id == null) {
            $branch_id = '%';
        }

        $sales = Customer::with('last_transaction')
            ->select('customers.id', 'customers.name AS customer', DB::raw('SUM(general_account_ledgers.credit) - SUM(general_account_ledgers.debit) AS balance'), 'customers.code')
            ->join('general_account_ledgers', 'general_account_ledgers.model_id', 'customers.id')
            ->join('branches', 'branches.id', '=', 'general_account_ledgers.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('general_account_ledgers.model_id', 'LIKE', $customer_id)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('model_name', 'Customer')
            ->groupBy('general_account_ledgers.model_id')
            ->orderBy('general_account_ledgers.date')
            ->limit(5)
            ->get();

        if ($customer_id == "%")
            $customer_id = "all";

        if ($branch_id == "%")
            $branch_id = "all";

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);

        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.customer_ledger_analysis.load_customer_last_transaction_report', compact('sales', 'branch', 'company_id', 'branch_id', 'customer_id'));
    }

    public function printLastTransaction($company_id, $branch_id, $customer_id)
    {

        if ($customer_id == 'all') {
            $customer_id = '%';
        }
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        $sales = DB::table('customers')
            ->select('customers.name AS customer', DB::raw('SUM(general_account_ledgers.credit) - SUM(general_account_ledgers.debit) AS balance'), 'customers.code', 'customers.id AS customer_id')
            ->join('general_account_ledgers', 'general_account_ledgers.model_id', 'customers.id')
            ->join('branches', 'branches.id', '=', 'general_account_ledgers.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $status = $request->status;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = CreditNote::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'credit_notes.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('credit_notes.status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_credit_notes_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'status', 'branch'));
    }

    public function printCreditNoteReport($from_date, $to_date, $company_id, $branch_id, $status)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = CreditNote::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'credit_notes.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('credit_notes.status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_credit_notes_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }

    public function creditNoteLinesReport()
    {
        return view('pages.reports.sales_and_cash_analysis.credit_notes_lines_report');
    }

    public function loadCreditNoteLinesReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $status = $request->status;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = CreditNote::where('branch_id', 'LIKE', $branch_id)
            ->select('credit_notes.*')
            ->join('branches', 'branches.id', '=', 'credit_notes.branch_id')
            ->join('credit_note_details', 'credit_note_details.credit_note_id', '=', 'credit_notes.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('credit_notes.status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_credit_notes_lines_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'status', 'branch'));
    }

    public function printCreditNoteLinesReport($from_date, $to_date, $company_id, $branch_id, $status)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = CreditNote::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'credit_notes.branch_id')
            ->join('credit_note_details', 'credit_note_details.credit_note_id', '=', 'credit_notes.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('credit_notes.status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.print_credit_notes_lines_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }

    public function invoiceReport()
    {

        return view('pages.reports.sales_and_cash_analysis.list_of_invoices_report');
    }

    public function loadInvoiceReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $status = $request->status;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = Order::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'orders.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('orders.status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_list_of_invoices_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'status', 'branch'));
    }

    public function invoiceLinesReport()
    {
        return view('pages.reports.sales_and_cash_analysis.invoice_lines_report');
    }

    public function loadInvoiceLinesReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $status = $request->status;
        $where = [];
        if ($company_id != '' && $company_id != '%' && $company_id != null)
            $where[] = ['branches.company_id', $company_id];

        if ($branch_id != '' && $branch_id != '%' && $branch_id != null)
            $where[] = ['orders.branch_id', $branch_id];

        if ($status != '' && $status != '%' && $status != null)
            $where[] = ['orders.status', $status];

        $sales = Order::with(['customer', 'order_items'])
            ->join('branches', 'branches.id', '=', 'orders.branch_id')
            ->select('orders.*')
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where($where)
            ->orderBy('order_date', 'DESC')
            ->get();

        if ($company_id == '%' || $company_id == null || $company_id == '')
            $company_id = 'all';

        if ($branch_id == "%" || $branch_id == null || $branch_id == '')
            $branch_id = "all";

        if ($status == "%" || $status == null || $status == '')
            $status = "all";

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);

        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.load_invoice_lines_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'status', 'branch'));
    }

    public function printInvoiceLinesReport($from_date, $to_date, $company_id, $branch_id, $status)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = Order::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'orders.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('orders.status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $status = $request->status;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = OrderInvoice::where('branch_id', 'LIKE', $branch_id)
            ->select('order_Invoices.*')
            ->join('branches', 'branches.id', '=', 'order_invoices.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('order_invoices.status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.sales_and_cash_analysis.load_orders_report', compact('sales', 'from_date', 'to_date', 'company_id', 'company_id', 'branch_id', 'status', 'branch'));
    }

    public function printOrderReport($from_date, $to_date, $company_id, $branch_id, $status)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = OrderInvoice::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'order_invoices.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('order_invoices.status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $status = $request->status;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }

        $sales = OrderInvoice::with(['customer', 'order_items'])
            ->join('branches', 'branches.id', '=', 'order_invoices.branch_id')
            ->select('order_invoices.*')
            ->where('branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('order_invoices.status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();

        if ($branch_id == "%")
            $branch_id = "all";

        if ($status == "%")
            $status = "all";

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);

        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);

        return view('pages.reports.sales_and_cash_analysis.load_order_lines_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'status', 'branch'));
    }

    public function printOrderLinesReport($from_date, $to_date, $company_id, $branch_id, $status)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = OrderInvoice::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'order_invoices.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(order_date)"), [$from_date, $to_date])
            ->where('order_invoices.status', 'LIKE', $status)
            ->orderBy('order_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
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
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }


        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'stores.code AS store', 'purchase_products.quantity AS quantity', 'unit_price', 'purchases.purchase_date', 'wbno', 'purchases.atc_no', 'purchases.id AS purchase_id')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.load_purchase_invoice_lines_report', compact('sales', 'from_date', 'to_date', 'product_id', 'store_id', 'category_id', 'supplier_id', 'company_id', 'branch_id', 'branch', 'status'));
    }

    public function printPurchaseInvoiceLinesReport($from_date, $to_date, $company_id, $branch_id, $store_id, $category_id, $product_id, $supplier_id, $status)
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
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($status == 'all') {
            $status = '%';
        }
        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'stores.code AS store', 'purchase_products.quantity AS quantity', 'unit_price', 'purchases.purchase_date', 'wbno', 'purchases.atc_no')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $status = $request->status;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = ReturnDebit::where('branch_id', 'LIKE', $branch_id)
            ->select('return_debits.*')
            ->join('branches', 'branches.id', '=', 'return_debits.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('return_debits.status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        if ($status == "%")
            $status = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.load_return_debit_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'status', 'branch'));
    }

    public function printReturnDebitReport($from_date, $to_date, $company_id, $branch_id, $status)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }
        $sales = ReturnDebit::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'return_debits.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereBetween(DB::raw("DATE(date)"), [$from_date, $to_date])
            ->where('return_debits.status', 'LIKE', $status)
            ->orderBy('date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;

        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        $store_ids = Store::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->get()->pluck('stores.id')->toArray();
        $sales = StoreProductBatch::whereIn('store_id', $store_ids)
            ->whereBetween(DB::raw("DATE(expiry_date)"), [$from_date, $to_date])
            ->orderBy('expiry_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.load_expiry_date_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'branch'));
    }

    public function printExpiryReport($from_date, $to_date, $company_id, $branch_id)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }

        $store_ids = Store::where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->get()->pluck('stores.id')->toArray();
        $sales = StoreProductBatch::whereIn('store_id', $store_ids)
            ->whereBetween(DB::raw("DATE(expiry_date)"), [$from_date, $to_date])
            ->orderBy('expiry_date', 'DESC')
            ->get();
        if ($branch_id == "%")
            $branch_id = "all";
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.print_expiry_date_report', compact('sales', 'from_date', 'to_date', 'branch'));
    }

    public function additionalInvoiceReport()
    {
        return view('pages.reports.inventory.additional_invoice_report');
    }

    // public function loadAdditionalInvoiceReport(Request $request)
    // {
    //     $from_date = $request->from_date;
    //     $to_date = $request->to_date;

    //     $supplier_id = $request->supplier_id;
    //     $company_id = $request->company_id;
    //     $branch_id = $request->branch_id;
    //     $status = $request->status;


    //     if ($supplier_id == 'all' || $supplier_id == '') {
    //         $supplier_id = '%';
    //     }
    //     if ($company_id == 'all' || $company_id == '') {
    //         $company_id = '%';
    //     }
    //     if ($branch_id == 'all' || $branch_id == '') {
    //         $branch_id = '%';
    //     }
    //     if ($status == 'all' || $status == '') {
    //         $status = '%';
    //     }

    //     $sales = DB::table('purchases')
    //         ->select('suppliers.name AS supplier', 'purchases.reference', 'purchase_expenses.reference AS ref', 'stores.code AS store', 'description', 'purchase_expenses.name AS expense', 'purchases.purchase_date', 'wbno', 'amount', 'purchase_expenses.status', 'purchases.created_at', 'users.name AS created_by')
    //         ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
    //         ->join('purchase_expenses', 'purchase_expenses.purchase_id', 'purchases.id')
    //         ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
    //         ->join('stores', 'stores.id', 'purchase_products.store_id')
    //         ->join('users', 'users.id', 'purchases.created_by')
    //         ->join('branches', 'branches.id', '=', 'stores.branch_id')
    //         ->where('branches.company_id', 'LIKE', $company_id)
    //         ->where('purchases.supplier_id', 'LIKE', $supplier_id)
    //         ->where('purchases.supplier_id', 'LIKE', $supplier_id)
    //         ->where('purchases.status', 'LIKE', $status)
    //         ->where('suppliers.code', 'LIKE', 'T%')
    //         ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
    //         ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
    //         ->orderBy('purchase_date')
    //         ->get();
    //     if ($supplier_id == "%")
    //         $supplier_id = "all";
    //     if ($branch_id == "%")
    //         $branch_id = "all";
    //     $company = null;
    //     if ($company_id != 'all')
    //         $company = Company::find($company_id);
    //     $branch = null;
    //     if ($branch_id != "all")
    //         $branch = Branch::find($branch_id);

    //     return view('pages.reports.inventory.load_additional_invoice_report', compact('sales', 'from_date', 'to_date', 'supplier_id', 'company_id', 'branch_id', 'branch', 'status'));
    // }
    public function loadAdditionalInvoiceReport(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $supplier_id = $request->supplier_id === 'all' || empty($request->supplier_id) ? '%' : $request->supplier_id;
        $company_id = $request->company_id === 'all' || empty($request->company_id) ? '%' : $request->company_id;
        $branch_id = $request->branch_id === 'all' || empty($request->branch_id) ? '%' : $request->branch_id;
        $status = $request->status === 'all' || empty($request->status) ? '%' : $request->status;

        $sales = DB::table('purchases')
            ->select(
                'purchase_expenses.reference',
                'purchase_expenses.id AS purchase_id',
                'suppliers.name AS supplier',
                'description',
                'branches.code AS branch',
                'purchases.purchase_date',
                'wbno',
                DB::raw('SUM(amount) AS amount'),  // Aggregating amount
                'purchase_expenses.status',
                'purchases.created_at',
                'users.name AS created_by',
                'suppliers.code'
            )
            ->join('purchase_products', 'purchase_products.purchase_id', '=', 'purchases.id')
            ->join('purchase_expenses', 'purchase_expenses.purchase_id', '=', 'purchases.id')
            ->join('suppliers', 'suppliers.id', '=', 'purchase_expenses.supplier_id')
            ->join('users', 'users.id', '=', 'purchases.created_by')
            ->join('branches', 'branches.id', '=', 'purchases.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('purchases.branch_id', 'LIKE', $branch_id)
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.status', 'LIKE', $status)
            //->whereRaw("TRIM(LOWER(suppliers.code)) LIKE LOWER(?)", ['T%'])
            ->whereBetween(DB::raw("DATE(purchases.purchase_date)"), [$from_date, $to_date])
            ->groupBy(
                'purchase_expenses.reference',
                'suppliers.name',
                'description',
                'purchase_expenses.name',
                'purchases.purchase_date',
                'wbno',
                'purchase_expenses.status',
                'purchases.created_at',
                'users.name'
            )
            ->orderBy('purchases.purchase_date')
            ->get();


        $company = $company_id !== '%' ? Company::find($company_id) : null;
        $branch = $branch_id !== '%' ? Branch::find($branch_id) : null;

        return view('pages.reports.inventory.load_additional_invoice_report', compact(
            'sales',
            'from_date',
            'to_date',
            'supplier_id',
            'company_id',
            'branch_id',
            'branch',
            'status'
        ));
    }

    public function printAdditionalInvoiceReport($from_date, $to_date, $company_id, $branch_id, $supplier_id, $status)
    {

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($status == 'all') {
            $status = '%';
        }
        $sales = DB::table('purchases')
            ->select(
                'purchase_expenses.reference AS ref',
                'suppliers.name AS supplier',
                'purchases.reference',
                'description',
                'branches.code AS branch',
                'purchases.purchase_date',
                'wbno',
                DB::raw('SUM(amount) AS amount'),  // Aggregating amount
                'purchase_expenses.status',
                'purchases.created_at',
                'users.name AS created_by',
                'suppliers.code'
            )
            ->join('purchase_products', 'purchase_products.purchase_id', '=', 'purchases.id')
            ->join('purchase_expenses', 'purchase_expenses.purchase_id', '=', 'purchases.id')
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->join('users', 'users.id', '=', 'purchases.created_by')
            ->join('branches', 'branches.id', '=', 'purchases.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('purchases.branch_id', 'LIKE', $branch_id)
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.status', 'LIKE', $status)
            //->whereRaw("TRIM(LOWER(suppliers.code)) LIKE LOWER(?)", ['T%'])
            ->whereBetween(DB::raw("DATE(purchases.purchase_date)"), [$from_date, $to_date])
            ->groupBy(
                'purchase_expenses.reference',
                'suppliers.name',
                'purchases.reference',
                'description',
                'purchase_expenses.name',
                'purchases.purchase_date',
                'wbno',
                'purchase_expenses.status',
                'purchases.created_at',
                'users.name'
            )
            ->orderBy('purchases.purchase_date')
            ->get();
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $status = $request->status;


        if ($supplier_id == 'all' || $supplier_id == '') {
            $supplier_id = '%';
        }
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }


        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'stores.code AS store', 'purchase_products.quantity AS quantity', 'unit_price', 'purchases.purchase_date', 'wbno', DB::raw('SUM(quantity * unit_price) AS total'), 'purchases.status', 'purchases.atc_no', 'purchases.created_at', 'users.name', 'purchases.id AS purchase_id')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->join('users', 'users.id', 'purchases.created_by')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.load_purchase_invoice_report', compact('sales', 'from_date', 'to_date', 'supplier_id', 'company_id', 'branch_id', 'branch', 'status'));
    }
    // public function loadPurchaseInvoiceReport(Request $request)
    // {
    //     $from_date = $request->from_date;
    //     $to_date = $request->to_date;

    //     $supplier_id = $request->supplier_id === 'all' || empty($request->supplier_id) ? '%' : $request->supplier_id;
    //     $company_id = $request->company_id === 'all' || empty($request->company_id) ? '%' : $request->company_id;
    //     $branch_id = $request->branch_id === 'all' || empty($request->branch_id) ? '%' : $request->branch_id;
    //     $status = $request->status === 'all' || empty($request->status) ? '%' : $request->status;

    //     $sales = DB::table('purchases')
    //         ->select(
    //             'purchases.reference',
    //             'suppliers.name AS supplier',
    //             'stores.code AS store',
    //             'purchases.purchase_date',
    //             'purchases.wbno',
    //             DB::raw('SUM(purchase_products.quantity) AS total_quantity'),
    //             DB::raw('SUM(purchase_products.quantity * purchase_products.unit_price) AS purchase_cost'),
    //             DB::raw('COALESCE(SUM(purchase_expenses.amount), 0) AS additional_cost'),
    //             DB::raw('(SUM(purchase_products.quantity * purchase_products.unit_price) + COALESCE(SUM(purchase_expenses.amount), 0)) AS actual_cost'),
    //             'purchases.status',
    //             'purchases.atc_no',
    //             'purchases.created_at',
    //             'users.name AS created_by',
    //             'branches.name AS branch'
    //         )
    //         ->join('purchase_products', 'purchase_products.purchase_id', '=', 'purchases.id')
    //         ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
    //         ->join('stores', 'stores.id', '=', 'purchase_products.store_id')
    //         ->join('users', 'users.id', '=', 'purchases.created_by')
    //         ->join('branches', 'branches.id', '=', 'stores.branch_id')
    //         ->leftJoin('purchase_expenses', 'purchase_expenses.purchase_id', '=', 'purchases.id') // Include expenses
    //         ->where('branches.company_id', 'LIKE', $company_id)
    //         ->where('purchases.supplier_id', 'LIKE', $supplier_id)
    //         ->where('purchases.status', 'LIKE', $status)
    //         ->where('purchases.branch_id', 'LIKE', $branch_id)
    //         ->whereBetween(DB::raw("DATE(purchases.purchase_date)"), [$from_date, $to_date])
    //         ->groupBy(
    //             'purchases.reference',
    //             'suppliers.name',
    //             'stores.code',
    //             'purchases.purchase_date',
    //             'purchases.wbno',
    //             'purchases.status',
    //             'purchases.atc_no',
    //             'purchases.created_at',
    //             'users.name',
    //             'branches.name'
    //         )
    //         ->orderBy('purchases.purchase_date')
    //         ->get();

    //     $company = $company_id !== '%' ? Company::find($company_id) : null;
    //     $branch = $branch_id !== '%' ? Branch::find($branch_id) : null;

    //     return view('pages.reports.inventory.load_purchase_invoice_report', compact(
    //         'sales', 'from_date', 'to_date', 'supplier_id', 'company_id', 'branch_id', 'branch', 'status'
    //     ));
    // }

    public function printPurchaseInvoiceReport($from_date, $to_date, $company_id, $branch_id, $supplier_id, $status)
    {

        if ($supplier_id == 'all') {
            $supplier_id = '%';
        }
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($status == 'all') {
            $status = '%';
        }
        $sales = DB::table('purchases')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'stores.code AS store', 'purchase_products.quantity AS quantity', 'unit_price', 'purchases.purchase_date', 'wbno', DB::raw('SUM(quantity * unit_price) AS total'), 'purchases.status', 'purchases.atc_no', 'purchases.created_at', 'users.name', 'purchases.id AS purchase_id')
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->join('stores', 'stores.id', 'purchase_products.store_id')
            ->join('products', 'products.id', 'purchase_products.product_id')
            ->join('users', 'users.id', 'purchases.created_by')
            ->join('branches', 'branches.id', '=', 'stores.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('purchases.supplier_id', 'LIKE', $supplier_id)
            ->where('purchases.status', 'LIKE', $status)
            ->where('purchases.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->groupBy('purchase_products.purchase_id')
            ->get();
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
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
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }


        $sales = DB::table('purchase_requests')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'purchase_product_requests.quantity', 'unit_price', 'purchase_requests.purchase_date', 'wbno', 'purchase_requests.status', 'purchase_requests.created_at', 'users.name', 'purchase_requests.id AS request_id')
            ->join('purchase_product_requests', 'purchase_product_requests.purchase_id', 'purchase_requests.id')
            ->join('suppliers', 'suppliers.id', 'purchase_requests.supplier_id')
            ->join('products', 'products.id', 'purchase_product_requests.product_id')
            ->join('users', 'users.id', 'purchase_requests.updated_by')
            ->join('branches', 'branches.id', '=', 'purchase_requests.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.load_purchase_request_report', compact('sales', 'from_date', 'to_date', 'supplier_id', 'company_id', 'branch_id', 'category_id', 'product_id', 'branch', 'status'));
    }

    public function printPurchaseRequestReport($from_date, $to_date, $company_id, $branch_id, $category_id, $product_id, $supplier_id, $status)
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
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all') {
            $branch_id = '%';
        }
        if ($status == 'all') {
            $status = '%';
        }
        $sales = DB::table('purchase_requests')
            ->select('suppliers.name AS supplier', 'reference', 'products.name AS product', 'purchase_product_requests.quantity', 'unit_price', 'purchase_requests.purchase_date', 'wbno', 'purchase_requests.status', 'purchase_requests.created_at', 'users.name')
            ->join('purchase_product_requests', 'purchase_product_requests.purchase_id', 'purchase_requests.id')
            ->join('suppliers', 'suppliers.id', 'purchase_requests.supplier_id')
            ->join('products', 'products.id', 'purchase_product_requests.product_id')
            ->join('users', 'users.id', 'purchase_requests.updated_by')
            ->join('branches', 'branches.id', '=', 'purchase_requests.branch_id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->where('purchase_product_requests.product_id', 'LIKE', $product_id)
            ->where('purchase_requests.supplier_id', 'LIKE', $supplier_id)
            ->where('purchase_requests.status', 'LIKE', $status)
            ->where('purchase_requests.branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(purchase_date)"), '>=', $from_date)
            ->where(DB::raw("DATE(purchase_date)"), '<=', $to_date)
            ->orderBy('purchase_date')
            ->get();
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $status = $request->status;

        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%';
        }
        if ($status == 'all' || $status == '') {
            $status = '%';
        }


        $sales = DB::table('intersite_transfers')
            ->select('vehicle_no', 'description', 'reference', 'products.name AS product', 'products.code AS code', 'intersite_transfer_products.quantity', 'cost_price', 'intersite_transfers.date', 'intersite_transfers.status', 'source_branch.code AS source', 'destination_branch.code AS destination', 'intersite_transfers.id AS transfer_id')
            ->join('intersite_transfer_products', 'intersite_transfer_products.intersite_transfer_id', 'intersite_transfers.id')
            ->join('branches as source_branch', 'source_branch.id', '=', 'intersite_transfers.source_branch_id')
            ->join('branches as destination_branch', 'destination_branch.id', '=', 'intersite_transfers.destination_branch_id')
            ->join('products', 'products.id', 'intersite_transfer_products.product_id')
            ->where('source_branch.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != "all")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.load_goods_in_transit_report', compact('sales', 'from_date', 'to_date', 'company_id', 'branch_id', 'branch', 'status'));
    }

    public function printGoodsInTransitReport($from_date, $to_date, $company_id, $branch_id, $status)
    {

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
            ->where('source_branch.company_id', 'LIKE', $company_id)
            ->where('intersite_transfers.status', 'LIKE', $status)
            ->where('intersite_transfers.destination_branch_id', 'LIKE', $branch_id)
            ->where(DB::raw("DATE(date)"), '>=', $from_date)
            ->where(DB::raw("DATE(date)"), '<=', $to_date)
            ->orderBy('date')
            ->get();
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
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
            ->groupBy('stock_cards.product_id')
            ->where('stock_cards.date', '<=', $date)
            ->with('store', 'product')
            ->join('products', 'products.id', 'stock_cards.product_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->havingRaw("(credit - debit) > 0")
            ->orderBy('stock_cards.created_at', 'desc');
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
        if ($category_id == '') {
            $stock_cards->orderBy('categories.name', 'asc');
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != "")
            $branch = Branch::find($branch_id);

        return view('pages.reports.inventory.product_valuation.load_product_valuation_report', compact('stock_cards', 'date', 'company_id', 'branch_id', 'store_id', 'category_id', 'product_id', 'branch', 'store_group'));
    }

    public function printProductValuationReport(Request $request)
    {

        $date = $request->date;
        $yesterday = date('Y-m-d', strtotime($date . '-1 days'));
        $product_id = $request->product_id;
        $category_id = $request->category_id;
        $company_id = $request->company_id;
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        // return 'Hello';
        $type = $request->type;
        $payer_id = $request->payer_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        if ($type == 'all')
            $type = '%';
        if ($payer_id == 'all')
            $payer_id = '%';
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all')
            $branch_id = '%';

        $query = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)
            ->orderBy('date')
            ->orderBy('general_account_ledgers.id');
        $ledgers = $query->get();

        // return $ledgers;


        // $credit_sum = $query->sum('credit');
        // $debit_sum = $query->sum('debit');
        $credit_sum = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)->sum('credit');
        $debit_sum = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)->sum('debit');


        $sum_cr_b_d = $this->generalAccountLedgerB4D($from_date, $company_id, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)
            ->where('model_name', 'LIKE', $type)->sum('credit');
        $sum_dr_b_d = $this->generalAccountLedgerB4D($from_date, $company_id, $branch_id, $type)
            ->where('model_id', 'LIKE', $payer_id)
            ->where('model_name', 'LIKE', $type)->sum('debit');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d;
        $balance = $credit_sum - $debit_sum + $balance_b_d;

        if ($company_id == '%') {
            $company_id = 'all';
        }
        if ($branch_id == '%')
            $branch_id = 'all';
        if ($payer_id == '%')
            $payer_id = 'all';
        if ($type == '%')
            $type = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.statements.load_account_statement', compact('ledgers', 'branch', 'from_date', 'to_date', 'balance', 'company_id', 'branch_id', 'payer_id', 'type', 'credit_sum', 'debit_sum', 'sum_cr_b_d', 'sum_dr_b_d', 'balance_b_d'));
    }

    public function accountBalance(Request $request)
    {

        return view('pages.reports.ap_ar.statements.account_balance');
    }

    public function loadAccountBalance(Request $request)
    {
        $type = $request->account_type;
        $date = $request->date;
        $company_id = $request->company_id;

        $branch_id = $request->branch_id;
        if ($type == 'all' || $type == '')
            $type = '%';

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';

        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';

        if ($type == "GeneralAccount") {
            $query = GeneralAccountLedger::select(DB::raw('SUM(credit) AS credit'), DB::raw('SUM(debit) AS debit'), 'number', 'general_accounts.description', 'general_account_ledgers.id')
                ->join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->whereDate('date', '<=', $date)
                ->where('model_name', 'LIKE', 'GeneralAccount')
                ->havingRaw('SUM(credit) <> SUM(debit)')
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
                ->havingRaw('SUM(credit) <> SUM(debit)')
                ->orderBy('code')
                ->groupBy('model_id');
        }
        if ($type == "Supplier") {
            $query = GeneralAccountLedger::select(DB::raw('SUM(credit) AS credit'), DB::raw('SUM(debit) AS debit'), 'code AS number', 'suppliers.name AS description', 'general_account_ledgers.id')
                ->join('suppliers', 'suppliers.id', '=', 'general_account_ledgers.model_id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->whereDate('date', '<=', $date)
                ->where('model_name', 'LIKE', 'Supplier')
                ->havingRaw('SUM(credit) <> SUM(debit)')
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

        return view('pages.reports.ap_ar.statements.load_account_balances', compact('ledgers', 'branch', 'type', 'date', 'company_id', 'branch_id', 'balance', 'credit_sum', 'debit_sum'));
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;

        $query1 = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
            ->whereIn(DB::raw('SUBSTR(general_accounts.number, 1, 1)'), ['R', 'C']);
        $query2 = $this->generalAccountLedgerBy(null, $to_date, $company_id, $branch_id, 'GeneralAccount')
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
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == '' || $branch_id == '%')
            $branch_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.trial_balance.load', compact('ledger1', 'ledger2', 'ledger3', 'ledger4', 'branch', 'from_date', 'to_date', 'company_id', 'branch_id', 'balance1', 'credit_sum1', 'debit_sum1', 'balance2', 'credit_sum2', 'debit_sum2', 'balance3', 'credit_sum3', 'debit_sum3', 'balance4', 'credit_sum4', 'debit_sum4'));
    }

    public function printTrialBalance($from, $to, $company_id, $branch_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.trial_balance.print', compact('ledger1', 'ledger2', 'ledger3', 'ledger4', 'branch', 'from', 'to', 'company_id', 'branch_id', 'balance1', 'credit_sum1', 'debit_sum1', 'balance2', 'credit_sum2', 'debit_sum2', 'balance3', 'credit_sum3', 'debit_sum3', 'balance4', 'credit_sum4', 'debit_sum4'));
    }

    public function balanceSheet()
    {
        $branches = Branch::select(['id', 'name', 'code'])->orderBy('name')->get();

        return view('pages.reports.ap_ar.balance_sheet.index', compact('branches'));
    }
    // public function loadBalanceSheet(Request $request)
    // {
    //     $to_date = $request->to_date;
    //     $branch_id = $request->branch_id;

    //     $query1 = $this->generalAccountLedgerBy(null, $to_date, $branch_id, 'GeneralAccount');
    //     $ledger1 = $query1->select(
    //         DB::raw('SUM(credit) AS credit'),
    //         DB::raw('SUM(debit) AS debit'),
    //         'number',
    //         'general_accounts.description',
    //         'general_account_ledgers.id'
    //     )
    //         ->whereNotIn('model_name', ['Customer', 'Supplier'])
    //         ->orderBy('number')
    //         ->groupBy('number')
    //         ->get();
    //     $ledger2 = DB::table('general_account_ledgers')
    //         ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->select(
    //             DB::raw('SUM(general_account_ledgers.credit) AS credit'),
    //             DB::raw('SUM(general_account_ledgers.debit) AS debit'),
    //             'general_accounts.description',
    //             'general_account_ledgers.id'
    //         )
    //         ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
    //         ->whereDate('general_account_ledgers.date', '<=', $to_date)
    //         ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Supplier'])
    //         ->groupBy('model_name')
    //         ->get();
    //     $ledger3 = DB::table('general_account_ledgers')
    //         ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->select(
    //             DB::raw('SUM(general_account_ledgers.credit) AS credit'),
    //             DB::raw('SUM(general_account_ledgers.debit) AS debit'),
    //             'general_accounts.description',
    //             'general_account_ledgers.id'
    //         )
    //         ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
    //         ->whereDate('general_account_ledgers.date', '<=', $to_date)
    //         ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Customer'])
    //         ->groupBy('model_name')
    //         ->get();


    //     $credit_sum1 = $query1->sum('credit');
    //     $debit_sum1 = $query1->sum('debit');
    //     $balance1 = $credit_sum1 - $debit_sum1;


    //     $branch = null;

    //     if ($branch_id == '' || $branch_id == '%')
    //         $branch_id = 'all';
    //     if ($branch_id != 'all')
    //         $branch = Branch::find($branch_id);
    //     return view('pages.reports.ap_ar.balance_sheet.load', compact('ledger1', 'ledger2', 'ledger3', 'branch', 'to_date', 'branch_id', 'balance1', 'credit_sum1', 'debit_sum1'));
    // }
    public function loadBalanceSheet(Request $request)
    {
        $to_date = $request->to_date;
        $branch_id = $request->branch_id == '' ? '%' : $request->branch_id;
        $company_id = $request->company_id == '' ? '%' : $request->company_id;

        // Helper function to get account type
        $getAccountType = function ($number) {
            $firstDigit = substr($number, 0, 1);
            if ($firstDigit == 'A')
                return 'asset';
            if ($firstDigit == 'L')
                return 'liability';
            if ($firstDigit == 'E')
                return 'equity';
            if ($firstDigit == 'R')
                return 'revenue';
            if ($firstDigit == 'C')
                return 'expense';
            return 'other';
        };

        // Fetch general accounts
        $generalAccountsQuery = $this->generalAccountLedgerBy(null, $to_date, $company_id, $branch_id, 'GeneralAccount');
        $generalAccounts = $generalAccountsQuery->select(
            DB::raw('SUM(credit) AS credit'),
            DB::raw('SUM(debit) AS debit'),
            'number',
            'general_accounts.description',
            'general_account_ledgers.id'
        )
            ->groupBy('number')
            ->orderBy('number')
            ->get();

        // Fetch customer accounts
        $customerAccountsQuery = $this->generalAccountLedgerBy(null, $to_date, $company_id, $branch_id, 'Customer');
        $customerAccounts = $customerAccountsQuery->select(
            DB::raw('SUM(general_account_ledgers.credit) AS credit'),
            DB::raw('SUM(general_account_ledgers.debit) AS debit'),
            DB::raw("'A150001' AS number"),
            DB::raw("'General Customer Control Account' AS description"),
            'general_account_ledgers.id'
        )
            ->groupBy('model_name')
            ->get();

        // Fetch supplier accounts
        $supplierAccountsQuery = $this->generalAccountLedgerBy(null, $to_date, $company_id, $branch_id, 'Supplier');
        $supplierAccounts = $supplierAccountsQuery->select(
            DB::raw('SUM(general_account_ledgers.credit) AS credit'),
            DB::raw('SUM(general_account_ledgers.debit) AS debit'),
            DB::raw("'L220010' AS number"),
            DB::raw("'Accounts Payable Control' AS description"),
            'general_account_ledgers.id'
        )
            ->groupBy('model_name')
            ->get();

        // Combine all accounts
        $allAccounts = $generalAccounts->concat($customerAccounts)->concat($supplierAccounts);

        // Separate accounts into assets, liabilities, equity, revenues, and expenses
        $assets = collect();
        $liabilities = collect();
        $equity = collect();
        $revenues = collect();
        $expenses = collect();

        foreach ($allAccounts as $account) {
            $type = $getAccountType($account->number);
            switch ($type) {
                case 'asset':
                    $assets->push($account);
                    break;
                case 'liability':
                    $liabilities->push($account);
                    break;
                case 'equity':
                    $equity->push($account);
                    break;
                case 'revenue':
                    $revenues->push($account);
                    break;
                case 'expense':
                    $expenses->push($account);
                    break;
            }
        }

        // Calculate net income
        $totalRevenue = $revenues->sum('credit') - $revenues->sum('debit');
        $totalExpenses = $expenses->sum('debit') - $expenses->sum('credit');
        $net_income = $totalRevenue - $totalExpenses;


        // Assuming $to_date is a string in a format like '2024-09-30'
        $date = Carbon::parse($to_date);

        // Get the previous year
        $previousYear = $date->year - 1;

        // Last day of the previous year
        $last_year_date = Carbon::create($previousYear, 12, 31)->format('Y-m-d');

        // First day of the current year
        $from_this_year_date = Carbon::create($date->year, 1, 1)->format('Y-m-d');
        $last_day_of_previous_year = $date->copy()->subYear()->endOfYear()->format('Y-m-d');
        $first_day_of_current_year = $date->copy()->startOfYear()->format('Y-m-d');

        // Fetch total expenses from inception to 2023-12-31
        // $retainedEarningsFromLastYear = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
        //     ->where('number', 'like', 'C%')
        //     ->whereDate('date', '<=', $last_day_of_previous_year)
        //     ->select(DB::raw('SUM(debit - credit) as total_expenses'))
        //     ->first()->total_expenses;


        $retainedEarningsFromLastYear_R = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where(function ($query) {
                $query->where('number', 'like', 'R%'); // Revenues
            })
            ->whereDate('date', '<=', $last_day_of_previous_year)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->select(DB::raw('SUM(credit - debit) as retained_earnings'))
            ->first()->retained_earnings;

        $retainedEarningsFromLastYear_C = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where(function ($query) {
                $query->where('number', 'like', 'C%');  // Expenses

            })
            ->whereDate('date', '<=', $last_day_of_previous_year)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->select(DB::raw('SUM(credit - debit) as retained_earnings'))
            ->first()->retained_earnings;
        $retainedEarningsFromLastYear = 0;
        if ($retainedEarningsFromLastYear_R > 0 && $retainedEarningsFromLastYear_C > 0)
            $retainedEarningsFromLastYear = $retainedEarningsFromLastYear_R + $retainedEarningsFromLastYear_C;
        else
            $retainedEarningsFromLastYear = abs($retainedEarningsFromLastYear_R) - abs($retainedEarningsFromLastYear_C);
        // Fetch retained earnings from 2024-01-01 to the given date ($to_date)

        $retainedEarningsFromSelectedYear_C = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where(function ($query) {
                $query->where('number', 'like', 'C%');  // Expenses

            })
            ->whereDate('date', '>=', $first_day_of_current_year)
            ->whereDate('date', '<=', $to_date)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->select(DB::raw('SUM(credit - debit) as retained_earnings'))
            ->first()->retained_earnings;

        $retainedEarningsFromSelectedYear_R = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where(function ($query) {
                $query->where('number', 'like', 'R%'); // Revenues
            })
            ->whereDate('date', '>=', $first_day_of_current_year)
            ->whereDate('date', '<=', $to_date)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->select(DB::raw('SUM(credit - debit) as retained_earnings'))
            ->first()->retained_earnings;
        $retainedEarningsFromSelectedYear = 0;
        if ($retainedEarningsFromSelectedYear_R > 0 && $retainedEarningsFromSelectedYear_C > 0)
            $retainedEarningsFromSelectedYear = $retainedEarningsFromSelectedYear_R + $retainedEarningsFromSelectedYear_C;
        else
            $retainedEarningsFromSelectedYear = abs($retainedEarningsFromSelectedYear_R) - abs($retainedEarningsFromSelectedYear_C);
        $company = null;
        if ($company_id != '%')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != '%') {
            $branch = Branch::find($branch_id);
        }
        return view('pages.reports.ap_ar.balance_sheet.load', compact('assets', 'liabilities', 'equity', 'net_income', 'branch', 'to_date', 'company_id', 'branch_id', 'retainedEarningsFromLastYear', 'retainedEarningsFromSelectedYear'));
    }
    // public function printBalanceSheet($to, $branch_id)
    // {
    //     $query1 = $this->generalAccountLedgerBy(null, $to, $branch_id, 'GeneralAccount');
    //     $query1 = $this->generalAccountLedgerBy(null, $to, $branch_id, 'GeneralAccount');
    //     $ledger1 = $query1->select(
    //         DB::raw('SUM(credit) AS credit'),
    //         DB::raw('SUM(debit) AS debit'),
    //         'number',
    //         'general_accounts.description',
    //         'general_account_ledgers.id'
    //     )
    //         ->whereNotIn('model_name', ['Customer', 'Supplier'])
    //         ->orderBy('number')
    //         ->groupBy('number')
    //         ->get();
    //     $ledger2 = DB::table('general_account_ledgers')
    //         ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->select(
    //             DB::raw('SUM(general_account_ledgers.credit) AS credit'),
    //             DB::raw('SUM(general_account_ledgers.debit) AS debit'),
    //             'general_accounts.description',
    //             'general_account_ledgers.id'
    //         )
    //         ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
    //         ->whereDate('general_account_ledgers.date', '<=', $to)
    //         ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Supplier'])
    //         ->groupBy('model_name')
    //         ->get();
    //     $ledger3 = DB::table('general_account_ledgers')
    //         ->leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
    //         ->select(
    //             DB::raw('SUM(general_account_ledgers.credit) AS credit'),
    //             DB::raw('SUM(general_account_ledgers.debit) AS debit'),
    //             'general_accounts.description',
    //             'general_account_ledgers.id'
    //         )
    //         ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
    //         ->whereDate('general_account_ledgers.date', '<=', $to)
    //         ->whereNotIn('general_account_ledgers.model_name', ['GeneralAccount', 'Customer'])
    //         ->groupBy('model_name')
    //         ->get();


    //     $credit_sum1 = $query1->sum('credit');
    //     $debit_sum1 = $query1->sum('debit');
    //     $balance1 = $credit_sum1 - $debit_sum1;


    //     $branch = null;
    //     if ($branch_id != 'all')
    //         $branch = Branch::find($branch_id);
    //     return view('pages.reports.ap_ar.balance_sheet.print', compact('ledger1', 'branch', 'to', 'branch_id', 'balance1', 'credit_sum1', 'debit_sum1'));
    // }
    public function printBalanceSheet($to_date, $company_id, $branch_id)
    {
        // Helper function to get account type
        $getAccountType = function ($number) {
            $firstDigit = substr($number, 0, 1);
            if ($firstDigit == 'A')
                return 'asset';
            if ($firstDigit == 'L')
                return 'liability';
            if ($firstDigit == 'E')
                return 'equity';
            if ($firstDigit == 'R')
                return 'revenue';
            if ($firstDigit == 'C')
                return 'expense';
            return 'other';
        };

        // Fetch general accounts
        $generalAccountsQuery = $this->generalAccountLedgerBy(null, $to_date, $company_id, $branch_id, 'GeneralAccount');
        $generalAccounts = $generalAccountsQuery->select(
            DB::raw('SUM(credit) AS credit'),
            DB::raw('SUM(debit) AS debit'),
            'number',
            'general_accounts.description',
            'general_account_ledgers.id'
        )
            ->groupBy('number')
            ->orderBy('number')
            ->get();

        // Fetch customer accounts
        $customerAccountsQuery = $this->generalAccountLedgerBy(null, $to_date, $company_id, $branch_id, 'Customer');
        $customerAccounts = $customerAccountsQuery->select(
            DB::raw('SUM(general_account_ledgers.credit) AS credit'),
            DB::raw('SUM(general_account_ledgers.debit) AS debit'),
            DB::raw("'A150001' AS number"),
            DB::raw("'General Customer Control Account' AS description"),
            'general_account_ledgers.id'
        )
            ->groupBy('model_name')
            ->get();

        // Fetch supplier accounts
        $supplierAccountsQuery = $this->generalAccountLedgerBy(null, $to_date, $company_id, $branch_id, 'Supplier');
        $supplierAccounts = $supplierAccountsQuery->select(
            DB::raw('SUM(general_account_ledgers.credit) AS credit'),
            DB::raw('SUM(general_account_ledgers.debit) AS debit'),
            DB::raw("'L220010' AS number"),
            DB::raw("'Accounts Payable Control' AS description"),
            'general_account_ledgers.id'
        )
            ->groupBy('model_name')
            ->get();

        // Combine all accounts
        $allAccounts = $generalAccounts->concat($customerAccounts)->concat($supplierAccounts);

        // Separate accounts into assets, liabilities, equity, revenues, and expenses
        $assets = collect();
        $liabilities = collect();
        $equity = collect();
        $revenues = collect();
        $expenses = collect();

        foreach ($allAccounts as $account) {
            $type = $getAccountType($account->number);
            switch ($type) {
                case 'asset':
                    $assets->push($account);
                    break;
                case 'liability':
                    $liabilities->push($account);
                    break;
                case 'equity':
                    $equity->push($account);
                    break;
                case 'revenue':
                    $revenues->push($account);
                    break;
                case 'expense':
                    $expenses->push($account);
                    break;
            }
        }

        // Calculate net income
        $totalRevenue = $revenues->sum('credit') - $revenues->sum('debit');
        $totalExpenses = $expenses->sum('debit') - $expenses->sum('credit');
        $net_income = $totalRevenue - $totalExpenses;
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all') {
            $branch = Branch::find($branch_id);
        }

        // Assuming $to_date is a string in a format like '2024-09-30'
        $date = Carbon::parse($to_date);

        // Get the previous year
        $previousYear = $date->year - 1;

        // Last day of the previous year
        $last_year_date = Carbon::create($previousYear, 12, 31)->format('Y-m-d');

        // First day of the current year
        $from_this_year_date = Carbon::create($date->year, 1, 1)->format('Y-m-d');
        $last_day_of_previous_year = $date->copy()->subYear()->endOfYear()->format('Y-m-d');
        $first_day_of_current_year = $date->copy()->startOfYear()->format('Y-m-d');

        // Fetch total expenses from inception to 2023-12-31
        // $retainedEarningsFromLastYear = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
        //     ->where('number', 'like', 'C%')
        //     ->whereDate('date', '<=', $last_day_of_previous_year)
        //     ->select(DB::raw('SUM(debit - credit) as total_expenses'))
        //     ->first()->total_expenses;


        $retainedEarningsFromLastYear_R = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where(function ($query) {
                $query->where('number', 'like', 'R%'); // Revenues
            })
            ->whereDate('date', '<=', $last_day_of_previous_year)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->select(DB::raw('SUM(credit - debit) as retained_earnings'))
            ->first()->retained_earnings;

        $retainedEarningsFromLastYear_C = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where(function ($query) {
                $query->where('number', 'like', 'C%');  // Expenses

            })
            ->whereDate('date', '<=', $last_day_of_previous_year)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->select(DB::raw('SUM(credit - debit) as retained_earnings'))
            ->first()->retained_earnings;
        $retainedEarningsFromLastYear = 0;
        if ($retainedEarningsFromLastYear_R > 0 && $retainedEarningsFromLastYear_C > 0)
            $retainedEarningsFromLastYear = $retainedEarningsFromLastYear_R + $retainedEarningsFromLastYear_C;
        else
            $retainedEarningsFromLastYear = abs($retainedEarningsFromLastYear_R) - abs($retainedEarningsFromLastYear_C);
        // Fetch retained earnings from 2024-01-01 to the given date ($to_date)

        $retainedEarningsFromSelectedYear_C = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where(function ($query) {
                $query->where('number', 'like', 'C%');  // Expenses

            })
            ->whereDate('date', '>=', $first_day_of_current_year)
            ->whereDate('date', '<=', $to_date)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->select(DB::raw('SUM(credit - debit) as retained_earnings'))
            ->first()->retained_earnings;

        $retainedEarningsFromSelectedYear_R = GeneralAccountLedger::join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where(function ($query) {
                $query->where('number', 'like', 'R%'); // Revenues
            })
            ->whereDate('date', '>=', $first_day_of_current_year)
            ->whereDate('date', '<=', $to_date)
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('general_account_ledgers.model_name', 'GeneralAccount')
            ->select(DB::raw('SUM(credit - debit) as retained_earnings'))
            ->first()->retained_earnings;
        $retainedEarningsFromSelectedYear = 0;
        if ($retainedEarningsFromSelectedYear_R > 0 && $retainedEarningsFromSelectedYear_C > 0)
            $retainedEarningsFromSelectedYear = $retainedEarningsFromSelectedYear_R + $retainedEarningsFromSelectedYear_C;
        else
            $retainedEarningsFromSelectedYear = abs($retainedEarningsFromSelectedYear_R) - abs($retainedEarningsFromSelectedYear_C);


        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all') {
            $branch = Branch::find($branch_id);
        }

        return view('pages.reports.ap_ar.balance_sheet.print', compact('assets', 'liabilities', 'equity', 'net_income', 'branch', 'to_date', 'branch_id'));
    }

    public function cashFlow()
    {
        return view('pages.reports.ap_ar.cash_flow.index');
    }

    public function loadCashFlow(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $company_id = $request->company_id;
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';

        $total_generated = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->whereIn('general_accounts.class', ['A12'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->sum('debit');

        $total_bank_transfer = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->whereIn('general_accounts.class', ['A11'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->sum('debit');

        $total_at_hand = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS total_at_hand')
            ->whereIn('general_accounts.class', ['A12'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->total_at_hand;

        $total_cash_in_bank = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS total_in_bank')
            ->whereIn('general_accounts.class', ['A11'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->total_in_bank;

        $total_amount_expended = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
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

    public function printCashFlow($from_date, $to_date, $company_id, $branch_id)
    {
        $total_generated = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->whereIn('general_accounts.class', ['A12'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->sum('debit');

        $total_bank_transfer = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->whereIn('general_accounts.class', ['A11'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->sum('debit');

        $total_at_hand = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS total_at_hand')
            ->whereIn('general_accounts.class', ['A12'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->total_at_hand;

        $total_cash_in_bank = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
            ->join('branches', 'branches.id', 'general_account_ledgers.branch_id')
            ->join('companies', 'companies.id', 'branches.company_id')
            ->selectRaw('SUM(credit) - SUM(debit) AS total_in_bank')
            ->whereIn('general_accounts.class', ['A11'])
            ->whereNotIn('model_name', ['Customer', 'Supplier'])
            ->where('company_id', 'LIKE', $company_id)
            ->first()
            ->total_in_bank;

        $total_amount_expended = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, 'GeneralAccount')
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


    private function generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id, $type = null)
    {
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($type != null && $type == "Customer") {
            $query = GeneralAccountLedger::join('customers', 'customers.id', '=', 'general_account_ledgers.model_id')
                ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->where('branches.company_id', 'LIKE', $company_id)
                ->whereDate('date', '<=', $to_date)
                ->where('model_name', 'Customer');
            if ($from_date != null)
                $query = $query->whereDate('date', '>=', $from_date);
            return $query;
        }
        if ($type != null && $type == "Supplier") {
            $query = GeneralAccountLedger::join('suppliers', 'suppliers.id', '=', 'general_account_ledgers.model_id')
                ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->where('branches.company_id', 'LIKE', $company_id)
                ->whereDate('date', '<=', $to_date)
                ->where('model_name', 'Supplier');
            if ($from_date != null)
                $query = $query->whereDate('date', '>=', $from_date);
            return $query;
        }
        if ($type != null && $type == "GeneralAccount") {
            $query = GeneralAccountLedger::leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
                ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
                ->where('general_account_ledgers.branch_id', 'like', $branch_id)
                ->where('branches.company_id', 'LIKE', $company_id)
                ->whereDate('date', '<=', $to_date)
                ->where('model_name', 'GeneralAccount');
            if ($from_date != null)
                $query = $query->whereDate('date', '>=', $from_date);
            return $query;
        }
        return GeneralAccountLedger::leftJoin('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where('general_account_ledgers.branch_id', 'like', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereDate('date', '>=', $from_date)
            ->whereDate('date', '<=', $to_date);

    }


    private function generalAccountLedgerB4D($from_date, $company_id, $branch_id, $type = null)
    {
        //To get account balance before start date
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($type != null && $type == "Customer") {
            return GeneralAccountLedger::join('customers', 'customers.id', '=', 'general_account_ledgers.model_id')
                ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
                ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
                ->where('branches.company_id', 'LIKE', $company_id)
                ->whereDate('date', '<', $from_date)
                ->where('model_name', 'Customer');
        }
        if ($type != null && $type == "Supplier") {
            return GeneralAccountLedger::join('suppliers', 'suppliers.id', '=', 'general_account_ledgers.model_id')
                ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
                ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
                ->where('branches.company_id', 'LIKE', $company_id)
                ->whereDate('date', '<', $from_date)
                ->where('model_name', 'Supplier');
        }
        if ($type != null && $type == "GeneralAccount") {
            return GeneralAccountLedger::join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
                ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
                ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
                ->where('branches.company_id', 'LIKE', $company_id)
                ->whereDate('date', '<', $from_date)
                ->where('model_name', 'GeneralAccount');
        }

        return GeneralAccountLedger::join('general_accounts', 'general_accounts.id', '=', 'general_account_ledgers.model_id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->whereDate('date', '<', $from_date);
    }

    public function incomeStatement(Request $request)
    {
        return view('pages.reports.ap_ar.statements.income');
    }

    public function loadIncomeStatement(Request $request)
    {
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $category_id1 = $request->category_id1;
        $category_id2 = $request->category_id2;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($category_id1 == 'all' || $category_id1 == '')
            $category_id1 = '%';
        if ($category_id2 == 'all' || $category_id2 == '')
            $category_id2 = '%';
        //        $income_year = $request->income_year;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $revenue_class = ['R40'];
        $cost_of_sale_class = ['C50'];
        $expense_class = ['C51', 'C52', 'C53', 'C54', 'C55', 'C56', 'C57', 'C58', 'C59', 'C60', 'C61', 'C62', 'C63'];
        $query = ChartOfAccount::select(DB::raw('SUM(credit) AS credit'), DB::raw('SUM(debit) AS debit'), 'number', 'general_accounts.description')
            ->join('general_accounts', 'general_accounts.class', 'chart_of_accounts.class')
            ->join('general_account_ledgers', 'model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->whereBetween('date', [$from_date, $to_date])
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('model_name', 'GeneralAccount')->groupBy('number');

        //        if ($from_month == '' || $to_month == '') {
//            $query->whereMonth('date', '<=', 12);
//        }

        //        if ($from_month != '') {
//            $query->whereMonth('date', '>=', $from_month);
//        }

        //        if ($to_month != '') {
//            $query->whereMonth('date', '<=', $to_month);
//        }

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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        //        if ($from_month == '')
//            $from_month = 'all';

        //        if ($to_month == '')
//            $to_month = 'all';

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
            'from_date' => $from_date,
            'to_date' => $to_date,
            //            'from_month' => $from_month,
//            'to_month' => $to_month,
//            'income_year' => $income_year,
            'company_id' => $company_id,
            'branch' => $branch,
            'branch_id' => $branch_id,
            'category_id1' => $category_id1,
            'category_id2' => $category_id2
        ]);
    }

    public function printIncomeStatement($from_date, $to_date, $company_id, $branch_id, $category_id1, $category_id2)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
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
            ->join('general_accounts', 'general_accounts.class', 'chart_of_accounts.class')
            ->join('general_account_ledgers', 'model_id', 'general_accounts.id')
            ->join('branches', 'general_account_ledgers.branch_id', 'branches.id')
            ->whereBetween('date', [$from_date, $to_date])
            ->where('general_account_ledgers.branch_id', 'LIKE', $branch_id)
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('model_name', 'GeneralAccount')
            ->groupBy('number');

        //        if ($from_date == 'all' || $to_date == 'all') {
//            $query->whereMonth('date', '<=', 12);
//        }
//
//        if ($from_date != 'all') {
//            $query->whereMonth('date', '>=', $from_date);
//        }
//
//        if ($to_date != 'all') {
//            $query->whereMonth('date', '<=', $to_date);
//        }

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

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        if ($from_date == '')
            $from_date = 'all';

        if ($to_date == '')
            $to_date = 'all';
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
            'from_month' => $from_date,
            'to_month' => $to_date,
            //            'income_year' => $income_year,
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $payee_id = $request->payee_id;
        $user_id = $request->user_id;
        if ($user_id == 'all' || $user_id == '')
            $user_id = '%';
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($payee_id == 'all' || $payee_id == '')
            $payee_id = '%';
        $query = $this->generalAccountLedgerBy($from_date, $to_date, $company_id, $branch_id);
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
        if ($company_id == '%') {
            $company_id = 'all';
        }
        if ($branch_id == '%')
            $branch_id = 'all';
        if ($payee_id == '%')
            $payee_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.remittance.load', compact('ledgers', 'branch', 'from_date', 'to_date', 'balance', 'company_id', 'branch_id', 'payee_id', 'user_id', 'credit_sum', 'debit_sum'));
    }

    public function printRemittance($from, $to, $company_id, $branch_id, $payee_id, $user_id)
    {
        if ($user_id == 'all' || $user_id == '')
            $user_id = '%';
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        if ($payee_id == 'all' || $payee_id == '')
            $payee_id = '%';
        $query = $this->generalAccountLedgerBy($from, $to, $company_id, $branch_id);
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $type = $request->type;
        $status = $request->status;
        if ($status == 'all' || $status == '')
            $status = '%';
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';

        $query = null;
        if ($type == "Invoice")
            $query = Order::where('branch_id', 'LIKE', $branch_id)
                ->join('branches', 'orders.branch_id', 'branches.id')
                ->where('branches.company_id', 'LIKE', $company_id)
                ->where('orders.status', 'LIKE', $status)->orderBy('order_date', "DESC")
                ->whereDate('order_date', '>=', $from_date)
                ->whereDate('order_date', '<=', $to_date)
                ->select('orders.*');
        if ($type == "Payment")
            $query = Payment::where('branch_id', 'LIKE', $branch_id)
                ->join('branches', 'payments.branch_id', 'branches.id')
                ->where('branches.company_id', 'LIKE', $company_id)
                ->where('payments.status', 'LIKE', $status)->orderBy('date', "DESC")
                ->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date)
                ->select('payments.*');
        if ($type == "Receipt")
            $query = Receipt::where('branch_id', 'LIKE', $branch_id)
                ->join('branches', 'receipts.branch_id', 'branches.id')
                ->where('branches.company_id', 'LIKE', $company_id)
                ->where('receipts.status', 'LIKE', $status)->orderBy('date', "DESC")
                ->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date)
                ->select('receipts.*');
        if ($type == "Journal")
            $query = Journal::where('branch_id', 'LIKE', $branch_id)
                ->join('branches', 'journals.branch_id', 'branches.id')
                ->where('branches.company_id', 'LIKE', $company_id)
                ->where('journals.status', 'LIKE', $status)->orderBy('date', "DESC")
                ->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date)
                ->select('journals.*');
        if ($type == "Interbank")
            $query = InterBank::where('branch_id', 'LIKE', $branch_id)
                ->join('branches', 'inter_banks.branch_id', 'branches.id')
                ->where('branches.company_id', 'LIKE', $company_id)
                ->where('inter_banks.status', 'LIKE', $status)->orderBy('date', "DESC")
                ->whereDate('date', '>=', $from_date)
                ->whereDate('date', '<=', $to_date)
                ->select('inter_banks.*');
        $payments = $query->get();

        if ($status == '%')
            $status = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all' || $branch_id != '%')
            $branch = Branch::find($branch_id);
        return view('pages.reports.ap_ar.document_status.load', compact('payments', 'branch', 'from_date', 'to_date', 'company_id', 'branch_id', 'type', 'status'));
    }

    public function customerList(Request $request)
    {
        return view('pages.reports.customer_ledger_analysis.customer_list_report');
    }

    public function loadCustomerListReport(Request $request)
    {
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::select('customers.*')
            ->where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'customers.branch_id', 'branches.id')
            ->join('companies', 'branches.company_id', 'companies.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->orderBy('companies.name')
            ->orderBy('branches.code')
            ->orderBy('branches.name')
            ->get();
        if ($company_id == '%') {
            $company_id = 'all';
        }
        if ($branch_id == '%')
            $branch_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.load_customer_list_report', compact('customers', 'branch', 'company_id', 'branch_id'));
    }

    public function printCustomerListReport($company_id, $branch_id)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::select('customers.*')
            ->where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'customers.branch_id', 'branches.id')
            ->join('companies', 'branches.company_id', 'companies.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->orderBy('companies.name')
            ->orderBy('branches.code')
            ->orderBy('branches.name')
            ->get();

        if ($branch_id == '%')
            $branch_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::where('branch_id', 'LIKE', $branch_id)
            ->select('customers.*')
            ->join('branches', 'customers.branch_id', 'branches.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('credit_limit', '>', 0)
            ->orderBy('customers.code')
            ->orderBy('customers.name')
            ->get();
        if ($company_id == '%') {
            $company_id = 'all';
        }
        if ($branch_id == '%')
            $branch_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.load_customer_with_credit_limit_report', compact('customers', 'branch', 'company_id', 'branch_id'));
    }

    public function printCustomerCreditLimitReport($company_id, $branch_id)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::where('branch_id', 'LIKE', $branch_id)
            ->select('customers.*')
            ->join('branches', 'customers.branch_id', 'branches.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('credit_limit', '>', 0)
            ->orderBy('customers.code')
            ->orderBy('customers.name')
            ->get();
        if ($branch_id == '%')
            $branch_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
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
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;

        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';

        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';

        $customers = Customer::select(DB::raw("ABS(SUM(credit) - SUM(debit)) as balance"), 'customers.*')
            ->join('branches', 'customers.branch_id', 'branches.id')
            ->join('general_account_ledgers', 'general_account_ledgers.model_id', '=', 'customers.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->where('customers.branch_id', 'LIKE', $branch_id)
            ->where('credit_limit', '>', 0)
            ->groupBy('model_id')
            ->havingRaw('ABS(SUM(credit) - SUM(debit)) > credit_limit')
            ->orderBy('code')
            ->orderBy('name')
            ->get();

        if ($company_id == '%')
            $company_id = 'all';

        if ($branch_id == '%')
            $branch_id = 'all';

        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);

        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);

        return view('pages.reports.customer_ledger_analysis.load_customer_exceed_credit_limit_report', compact('customers', 'branch', 'company_id', 'branch_id'));
    }

    public function printCustomerExceededCreditLimitReport($company_id, $branch_id)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $customers = Customer::select(DB::raw("ABS(SUM(credit) - SUM(debit)) as balance"), 'customers.*')
            ->join('branches', 'customers.branch_id', 'branches.id')
            ->where('branches.company_id', 'LIKE', $company_id)
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
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.customer_ledger_analysis.print_customer_exceed_credit_limit_report', compact('customers', 'branch', 'branch_id'));
    }

    public function supplierList(Request $request)
    {
        return view('pages.reports.suppliers.supplier_list_report');
    }

    public function loadSupplierListReport(Request $request)
    {
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $suppliers = Supplier::select('suppliers.*')
            ->where('branch_id', 'LIKE', $branch_id)
            ->orWhereNull('suppliers.branch_id')
            ->leftJoin('branches', 'suppliers.branch_id', 'branches.id')
            ->leftJoin('companies', 'branches.company_id', 'companies.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->orderBy('companies.name')
            ->orderBy('branches.code')
            ->orderBy('branches.name')
            ->get();
        if ($company_id == '%') {
            $company_id = 'all';
        }
        if ($branch_id == '%')
            $branch_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.suppliers.load_supplier_list_report', compact('suppliers', 'branch', 'company_id', 'branch_id'));
    }

    public function printSupplierListReport($company_id, $branch_id)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $suppliers = Supplier::select('suppliers.*')
            ->where('branch_id', 'LIKE', $branch_id)
            ->join('branches', 'suppliers.branch_id', 'branches.id')
            ->join('companies', 'branches.company_id', 'companies.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->orderBy('companies.name')
            ->orderBy('branches.code')
            ->orderBy('branches.name')
            ->get();

        if ($branch_id == '%')
            $branch_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.suppliers.print_supplier_list_report', compact('suppliers', 'branch', 'branch_id'));
    }

    public function productList(Request $request)
    {
        return view('pages.reports.inventory.products.product_list_report');
    }

    public function loadProducListReport(Request $request)
    {
        $company_id = $request->company_id;
        $branch_id = $request->branch_id;
        $category_id = $request->category_id;

        if ($company_id == 'all' || $company_id == '') {
            $company_id = '%';
        }
        if ($branch_id == 'all' || $branch_id == '' || $branch_id == null)
            $branch_id = '%';
        if ($category_id == 'all' || $category_id == '')
            $category_id = '%';
        $records = Product::select('products.*', 'branches.code AS branch_code')
            ->where('products.category_id', 'LIKE', $category_id)
            ->leftJoin('store_products', 'products.id', 'store_products.product_id')
            ->leftJoin('stores', 'stores.id', 'store_products.store_id')
            ->leftJoin('branches', 'stores.branch_id', 'branches.id')
            ->join('companies', 'branches.company_id', 'companies.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->orderBy('companies.name')
            ->orderBy('branches.code')
            ->orderBy('branches.name')
            ->get();
        if ($company_id == '%') {
            $company_id = 'all';
        }
        if ($branch_id == '%')
            $branch_id = 'all';
        if ($category_id == '%')
            $category_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $category = null;
        if ($category_id != 'all')
            $category = Category::find($category_id);
        return view('pages.reports.inventory.products.load_product_list_report', compact('records', 'category', 'company_id', 'branch_id', 'category_id'));
    }

    public function printProductListReport($company_id, $branch_id)
    {
        if ($company_id == 'all' || $company_id == '')
            $company_id = '%';
        if ($branch_id == 'all' || $branch_id == '')
            $branch_id = '%';
        $records = Product::select('products.*')
            ->where('branch_id', 'LIKE', $branch_id)
            ->leftJoin('store_products', 'products.id', 'store_products.product_id')
            ->leftJoin('stores', 'stores.id', 'store_products.store_id')
            ->leftJoin('branches', 'stores.branch_id', 'branches.id')
            ->join('companies', 'branches.company_id', 'companies.id')
            ->where('branches.company_id', 'LIKE', $company_id)
            ->orderBy('companies.name')
            ->orderBy('branches.code')
            ->orderBy('branches.name')
            ->get();

        if ($branch_id == '%')
            $branch_id = 'all';
        $company = null;
        if ($company_id != 'all')
            $company = Company::find($company_id);
        $branch = null;
        if ($branch_id != 'all')
            $branch = Branch::find($branch_id);
        return view('pages.reports.inventory.products.product_list_report', compact('records', 'branch', 'branch_id'));
    }

    public function generalLedgerList(Request $request): View
    {
        $classes = GeneralAccount::select('class')->distinct()->get();
        return view('pages.reports.ap_ar.general_ledger.general_ledger_list_report', compact('classes'));
    }

    public function loadGeneralLedgerListReport(Request $request)
    {
        $general_account_class = $request->general_account_id;

        if ($general_account_class == 'all' || $general_account_class == '')
            $general_account_class = '%';
        $records = GeneralAccount::select('general_accounts.*')
            ->where('class', 'LIKE', $general_account_class)
            ->orderBy('number')
            ->get();

        if ($general_account_class == '%')
            $general_account_class = 'all';


        if ($general_account_class != 'all')
            $general_account = GeneralAccount::find($general_account_class);
        return view('pages.reports.ap_ar.general_ledger.load_general_ledger_list_report', compact('records', 'general_account_class'));
    }

    public function printGeneralLedgerListReport($general_account_class)
    {
        if ($general_account_class == 'all' || $general_account_class == '')
            $general_account_class = '%';
        $records = GeneralAccount::select('general_accounts.*')
            ->where('class', 'LIKE', $general_account_class)
            ->orderBy('number')
            ->get();
        $general_account = null;
        if ($general_account_class != 'all')
            $general_account = GeneralAccount::find($general_account_class);
        return view('pages.reports.ap_ar.general_ledger.print_general_ledger_list_report', compact('records', 'general_account'));
    }

    public function showROCustomerReport()
    {
        return view('pages.reports.relation_officers.customer_list');
    }

    /**
     * Load the Relation Officer Report Data
     */
    public function loadROCustomerReport(Request $request)
    {
        $from_date = date('Y-m-d', strtotime($request->from_date));
        $to_date = date('Y-m-d', strtotime($request->to_date));
        $branch_id = $request->branch_id;

        if ($branch_id == 'all' || $branch_id == '') {
            $branch_id = '%'; // Ensure 'all' branches are included
        }

        // Fetch Relation Officers and count customers
        $relationOfficers = DB::table('users as ro')
            ->select(
                'ro.id as ro_id',
                'ro.name as ro_name',
                'ro.surname as ro_surname',
                'ro.user_code as ro_code',
                'ro.phone as ro_phone',
                'ro.email as ro_email',
                'branches.name as branch_name',
                DB::raw('COUNT(customers.id) as total_customers'),
                DB::raw("SUM(CASE WHEN customers.created_at BETWEEN '$from_date' AND '$to_date' THEN 1 ELSE 0 END) as new_customers")
            )
            ->join('branches', 'ro.branch_id', '=', 'branches.id')
            ->leftJoin('customers', 'customers.relation_officer', '=', 'ro.id')
            ->where('ro.is_sale_representative', 1)
            ->where('ro.branch_id', 'LIKE', $branch_id)
            ->groupBy('ro.id', 'ro.name', 'ro.surname', 'ro.user_code', 'ro.phone', 'ro.email', 'branches.name')
            ->get();

        // Fetch Customers Assigned to Each RO
        $customers = DB::table('customers')
            ->select('id', 'name', 'code', 'phone', 'email', 'relation_officer', 'created_at')
            ->where('branch_id', 'LIKE', $branch_id)
            ->get()
            ->groupBy('relation_officer'); // Group by relation_officer (ro.id)

        // Handle "all" branches scenario
        $branch = null;
        if ($branch_id != '%') {
            $branch = Branch::find($branch_id);
        }

        return view('pages.reports.relation_officers.load_customers', compact('relationOfficers', 'customers', 'from_date', 'to_date', 'branch_id'));
    }

    public function slowOverstayedReport()
    {
        return view('pages.reports.inventory.slow_overstay.slow_overstay_report');
    }

    public function loadslowOverstayedReport(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|integer',
            'company_id' => 'required|integer',
            'report_type' => 'required|string', // New field for selecting report type
        ]);

        if ($validated['report_type'] == 'overstayed') {
            // Overstayed Inventory Query
            $inventory = DB::table('purchase_products AS pp')
                ->join('purchases AS pur', 'pp.purchase_id', '=', 'pur.id')
                ->join('products AS p', 'pp.product_id', '=', 'p.id')
                ->join('branches AS b', 'pur.branch_id', '=', 'b.id')
                ->join('store_products AS sp', 'pp.product_id', '=', 'sp.product_id')
                ->join('stores AS s', 'sp.store_id', '=', 's.id')
                ->select(
                    'p.id AS product_id',
                    'p.name AS product_name',
                    'p.code AS product_code',
                    'b.name AS branch_name',
                    's.name AS store_name',
                    's.code AS store_code',
                    'pp.updated_at AS last_received_date',
                    DB::raw('DATEDIFF(CURDATE(), pp.updated_at) AS days_since_received'),
                    'sp.qty_available AS available_quantity'
                )
                ->where('pur.branch_id', $validated['branch_id'])
                ->where('sp.qty_available', '>', 0)
                ->havingRaw('days_since_received > 30') // Overstayed products (30+ days)
                ->orderByDesc('days_since_received')
                ->get();
            $type = 'overstayed';
            return view('pages.reports.inventory.slow_overstay.load_slow_overstay_report', compact('inventory', 'type'));

        } elseif ($validated['report_type'] == 'slow_moving') {
            // Slow Moving Inventory Query
            $inventory = DB::table('order_details AS od')
                ->join('orders AS o', 'od.order_id', '=', 'o.id')
                ->join('products AS p', 'od.store_product_id', '=', 'p.id')
                ->join('branches AS b', 'o.branch_id', '=', 'b.id')
                ->join('store_products AS sp', 'od.store_product_id', '=', 'sp.product_id')
                ->join('stores AS s', 'sp.store_id', '=', 's.id')
                ->select(
                    'p.id AS product_id',
                    'p.name AS product_name',
                    'p.code AS product_code',
                    'b.name AS branch_name',
                    's.name AS store_name',
                    's.code AS store_code',
                    'od.updated_at AS last_sold_date',
                    DB::raw('DATEDIFF(CURDATE(), od.updated_at) AS days_since_sold'),
                    'sp.qty_available AS available_quantity'
                )
                ->where('o.branch_id', $validated['branch_id'])
                ->where('sp.qty_available', '>', 0)
                ->havingRaw('days_since_sold > 60') // Slow-moving products (60+ days)
                ->orderByDesc('days_since_sold')
                ->get();
            $type = 'slow_moving';
            return view('pages.reports.inventory.slow_overstay.load_slow_overstay_report', compact('inventory', 'type'));
        }

        return back()->withErrors(['error' => 'Invalid report type selected.']);
    }

    public function backdatedEntriesReport()
    {
        return view('pages.reports.inventory.user_entries.backdated_postdated_entries_report');
    }

    public function loadBackdatedEntriesReport(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'branch_id' => 'required|integer',
            'company_id' => 'required|integer',
            'entry_type' => 'required|string',
        ]);

        // Define tables and their respective fields
        $tableMap = [
            'credit_notes' => ['table' => 'credit_notes', 'date' => 'date', 'created' => 'created_at', 'branch' => 'credit_notes.branch_id'],
            'inter_banks' => ['table' => 'inter_banks', 'date' => 'date', 'created' => 'created_at', 'branch' => 'inter_banks.branch_id'],
            'intersite_transfers' => ['table' => 'intersite_transfers', 'date' => 'date', 'created' => 'created_at', 'branch' => 'source_branch_id'],
            'interstore_transfers' => ['table' => 'interstore_transfers', 'date' => 'date', 'created' => 'created_at', 'branch' => 'interstore_transfers.branch_id'],
            'journals' => ['table' => 'journals', 'date' => 'date', 'created' => 'created_at', 'branch' => 'journals.branch_id'],
            'order_invoices' => ['table' => 'order_invoices', 'date' => 'order_date', 'created' => 'created_at', 'branch' => 'order_invoices.branch_id'],
            'orders' => ['table' => 'orders', 'date' => 'order_date', 'created' => 'created_at', 'branch' => 'orders.branch_id'],
            'payments' => ['table' => 'payments', 'date' => 'date', 'created' => 'created_at', 'branch' => 'payments.branch_id'],
            'proformers' => ['table' => 'proformers', 'date' => 'order_date', 'created' => 'created_at', 'branch' => 'proformers.branch_id'],
            'purchase_expenses' => ['table' => 'purchase_expenses', 'date' => 'date', 'created' => 'created_at', 'branch' => 'purchases.branch_id', 'join' => 'purchases'],
            'purchases' => ['table' => 'purchases', 'date' => 'purchase_date', 'created' => 'created_at', 'branch' => 'purchases.branch_id'],
            'receipts' => ['table' => 'receipts', 'date' => 'date', 'created' => 'created_at', 'branch' => 'receipts.branch_id'],
            'return_debits' => ['table' => 'return_debits', 'date' => 'date', 'created' => 'created_at', 'branch' => 'return_debits.branch_id'],
        ];

        // Validate the report type
        if (!array_key_exists($validated['type'], $tableMap)) {
            return response()->json(['error' => 'Invalid report type selected.'], 400);
        }

        // Get table configuration
        $tableConfig = $tableMap[$validated['type']];
        $table = $tableConfig['table'];
        $dateColumn = $tableConfig['date'];
        $createdColumn = $tableConfig['created'];
        $branchColumn = $tableConfig['branch'];

        // Select required columns
        $columns = [
            "$table.id",
            "$table.$dateColumn AS date",
            "$table.$createdColumn AS created_at"
        ];

        // Include reference column if available
        if (Schema::hasColumn($table, 'reference')) {
            $columns[] = "$table.reference";
        }
        if (Schema::hasColumn($table, 'receipt_no')) {
            $columns[] = "$table.receipt_no AS reference";
        }

        // Include amount column if available
        if (Schema::hasColumn($table, 'amount')) {
            $columns[] = "$table.amount";
        }

        // Include description if available
        if (Schema::hasColumn($table, 'description')) {
            $columns[] = "$table.description";
        }

        // Include `branch_id`
        if ($branchColumn) {
            $columns[] = "$branchColumn AS branch_id";
        }

        // Identify user column (`posted_by`, `created_by`, `updated_by`)
        $userColumn = null;
        foreach (['posted_by', 'created_by', 'updated_by'] as $col) {
            if (Schema::hasColumn($table, $col)) {
                $userColumn = $col;
                break;
            }
        }

        // Include user column if found
        if ($userColumn) {
            $columns[] = "$table.$userColumn";
        }

        // Base Query
        $query = DB::table($table);

        // Join with `purchases` for `purchase_expenses` to get `branch_id`
        if ($validated['type'] === 'purchase_expenses') {
            $query->join('purchases', 'purchases.id', '=', 'purchase_expenses.purchase_id');
        }

        // Join with `branches` to get branch name
        $query->leftJoin('branches', 'branches.id', '=', $branchColumn);

        // Join with `users` to get user name
        if ($userColumn) {
            $query->leftJoin('users', "users.id", '=', "$table.$userColumn");
        }

        // Select columns
        $query->select(array_merge($columns, [
            'branches.name AS branch_name',
            DB::raw("CONCAT(users.firstname, ' ', users.surname) AS user_name")
        ]));

        // Apply Backdated or Postdated Condition
        if ($validated['entry_type'] == 'backdated') {
            $query->whereRaw("DATE($table.$dateColumn) < DATE($table.$createdColumn)");
        } elseif ($validated['entry_type'] == 'postdated') {
            $query->whereRaw("DATE($table.$dateColumn) > CURDATE()");
        }

        // Apply filters
        $query->whereBetween("$table.$dateColumn", [$validated['from_date'], $validated['to_date']])
            ->where($branchColumn, $validated['branch_id'])
            ->orderBy("$table.$dateColumn", 'desc');

        // Fetch results
        $reports = $query->get();
        $route_name = $this->getRouteName($validated['type']);
        return view('pages.reports.inventory.user_entries.load_backdated_postdated_entries_report', compact('reports', 'route_name'));
    }

    public function getRouteName($type)
    {
        $route_name = "orders.show";
        switch ($type) {
            case 'credit_notes':
                $route_name = 'credit.note.show';
                break;
            case 'inter_banks':
                $route_name = 'interbank.show';
                break;
            case 'intersite_transfers':
                $route_name = 'intersite.show';
                break;
            case 'interstore_transfers.show':
                $route_name = 'interstore.show';
                break;
            case 'journals':
                $route_name = 'journal.show';
                break;
            case 'order_invoices':
                $route_name = 'order.invoice.show';
                break;
            case 'payments':
                $route_name = 'payment.show';
                break;
            case 'purchases':
                $route_name = 'purchases.show';
                break;
            case 'receipts':
                $route_name = 'receipt.payment.show';
                break;
            case 'return_debits':
                $route_name = 'return.debit.show';
                break;
            default:
                $route_name = "orders.show";
        }
        return $route_name;
    }

    public function userEntriesReport()
    {
        return view('pages.reports.inventory.user_entries.user_entries_report');
    }

    public function loadUserEntriesReport(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'branch_id' => 'required|integer',
            'company_id' => 'required|integer',
        ]);

        // Map report types to tables with correct fields
        $tableMap = [
            'credit_notes' => ['table' => 'credit_notes', 'date' => 'date', 'amount' => 'amount', 'branch' => 'branch_id', 'reference' => 'reference'],
            'inter_banks' => ['table' => 'inter_banks', 'date' => 'date', 'amount' => 'amount', 'branch' => 'branch_id', 'reference' => 'reference'],
            'intersite_transfers' => ['table' => 'intersite_transfers', 'date' => 'date', 'amount' => null, 'branch' => 'source_branch_id', 'reference' => 'reference'],
            'interstore_transfers' => ['table' => 'interstore_transfers', 'date' => 'date', 'amount' => null, 'branch' => 'branch_id', 'reference' => 'reference'],
            'journals' => ['table' => 'journals', 'date' => 'date', 'amount' => 'amount', 'branch' => 'branch_id', 'reference' => 'reference'],
            'order_invoices' => ['table' => 'order_invoices', 'date' => 'order_date', 'amount' => 'total', 'branch' => 'branch_id', 'reference' => 'reference'],
            'orders' => ['table' => 'orders', 'date' => 'order_date', 'amount' => 'total', 'branch' => 'branch_id', 'reference' => 'reference'],
            'payments' => ['table' => 'payments', 'date' => 'date', 'amount' => 'amount', 'branch' => 'branch_id', 'reference' => 'receipt_no'],
            'proformers' => ['table' => 'proformers', 'date' => 'order_date', 'amount' => 'total', 'branch' => 'branch_id', 'reference' => 'reference'],
            'purchase_expenses' => ['table' => 'purchase_expenses', 'date' => 'date', 'amount' => 'amount', 'branch' => 'purchases.branch_id', 'reference' => 'reference', 'join' => 'purchases'],
            'purchases' => ['table' => 'purchases', 'date' => 'purchase_date', 'amount' => null, 'branch' => 'branch_id', 'reference' => 'reference'],
            'receipts' => ['table' => 'receipts', 'date' => 'date', 'amount' => 'amount', 'branch' => 'branch_id', 'reference' => 'receipt_no'],
            'return_debits' => ['table' => 'return_debits', 'date' => 'date', 'amount' => 'amount', 'branch' => 'branch_id', 'reference' => 'reference'],
        ];

        // Validate report type
        if (!array_key_exists($validated['type'], $tableMap)) {
            return response()->json(['error' => 'Invalid report type selected.'], 400);
        }

        // Get table configuration
        $tableConfig = $tableMap[$validated['type']];
        $table = $tableConfig['table'];
        $dateColumn = $tableConfig['date'];
        $amountColumn = $tableConfig['amount'];
        $branchColumn = $tableConfig['branch'];
        $referenceColumn = $tableConfig['reference'];

        // Select common columns
        $columns = ["$table.id", "$table.$dateColumn AS date"];

        // Include amount column if available
        if ($amountColumn) {
            $columns[] = "$table.$amountColumn AS amount";
        }

        // Use `receipt_no` for `receipts` and `payments`, otherwise use `reference`
        if ($referenceColumn) {
            $columns[] = "$table.$referenceColumn AS reference";
        }

        // Handle tables that have `description`
        if (Schema::hasColumn($table, 'description')) {
            $columns[] = "$table.description";
        }

        // Special case: `purchase_expenses` needs a join with `purchases` to get branch_id
        if ($validated['type'] === 'purchase_expenses') {
            $columns[] = 'purchases.branch_id AS branch_id';
        } else {
            $columns[] = "$table.$branchColumn AS branch_id";
        }

        // Identify user column (`posted_by`, `created_by`, `updated_by`, etc.)
        $userColumn = null;
        $possibleUserColumns = ['posted_by', 'created_by', 'updated_by'];
        foreach ($possibleUserColumns as $col) {
            if (Schema::hasColumn($table, $col)) {
                $userColumn = $col;
                break;
            }
        }

        // Include user column if found
        if ($userColumn) {
            $columns[] = "$table.$userColumn";
        }

        // Build Query with necessary joins
        $query = DB::table($table);

        // Join with `purchases` for `purchase_expenses` to get branch_id
        if ($validated['type'] === 'purchase_expenses') {
            $query->join('purchases', 'purchases.id', '=', 'purchase_expenses.purchase_id');
        }

        // Join with branches to get branch name
        if ($branchColumn) {
            $query->leftJoin('branches', 'branches.id', '=', $branchColumn);
        }

        // Join with users to get user name
        if ($userColumn) {
            $query->leftJoin('users', "users.id", '=', "$table.$userColumn");
        }

        // Select final columns
        $query->select(array_merge($columns, [
            'branches.name AS branch_name',
            DB::raw("CONCAT(users.firstname, ' ', users.surname) AS user_name")
        ]));

        // Apply filters
        $query->whereBetween("$table.$dateColumn", [$validated['from_date'], $validated['to_date']]);

        // Special case: `intersite_transfers` uses `source_branch_id` for filtering
        if ($validated['type'] === 'intersite_transfers') {
            $query->where(function ($q) use ($validated, $table) {
                $q->where("$table.source_branch_id", $validated['branch_id'])
                    ->orWhere("$table.destination_branch_id", $validated['branch_id']);
            });
        } // Special case: `purchase_expenses` needs branch_id from `purchases`
        elseif ($validated['type'] === 'purchase_expenses') {
            $query->where("purchases.branch_id", $validated['branch_id']);
        } else {
            $query->where("$table.$branchColumn", $validated['branch_id']);
        }

        // Order by date
        $query->orderBy("$table.$dateColumn", 'desc');

        // Get final results
        $reports = $query->get();
        $route_name = $this->getRouteName($validated['type']);
        return view('pages.reports.inventory.user_entries.load_user_entries_report', compact('reports', 'route_name'));
    }

}
