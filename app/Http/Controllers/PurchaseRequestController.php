<?php

namespace App\Http\Controllers;

use App\Models\PurchaseExpense;
use App\Models\PurchaseProductRequest;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
use App\Models\AuditLog;
use App\Models\User;

/**
 * Description of PurchaseController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class PurchaseRequestController extends Controller
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
        return view('pages.inventories.purchases.request.index', [
            'records' => PurchaseRequest::select('purchase_requests.*')->orderBy('purchase_date', 'DESC')
                ->join('suppliers', 'suppliers.id', 'purchase_requests.supplier_id')
                // ->where('branch_id', 'LIKE', User::userBranchAction())
                ->take(10)->get()
        ]);
    }
    public function search(Request $request)
    {
        \Cart::clear();
        $search_value = $request->refno;
        $records = PurchaseRequest::select('purchase_requests.*')->where('invoice', 'LIKE', "%$search_value%")
            ->join('suppliers', 'suppliers.id', 'purchase_requests.supplier_id')
            ->where('purchase_requests.branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('purchase_date', 'DESC')->take(10)->get();
        return view('pages.inventories.purchases.request.index', [
            'records' => $records
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  PurchaseRequest  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, PurchaseRequest $purchase)
    {
        return view('pages.inventories.purchases.request.show', [
            'record' => $purchase,
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
        //dd(\Cart::getContent());
        return view('pages.inventories.purchases.request.create', [
            'model' => new PurchaseRequest,
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
    public function store(Request $request)
    {

        $amount = 0;
        DB::beginTransaction();
        try {
            $purchase_id = DB::table('purchase_requests')->insertGetId([
                'supplier_id' => $request->supplier_id,
                'invoice' => $request->invoice,
                'purchase_date' => Carbon::now(),
                'branch_id' => User::userBranchAction(),
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
                DB::table('purchase_product_requests')->insert([
                    'purchase_id' => $purchase_id,
                    'product_id' => $product->id,
                    'quantity' => $product->quantity,
                    'unit_price' => $product->price,
                    'user_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            }

            $action = "Made a purchase with invoice $request->invoice from supplier: " . Supplier::find($request->supplier_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Purchase saved successfully');
            \Cart::clear();
            return redirect()->route('purchases.request.index');
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
      * @param  PurchaseRequest  $purchase
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, PurchaseRequest $purchase)
    {

        if (\Cart::getContent()->isEmpty())
            $this->loadToCart($purchase);
        $cart_products = \Cart::getContent();
        return view('pages.inventories.purchases.request.edit', [
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
      * @param  PurchaseRequest  $purchase
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, PurchaseRequest $purchase)
    {
        //return "Cool";
        $amount = 0;
        $purchase_id = $purchase->id;
        DB::beginTransaction();
        try {
            DB::table('purchase_requests')->where('purchase_requests.id', $purchase_id)->update([
                'supplier_id' => $request->supplier_id,
                'invoice' => $request->invoice,
                'purchase_date' => Carbon::now(),
                'branch_id' => User::userBranchAction(),
                'updated_by' => $request->updated_by,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('purchase_product_requests')->where('purchase_id', $purchase->id)->delete();
            foreach (\Cart::getContent() as $product) {
                $cart_attributes = $product->attributes;
                //dd( $cart_attributes);

                $selling_price = $product->price;
                //This will be uncommented later if the logic has changed
                //$selling_price = optional(BranchProductPrice::find($product->id))->selling_price;
                
                DB::table('purchase_product_requests')->updateOrInsert(
                    [
                        'purchase_id' => $purchase_id,
                        'product_id' => $product->id
                    ],
                    [
                        'purchase_id' => $purchase_id,
                        'product_id' => $product->id,
                        'quantity' => $product->quantity,
                        'unit_price' => $product->price,
                        'user_id' => auth()->id(),
                        'status' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );

                $action = "Modified a purchase with invoice $request->invoice from supplier: " . Supplier::find($request->supplier_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                DB::commit();
                session()->flash('app_message', 'Purchase updated successfully');
            }

            return redirect()->route('purchases.request.index');
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
      * @param  PurchaseRequest  $purchase
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Destroy $request, PurchaseRequest $purchase)
    {

        $amount = 0;
        $purchase_id = $purchase->id;
        $invoice = $purchase->invoice;
        DB::beginTransaction();
        try {
            DB::table('purchase_product_requests')->where('purchase_id', $purchase->id)->delete();
            DB::table('purchase_requests')->where(['id' => $purchase->id])->delete();
           

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
            'qty_supplied' => 'required',
        ]);
        $product = Product::find($request->product_id);
        $add = \Cart::add([
            'id' => $request->product_id,
            'name' => $product->name,
            'price' => $request->unit_price ?? 0,
            'quantity' => $request->qty_supplied,
            'attributes' => array('code' => $product->code),
        ]);
        //dd(\Cart::getContent());
        if ($add) {
            session()->flash('app_message', 'Product is Added to Cart Successfully !');
            if ($request->has('type') && $request->type == "create")
                return redirect()->back()->withInput();
            return redirect()->route('purchases.request.edit', $request->purchase_id)->withInput();

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
    public function loadToCart(PurchaseRequest $purchase)
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
    public function printInvoice(PurchaseRequest $purchase)
    {
        $purchase = PurchaseRequest::with('supplier')->where('id', $purchase->id)->first();

        $purchase_details = PurchaseProductRequest::with('Product')->where('purchase_id', $purchase->id)->get();

        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        $utility = new Utility();
        return view('pages.inventories.purchases.request.print', compact('purchase_details', 'purchase', 'company', 'utility'));
    }
    public function generateWaybill(Request $request, PurchaseRequest $purchase)
    {
        $purchase->waybill_no = $request->waybill_no;
        $purchase->driver_name = $request->driver_name;
        $purchase->location_id = $request->location_id;
        $purchase->warehouse = $request->warehouse;
        $purchase->vehicle_reg_no = $request->vehicle_reg_no;
        $purchase->transporter = $request->transporter;
        $purchase->save();
        return back();
    }
    public function printWaybill(PurchaseRequest $purchase)
    {
        $purchase = PurchaseRequest::with('supplier')->where('id', $purchase->id)->first();

        $purchase_details = PurchaseProduct::with('Product')->where('purchase_id', $purchase->id)->get();

        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        $utility = new Utility();
        return view('pages.inventories.purchases.request.print_waybill', compact('purchase_details', 'purchase', 'company', 'utility'));
    }
    public function expense(Request $request)
    {
        DB::table('purchase_expenses')->updateOrInsert(['purchase_id' => $request->purchase_id, 'name' => $request->name], ['amount' => $request->amount, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $action = "Added purchase expense $request->name";
        AuditLog::auditLog(Auth::id(), $action);
        $purchase_expenses = PurchaseExpense::where('purchase_id', $request->purchase_id)->orderBy('name')->get();
        return view('pages.inventories.purchases.request.load_expenses', compact('purchase_expenses'));
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
    public function approve(Request $request, PurchaseRequest $purchase)
    {
        return $purchase;
    }
}