<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CreditNote;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\StoreProduct;
use App\Models\Customer;
use App\Models\BankAccount;
use App\Models\Supplier;
use App\Models\Store;
use App\Models\Purchase;
use App\Models\SupplierLedger;
use Carbon\Carbon;
use App\Models\LoanCollector;
use App\Models\Loan;
use App\Models\BranchProductPrice;
use GuzzleHttp\Psr7\Response;
use App\Models\User;

class MisController extends Controller
{
    public function loadproducts(Request $request)
    {
        $types = DB::table('products')
            ->select('products.id', 'products.name')
            ->where('category_id', 'like', $request->query->get('category_id'))
            ->orderBy('name', 'asc')
            ->get();
        $result = "";
        if ($types->count() > 1)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->name . "</option>";
        }
        return $result;
    }
    public function loadAvailableProducts(Request $request)
    {
        $types = DB::table('products')
            ->select('products.id', 'products.name')
            ->join('store_products', 'store_products.product_id', 'products.id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where('category_id', 'like', $request->query->get('category_id'))
            ->where('store_products.store_id', $request->query->get('store_id'))
            ->where('qty_available', '>', 0)
            ->orderBy('name', 'asc')
            ->get();
        $result = "";
        if ($types->count() > 0)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->name . "</option>";
        }
        return $result;
    }
    public function loadbranches(Request $request)
    {
        $types = DB::table('branches')
            ->select('branches.id', 'branches.name')
            ->where('bank_id', 'like', $request->query->get('bank_id'))
            ->where('id', 'LIKE', User::userBranchAction())
            ->orderBy('name', 'asc')
            ->get();
        $result = "";
        if ($types->count() > 1)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->name . "</option>";
        }
        return $result;
    }
    public function loadBankAccounts(Request $request)
    {
        //$payment_mode = $request->payment_mode == 'Cash' ? $request->payment_mode : '%';
        $types = DB::table('bank_accounts')
            ->select('account_no', 'account_name', 'bank_accounts.id')
            ->join('branches', 'branches.id', 'bank_accounts.branch_id')
            ->where('branch_id', 'LIKE', User::userBranchAction());
        if ($request->payment_mode != "Cash") {
            $types = $types->where('account_type', 'like', '%')->where('account_type', '<>', 'Cash')
                ->orderBy('account_no', 'asc')->get();
        } else if ($request->payment_mode == "Cash") {
            $types = $types->where('account_type', '=', 'Cash')
                ->orderBy('account_no', 'asc')->get();
        }

        //$types->orderBy('account_no', 'asc')->get();
        $result = "";
        if ($types->count() > 0)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->account_no . '-' . $type->account_name . "</option>";
        }
        return $result;
    }
    public function loadStoreProduct($categor_id)
    {
        $stores = DB::table('store_products')
            ->select('products.name', 'stores.name as store', 'store_products.qty_available', 'selling_price', 'cost_price', 'store_products.id')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->join('stores', 'stores.id', '=', 'store_products.store_id')
            ->join('branch_product_prices', 'branch_product_prices.id', '=', 'products.id')
            ->where('store_products.qty_available', '>', 0)
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where('products.category_id', '=', $categor_id)->get();
        return view('pages.pos.load_store_products', compact('stores'));

    }
    public function loadAccountName(Request $request)
    {
        return BankAccount::find($request->bank_account_id)->account_name;
    }
    public function loadAccountBalance(Request $request)
    {
        return BankAccount::find($request->bank_account_id)->account_balance;
    }
    public function loadSupplierBalance(Request $request)
    {
        //return Supplier::find($request->supplier_id)->opening_balance;
        $cr = SupplierLedger::where(['supplier_id' => $request->supplier_id])->sum('cr');
        $dr = SupplierLedger::where(['supplier_id' => $request->supplier_id])->sum('dr');
        return $cr - $dr;

    }
    public function loadCustomers(Request $request)
    { 
        $types = DB::table('customers')
            ->select('customers.id', 'customers.name', 'customers.code')
            ->where('type', '=', $request->type)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('name', 'asc')
            ->get();
        return view('misc.ajax.customers', ['records' => $types]);

    }

    public function loadCustomerOrders(Request $request)
    {   //return $request->type;
        $customer = Customer::find($request->customer_id);
        $credit_notes = CreditNote::where('customer_id', $customer->id)->pluck('order_id')->toArray();
        $orders = Order::where(['customer_id' => $customer->id, 'status' => 1])
            ->whereNotIn('id', $credit_notes)
            ->orderBy('id', 'desc')->get();
        //        if (count($orders) > 1)
        $result = "<tr>";
        foreach ($orders as $order) {
            $result .= '<tr>
                        <td>
                            <a href="javascript:void(0)" class="invoice" onclick="load()"
                               data-val="' . $order->reference . '">' . $order->reference . '</a>
                        </td>
                        <td>' . \Carbon\Carbon::parse($order->order_date)->toFormattedDateString() . '</td>
                    </tr>';
        }
        $result .= "</tr>";
        return $result;
    }

    public function loadSuppliers(Request $request)
    { //return $request->type;
        $types = DB::table('suppliers')
            ->select('suppliers.id', 'suppliers.name', 'suppliers.phone')
            ->where('type', '=', $request->type)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('name', 'asc')
            ->get();
        $result = "";
        if ($types->count() > 1)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->name;
            if ($type->phone != null)
                $result .= "-" . $type->phone;
            $request .= "</option>";
        }
        return $result;
    }
    public function loadStoreProducts(Request $request)
    {
        $store_id = $request->store_id;
        $category_id = $request->category_id;
        if ($store_id == "all")
            $store_id = "%";
        if ($category_id == "all")
            $category_id = "%";
        $types = DB::table('store_products')
            ->join('products', 'products.id', '=', 'store_products.product_id')
            ->select('products.id', 'products.name')
            ->where('store_products.store_id', 'LIKE', $store_id)
            ->where('products.category_id', 'LIKE', $category_id)
            ->orderBy('name', 'asc')
            ->get();
        $result = "";
        if ($types->count() > 1)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->name . "</option>";
        }
        return $result;
    }
    public function getCostPrice(Request $request)
    {
        $product_id = $request->product_id;
        $store_id = $request->store_id;
        $price = Purchase::select('unit_price')->where('source_store_id', $store_id)
            ->where('purchase_products.product_id', $product_id)
            ->join('purchase_products', 'purchase_products.purchase_id', 'purchases.id')
            ->orderBy('purchase_products.updated_at', 'DESC')->first();
        $old_price = BranchProductPrice::where(['store_id' => $store_id, 'product_id' => $product_id, 'status' => 1])->first();
        return $price != null ? $price->unit_price : ($old_price != null ? $old_price->cost_price : 0);
    }
    public function loadStoreProductQuantity(Request $request)
    {
        $store_id = $request->store_id;
        $product_id = $request->product_id;
        return StoreProduct::where(['store_id' => $store_id, 'product_id' => $product_id])->pluck('qty_available')->first();
    }
    public function loadSupplierInvoices(Request $request)
    {
        $types = DB::table('purchases')
            ->select('purchases.id', 'purchases.invoice', 'purchases.purchase_date')
            ->where('supplier_id', '=', $request->supplier_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $result = "";
        if ($types->count() > 1)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->invoice . "</option>";
        }
        return $result;
    }
    public function loadCustomerOverdueInvoices(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $types = $customer->orders()->whereDate('due_date', '<=', 'CURDATE()')
            ->where('due', '>', 0)
            ->where('payment_mode', 'Credit')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('due_date', 'ASC')->get();
        $result = "";
        if ($types->count() > 1)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->invoice_no . '-&#8358;' . number_format($type->due, 2) . '(' . Carbon::parse($type->due_date)->toFormattedDateString() . ")</option>";
        }
        return $result;
    }
    public function loadCustomerUnPaidInvoices(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $types = $customer->orders()->where('due', '>', 0)
            ->where('payment_mode', 'Credit')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('due_date', 'ASC')->get();
        $result = "";
        if ($types->count() > 1)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->invoice_no . '-&#8358;' . number_format($type->due, 2) . '(' . Carbon::parse($type->due_date)->toFormattedDateString() . ")</option>";
        }
        return $result;
    }
    public function loadLoanBalance(Request $request)
    {
        $loan_id = $request->loan_id;
        /*$loan = Loan::find($loan_id);
         $loan->amount;
         $sum_paid = $loan->payments()->sum('amount');*/
        $balance = Loan::where('balance', '>', 0)->where('loan_collector_id', $loan_id)->sum('balance');
        return $balance;
    }
    public function loadStores(Request $request)
    {
        $types = DB::table('stores')
            ->select('stores.id', 'stores.name')
            ->where('branch_id', 'like', $request->query->get('branch_id'))
            ->orderBy('name', 'asc')
            ->get();
        $result = "";
        if ($types->count() > 1)
            $result .= "<option value=''>Select....</option>";
        foreach ($types as $type) {
            $result .= "<option value='" . $type->id . "'>" . $type->name . "</option>";
        }
        return $result;
    }
    public function getSellingPrice(Request $request)
    {
        $product_id = $request->product_id;
        $branch_id = $request->branch_id;
        $price = BranchProductPrice::select('selling_price')->where('branch_id', $branch_id)
            ->where('product_id', $product_id)
            ->orderBy('updated_at', 'DESC')->first();
        return $price != null ? $price->selling_price : 0;
    }
    public function getLastTwoSellingPrice(Request $request)
    {
        $product_id = $request->product_id;
        $branch_id = $request->branch_id;
        $oldprice = 0;
        $newprice = 0;
        $prices = BranchProductPrice::select('selling_price')->where('branch_id', $branch_id)
            ->where('product_id', $product_id)
            ->orderBy('updated_at', 'ASC')->take(2)->get();
        $count = 1;
        foreach ($prices as $price) {
            if ($count == 1)
                $oldprice = $price->selling_price;
            else
                $newprice = $price->selling_price;
            $count++;
        }
        //return \json_encode($prices);
        return $prices != null ? $oldprice . "," . $newprice : "xx," . "yy";
    }
    public function generateProductCode(Request $request)
    {
        $category = Category::find($request->category_id);
        $length = strlen($category->code);
        $current = DB::table('products')->select(DB::raw("MAX(SUBSTR(code,$length+1)) as max"))->where('category_id', $category->id)->first();
        return $category->code . str_pad(($current->max + 1), 4, "0", STR_PAD_LEFT);
    }
    public static function nextCustomerCode(Request $request)
    {
        $account_type = $request->account_type;
        $user_branch = User::userBranchAction();
        $branch_code = Branch::find($user_branch)->code;
        $prefix_code = $branch_code . $account_type;
        $length = strlen($prefix_code);
        $query = DB::table('customers')->select(DB::raw("MAX(SUBSTR(code,$length+1)) as max"))->where('branch_id', $user_branch)->first();
        return $query == null ? $prefix_code . str_pad(1, 6, '0', STR_PAD_LEFT) : $prefix_code . str_pad($query->max + 1, 6, '0', STR_PAD_LEFT);
    }
}
