<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseExpense;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AdditionalInvoiceController extends Controller
{
    //
    public function index(Request $request){
        $invoices = PurchaseExpense::orderBy('id','desc')->get();
        return view('pages.inventories.expenses.index', compact('invoices'));
    }

    public function create(){
        $purchases = Purchase::orderBy('id','desc')->get();
        $suppliers = Supplier::orderBy('code','asc')->get();
        return view('pages.inventories.expenses.create',compact('purchases', 'suppliers'));
    }

    public function store(Request $request){

    }

    public function edit(PurchaseExpense $invoice){
        $purchases = Purchase::orderBy('id','desc')->get();
        $suppliers = Supplier::orderBy('code','asc')->get();
        return view('pages.inventories.expenses.create', compact('invoice','purchases', 'suppliers'));
    }

    public function post(PurchaseExpense $invoice){

    }

    public function reverse(PurchaseExpense $invoice){

    }

    public function print(PurchaseExpense $invoice){

    }
}
