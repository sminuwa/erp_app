<?php

namespace App\Http\Controllers;

use App\Models\GeneralAccount;
use App\Models\PurchaseExpense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Http\Requests\Purchases\Index;
use App\Http\Requests\Purchases\Show;
use App\Http\Requests\Purchases\Create;
use App\Http\Requests\Purchases\StoreRequest;
use App\Http\Requests\Purchases\Edit;
use App\Http\Requests\Purchases\Update;
use App\Http\Requests\Purchases\Destroy;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseProduct;
use App\Models\Setting;
use App\Models\Utility;
use App\Models\StoreProduct;
use App\Models\BranchProductPrice;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Description of PurchaseController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class PurchaseGRNController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        \Cart::clear();
        return view('pages.inventories.purchases.grn.index', [
            'records' => Purchase::select('purchases.*')->orderBy('created_at', 'DESC')
                ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                // ->where('branch_id', 'LIKE', User::userBranchAction())
                ->take(10)->get(),
            'suppliers' => Supplier::where('code', 'like', 'TS%')->orderBy('name')->get(),
        ]);
    }
    public function search(Request $request)
    {
        \Cart::clear();
        $search_value = $request->refno;
        $records = Purchase::select('purchases.*')->where('invoice', 'LIKE', "%$search_value%")
            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
            // ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('purchase_date', 'DESC')->take(10)->get();
        return view('pages.inventories.purchases.grn.index', [
            'records' => $records
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, Purchase $purchase)
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('pages.inventories.purchases.grn.show', [
            'record' => $purchase,
            'suppliers' => $suppliers,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Create  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Create $request)
    {
        //\Cart::clear();
        return view('pages.inventories.purchases.grn.create', [
            'model' => new Purchase,
            'products' => Product::all(),
            'suppliers' => Supplier::orderBy('name', 'asc')->get(),
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->get(),
            'categories' => Category::all(),
            'cart_products' => \Cart::getContent(),
            'type' => 'create',

        ]);
    } /**
      * Store a newly created resource in storage.
      *
      * @param  Store  $request
      * @return \Illuminate\Http\Response
      */
    public function store(StoreRequest $request)
    {

        $amount = 0;
        DB::beginTransaction();
        try {
            $purchase_datetime = date('Y-m-d H:i:s', strtotime("$request->purchase_date $request->purchase_time"));
            $purchase_id = DB::table('purchases')->insertGetId([
                'supplier_id' => $request->supplier_id,
                'invoice' => $request->invoice,
                'purchase_date' => $purchase_datetime,
                'purchase_mode' => 'Cash',
                'vehicle_reg_no' => $request->vehicle_reg_no,
                'source_store_id' => $request->source_store_id,
                'destination_store_id' => $request->source_store_id,
                'updated_by' => $request->updated_by,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            foreach (\Cart::getContent() as $product) {
                $cart_attributes = $product->attributes;
                //dd( $cart_attributes);

                $selling_price = $product->price;
                //This will be uncommented later if the logic has changed
                //$selling_price = optional(BranchProductPrice::find($product->id))->selling_price;
                DB::table('purchase_products')->insert([
                    'purchase_id' => $purchase_id,
                    'product_id' => $product->id,
                    'qty_supplied' => $product->quantity,
                    'unit_price' => $product->price,
                    'selling_price' => $selling_price == null ? 0 : $selling_price,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                // $record = StoreProduct::where(['store_id' => $request->source_store_id, 'product_id' => $product->id])->first();
                // if ($record != null) {
                //     DB::table('store_products')->where([
                //         'store_id' => $request->source_store_id,
                //         'product_id' => $product->id
                //     ])->increment('qty_available', $product->quantity);
                // } else {
                //     DB::table('store_products')->insert([
                //         'store_id' => $request->source_store_id,
                //         'product_id' => $product->id,
                //         'qty_available' => $product->quantity,
                //         'created_at' => Carbon::now(),
                //         'updated_at' => Carbon::now()
                //     ]);
                // }

                // BranchProductPrice::updateOrCreate(
                //     ['product_id' => $product->id, 'store_id' => $request->source_store_id],
                //     [
                //         'cost_price' => $product->price,
                //         'created_at' => Carbon::now(),
                //         'updated_at' => Carbon::now(),
                //         'updated_by' => Auth::id()
                //     ]
                // );

                // DB::table('transfer_products')->insert([
                //     'source_store_id' => $request->source_store_id,
                //     'purchase_id' => $purchase_id,
                //     'product_id' => $product->id,
                //     'destination_store_id' => $request->source_store_id,
                //     'qty_transfered' => $product->quantity,
                //     'qty_available' => $product->quantity,
                //     'transfered_by' => $request->updated_by,
                //     'status' => 'Completed',
                //     'nature' => 'Purchase',
                //     'stock_in_out' => 'in',
                //     'created_at' => Carbon::now(),
                //     'updated_at' => Carbon::now()
                // ]);
                // $amount += $product->price * $product->quantity;
                // DB::table('stock_cards')->insert([
                //     'store_id' => $request->source_store_id,
                //     'product_id' => $product->id,
                //     'cr' => $product->quantity,
                //     'dr' => 0,
                //     'refno' => $request->invoice,
                //     'type' => 'Purchase',
                //     'date' => $purchase_datetime,
                //     'user_id' => Auth::id(),
                //     'priority' => 3,
                //     'created_at' => Carbon::now(),
                //     'updated_at' => Carbon::now()
                // ]);
            }



            // if ($request->purchase_mode == "Credit") {
            //     $cr = $amount;
            //     $dr = 0;
            //     DB::table('suppliers')->where(['id' => $request->supplier_id])->where('branch_id', 'LIKE', User::userBranchAction())->increment('opening_balance', $cr);
            // }

            // DB::table('supplier_ledgers')->insert([
            //     'supplier_id' => $request->supplier_id,
            //     'purchase_id' => $purchase_id,
            //     'description' => 'Purchase of products',
            //     'payment_mode' => $request->purchase_mode,
            //     'Ref' => $request->invoice,
            //     'cr' => $cr,
            //     'dr' => $dr,
            //     'date' => Carbon::now(),
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ]);
            $action = "Made a purchase with invoice $request->invoice from supplier: " . Supplier::find($request->supplier_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Purchase saved successfully');
            \Cart::clear();
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_message', 'Something is wrong while saving Purchase');
            throw $e;
        }
        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Edit  $request
      * @param  Purchase  $purchase
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, Purchase $purchase)
    {

        if (\Cart::getContent()->isEmpty())
            $this->loadToCart($purchase);
        $cart_products = \Cart::getContent();
        return view('pages.inventories.purchases.grn.edit', [
            'model' => $purchase,
            'products' => Product::all(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'stores' => Store::where('branch_id', 'LIKE', User::userBranchAction())->get(),
            'categories' => Category::all(),
            'cart_products' => $cart_products,
            'type' => 'edit',

        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Update  $request
      * @param  Purchase  $purchase
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, Purchase $purchase)
    {
        //return "Cool";
        $amount = 0;
        $purchase_id = $purchase->id;
        DB::beginTransaction();
        try {
            DB::table('purchases')->where('id', $purchase->id)->update([
                'supplier_id' => $request->supplier_id,
                'invoice' => $request->invoice,
                'purchase_date' => $request->purchase_date,
                'purchase_mode' => $request->purchase_mode,
                'vehicle_reg_no' => $request->vehicle_reg_no,
                'source_store_id' => $request->source_store_id,
                'destination_store_id' => $request->source_store_id,
                'updated_by' => $request->updated_by,
            ]);
            // $ledger = DB::table('supplier_ledgers')->where(['purchase_id' => $purchase->id])->select('cr', 'dr', 'payment_mode')->first();
            // if (optional($ledger)->payment_mode == "Credit")
            //     DB::table('suppliers')->where(['id' => $request->supplier_id])->decrement('opening_balance', $ledger->dr);
            // DB::table('transfer_products')->where(['purchase_id' => $purchase->id])->delete();
            // foreach ($purchase->purchasedProducts()->get() as $data) {
            //     DB::table('store_products')->where(['store_id' => $request->source_store_id, 'product_id' => $data->product_id])->decrement('qty_available', $data->qty_supplied);
            //     DB::table('stock_cards')->where(['store_id' => $request->source_store_id, 'product_id' => $data->product_id, 'refno' => $request->old_invoice])->delete();
            // }
            DB::table('purchase_products')->where(['purchase_id' => $purchase->id])->delete();
            DB::table('supplier_ledgers')->where(['purchase_id' => $purchase->id])->delete();
            //dd(\Cart::getContent());
            if (\Cart::getContent()->count() > 0) {
                foreach (\Cart::getContent() as $product) {
                    $cart_attributes = $product->attributes;
                    //dd( $cart_attributes);
                    $selling_price = $product->price;
                    //This will be uncommented later if the logic has changed
                    //$selling_price = optional(BranchProductPrice::find($product->id))->selling_price;
                    DB::table('purchase_products')->insert([
                        'purchase_id' => $purchase_id,
                        'product_id' => $product->id,
                        'qty_supplied' => $product->quantity,
                        'unit_price' => $product->price,
                        'selling_price' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    // DB::table('store_products')->where([
                    //     'store_id' => $request->source_store_id,
                    //     'product_id' => $product->id
                    // ])->increment('qty_available', $product->quantity);

                    /*BranchProductPrice::updateOrCreate(
                    ['store_id' => $request->source_store_id,
                    'product_id' => $product->id, 'selling_price' => $selling_price], ['cost_price' => $product->price, 'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(), 'updated_by' => Auth::id()]
                    );*/
                    // DB::table('transfer_products')->insert([
                    //     'source_store_id' => $request->source_store_id,
                    //     'purchase_id' => $purchase_id,
                    //     'product_id' => $product->id,
                    //     'destination_store_id' => $request->source_store_id,
                    //     'qty_transfered' => $product->quantity,
                    //     'qty_available' => $product->quantity,
                    //     'transfered_by' => $request->updated_by,
                    //     'status' => 'Completed',
                    //     'nature' => 'Purchase',
                    //     'stock_in_out' => 'in',
                    //     'created_at' => Carbon::now(),
                    //     'updated_at' => Carbon::now()
                    // ]);
                    // $amount += $product->price * $product->quantity;
                    // DB::table('stock_cards')->insert([
                    //     'store_id' => $request->source_store_id,
                    //     'product_id' => $product->id,
                    //     'cr' => $product->quantity,
                    //     'dr' => 0,
                    //     'refno' => $request->invoice,
                    //     'type' => 'Purchase',
                    //     'date' => $request->purchase_date,
                    //     'user_id' => Auth::id(),
                    //     'created_at' => Carbon::now(),
                    //     'updated_at' => Carbon::now()
                    // ]);
                }

                // if ($purchase->purchase_mode == "Credit" && $request->purchase_mode == "Cash") {
                //     DB::table('suppliers')->where(['id' => $request->supplier_id])->decrement('opening_balance', $purchase->totalProductCost()->total);
                // }
                // if ($request->purchase_mode == "Cash") {
                //     $cr = $amount;
                //     $dr = $amount;
                // }
                // if ($request->purchase_mode == "Credit") {
                //     $cr = $amount;
                //     $dr = 0;
                //     DB::table('suppliers')->where(['id' => $request->supplier_id])->increment('opening_balance', $dr);
                // }

                // DB::table('supplier_ledgers')->insert([
                //     'supplier_id' => $request->supplier_id,
                //     'purchase_id' => $purchase_id,
                //     'description' => 'Purchase of products',
                //     'Ref' => $request->invoice,
                //     'cr' => $cr,
                //     'dr' => $dr,
                //     'payment_mode' => $request->purchase_mode,
                //     'date' => Carbon::now(),
                //     'created_at' => Carbon::now(),
                //     'updated_at' => Carbon::now(),
                // ]);
                $action = "Modified a purchase with invoice $request->invoice from supplier: " . Supplier::find($request->supplier_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                DB::commit();
                session()->flash('app_message', 'Purchase updated successfully');
            }

            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_message', 'Something is wrong while updating Purchase');
            throw $e;
        }
        \Cart::clear();
        return redirect()->back();
    } /**
      * Delete a  resource from  storage.
      *
      * @param  Destroy  $request
      * @param  Purchase  $purchase
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Destroy $request, Purchase $purchase)
    {

        $amount = 0;
        $purchase_id = $purchase->id;
        $invoice = $purchase->invoice;
        DB::beginTransaction();
        try {
            DB::table('purchase_products')->where(['purchase_id' => $purchase->id])->delete();
            DB::table('purchases')->where('id', $purchase->id)->delete();

            $action = "Deleted a purchase with invoice $invoice from supplier: " . Supplier::find($purchase->supplier_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Purchase cancelled successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Something is wrong while deleting Purchase');
            throw $e;
        }
        return redirect()->back();
    }
    public function addToCart(Request $request)
    {
        //return $request;
        $validated = $request->validate([
            'product_id' => 'required',
            'unit_price' => 'required',
            'qty_supplied' => 'required',
        ]);
        $product = Product::find($request->product_id);
        $add = \Cart::add([
            'id' => $request->product_id,
            'name' => $product->name,
            'price' => $request->unit_price,
            'quantity' => $request->qty_supplied,
            'attributes' => array('code' => $product->code),
        ]);
        //dd(\Cart::getContent());
        if ($add) {
            session()->flash('app_message', 'Product is Added to Cart Successfully !');
            if ($request->has('type') && $request->type == "create")
                return redirect()->back()->withInput();
            return redirect()->route('purchases.edit', $request->purchase_id)->withInput();

        } else {

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
    public function loadToCart(Purchase $purchase)
    {
        //\Cart::clear();
        foreach ($purchase->purchasedProducts()->get() as $data) {
            $qty = $data->qty_supplied == 0 ? 1 : $data->qty_supplied;
            $product = Product::find($data->product_id);
            \Cart::add([
                'id' => $data->product_id,
                'name' => $product->name,
                'price' => $data->unit_price,
                'quantity' => $qty,
                'attributes' => array('code' => $product->code),
            ]);
        }
    }
    public function updateCart(Request $request)
    {

        \Cart::update(
            $request->id,
            [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity,
                ],
                'price' => $request->cost_price
            ]
        );

        session()->flash('success', 'Item Cart is Updated Successfully !');
        if ($request->ajax()) {
            return \Cart::getTotal();
        }
        return redirect()->back();
    }
    public function printInvoice(Purchase $purchase)
    {
        $purchase = Purchase::with('supplier')->where('id', $purchase->id)->first();

        $purchase_details = PurchaseProduct::with('Product')->where('purchase_id', $purchase->id)->get();

        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        $utility = new Utility();
        return view('pages.inventories.purchases.grn.print', compact('purchase_details', 'purchase', 'company', 'utility'));
    }
    public function generateWaybill(Request $request, Purchase $purchase)
    {
        $purchase->waybill_no = $request->waybill_no;
        $purchase->driver_name = $request->driver_name;
        $purchase->transporter_phone = $request->transporter_phone;
        $purchase->warehouse = $request->warehouse;
        $purchase->vehicle_reg_no = $request->vehicle_reg_no;
        $purchase->transporter = $request->transporter;
        $purchase->save();
        return back();
    }
    public function printWaybill(Purchase $purchase)
    {
        $purchase = Purchase::with('supplier')->where('id', $purchase->id)->first();

        $purchase_details = PurchaseProduct::with('Product')->where('purchase_id', $purchase->id)->get();

        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        $utility = new Utility();
        return view('pages.inventories.purchases.grn.print_waybill', compact('purchase_details', 'purchase', 'company', 'utility'));
    }
    public function expense(Request $request)
    {
        DB::table('purchase_expenses')->updateOrInsert(['purchase_id' => $request->purchase_id, 'supplier_id' => $request->supplier_id, 'description' => $request->description], ['amount' => $request->amount, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $action = "Added purchase expense $request->name";
        AuditLog::auditLog(Auth::id(), $action);
        $purchase_expenses = PurchaseExpense::where('purchase_id', $request->purchase_id)->get();
        return view('pages.inventories.purchases.grn.load_expenses', compact('purchase_expenses'));
    }
    public function deleteExpense(Request $request, PurchaseExpense $expense)
    {
        $expense_item = $expense->name;
        if ($expense->delete()) {
            $action = "Deleted purchase expense $expense_item";
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Successfully deleted');
            return back();
        } else {
            session()->flash('app_error', 'Cannot be deleted');
            return back();
        }
    }
    public function approve(Request $request, Purchase $purchase)
    {
        return $purchase;
    }
}