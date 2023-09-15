<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StoreProductPrice;
use App\Http\Requests\StoreProductPrices\Index;
use App\Http\Requests\StoreProductPrices\Show;
use App\Http\Requests\StoreProductPrices\Create;
use App\Http\Requests\StoreProductPrices\StoreRequest;
use App\Http\Requests\StoreProductPrices\Edit;
use App\Http\Requests\StoreProductPrices\Update;
use App\Http\Requests\StoreProductPrices\Destroy;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use StoreProduct;
use App\Models\User;


/**
 * Description of StoreProductPriceController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class StoreProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        $user_branch = User::userBranchAction();
        $records = StoreProductPrice::select('store_product_prices.*', 'stores.name', 'products.name')
            ->join('stores', 'stores.id', 'store_product_prices.store_id')
            ->join('products', 'products.id', 'store_product_prices.product_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('branch_id', 'LIKE', $user_branch)
            ->latest('products.name')
            ->get();
        return view('pages.store_product_prices.index', ['records' => $records]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  StoreProductPrice  $storeproductprice
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, StoreProductPrice $storeproductprice)
    {
        return view('pages.store_product_prices.show', [
            'record' => $storeproductprice,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {
        $user_branch = User::userBranchAction();

        $products = Product::all(['id', 'name']);
        $stores = Store::select('id', 'name')->where('branch_id', 'LIKE', $user_branch)->get();
        $categories = Category::all(['id', 'name']);
        $branches = Branch::select('id', 'name')->where('id', 'LIKE', $user_branch)->get();
        return view('pages.store_product_prices.create', [
            'model' => new StoreProductPrice,
            'categories' => $categories,
            'products' => $products,
            'stores' => $stores,
            'branches' => $branches,
        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(StoreRequest $request)
    {
        $count = 0;
        if ($request->store_id == "all") {
            $stores = Store::where('branch_id', User::userBranchAction())->get();
            foreach ($stores as $store) {
                $status = StoreProductPrice::updateOrCreate(['store_id' => $store->id,
                    'product_id' => $request->product_id, 'status' => $request->status], [
                    'selling_price' => $request->selling_price,
                    //'cost_price'=>$request->cost_price,
                    'updated_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()]);
                if ($status)
                    $count++;
            }
            if ($count > 0) {
                $action = "Set a product price to $request->selling_price for all stores in " . Branch::find($request->branch_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product price saved successfully');
                return redirect()->route('store_product_prices.index');
            }
        }
        else {
            //$model = new StoreProductPrice;
            //$model->fill($request->all());
            $status = StoreProductPrice::updateOrCreate(['store_id' => $request->store_id,
                    'product_id' => $request->product_id, 'status' => $request->status], [
                    'selling_price' => $request->selling_price,
                    //'cost_price'=>$request->cost_price,
                    'updated_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()]);
            if ($status) {
                $action = "Set a product price to $request->selling_price for " . Product::find($request->product_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product price saved successfully');
                return redirect()->route('store_product_prices.index');
            }
            else {
                session()->flash('app_message', 'Something is wrong while saving StoreProductPrice');
            }
        }

        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  StoreProductPrice  $storeproductprice
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, StoreProductPrice $storeproductprice)
    {
        $user_branch = User::userBranchAction();
        $products = Product::all(['id', 'name']);
        $stores = Store::select('id', 'name')->where('branch_id', 'LIKE', $user_branch)->get();
        $categories = Category::all(['id', 'name']);
        $branches = Branch::select('id', 'name')->where('id', 'LIKE', $user_branch)->get();
        return view('pages.store_product_prices.edit', [
            'model' => $storeproductprice,
            'categories' => $categories,
            'products' => $products,
            'stores' => $stores,
            'branches' => $branches,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  StoreProductPrice  $storeproductprice
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, StoreProductPrice $storeproductprice)
    {
        $count = 0;
        if ($request->store_id == "all") {
            $stores = Store::where('branch_id', User::userBranchAction())->get();
            foreach ($stores as $store) {
                $status = StoreProductPrice::updateOrCreate(['store_id' => $store->id,
                    'product_id' => $request->product_id, 'status' => $request->status], [
                    'selling_price' => $request->selling_price,
                    'updated_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()]);
                if ($status)
                    $count++;
            }
            if ($count > 0) {
                $action = "Set a product price to $request->selling_price for all stores in " . Branch::find($request->branch_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product price saved successfully');
                return redirect()->route('store_product_prices.index');
            }
        }
        else {
            $storeproductprice->fill($request->all());
            if ($storeproductprice->save()) {
                $action = "Modified a product price to $storeproductprice->selling_price: " . $storeproductprice->product->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product price successfully updated');
                return redirect()->route('store_product_prices.index');
            }
            else {
                session()->flash('app_error', 'Something is wrong while updating store product price');
            }
        }
        return redirect()->back();
    } /**
    
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  StoreProductPrice  $storeproductprice
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, StoreProductPrice $storeproductprice)
    {
        if ($storeproductprice->delete()) {
            $action = "Deleted a product price to $storeproductprice->selling_price: " . $storeproductprice->product->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Store product price successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting store product price');
        }

        return redirect()->back();
    }
    public function openingBalance(Create $request)
    {
        $products = Product::all(['id', 'name']);
        $stores = Store::where('branch_id', User::userBranchAction())->get();
        $categories = Category::all(['id', 'name']);
        return view('pages.store_product_prices.stock_opening_balance', [
            'model' => new StoreProductPrice,
            'categories' => $categories,
            'products' => $products,
            'stores' => $stores,
        ]);
    }
    public function storeStockBalance(Request $request)
    {
        $data = $request->store_id;
        DB::beginTransaction();
        try {
            DB::table('store_products')->updateOrInsert(['store_id' => $request->store_id, 'product_id' => $request->product_id],
            ['qty_available' => $request->qty,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('stock_cards')->updateOrInsert(['store_id' => $request->store_id, 'product_id' => $request->product_id],
            ['cr' => $request->qty,
                'type' => 'Opening Balance',
                'refno' => 'OPBalance',
                'date' => Carbon::now(),
                'user_id' => Auth::id(),
                'priority' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            session()->flash('app_message', 'Stock balance successfully set');
            $action = "Set stock opening balance to $request->qty store : " . Store::find($request->store_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Stock balance failed  to set');
            throw ($e);
        }

        return redirect()->back();

    }
     public function editCostPrice(Request $request)
    {
        $user_branch = User::userBranchAction();
        $products = Product::all(['id', 'name']);
        $stores = Store::select('id', 'name')->where('branch_id', 'LIKE', $user_branch)->get();
        $categories = Category::all(['id', 'name']);
        $branches = Branch::select('id', 'name')->where('id', 'LIKE', $user_branch)->get();
        return view('pages.store_product_prices.cost_price', [
            'model' => new StoreProductPrice(),
            'categories' => $categories,
            'products' => $products,
            'stores' => $stores,
            'branches' => $branches,

        ]);
    } 
    public function updateCostPrice(Request $request)
    {
        $count = 0;
        if ($request->store_id == "all") {
             $stores = Store::where('branch_id', User::userBranchAction())->get();
            foreach ($stores as $store) {
                $status = StoreProductPrice::where(['store_id' => $store->id,
                'product_id' => $request->product_id])->update([
                    'cost_price' => $request->cost_price,
                    'updated_by' => Auth::id(),
                    'updated_at' => Carbon::now()]);
                if ($status)
                    $count++;
            }
            if ($count > 0) {
                $action = "Set a product cost price to $request->cost_price for all stores in " . Branch::find($request->branch_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product price saved successfully');
                return redirect()->route('store_product_prices.index');
            }
        }
        else {
            $status = DB::table('store_product_prices')->where(['store_id' => $request->store_id,
                'product_id' => $request->product_id])->update([
                    'cost_price' => $request->cost_price,
                    'updated_by' => Auth::id(),
                'updated_at'=>Carbon::now()]);
            if ($status) {
                $action = "Modified a product cost price to $request->cost_price: " . Product::find($request->product_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product cost price successfully updated');
                return redirect()->route('store_product_prices.index');
            }
            else {
                session()->flash('app_error', 'Something is wrong while updating store product cost price');
            }
        }
        return redirect()->back();
    }
}