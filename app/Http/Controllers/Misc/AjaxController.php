<?php

namespace App\Http\Controllers\Misc;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
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
}
