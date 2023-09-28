<?php

namespace App\Http\Controllers\Inventory;

use App\Classes\Transaction;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\TempPurchaseProduct;
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
        TempPurchaseProduct::clearAll();
        return  view('pages.inventories.purchases.create');
    }

    public function createAjax(Request $request){
        $temp = new TempPurchaseProduct();
        $temp->product_id = $request->product_id;
        $temp->quantity = $request->quantity;
        $temp->unit_price = $request->unit_price;
        $temp->user_id = auth()->id();
        $temp->save();
        return view('pages.inventories.purchases.ajax.create',['products'=>TempPurchaseProduct::products()]);
    }
}
