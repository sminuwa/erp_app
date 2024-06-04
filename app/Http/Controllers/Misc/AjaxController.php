<?php

namespace App\Http\Controllers\Misc;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\StoreProduct;

class AjaxController extends Controller
{
    public function categories()
    {
        $records = Category::orderBy('code', 'asc')->get();
        return view('misc.ajax.categories', compact('records'));
    }

    public function customers()
    {
        $user_branch = User::userBranchAction();
        $records = Customer::where('branch_id', $user_branch)->orderBy('code', 'asc')->get();
        return view('misc.ajax.customers', compact('records'));
    }

    public function suppliers()
    {
        $records = Supplier::orderBy('code', 'asc')->get();
        return view('misc.ajax.suppliers', compact('records'));
    }

    public function products(Request $request)
    {
        $category_id = $request->category_id;
        $records = Product::with('category')->forCategory($category_id)->orderBy('code', 'asc')->get();
        return view('misc.ajax.products', compact('records'));
    }

    public function stores(Request $request)
    {
        $branch_id = $request->branch_id;
        $records = Store::forBranch($branch_id)->orderBy('code', 'asc')->get();
        return view('misc.ajax.stores', compact('records'));
    }

    public function branches(Request $request)
    {
        $records = Branch::orderBy('code', 'asc')->get();
        return view('misc.ajax.branches', compact('records'));
    }

    public function chart_of_accounts()
    {
        $records = ChartOfAccount::orderBy('class', 'asc')->get();
        return view('misc.ajax.chart-of-accounts', compact('records'));
    }

    public function general_accounts()
    {
        $records = GeneralAccount::orderBy('number', 'asc')->get();
        return view('misc.ajax.general-accounts', compact('records'));
    }

    public function companies(Request $request)
    {
        $records = Company::orderBy('name', 'asc')->get();
        return view('misc.ajax.companies', compact('records'));
    }
    public function users()
    {
        $records = User::orderBy('user_code', 'asc')->get();
        return view('misc.ajax.users', compact('records'));
    }
    public function storeProducts(Request $request)
    {
        $records = StoreProduct::with('product','store')->forProducts($request->store_id)->get();
        return view('misc.ajax.store-products', compact('records'));
    }
}
