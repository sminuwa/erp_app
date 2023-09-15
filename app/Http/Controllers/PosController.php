<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Models\StoreProduct;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PosController extends Controller
{
    public function index(Request $request)
    {
        if (!strpos(url()->previous(), 'pos')) //Clear the cart after leaving the POS page
            \Cart::clear();
        $user_branch = User::userBranchAction();
        $category_id = 0;
        $store_id = 0;
        $stores = StoreProduct::select('store_products.id', 'products.name', 'stores.name AS store', 'qty_available', 'selling_price', 'cost_price')->distinct()
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('store_product_prices', function ($join) {
                $join->on('store_product_prices.product_id', '=', 'products.id')
                    ->on('store_product_prices.store_id', '=', 'stores.id');

            })
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stores.branch_id', 'LIKE', $user_branch)
            ->where('store_product_prices.status', 1);
        if ($request->has('category_id') && $request->has('store_id')) {
            $category_id = $request->category_id;
            $store_id = $request->store_id;
            if ($request->category_id == 'all')
                $category_id = '%';
            if ($request->store_id == 'all')
                $store_id = '%';
            $stores = $stores->where('store_products.store_id', 'LIKE', $store_id)
                ->where('products.category_id', 'LIKE', $category_id);
        }

        $stores = $stores->where('store_products.qty_available', '>', 0)
            ->orderBy('products.name')->orderBy('stores.name')->get();

        $customers = Customer::where('type', 'credit')->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $cart_products = \Cart::getContent();
        $categories = Category::orderBy('name', 'ASC')->get();
        $store = Store::where('id', 'LIKE', $user_branch)->get();
        $debtor = new CustomerController();
        $receipt_no = $debtor->generateReceiptNo();
        return view('pages.pos.index', compact('stores', 'customers', 'cart_products', 'categories', 'store', 'category_id', 'store_id', 'receipt_no'));
    }
    public function wholeSale(Request $request)
    {
        if (!strpos(url()->previous(), 'pos')) //Clear the cart after leaving the POS page
            \Cart::clear();
        $user_branch = User::userBranchAction();
        $category_id = 0;
        $store_id = 0;
        $stores = StoreProduct::select('store_products.id', 'products.name', 'stores.name AS store', 'qty_available', 'selling_price', 'cost_price')->distinct()
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('store_product_prices', function ($join) {
                $join->on('store_product_prices.product_id', '=', 'products.id')
                    ->on('store_product_prices.store_id', '=', 'stores.id');

            })
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stores.branch_id', 'LIKE', $user_branch)
            ->where('store_product_prices.status', 1);
        if ($request->has('category_id') && $request->has('store_id')) {
            $category_id = $request->category_id;
            $store_id = $request->store_id;
            if ($request->category_id == 'all')
                $category_id = '%';
            if ($request->store_id == 'all')
                $store_id = '%';
            $stores = $stores->where('store_products.store_id', 'LIKE', $store_id)
                ->where('products.category_id', 'LIKE', $category_id);
        }

        $stores = $stores->where('store_products.qty_available', '>', 0)
            ->orderBy('products.name')->orderBy('stores.name')->get();

        $customers = Customer::where('type', 'credit')->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $cart_products = \Cart::getContent();
        $categories = Category::orderBy('name', 'ASC')->get();
        $store = Store::where('id', 'LIKE', $user_branch)->get();
        $debtor = new CustomerController();
        $receipt_no = $debtor->generateReceiptNo();
        return view('pages.pos.whole_sale', compact('stores', 'customers', 'cart_products', 'categories', 'store', 'category_id', 'store_id', 'receipt_no'));
    }
    //This is no longer used but keep it
    public function edit(Request $request, Order $order)
    {

        $category_id = 0;
        $store_id = 0;
        $user_branch = User::userBranchAction();
        $stores = StoreProduct::select('store_products.id', 'products.name', 'stores.name AS store', 'qty_available', 'selling_price', 'cost_price')->distinct()
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('store_product_prices', function ($join) {
                $join->on('store_product_prices.product_id', '=', 'products.id')
                    ->on('store_product_prices.store_id', '=', 'stores.id');

            })
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stores.branch_id', 'LIKE', $user_branch)
            ->where('store_product_prices.status', 1);
        if ($request->has('category_id') && $request->has('store_id')) {
            $category_id = $request->category_id;
            $store_id = $request->store_id;
            if ($request->category_id == 'all')
                $category_id = '%';
            if ($request->store_id == 'all')
                $store_id = '%';
            $stores = $stores->where('store_products.store_id', 'LIKE', $store_id)
                ->where('products.category_id', 'LIKE', $category_id);
        }

        $stores = $stores->where('store_products.qty_available', '>', 0)
            ->orderBy('products.name')->orderBy('stores.name')->get();


        $customers = Customer::where('type', 'credit')->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        if (\Cart::getContent()->isEmpty())
            $this->loadToCart($order);
        $cart_products = \Cart::getContent();
        $categories = Category::orderBy('name', 'ASC')->get();
        $store = Store::where('id', 'LIKE', $user_branch)->get();
        return view('pages.pos.edit', compact('stores', 'customers', 'cart_products', 'categories', 'store', 'category_id', 'store_id', 'order'));
    }

    public function loadToCart(Order $order)
    {
        //\Cart::clear();
        foreach ($order->order_items()->where('status', 1)->get() as $data) {
            $qty = $data->quantity == 0 ? 1 : $data->quantity;
            \Cart::add([
                'id' => $data->store_product_id,
                'name' => $data->storeProduct->product->name,
                'price' => $data->sold_price,
                'quantity' => $qty,
                'attributes' => array('cost_price' => $data->cost_price, 'selling_price' => $data->selling_price, 'discount' => 0),
            ]);
        }
    }
    public function barcodeSearch(Request $request)
    {
        $barcode = $request->barcode;
        $user_branch = User::userBranchAction();
        $store = StoreProduct::select('store_products.id', 'products.name', 'stores.name AS store', 'qty_available', 'selling_price', 'cost_price')->distinct()
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('store_product_prices', function ($join) {
                $join->on('store_product_prices.product_id', '=', 'products.id')
                    ->on('store_product_prices.store_id', '=', 'stores.id');

            })
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stores.branch_id', 'LIKE', $user_branch)
            ->where('products.barcode', $barcode)
            ->where('store_product_prices.status', 1)
            ->where('store_products.qty_available', '>', 0)
            ->orderBy('products.name')->orderBy('stores.name')->first();

        $add = \Cart::add([
            'id' => $store->id,
            'name' => $store->name,
            'price' => $store->selling_price,
            'quantity' => 1,
            'attributes' => array('cost_price' => $store->cost_price, 'selling_price' => $store->selling_price, 'qty_available' => $store->qty_available, 'discount' => 0),
        ]);
        $cart_products = \Cart::getContent();
        return view('pages.pos.load_cart_barcode_search', compact('cart_products'));
    }
}