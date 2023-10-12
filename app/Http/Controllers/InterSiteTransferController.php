<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\IntersiteTransfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TransferProduct;
use App\Http\Requests\TransferProducts\Index;
use App\Http\Requests\TransferProducts\Show;
use App\Http\Requests\TransferProducts\Create;
use App\Http\Requests\TransferProducts\StoreRequest;
use App\Http\Requests\TransferProducts\Edit;
use App\Http\Requests\TransferProducts\Update;
use App\Http\Requests\TransferProducts\Destroy;
use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use App\Models\StoreProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Description of InterStoreTransferController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class InterSiteTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        \Cart::session('_token')->clear();
        $records = IntersiteTransfer::where('source_branch_id', 'LIKE', User::userBranchAction())
            ->take(10)->get();
        return view('pages.inventories.transfers.inter_site.index', ['records' => $records]);
    }
    public function search(Index $request)
    {
        $refno = $request->refno;
        $records = TransferProduct::where(['nature' => 'Transfer', 'stock_in_out' => 'out', 'transfer_products.status' => 1])
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where('transfer_products.type', 'intersite')
            ->where('refno', 'LIKE', "%$refno%")
            ->join('stores', 'stores.id', 'transfer_products.source_store_id')
            ->groupBy(['refno', 'source_store_id', 'product_id'])->orderBy('transfer_products.created_at', 'DESC')->get();
        return view('pages.inventories.transfers.inter_site.index', ['records' => $records]);
    }
    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  TransferProduct  $transferproduct
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, IntersiteTransfer $intersiteTransfer)
    {
        return view('pages.inventories.transfers.inter_site.show', [
            'record' => $intersiteTransfer,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Create  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Create $request)
    {
        //\Cart::session('_token')->clear();

        $products = Product::select('products.id', 'products.name', 'products.code')
            ->join('store_products', 'store_products.product_id', 'products.id')
            ->where('qty_available', '>', 0)->get();
        $categories = Category::all(['id', 'name']);
        $stores = Store::where('branch_id', 'LIKE', User::userBranchAction())->get();
        $cartItems = \Cart::session('_token')->getContent(); //\Cart::getContent();
        $branches = Branch::where('id', '<>', User::userBranchAction())->orderBy('name')->get();

        return view('pages.inventories.transfers.inter_site.create', [
            'model' => new TransferProduct,
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'transfer_products' => $cartItems,
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
        $model = new TransferProduct;
        DB::beginTransaction();
        try {
            if (\Cart::session('_token')->getContent()->count() > 0) {
                //$transfer_id = $this->getNextTransferID();
                $refno = $this->generateRefNo();
                $transfer_branch_id = $request->transfer_branch_id;
                $transfer_id = DB::table('intersite_transfers')->insertGetId([
                    'reference_no' => $refno,
                    'source_branch_id' => User::userBranchAction(),
                    'destination_branch_id' => $transfer_branch_id,
                    'requested_by' => Auth::id(),
                    'date_requested' => date('Y-m-d'),
                    'vehicle_no' => $request->vehicle_no,
                ]);
                foreach (\Cart::session('_token')->getContent() as $product) {
                    $attribute = $product->attributes;
                    $source_store_id = $attribute['source_store_id'];
                    
                    $product_id = $attribute['product_id'];

                    DB::table('intersite_transfer_products')->insert([
                        'intersite_transfer_id' => $transfer_id,
                        'source_store_id' => $source_store_id,
                        'product_id' => $product_id,
                        'quantity_requested' => $product->quantity,
                        'quantity_approved' => $product->quantity,
                        'cost_price' => $product->price,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    $action = "Made transfer request of product from " . Store::find($source_store_id)->name . " to  branch" . Store::find($transfer_branch_id)->name;
                    AuditLog::auditLog(Auth::id(), $action);
                    session()->flash('app_message', 'Stock transfered successfully');
                    DB::commit();
                }
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('app_message', 'Something is wrong while transfering stock');
            throw $ex;
        }
        \Cart::session('_token')->clear();
        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Edit  $request
      * @param  TransferProduct  $transferproduct
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, IntersiteTransfer $intersiteTransfer)
    {
        //\Cart::session('_token')->clear();
        $products = Product::all(['id', 'name']);
        $categories = Category::all(['id', 'name']);
        $stores = Store::where('branch_id', 'LIKE', User::userBranchAction())->get();
        $this->loadToCart($intersiteTransfer);
        $cartItems = \Cart::session('_token')->getContent();
        $branches = Branch::where('id', '<>', User::userBranchAction())->orderBy('name')->get();
        return view('pages.inventories.transfers.inter_site.edit', [
            'model' => $intersiteTransfer,
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'transfer_products' => $cartItems,
            'branches' => $branches,
        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Update  $request
      * @param  TransferProduct  $transferproduct
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, TransferProduct $transferproduct)
    {
        $source_qty = 0;
        $destination_qty = 0;
        if ($transferproduct->status == 'Cancelled' && $request->status == 'Completed') {
            $source_qty = 0 - $request->qty_transfered;
            $destination_qty = -1 * $source_qty;
        } else if ($transferproduct->status == 'Completed' && $request->status == 'Cancelled') {
            $source_qty = $request->qty_transfered;
            $destination_qty = 0 - $source_qty;
        } else {
            $source_qty = $transferproduct->qty_transfered - $request->qty_transfered;
            if ($source_qty > 0)
                $destination_qty = 0 - $source_qty;
            if ($source_qty < 0)
                $destination_qty = -1 * $source_qty;
        }

        DB::beginTransaction();
        try {
            DB::table('stock_cards')->where('refno', $transferproduct->refno)->update(['status' => 0]);
            $transferproduct->fill($request->all());
            DB::table('stock_cards')->updateOrInsert(['refno' => $transferproduct->refno], [
                'store_id' => $transferproduct->source_store_id,
                'product_id' => $transferproduct->product_id,
                'cr' => 0,
                'dr' => $source_qty,
                'refno' => $transferproduct->refno,
                'type' => 'Transfer',
                'date' => $request->transfer_date,
                'user_id' => Auth::id(),
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            DB::table('stock_cards')->updateOrInsert(['refno' => $transferproduct->refno], [
                'store_id' => $transferproduct->destination_store_id,
                'product_id' => $transferproduct->product_id,
                'cr' => $destination_qty,
                'dr' => 0,
                'refno' => $transferproduct->refno,
                'type' => 'Transfer',
                'date' => $request->transfer_date,
                'user_id' => Auth::id(),
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            StoreProduct::where(['store_id' => $request->source_store_id, 'product_id' => $request->product_id])->increment('qty_available', $source_qty);
            StoreProduct::where(['store_id' => $request->destination_store_id, 'product_id' => $request->product_id])->increment('qty_available', $destination_qty);
            if ($transferproduct->save()) {
                $action = "Updated stock transfer made from " . Store::find($request->source_store_id)->name . " to " . Store::find($request->destination_store_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Stock transfer successfully updated');
                return redirect()->route('intersite.index');
            } else {
                session()->flash('app_error', 'Something is wrong while updating stock transfer');
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        \Cart::session('_token')->clear();
        return redirect()->back();
    } /**
      * Delete a  resource from  storage.
      *
      * @param  Destroy  $request
      * @param  TransferProduct  $transferproduct
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Destroy $request, TransferProduct $transferproduct)
    {

        DB::beginTransaction();
        try {
            $all_transfers = TransferProduct::where('transfer_id', $transferproduct->transfer_id)->get();
            foreach ($all_transfers as $transfer) {
                DB::table('store_products')->where(['store_id' => $transfer->source_store_id, 'product_id' => $transfer->product_id])->increment('qty_available', $transfer->qty_transfered);
                DB::table('store_products')->where(['store_id' => $transfer->destination_store_id, 'product_id' => $transfer->product_id])->decrement('qty_available', $transfer->qty_transfered);
                DB::table('transfer_products')->where('id', $transfer->id)->delete();
            }
            session()->flash('app_message', 'Stock transfer cancelled successfully');
            $action = "Deleted stock transfer made from " . Store::find($transfer->source_store_id)->name . " to " . Store::find($transfer->destination_store_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('app_error', 'Something is wrong while cancelling stock transfer');
            throw $ex;
        }
        \Cart::session('_token')->clear();
        return redirect()->back();
    }
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'source_store_id' => 'required',
            'qty_transfered' => 'required',
        ]);
        $product = Product::find($request->product_id);
        $add = \Cart::session('_token')->add([
            'id' => $this->generateRandomString(),
            'name' => $product->name,
            'price' => $request->unit_price,
            //Thos is not applicable here
            'quantity' => $request->qty_transfered,
            'attributes' => array(
                'source_store_id' => $request->source_store_id,
                'product_id' => $request->product_id,
                'code' => $product->code,
            ),
        ]);
        //dd(\Cart::getContent());

        if ($add) {
            session()->flash('app_message', 'Product is Added to Cart Successfully !');
            return redirect()->back()->withInput();

        } else {

            session()->flash('app_error', 'Product not added to cart');
            return redirect()->back()->withInput();
        }
    }
    public function removeCart(Request $request, $id)
    {
        \Cart::session('_token')->remove($request->id);
        session()->flash('app_message', 'Item Cart Remove Successfully !');

        return redirect()->back();
    }

    public function clearAllCart()
    {
        \Cart::session('_token')->clear();

        session()->flash('app_message', 'All Item Cart Clear Successfully !');

        return redirect()->back();
    }
    public function loadToCart(IntersiteTransfer $transfer)
    {
        \Cart::session('_token')->clear();
        foreach ($transfer->requestProducts()->get() as $data) {
            if ($data != null) {
                $product = Product::find($data->product_id);
                \Cart::session('_token')->add([
                    'id' => $this->generateRandomString(),
                    //$this->generateRandomString(),
                    'name' => $product->name,
                    'price' => $data->price,
                    'quantity' => $data->qty_transfered,
                    'attributes' => array(
                        'source_store_id' => $data->source_store_id,
                        'product_id' => $data->product_id ?? 1,
                        'code' => $product->code,
                    ),
                ]);
            }

        }
    }
    public function generateRandomString($length = 5)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function getNextTransferID()
    {
        $no = DB::table('transfer_products')->select(DB::raw('MAX(transfer_id) as max'))->where('nature', 'transfer')->first();
        return $no->max + 1;
    }
    public function generateRefNo()
    {
        $no = $this->getNextTransferID();
        return "TR" . date('y') . '' . date('m') . str_pad(($no), 4, "0", STR_PAD_LEFT);
    }
    public function printStockTransfer($transfer_id)
    {
        return view('pages.inventories.transfers.inter_site.print')->with(['transfers' => TransferProduct::where(['transfer_id' => $transfer_id, 'stock_in_out' => 'out'])->get()]);
    }
}