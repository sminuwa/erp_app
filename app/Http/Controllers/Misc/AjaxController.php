<?php

namespace App\Http\Controllers\Misc;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function categories(){
        $records = Category::orderBy('name','asc')->get();
        return view('misc.ajax.categories', compact('records'));
    }

    public function customers(){
        $records = Customer::orderBy('name','asc')->get();
        return view('misc.ajax.customers', compact('records'));
    }

    public function suppliers(){
        $records = Supplier::orderBy('name','asc')->get();
        return view('misc.ajax.suppliers', compact('records'));
    }

    public function products(){
        $records = Product::orderBy('name','asc')->get();
        return view('misc.ajax.products', compact('records'));
    }

    public function chart_of_accounts(){
        $records = ChartOfAccount::orderBy('class','asc')->get();
        return view('misc.ajax.chart-of-accounts', compact('records'));
    }

    public function general_accounts(){
        $records = GeneralAccount::orderBy('number','asc')->get();
        return view('misc.ajax.general-accounts', compact('records'));
    }
}
