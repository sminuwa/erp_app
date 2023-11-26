<?php

namespace App\Http\Controllers;

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
        $user = auth()->user();
        $branch = $user->branch;
        $records = StockAdjustment::where(['branch_id'=>$branch->id])->orderBy('reference', 'desc')->get();
        return view('pages.inventories.stock_adjustments.index',compact('records'));
    }

    public function show(Show $request, StockAdjustment $stockadjustment)
    {
        return view('pages.inventories.stock_adjustments.show', [
            'record' => $stockadjustment,
        ]);

    }

    public function create(Create $request)
    {

        $products = Product::select('products.id', 'products.name','code')->join('store_products', 'store_products.product_id', 'products.id')->get();
        $categories = Category::all(['id', 'name']);
        $stores = Store::where('branch_id', 'LIKE', User::userBranchAction())->get();
        $cartItems = \Cart::getContent(); //\Cart::getContent();

        return view('pages.inventories.stock_adjustments.create', [
            'model' => new StockAdjustment,
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'adjusment_products' => $cartItems,
            'refno' => $this->generateRefNo(),
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
            if($stock->save()){
                if (count($items) > 0) {
                    StockAdjustmentDetail::where('stock_adjustment_id', $stock->stock_adjustment_id)->delete();
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
                $action = "Stock Adjustment created $stock->reference";
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
        return redirect()->back();
    }

    public function edit(Edit $request, StockAdjustment $stockadjustment)
    {
        //return $stockadjustment;
        $products = Product::select('products.id', 'products.name')->join('store_products', 'store_products.product_id', 'products.id')->get();
        $categories = Category::all(['id', 'name']);
        $stores = Store::where('id', 'LIKE', User::userBranchAction())->get();
        //\Cart::clear();
        if (\Cart::isEmpty())
            $this->loadToCart($stockadjustment);
        $cartItems = \Cart::getContent(); //\Cart::getContent();

        return view('pages.inventories.stock_adjustments.edit', [
            'model' => $stockadjustment,
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'adjusment_products' => $cartItems,
            'refno' => $this->generateRefNo(),
        ]);
    }

    public function update(Update $request, StockAdjustment $stockadjustment)
    {

        DB::beginTransaction();

        try {

            if (\Cart::getContent()->count() > 0) {

                $refno = $request->refno;
                foreach (\Cart::getContent() as $product) {
                    $attribute = $product->attributes;
                    $store_id = $attribute['store_id'];
                    $product_id = $attribute['product_id'];

                    $sign = $attribute['sign'];
                    $adjusted_qty = $product->quantity;
                    if ($sign == "-")
                        $adjusted_qty = 0 - $adjusted_qty;
                    //Undo previous
                    $prev_data = StockAdjustment::where(['store_id' => $store_id, 'product_id' => $product_id, 'refno' => $refno])->first();
                    if ($prev_data->adjusted_qty < 0) // if the qty is taken out
                        DB::table('store_products')->where(['store_id' => $store_id, 'product_id' => $product_id])->increment('qty_available', abs($prev_data->adjusted_qty));
                    else // if the quantity is added
                        DB::table('store_products')->where(['store_id' => $store_id, 'product_id' => $product_id])->decrement('qty_available', abs($prev_data->adjusted_qty));

                    //get the current available quantity after restored back the quantity
                    $qty_data = StoreProduct::where(['store_id' => $store_id, 'product_id' => $product_id])->first();
                    $available_qty = $qty_data->qty_available;
                    //Insert new ones
                    DB::table('store_products')->where(['store_id' => $store_id, 'product_id' => $product_id])->increment('qty_available', $adjusted_qty);
                    //StoreProduct::where(['store_id' => $store_id, 'product_id' => $product_id])->increment('qty_available', $adjusted_qty);

                    //Remove the prevoius one
                    DB::table('stock_adjustments')->where(['store_id' => $store_id, 'product_id' => $product_id, 'refno' => $stockadjustment->refno])->delete();

                    DB::table('stock_adjustments')->insert([
                        'store_id' => $store_id,
                        'product_id' => $product_id,
                        'adjusted_qty' => $adjusted_qty,
                        'available_qty' => $available_qty,
                        'adjusted_by' => Auth::id(),
                        'refno' => $refno,
                        'date' => $request->date,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    session()->flash('app_message', 'Stock adjusted successfully');
                    $action = "updated stock adjustment $refno : " . $adjusted_qty;
                    AuditLog::auditLog(Auth::id(), $action);
                    DB::commit();
                    \Cart::clear();
                    return redirect()->route('stock_adjustments.index');
                }
            }
        }
        catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('app_message', 'Something is wrong while adjusting stock');
            \Cart::clear();
            return redirect()->route('stock_adjustments.index');
            throw $ex;
        }
        \Cart::clear();
        return redirect()->route('stock_adjustments.index');
    }

    public function destroy(Destroy $request, StockAdjustment $stockadjustment)
    {
        DB::beginTransaction();
        try {

            foreach ($stockadjustment->adjustedProducts()->get() as $product) {

                $store_id = $product->store_id;
                $product_id = $product->product_id;
                $adjusted_qty = $product->adjusted_qty;
                //Undo previous
                if ($adjusted_qty < 0)
                    DB::table('store_products')->where(['store_id' => $store_id, 'product_id' => $product_id])->increment('qty_available', abs($adjusted_qty));
                else
                    DB::table('store_products')->where(['store_id' => $store_id, 'product_id' => $product_id])->decrement('qty_available', abs($adjusted_qty));
            }
            DB::table('stock_adjustments')->where('refno', $stockadjustment->refno)->delete();
            $action = "Deleted stock adjustment $stockadjustment->refno : " . $adjusted_qty;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Stock adjustment deleted successfully');
            DB::commit();
        }
        catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('app_message', 'Something is wrong while deleting stock adjustment');
            throw $ex;
        }
        \Cart::clear();
        return redirect()->route('stock_adjustments.index');
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'store_id' => 'required',
            'available_qty' => 'required',
            'adjusted_qty' => 'required'
        ]);
        $sign = "+";
        if ($request->operation == -1)
            $sign = "-";
        $add = \Cart::add([
            'id' => generateRandomString(),
            'name' => Product::find($request->product_id)->name,
            'price' => 0, //Thos is not applicable here
            'quantity' => abs($request->adjusted_qty),
            'attributes' => array('store_id' => $request->store_id,
                'available_qty' => $request->available_qty,
                'product_id' => $request->product_id,
                'sign' => $sign
            ),
        ]);
        //dd(\Cart::getContent());

        if ($add) {
            session()->flash('app_message', 'Product is Added to Cart Successfully !');
            return redirect()->back()->withInput();

        }
        else {

            session()->flash('app_error', 'Product not added to cart');
            return redirect()->back()->withInput();
        }
    }
    public function removeCart(Request $request, $id)
    {
        \Cart::remove($request->id);
        session()->flash('app_message', 'Item Cart Remove Successfully !');

        return redirect()->back();
    }

    public function clearAllCart()
    {
        \Cart::clear();

        session()->flash('app_message', 'All Item Cart Clear Successfully !');

        return redirect()->back();
    }
    public function loadToCart($items)
    {
        \Cart::clear();
        foreach ($items as $data) {
            $product = Product::find($data->product_id);
            \Cart::add([
                'id' => generateRandomString(),
                'name' => $product->name,
                'price' => 0,
                'quantity' => abs($data->quantity),
                'attributes' => array(
                    'available_qty' => $data->available_qty,
                    'store_id' => $data->store_id,
                    'product_id' => $data->product_id,
                    'code' => $product->code,
                    'operation' => $items->stockAdjustment->operation ?? 'in',
                ),
            ]);
            $sign = "+";
        }
    }
    public function updateCart(Request $request)
    {

        $sign = "+";
        if ($request->quantity < 0)
            $sign = "-";
        \Cart::update(
            $request->id,
        [
            'quantity' => [
                'relative' => false,
                'value' => abs($request->quantity)
            ],
            'attributes' => array('sign' => $sign,
                'store_id' => $request->store_id,
                'product_id' => $request->product_id,
                'available_qty' => $request->available_qty
            )
        ]
        );
        //dd(\Cart::getContent());
        session()->flash('success', 'Item Cart is Updated Successfully !');

        return redirect()->back();
    }

    public function getNextTransferID()
    {
        $no = DB::table('stock_adjustments')->select(DB::raw('MAX(id) as max'))->first();
        return $no->max + 1;
    }
    public function generateRefNo()
    {
        $no = $this->getNextTransferID();
        return "SA" . date('y') . '' . date('m') . str_pad(($no), 4, "0", STR_PAD_LEFT);
    }
    public function printStockAdjusment($refno)
    {
        return view('pages.inventories.transfer_products.print')->with(['transfers' => StockAdjustment::where(['refno' => $refno])->get()]);
    }
}
