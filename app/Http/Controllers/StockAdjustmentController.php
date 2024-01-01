<?php

namespace App\Http\Controllers;

use App\Classes\CostPrice;
use App\Classes\Transaction;
use App\Models\StockAdjustmentDetail;
use App\Models\StockCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Http\Requests\StockAdjustments\Index;
use App\Http\Requests\StockAdjustments\Show;
use App\Http\Requests\StockAdjustments\Create;

use App\Http\Requests\StockAdjustments\Edit;
use App\Http\Requests\StockAdjustments\Update;
use App\Http\Requests\StockAdjustments\Destroy;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use App\Models\StoreProduct;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\AuditLog;
use App\Models\User;


/**
 * Description of StockAdjustmentController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class StockAdjustmentController extends Controller
{

    public function index(Index $request)
    {
        $this->authorize('stock_adjustments.index');
        \Cart::clear();
        $user = auth()->user();
        $branch = $user->branch;
        $records = StockAdjustment::where(['branch_id'=>$branch->id])->orderBy('reference', 'desc')->get();
        return view('pages.inventories.stock_adjustments.index',compact('records'));
    }

    public function show(Show $request, StockAdjustment $stockAdjustment)
    {
        return view('pages.inventories.stock_adjustments.show', [
            'record' => $stockAdjustment,
        ]);

    }

    public function create(Create $request)
    {
        $this->authorize('stock_adjustments.create1');
        $products = Product::select('products.id', 'products.name','code')
            ->join('store_products', 'store_products.product_id', 'products.id')
            ->orderBy('code','asc')
            ->get();
        $stores = Store::where('branch_id', User::userBranchAction())->get();
        return view('pages.inventories.stock_adjustments.create', [
            'model' => new StockAdjustment,
            'products' => $products,
            'stores' => $stores,
        ]);
    }

    public function store(Request $request)
    {

        $user = auth()->user();
        $branch = $user->branch;
        $stock_adjustment_id = $request->stock_adjustment_id;
        $operation = $request->operation;
        $date = $request->date;
        $description = $request->description;
        $stock = StockAdjustment::find($stock_adjustment_id);
        $items = \Cart::getContent();

        DB::beginTransaction();
        try {
            if(!$stock){
                $stock = new StockAdjustment();
                $stock->reference = StockAdjustment::generateNewNumber();
                $stock->created_by = auth()->id();
                $stock->status = 0;
            }
            $stock->branch_id = $branch->id;
            $stock->operation = $operation;
            $stock->description = $description;
            $stock->date = $date;
            if($stock->save()){
                if (count($items) > 0) {
                    StockAdjustmentDetail::where('stock_adjustment_id', $stock->id)->delete();
                    foreach ($items as $product) {
                        $detail = new StockAdjustmentDetail();
                        $attribute = $product->attributes;
                        $detail->stock_adjustment_id = $stock->id;
                        $detail->store_id = $attribute['store_id'];
                        $detail->product_id = $attribute['product_id'];
                        $detail->quantity = $product->quantity;
                        $detail->cost_price = $product->price;
                        $detail->expiry_date = $attribute['expiry_date'];
                        $detail->save();
                    }
                }
                $action = "Stock Adjustment created/updated $stock->reference";
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Stock Adjustment successfully');
                DB::commit();
            }else{
                DB::rollBack();
            }
        }
        catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('app_message', 'Something is wrong while transfering stock');
            throw $ex;
        }
        \Cart::clear();
        return back();
    }

    public function edit(Request $request, StockAdjustment $stockAdjustment)
    {
        $products = Product::select('products.id', 'products.name','code')
            ->join('store_products', 'store_products.product_id', 'products.id')
            ->orderBy('code','asc')
            ->get();
        $stores = Store::where('branch_id', User::userBranchAction())->get();
        $items = $stockAdjustment->products;

        if (\Cart::isEmpty()){
            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                \Cart::add([
                    'id' => generateRandomString(),
                    'name' => $product->name,
                    'price' => 0,
                    'quantity' => abs($item->quantity),
                    'attributes' => array(
                        'available_qty' => $item->available_qty ?? '',
                        'store_id' => $item->store_id,
                        'product_id' => $item->product_id,
                        'code' => $product->code,
                        'expiry_date' => $item->expiry_date ?? '',
                    ),
                ]);

            }
        }
        return view('pages.inventories.stock_adjustments.create', [
            'model' => $stockAdjustment,
            'products' => $products,
            'stores' => $stores,
        ]);
    }

    public function post(Request $request, StockAdjustment $stockAdjustment){
        $method = $request->method();
        if($method == 'POST'){
            $stockAdjustment->status = 1;
            $stockAdjustment->posted_by = auth()->id();
            $items = $stockAdjustment->products;
            DB::beginTransaction();
//            return $stockAdjustment;
            if($stockAdjustment->save()){

                $new_cost_price = [];
                foreach ($items as $item) {
                    $new_cost_price[$item->product_id] = [
                        'quantity' => $item->quantity,
                        'price' => $item->cost_price,
                        'store_id' => $item->store_id,
                        'expiry_date' => $item->expire_date,
                    ];
                }

                /*return
                    CostPrice::newCostPrice(
                        $new_cost_price,
                        $stockAdjustment->reference,
                        $stockAdjustment->branch_id,
                        $stockAdjustment->date,
                        TRANSACTION_TYPE_ADJUSTMENT,
                        $stockAdjustment->operation
                    );*/

                if (
                    Transaction::stock_adjustment($stockAdjustment->id)['status']
                    && CostPrice::newCostPrice(
                        $new_cost_price,
                        $stockAdjustment->reference,
                        $stockAdjustment->branch_id,
                        $stockAdjustment->date,
                        TRANSACTION_TYPE_ADJUSTMENT,
                        $stockAdjustment->operation
                    )['status']
                ) {
                    $action = "Posted stock adjustment with reference $stockAdjustment->reference";
                    AuditLog::auditLog(Auth::id(), $action);
                    DB::commit();
                } else {
                    DB::rollback();
                    session()->flash('app_error', 'Something went wrong while posting stock adjustment');
                }

            }
        }
        return back();
    }

    public function print(StockAdjustment $stockAdjustment)
    {
        $record = $stockAdjustment;
        return view('pages.inventories.stock_adjustments.print', compact('record'));
    }

    public function delete(Request $request, StockAdjustment $stockAdjustment){
        $method = $request->method();
        $stock_adjustment_id = $stockAdjustment->id;
        if($method == 'POST'){
            if($stockAdjustment->status == 0){
                DB::beginTransaction();
                if($stockAdjustment->delete()){
                    StockAdjustmentDetail::where('stock_adjustment_id',$stock_adjustment_id)->delete();
                    session()->flash('app_message', 'Stock Adjustment record deleted successfully!');
                    DB::commit();
                }
            }else
                session()->flash('app_error', 'Stock Adjustment record cannot be deleted!');
        }else{
            session()->flash('app_error', 'Invalid request methods.');
            DB::rollBack();
        }
        return redirect()->route('stock_adjustments.index');
    }
}
