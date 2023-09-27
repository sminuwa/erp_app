<?php

namespace App\Http\Controllers\Inventory;

use App\Classes\Transaction;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    //
    public function index(Request $request)
    {
        \Cart::clear();
        $records = Purchase::select('purchases.*')
            ->orderBy('purchase_date', 'DESC')
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            ->take(10)
            ->get();
        return view('pages.inventories.purchases.index', compact('records'));
    }

    public function create(){
        return  view('pages.inventories.purchases.create');

    }
}
