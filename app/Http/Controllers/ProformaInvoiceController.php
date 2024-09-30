<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OrderDetail;
use App\Models\Proformer;
use App\Models\ProformerDetail;
use App\Models\Category;
use App\Models\Store;
use App\Models\Setting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StoreProduct;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\Utility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ProformaInvoiceController extends Controller
{
    //

    public function delete(Proformer $proforma)
    {
        if ($proforma->delete()) {
            $action = "Deleted proforma invoice with reference " . $proforma->reference;
            AuditLog::auditLog(auth()->id(), $action);
            session()->flash('app_message', 'Deleted successfully');
        }
        return redirect()->back();
    }
    public function close(Request $request, Proformer $proforma)
    { 
        $proforma->status = 1;// 1 means closed
        if ($proforma->save()) {
            $action = "Closed proforma $proforma->invoice_no";
            session()->flash('app_success', 'Proforma closed successfully');
            AuditLog::auditLog(Auth::id(), $action);
            return redirect(route('proformer.list'));

        }
        return back()->with('error', 'Something went wrong.');
    }
    public function proformer_list()
    {
        \Cart::clear();
        $user = Auth::user();
        $orders = Proformer::latest('order_date')->with('customer')->where('branch_id', 'LIKE', User::userBranchAction());
        if ($user->hasRole('Sales-Manager'))
            $orders = $orders->where('sold_by', Auth::id());
        $orders = $orders->whereBetween('order_date', [date('Y-m-d', strtotime(Carbon::now()->subDays(7))), date('Y-m-d')])->orderBy('status', 'asc')->get();
        return view('pages.order.proformers', compact('orders'));
    }
    public function proformer_show($id)
    {
        $order = Proformer::with('customer')->where('branch_id', 'LIKE', User::userBranchAction())->where('id', $id)->first();
        $order_details = ProformerDetail::with('product')->where(['order_id' => $id, 'status' => 1])->get();
        //return $order_details;
        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        return view('pages.order.show_proformer', compact('order_details', 'order', 'company'));
    }

    public function updateProforma(Request $request, Proformer $order)
    {
        $invoice = $order->invoice_no;
        $reference = $order->reference;
        $inputs = $request->except('_token');

        $rules = [];
        if ($request->has('customer') && $request->customer == "" && $request->customer_id == "") {
            $rules = [
                'customer' => 'required',
            ];
        }
        if ($request->has('customer_id') && $request->customer_id == "" && $request->customer == "") {
            $rules = [
                'customer' => 'required',
            ];
        }

        $customMessages = [
            'customer_id.required' => 'Select a Customer first!.',
            //'customer_id.integer' => 'Invalid Customer!.'
        ];

        $validator = Validator::make($inputs, $rules, $customMessages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $customer_id = $request->input('customer_id');

        $sub_total = str_replace(',', '', \Cart::getSubTotal());
        $tax = 0;
        $total = str_replace(',', '', \Cart::getTotal());


        $pay = $request->input('pay');
        //$due = $total - $pay;
        $order_id = $order->id;
        $amount_paid = 0;

        //$customer_id = $request->input('customer_id');
        DB::beginTransaction();
        try {
            $due_date = $request->input('due_date');
            DB::table('proformers')->where('id', $order->id)->update([
                'reference' => $reference,
                'customer_id' => $customer_id,
                'order_date' => $request->order_date,
                'order_status' => 'approved',
                'total_products' => \Cart::getTotalQuantity(),
                'total' => $total,
                'invoice_no' => $invoice,
                'sold_by' => Auth::id(),
                'status' => 1,
                'description' => $request->description,
                'branch_id' => User::userBranchAction(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $contents = \Cart::getContent();
            $products = [];
            $total_discount = 0;
            DB::table('proformer_details')->where('order_id', $order->id)->delete();
            foreach ($contents as $content) {
                $total_discount += $content->attributes['discount'] * $content->quantity;
                $product_id = $content->id;
                $store = Store::where('code', $content->attributes['store'])->first();
                $order_detail = new OrderDetail();
                DB::table('proformer_details')->insert([
                    'order_id' => $order_id,
                    'product_id' => $product_id,
                    'store_id' => $store->id,
                    'quantity' => $content->quantity,
                    'unit_cost' => $content->price,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $action = "Updated proforma $invoice: $total";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        \Cart::clear();

        session()->flash('Proforma invoice updated successfully');
        return redirect()->route('proformer.show', $order_id);
    }
    public function editProformer(Proformer $order)
    {

        $user_branch = User::userBranchAction();
        $stores = StoreProduct::select('store_products.id', 'products.name', 'products.code', 'stores.name AS store', 'qty_available', 'selling_price', 'cost_price', 'unit')->distinct()
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->join('branch_product_prices', function ($join) {
                $join->on('branch_product_prices.product_id', '=', 'products.id')
                    ->on('branch_product_prices.branch_id', '=', 'branches.id');

            })
            ->where('stores.branch_id', 'LIKE', $user_branch)
            ->where('branch_product_prices.status', 1)
            ->orderBy('products.name')->orderBy('stores.name')->limit(100)->get();
        //TODO:: remove limit here

        $customers = Customer::active()->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        if (\Cart::getContent()->isEmpty()) {
            foreach ($order->order_items()->get() as $item) {
                $selling_price = $item->selling_price;
                $cost_price = $item->cost_price;
                // $storeProduct = StoreProduct::where(['product_id' => $item->product_id, 'store_id' => $item->store_id])->first();
                // if (!$storeProduct)
                //     continue;
                $qty_available = $item->qty_available;
                $store = $item->store->code ?? '';
                $qty = $item->quantity == 0 ? 1 : $item->quantity;

                $add = \Cart::add([
                    'id' => $item->product_id,
                    'name' => $item->product->name ?? '',
                    'price' => $item->unit_cost,
                    'quantity' => $qty,
                    'attributes' => array(
                        'cost_price' => $item->unit_cost,
                        'code' => $item->product->code ?? '',
                        'selling_price' => $selling_price,
                        'qty_available' => $qty_available,
                        'discount' => 0,
                        'store' => $store,
                        'unit' => $item->product->unit ?? '',
                    ),
                ]);

            }
        }

        $cart_products = \Cart::getContent();
        //dd($cart_products);
        $categories = Category::orderBy('name', 'ASC')->get();
        $store = Store::where('branch_id', 'LIKE', $user_branch)->get();
        return view('pages.pos.proformer', compact('stores', 'customers', 'cart_products', 'categories', 'store', 'order'));
    }
    public function print_proformer($order_id)
    {
        $order = Proformer::with('customer')->where('id', $order_id)->first();
        //return $order;
        $order_details = ProformerDetail::with('product')->where(['order_id' => $order_id, 'status' => 1])->get();
        //return $order_details;
        //$company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('created_at')->first();
        $company = Setting::find(1);
        $utility = new Utility();
        return view('pages.order.proformer_print', compact('order_details', 'order', 'company', 'utility'));
    }
    public function final_proformer(Request $request)
    {
        $invoice = $this->generateProfomerInvoice('PFI');
        $inputs = $request->except('_token');
        $description = $request->description;

        $rules = [];

        $rules = [
            'customer_id' => 'required|exists:customers,id',
        ];

        $customMessages = [
            'customer_id.required' => 'Select a Customer first!.',
            //'customer_id.integer' => 'Invalid Customer!.'
        ];

        $validator = Validator::make($inputs, $rules, $customMessages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $customer_id = $request->input('customer_id');

        $customer = Customer::findOrFail($customer_id);
        $items = $this->orderItems();

        $sub_total = str_replace(',', '', \Cart::getSubTotal());
        $tax = 0;
        $total = str_replace(',', '', \Cart::getTotal());


        $pay = $request->input('pay');
        //$due = $total - $pay;
        $order_id = 0;
        $amount_paid = 0;
        //$customer_id = $request->input('customer_id');
        DB::beginTransaction();
        try {
            $due_date = $request->input('due_date');
            $order_id = DB::table('proformers')->insertGetId([
                'reference' => Proformer::generateNewNumber(),
                'customer_id' => $customer_id,
                'order_date' => $request->order_date,
                'order_status' => 'approved',
                'total_products' => \Cart::getTotalQuantity(),
                'total' => $total,
                'invoice_no' => $invoice,
                'sold_by' => Auth::id(),
                'status' => 0,
                'description' => $description,
                'branch_id' => User::userBranchAction(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $contents = \Cart::getContent();
            //dd($contents);
            $products = [];
            $total_discount = 0;
            foreach ($contents as $content) {
                $total_discount += $content->attributes['discount'] * $content->quantity;
                $product_id = $content->id;
                $store = Store::where('code', $content->attributes['store'])->first();

                $order_detail = new OrderDetail();
                DB::table('proformer_details')->insert([
                    'order_id' => $order_id,
                    'product_id' => $product_id,
                    'store_id' => $store->id,
                    'quantity' => $content->quantity,
                    'unit_cost' => $content->price,
                    //get available product in stock before sale
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $action = "Issue proforma $invoice: $total";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        \Cart::clear();

        session()->flash('Proforma created successfully');
        return redirect()->route('proformer.show', $order_id);

    }
    public function destroy_proformer(Request $request, Proformer $proformer)
    {

        DB::beginTransaction();
        try {

            $invoice_no = $proformer->invoice_no;
            DB::table('proformers')->where('id', $proformer->id)->delete();
            DB::table('proformer_details')->where('order_id', $proformer->id)->delete();
            session()->flash('app_message', 'Order deleted successfully');
            $action = "Deleted order invoice  with invoice $invoice_no ";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $e) {
            session()->flash('app_error', 'Order could not be deleted!');
            DB::rollBack();
            throw $e;
        }
        return redirect()->back();
    }
    public function proformer_search(Request $request)
    {
        $search_value = $request->refno;

        $orders = Proformer::with('customer')->select('orders.*', 'customers.name')->latest('order_date')->join(
            'customers',
            'customers.id',
            'orders.customer_id'
        );
        if (Auth::user()->hasRole('Sales-Manager'))
            $orders = $orders->where('sold_by', Auth::id());
        $orders = $orders->where('proformers.branch_id', 'LIKE', User::userBranchAction())
            ->where(
                'proformers.branch_id',
                'LIKE',
                User::userBranchAction()
            )->where(
                'order_status',
                'approved'
            )
            ->where(
                function ($query) use ($search_value) {
                    $query->where('reference', 'LIKE', "%$search_value%")
                        ->orWhere(
                            'invoice_no',
                            'LIKE',
                            "%$search_value%"
                        )
                        ->orWhere(
                            'customers.name',
                            'LIKE',
                            "%$search_value%"
                        )
                        ->orWhere(
                            'customers.phone',
                            'LIKE',
                            "%$search_value%"
                        );
                }
            )->get(
            );

        return view('pages.order.proformers', compact('orders'));
    }
    public function generateProfomerInvoice($type)
    {
        if ($type == "PFI")
            $invoice = DB::table('proformers')->select(DB::raw('MAX(SUBSTR(invoice_no,8,11)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where(DB::raw('MONTH(created_at)'), '=', date('m'))->first();
        if ($type == "ODR")
            $invoice = DB::table('order_invoices')->select(DB::raw('MAX(SUBSTR(invoice_no,8,11)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where(DB::raw('MONTH(created_at)'), '=', date('m'))->first();
        return $type . date('y') . '' . date('m') . str_pad(($invoice->max + 1), 6, "0", STR_PAD_LEFT);
    }
    public static function orderItems()
    {
        $items = [];
        $contents = \Cart::getContent();
        foreach ($contents as $content) {
            $items[$content->id] = $content->quantity;
        }
        return $items;
    }
    
}
