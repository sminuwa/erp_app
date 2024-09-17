<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BranchProductPrice;
use App\Http\Requests\BranchProductPrices\Index;
use App\Http\Requests\BranchProductPrices\Show;
use App\Http\Requests\BranchProductPrices\Create;
use App\Http\Requests\BranchProductPrices\StoreRequest;
use App\Http\Requests\BranchProductPrices\Edit;
use App\Http\Requests\BranchProductPrices\Update;
use App\Http\Requests\BranchProductPrices\Destroy;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;
use App\Imports\ProductPriceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

/**
 * Description of BranchProductPriceController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class BranchProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request): View|Response
    {
        $user_branch = User::userBranchAction();
        $records = BranchProductPrice::select('branch_product_prices.*', 'branches.name AS branch_name', 'branches.code AS branch_code', 'products.name AS product_name', 'products.code AS product_code')
            ->join('branches', 'branches.id', 'branch_product_prices.branch_id')
            ->join('products', 'products.id', 'branch_product_prices.product_id')
            ->where('branch_product_prices.branch_id', 'LIKE', $user_branch)
            ->where('branch_product_prices.retail_selling_price', '>', 0)
            ->where('products.status',1)
            ->latest('products.name')
            ->get();
        return view('pages.branch_product_prices.index', ['records' => $records]);
    } /**
      * Display the specified resource.
      *
      * @param  Show  $request
      * @param  BranchProductPrice  $BranchProductPrice
      * @return \Illuminate\Http\Response
      */
    public function show(Show $request, BranchProductPrice $BranchProductPrice): View|Response
    {
        return view('pages.branch_product_prices.show', [
            'record' => $BranchProductPrice,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Create  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Create $request): View|Response
    {
        $user_branch = User::userBranchAction();

        $products = Product::where('status', 1)->get();
        $categories = Category::all(['id', 'name', 'code']);
        $branches = Branch::select('id', 'name', 'code')->where('id', 'LIKE', $user_branch)
            ->where('status', 1)->get();
        return view('pages.branch_product_prices.create', [
            'model' => new BranchProductPrice,
            'categories' => $categories,
            'products' => $products,
            'branches' => $branches,
        ]);
    } /**
      * Store a newly created resource in storage.
      *
      * @param  Store  $request
      *
      */
    public function store(StoreRequest $request)
    {
        $count = 0;
        //$model = new BranchProductPrice;
        //$model->fill($request->all());
        try {
            $status = BranchProductPrice::updateOrCreate([
                'branch_id' => $request->branch_id,
                'product_id' => $request->product_id,
                'status' => $request->status
            ], [
                'retail_selling_price' => str_replace(',', '', $request->retail_selling_price),
                'whole_selling_price' => str_replace(',', '', $request->whole_selling_price),
                //'cost_price'=>$request->cost_price,
                'updated_by' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            if ($status) {
                $action = "Set a product price to $request->retail_selling_price , $request->whole_selling_price for " . Product::find($request->product_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Branch product price saved successfully');
                //            return redirect()->route('branch_product_prices.index');
                return redirect()->back()->with('app_message', 'Branch product price saved successfully');
            } else {
                session()->flash('app_message', 'Something is wrong while saving branch product price');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Edit  $request
      * @param  BranchProductPrice  $BranchProductPrice
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, BranchProductPrice $BranchProductPrice): View|Response
    {
        $user_branch = User::userBranchAction();
        $products = Product::where('status', 1)->get();
        $stores = Store::select('id', 'name')->where('branch_id', 'LIKE', $user_branch)->where('status', 1)->get();
        $categories = Category::all(['id', 'name']);
        $branches = Branch::select('id', 'name')->where('id', 'LIKE', $user_branch)->where('status', 1)->get();
        return view('pages.branch_product_prices.edit', [
            'model' => $BranchProductPrice,
            'categories' => $categories,
            'products' => $products,
            'stores' => $stores,
            'branches' => $branches,

        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Update  $request
      * @param  BranchProductPrice  $BranchProductPrice
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, BranchProductPrice $BranchProductPrice)
    {
        $count = 0;
        if ($request->store_id == "all") {
            $stores = Store::where('branch_id', User::userBranchAction())->get();
            foreach ($stores as $store) {
                $status = BranchProductPrice::updateOrCreate([
                    'store_id' => $store->id,
                    'product_id' => $request->product_id,
                    'status' => $request->status
                ], [
                    'selling_price' => $request->selling_price,
                    'updated_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                if ($status)
                    $count++;
            }
            if ($count > 0) {
                $action = "Set a product price to $request->selling_price for all stores in " . Branch::find($request->branch_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product price saved successfully');
                return redirect()->route('branch_product_prices.index');
            }
        } else {
            $BranchProductPrice->fill($request->all());
            if ($BranchProductPrice->save()) {
                $action = "Modified a product price to $BranchProductPrice->selling_price: " . $BranchProductPrice->product->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product price successfully updated');
                return redirect()->route('branch_product_prices.index');
            } else {
                session()->flash('app_error', 'Something is wrong while updating store product price');
            }
        }
        return redirect()->back();
    } /**

* Delete a  resource from  storage.
*
* @param  Destroy  $request
* @param  BranchProductPrice  $BranchProductPrice
* @return \Illuminate\Http\Response
* @throws \Exception
*/
    public function destroy(Destroy $request, BranchProductPrice $BranchProductPrice)
    {
        if ($BranchProductPrice->delete()) {
            $action = "Deleted a product price to $BranchProductPrice->selling_price: " . $BranchProductPrice->product->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Store product price successfully deleted');
        } else {
            session()->flash('app_error', 'Error occurred while deleting store product price');
        }

        return redirect()->back();
    }
    public function openingBalance(Create $request): View|Response
    {
        $this->authorize('stock_opening_balance.create');
        $products = Product::where('status', 1);
        $stores = Store::where('branch_id', User::userBranchAction())->where('status', 1)->get();
        $categories = Category::all(['id', 'name']);
        return view('pages.branch_product_prices.stock_opening_balance', [
            'model' => new BranchProductPrice,
            'categories' => $categories,
            'products' => $products,
            'stores' => $stores,
        ]);
    }
    public function storeStockBalance(Request $request)
    {
        $this->authorize('stock_opening_balance.store');
        $data = $request->store_id;
        DB::beginTransaction();
        try {
            DB::table('store_products')->updateOrInsert(
                ['store_id' => $request->store_id, 'product_id' => $request->product_id],
                [
                    'qty_available' => $request->qty,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
            DB::table('stock_cards')->updateOrInsert(
                ['store_id' => $request->store_id, 'product_id' => $request->product_id],
                [
                    'cr' => $request->qty,
                    'type' => 'Opening Balance',
                    'refno' => 'OPBalance',
                    'date' => Carbon::now(),
                    'user_id' => Auth::id(),
                    'priority' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
            session()->flash('app_message', 'Stock balance successfully set');
            $action = "Set stock opening balance to $request->qty store : " . Store::find($request->store_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Stock balance failed  to set');
            throw ($e);
        }

        return redirect()->back();

    }
    public function editCostPrice(Request $request): View|Response
    {
        $this->authorize('store_product_cost_prices.edit');
        $user_branch = User::userBranchAction();
        $products = Product::where('status', 1);
        $stores = Store::select('id', 'name')->where('branch_id', 'LIKE', $user_branch)->where('status', 1)->get();
        $categories = Category::all(['id', 'name']);
        $branches = Branch::select('id', 'name')->where('id', 'LIKE', $user_branch)->where('status', 1)->get();
        return view('pages.branch_product_prices.cost_price', [
            'model' => new BranchProductPrice(),
            'categories' => $categories,
            'products' => $products,
            'stores' => $stores,
            'branches' => $branches,

        ]);
    }
    public function updateCostPrice(Request $request)
    {
        $this->authorize('store_product_cost_prices.edit');
        $count = 0;
        if ($request->store_id == "all") {
            $stores = Store::where('branch_id', User::userBranchAction())->where('status',1)->get();
            foreach ($stores as $store) {
                $status = BranchProductPrice::where([
                    'store_id' => $store->id,
                    'product_id' => $request->product_id
                ])->update([
                            'cost_price' => $request->cost_price,
                            'updated_by' => Auth::id(),
                            'updated_at' => Carbon::now()
                        ]);
                if ($status)
                    $count++;
            }
            if ($count > 0) {
                $action = "Set a product cost price to $request->cost_price for all stores in " . Branch::find($request->branch_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product price saved successfully');
                return redirect()->route('branch_product_prices.index');
            }
        } else {
            $status = DB::table('branch_product_prices')->where([
                'store_id' => $request->store_id,
                'product_id' => $request->product_id
            ])->update([
                        'cost_price' => $request->cost_price,
                        'updated_by' => Auth::id(),
                        'updated_at' => Carbon::now()
                    ]);
            if ($status) {
                $action = "Modified a product cost price to $request->cost_price: " . Product::find($request->product_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store product cost price successfully updated');
                return redirect()->route('branch_product_prices.index');
            } else {
                session()->flash('app_error', 'Something is wrong while updating store product cost price');
            }
        }
        return redirect()->back();
    }
    public function importForm(): View|Response
    {
        $this->authorize('price.import.form');
        return view('pages.branch_product_prices.import');
    }
    public function import(Request $request)
    {
        $this->authorize('price.import');
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $file = $request->file('file');
        $import = new ProductPriceImport();
        $rows = Excel::toCollection($import, $file)->first();
        $user_branch = User::userBranchAction();
        $faileds = [];
        $count = 0;
        $data = array();
        try {
            foreach ($rows as $row) {
                $product_id = Product::product(trim($row['product_code']))->first() != null ? Product::product(trim($row['product_code']))->first()->id : 0;
                if ($product_id != 0) {
                    BranchProductPrice::updateOrInsert(
                        ['branch_id' => $user_branch, 'product_id' => $product_id],
                        [
                            'branch_id' => $user_branch,
                            'product_id' => $product_id,
                            'selling_price' => $row['retail_price'],
                            'retail_selling_price' => $row['retail_price'],
                            'whole_selling_price' => $row['whole_price'],
                            'cost_price' => $row['cost_price'],
                            'updated_by' => Auth::id(),
                            'updated_at' => Carbon::now(),
                        ]
                    );
                    $count++;
                } else {
                    $faileds[] = [$row['product_code'], $row['selling_price'], $row['cost_price']];
                }

            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
        //dd($faileds);
        session()->flash('app_message', 'File imported and records updated/inserted successfully!');
        return view('pages.branch_product_prices.import', ['faileds' => $faileds, 'count' => $count]);
    }
}
