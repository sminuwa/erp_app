<?php

namespace App\Http\Controllers;

use App\Classes\Transaction;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderInvoice;
use App\Models\OrderInvoiceDetail;
use App\Models\Product;
use App\Models\Proformer;
use App\Models\ProformerDetail;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Darryldecode\Cart\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use App\Models\Utility;
use App\Models\PaymentMode;
use App\Models\StoreProduct;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerLedger;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\BankAccount;
use App\Models\SupplierLedger;
use App\Models\AuditLog;
use App\Models\User;

class InvoiceController extends Controller
{
    public function create(Request $request)
    {

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
        /*$rules = [
         'customer_id' => 'required | integer',
         ];*/
        $customMessages = [
            'customer_id.required' => 'Select a Customer first!.',
            //'customer_id.integer' => 'Invalid Customer!.'
        ];

        $validator = Validator::make($inputs, $rules, $customMessages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $customer_id = $request->input('customer_id');
        if ($request->has('customer') && ($request->customer != "")) {
            $customer_id = Customer::insertGetId(['name' => $request->customer, 'phone' => $request->phone, 'address' => $request->address, 'branch_id' => User::userBranchAction()]);
        }
        $customer = Customer::findOrFail($customer_id);

        $contents = \Cart::getContent();

        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        $sale_mode = $request->sale_mode;

        return view('pages.pos.invoice', compact('customer', 'contents', 'company', 'sale_mode'));
    }

    public function print($order_id)
    {
        $order = Order::with('customer')->where('id', $order_id)->first();
        //return $order;
        $order_details = OrderDetail::with('storeProduct')->where(['order_id' => $order_id, 'status' => 1])->get();
        //return $order_details;
        //$company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('created_at')->first();
        $company = Setting::find(1);
        $utility = new Utility();
        return view('pages.order.print', compact('order_details', 'order', 'company', 'utility'));
    }
    public function print_proformer($order_id)
    {
        $order = Proformer::with('customer')->where('id', $order_id)->first();
        //return $order;
        $order_details = ProformerDetail::with('storeProduct')->where(['order_id' => $order_id, 'status' => 1])->get();
        //return $order_details;
        //$company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('created_at')->first();
        $company = Setting::find(1);
        $utility = new Utility();
        return view('pages.order.proformer_print', compact('order_details', 'order', 'company', 'utility'));
    }
    public function print_order_invoice($order_id)
    {
        $order = OrderInvoice::with('customer')->where('id', $order_id)->first();
        //return $order;
        $order_details = OrderInvoiceDetail::with('storeProduct')->where(['order_id' => $order_id, 'status' => 1])->get();
        //return $order_details;
        //$company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('created_at')->first();
        $company = Setting::find(1);
        $utility = new Utility();
        return view('pages.order.order_invoice_print', compact('order_details', 'order', 'company', 'utility'));
    }

    public function order_print($order_id)
    {
        $order = OrderInvoice::with('customer')->where('id', $order_id)->first();
        //return $order;
        $order_details = OrderInvoiceDetail::with('storeProduct')->where(['order_id' => $order_id, 'status' => 1])->get();
        //return $order_details;
        //$company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('created_at')->first();
        $company = Setting::find(1);
        $utility = new Utility();
        return view('pages.order.order_print', compact('order_details', 'order', 'company', 'utility'));
    }

    public function final_invoice(Request $request)
    {
        //dd(\Cart::getContent());
        $invoice_id = $request->invoice_id;

        $inputs = $request->except('_token');
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

        $total_sales = \Cart::getTotal();
        //Check to make sure that the amount has not exceeded the credit limit set for the customer.
        if (Transaction::check_transaction_limit($customer_id, $total_sales) == false) {
            session()->flash('app_error', 'The amount has exceeded the customer credit limit');
            return redirect()->back();
        }

        $sub_total = str_replace(',', '', \Cart::getSubTotal());
        $tax = 0;
        $total = str_replace(',', '', \Cart::getTotal());


        $pay = $request->input('pay');
        //$due = $total - $pay;
        $order_id = 0;
        $amount_paid = 0;
        $reference = Order::generateNewNumber();
        $order_date = $request->order_date;
        //$customer_id = $request->input('customer_id');
        DB::beginTransaction();
        try {
            $payment_mode = 'Cash';
            $due_date = $request->input('due_date');
            $discount = 0;
            $refund = 0;
            if ($request->has('discount') && str_replace(',', '', $request->discount) > 0)
                $discount = $request->discount;
            if ($request->has('refund') && str_replace(',', '', $request->refund) > 0)
                $refund = $request->refund;
            $running_balance = $this->runninigBalance($customer_id);
            if ($running_balance < 0) { // in case the company owes a customer

                if ($running_balance <= $total)
                    $amount_paid = abs($running_balance);
                else
                    $amount_paid = abs($running_balance) - $total;

            }

            $invoice = Order::find($invoice_id);
            if (!$invoice) {
                $invoice = new \App\Models\Order();
                $invoice->reference = $reference;
                $invoice->branch_id = User::userBranchAction();
                $invoice->order_status = 'approved';
                $invoice->sold_by = Auth::id();
                $invoice->discount = $discount;
                $invoice->refund = $refund;
                $invoice->customer_id = $customer_id;
                $invoice->invoice_no = $reference;
            } else {
                $invoice->order_invoice_id = $request->order_invoice_id ?? 0;
            }
            $invoice->pay = $payment_mode == "Credit" ? $amount_paid : $total;
            $invoice->due = $payment_mode == "Credit" ? ($total - $amount_paid) : 0;
            $invoice->order_date = $order_date;
            $invoice->total_products = \Cart::getTotalQuantity();
            $invoice->sub_total = $sub_total;
            $invoice->vat = $tax;
            $invoice->total = $total;
            $invoice->status = 0;
            if ($invoice->save()) {
                OrderDetail::where('order_id', $invoice->id)->delete();
                $contents = \Cart::getContent();
                $products = [];
                $total_discount = 0;
                $store_products = [];
                foreach ($contents as $content) {
                    $total_discount += $content->attributes['discount'] * $content->quantity;
                    $store = StoreProduct::find($content->id);
                    $qtyAval = $store->qty_available;
                    //$store->qty_available = $qtyAval - $content->quantity;
                    $order_detail = new OrderDetail();
                    DB::table('order_details')->insert([
                        'order_id' => $invoice->id,
                        'store_product_id' => $content->id,
                        'quantity' => $content->quantity,
                        'original_quantity_sold' => $content->quantity,
                        'selling_price' => $content->attributes['selling_price'],
                        'sold_price' => $content->price,
                        'cost_price' => $content->attributes['cost_price'],
                        'total' => $content->getPriceSum(),
                        'avail_qty_before_sale' => $qtyAval,
                        'unit' => $content->attributes['unit'],
                        //get available product in stock before sale
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    $store_products[$content->id] = $content->quantity;
                }
                //Upate the Order table with the discount
                //DB::table('orders')->where('id', $invoice->id)->increment('discount', $total_discount);

                DB::table('order_invoices')->where('id', $request->order_invoice_id)->update([
                    'status' => 3,
                    'approved_by' => auth()->id(),
                    'updated_at' => Carbon::now()
                ]);

                //Transaction::sale($store_products, $customer_id, $reference, $order_date);

                $action = "Made a sell of $invoice: $total";
                AuditLog::auditLog(Auth::id(), $action);
                DB::commit();
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        \Cart::clear();

        session()->flash('Invoice created successfully');
        return redirect()->route('orders.show', $invoice->id);

    }
    public function final_proformer(Request $request)
    {
        $invoice = $this->generateProfomerInvoice('PFI');
        $inputs = $request->except('_token');


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
            $payment_mode = 'Cash';
            $due_date = $request->input('due_date');
            $running_balance = $this->runninigBalance($customer_id);
            if ($running_balance < 0) { // in case the company owes a customer

                if ($running_balance <= $total)
                    $amount_paid = abs($running_balance);
                else
                    $amount_paid = abs($running_balance) - $total;

            }

            $order_id = DB::table('proformers')->insertGetId([
                'reference' => Proformer::generateNewNumber(),
                'customer_id' => $customer_id,
                //                'payment_mode' => $payment_mode,
//                'due_date' => $due_date,
                'pay' => $payment_mode == "Credit" ? $amount_paid : $total,
                'due' => $payment_mode == "Credit" ? ($total - $amount_paid) : 0,
                'order_date' => $request->order_date,
                //date('Y-m-d'),
                'order_status' => 'approved',
                'total_products' => \Cart::getTotalQuantity(),
                'sub_total' => $sub_total,
                'vat' => $tax,
                'total' => $total,
                'invoice_no' => $invoice,
                'sold_by' => Auth::id(),
                'status' => 1,
                'branch_id' => User::userBranchAction(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $contents = \Cart::getContent();
            $products = [];
            $total_discount = 0;
            foreach ($contents as $content) {
                $total_discount += $content->attributes['discount'] * $content->quantity;
                $store = StoreProduct::find($content->id);
                $qtyAval = $store->qty_available;
                //$store->qty_available = $qtyAval - $content->quantity;
                $order_detail = new OrderDetail();
                DB::table('proformer_details')->insert([
                    'order_id' => $order_id,
                    'store_product_id' => $content->id,
                    'quantity' => $content->quantity,
                    'original_quantity_sold' => $content->quantity,
                    'selling_price' => $content->attributes['selling_price'],
                    'sold_price' => $content->price,
                    'cost_price' => $content->attributes['cost_price'],
                    'total' => $content->getPriceSum(),
                    'avail_qty_before_sale' => $qtyAval,
                    //get available product in stock before sale
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
            //Upate the Order table with the discount
            DB::table('proformers')->where('id', $order_id)->increment('discount', $total_discount);

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
    public function final_order_invoice(Request $request)
    {
        $invoice = $this->generateProfomerInvoice('ODR');
        $inputs = $request->except('_token');

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

        /*if ((\Cart::getTotal() + Customer::find($customer_id)->runningBalance()) > Customer::find($customer_id)->credit_limit) {
            session()->flash('app_error', 'The amount has exceeded the customer credit limit');
            return redirect()->back();
        }*/
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
            $payment_mode = 'Cash';
            $due_date = $request->input('due_date');
            $running_balance = $this->runninigBalance($customer_id);
            if ($running_balance < 0) { // in case the company owes a customer

                if ($running_balance <= $total)
                    $amount_paid = abs($running_balance);
                else
                    $amount_paid = abs($running_balance) - $total;

            }

            $order_id = DB::table('order_invoices')->insertGetId([
                'reference' => OrderInvoice::generateNewNumber(),
                'customer_id' => $customer_id,
                'pay' => $payment_mode == "Credit" ? $amount_paid : $total,
                'due' => $payment_mode == "Credit" ? ($total - $amount_paid) : 0,
                'order_date' => $request->order_date,
                'order_status' => 'approved',
                'total_products' => \Cart::getTotalQuantity(),
                'sub_total' => $sub_total,
                'vat' => $tax,
                'total' => $total,
                'invoice_no' => $invoice,
                'sold_by' => Auth::id(),
                'status' => 0,
                'branch_id' => User::userBranchAction(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $contents = \Cart::getContent();
            $products = [];
            $total_discount = 0;
            foreach ($contents as $content) {
                $total_discount += $content->attributes['discount'] * $content->quantity;
                $store = StoreProduct::find($content->id);
                $qtyAval = $store->qty_available;
                //$store->qty_available = $qtyAval - $content->quantity;
                $order_detail = new OrderDetail();
                DB::table('order_invoice_details')->insert([
                    'order_id' => $order_id,
                    'store_product_id' => $content->id,
                    'quantity' => $content->quantity,
                    'original_quantity_sold' => $content->quantity,
                    'selling_price' => $content->attributes['selling_price'],
                    'sold_price' => $content->price,
                    'cost_price' => $content->attributes['cost_price'],
                    'total' => $content->getPriceSum(),
                    'avail_qty_before_sale' => $qtyAval,
                    //get available product in stock before sale
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
            //Upate the Order table with the discount
            DB::table('order_invoices')->where('id', $order_id)->increment('discount', $total_discount);

            $action = "Issue order invoice $invoice: $total";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        //\Cart::clear();

        session()->flash('Order invoice created successfully');
        return redirect()->route('order.invoice.show', $order_id);

    }
    public function getTotalDiscount($order_id)
    {
        return OrderDetail::sum('discount_unit_price')->where(['order_id' => $order_id, 'status' => 1])->first();
    }
    public function generateInvoice()
    {
        $invoice = DB::table('orders')->select(DB::raw('MAX(SUBSTR(invoice_no,8,11)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where(DB::raw('MONTH(created_at)'), '=', date('m'))->first();
        return Auth::user()->user_code . date('y') . '' . date('m') . str_pad(($invoice->max + 1), 6, "0", STR_PAD_LEFT);
    }
    public function generateProfomerInvoice($type)
    {
        if ($type == "PFI")
            $invoice = DB::table('proformers')->select(DB::raw('MAX(SUBSTR(invoice_no,8,11)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where(DB::raw('MONTH(created_at)'), '=', date('m'))->first();
        if ($type == "ODR")
            $invoice = DB::table('order_invoices')->select(DB::raw('MAX(SUBSTR(invoice_no,8,11)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where(DB::raw('MONTH(created_at)'), '=', date('m'))->first();
        return $type . date('y') . '' . date('m') . str_pad(($invoice->max + 1), 6, "0", STR_PAD_LEFT);
    }
    public function runninigBalance($customer_id)
    {
        $cr = CustomerLedger::where('customer_id', $customer_id)->sum('cr');
        $dr = CustomerLedger::where('customer_id', $customer_id)->sum('dr');
        return $cr - $dr;
    }
    public function waybill_print($order_id)
    {
        $order = Order::with('customer')->where('id', $order_id)->first();
        //return $order;
        $order_details = OrderDetail::with('storeProduct')->where(['order_id' => $order_id, 'status' => 1])->get();
        //return $order_details;
        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('created_at')->first();
        $utility = new Utility();
        return view('pages.order.print_waybill', compact('order_details', 'order', 'company', 'utility'));
    }
    public function pos_print($order_id)
    {
        $order = Order::with('customer')->where('id', $order_id)->first();
        //return $order->branch;
        $order_details = OrderDetail::with('storeProduct')->where(['order_id' => $order_id, 'status' => 1])->get();
        //return $order_details;
        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->orderBy('created_at')->first();
        $utility = new Utility();
        return view('pages.order.print_pos', compact('order_details', 'order', 'company', 'utility'));
    }
    public function updateInvoice(Request $request, Order $order)
    {
        $invoice = $order->invoice_no;
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
        //return \Cart::getTotal().", ".Customer::find($customer_id)->credit_limit.",, ".$request->input('sale_mode');
        if ($request->input('sale_mode') == "Credit" && (\Cart::getTotal() + Customer::find($customer_id)->runningBalance()) > Customer::find($customer_id)->credit_limit) {
            session()->flash('app_error', 'The amount has exceeded the customer credit limit');
            return redirect()->back();
        }

        if ($request->has('customer') && ($request->customer != "")) {
            Customer::where('id', $order->customer_id)->update(['name' => $request->customer, 'phone' => $request->phone, 'address' => $request->address, 'branch_id' => User::userBranchAction()]);
            $customer_id = $order->customer_id;
        }

        DB::table('customers')->where('id', $customer_id)->update(['name' => $request->customer, 'phone' => $request->phone, 'address' => $request->address]);

        //$customer = Customer::findOrFail($customer_id);


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
            $payment_mode = $request->input('sale_mode');
            $due_date = $request->input('due_date');
            $running_balance = $this->runninigBalance($customer_id);
            if ($running_balance < 0) { // in case the company owes a customer

                if ($running_balance <= $total)
                    $amount_paid = abs($running_balance);
                else
                    $amount_paid = abs($running_balance) - $total;

            }
            DB::table('orders')->where('id', $order->id)->update([
                'customer_id' => $customer_id,
                //                'payment_mode' => $payment_mode,
                'due_date' => $due_date,
                'pay' => $payment_mode == "Credit" ? $amount_paid : $total,
                'due' => $payment_mode == "Credit" ? ($total - $amount_paid) : 0,
                'order_date' => $request->order_date,
                //date('Y-m-d'),
                'order_status' => 'approved',
                'total_products' => \Cart::getTotalQuantity(),
                'sub_total' => $sub_total,
                'vat' => $tax,
                'total' => $total,
                'invoice_no' => $invoice,
                'modified_by' => Auth::id(),
                'status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $contents = \Cart::getContent();
            $products = [];
            $total_discount = 0;

            //Put back the previous quantity
            $sales = OrderDetail::where('order_id', $order_id)->where('status', '=', 1)->get();
            foreach ($sales as $sale) {
                $qty = DB::table('store_products')->where('id', $sale->store_product_id)->first();
                DB::table('store_products')->where('id', $sale->store_product_id)->update([
                    'qty_available' => $qty->qty_available + $sale->quantity,
                    'updated_at' => Carbon::now()
                ]);
            }
            DB::table('order_details')->where('order_id', $order_id)->update(['status' => 0]);

            DB::table('transfer_products')->where('refno', $invoice)->update(['status' => 'Cancelled']);
            DB::table('stock_cards')->where('refno', $invoice)->update(['status' => 0]);
            foreach ($contents as $content) {
                //Put back the previous quantity
                /*$restored_qty = $order->order_items()->where('store_product_id', $content->id)->first();

               if($restored_qty?->quantity >0)
                   DB::table('store_products')->where('id', $content->id)->increment('qty_available', $restored_qty->$restored_qty?->quantity);*/
                $total_discount += $content->attributes['discount'] * $content->quantity;
                $store = StoreProduct::find($content->id);
                $qtyAval = $store->qty_available;

                $order_detail = new OrderDetail();
                DB::table('order_details')->insert([
                    'store_product_id' => $content->id,
                    'order_id' => $order->id,
                    'quantity' => $content->quantity,
                    'selling_price' => $content->attributes['selling_price'],
                    'sold_price' => $content->price,
                    'cost_price' => $content->attributes['cost_price'],
                    'total' => $content->getPriceSum(),
                    'avail_qty_before_sale' => $qtyAval,
                    //get available product in stock before sale
                    'status' => 1,
                    'last_modified_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                DB::table('store_products')->where('id', $content->id)->update([
                    'qty_available' => $qtyAval - $content->quantity,
                    'updated_at' => Carbon::now()
                ]);

                DB::table('transfer_products')->updateOrInsert([
                    'source_store_id' => $store->store->id,
                    'product_id' => $store->product->id,
                    'refno' => $invoice
                ], [
                    'source_store_id' => $store->store->id,
                    'product_id' => $store->product->id,
                    'destination_store_id' => $store->store->id,
                    'qty_transfered' => $content->quantity,
                    'qty_available' => $qtyAval,
                    //Before Sale
                    'transfered_by' => Auth::id(),
                    'status' => 'Completed',
                    'nature' => 'Sale',
                    'stock_in_out' => 'out',
                    'refno' => $invoice,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                DB::table('stock_cards')->updateOrInsert([
                    'store_id' => $store->store->id,
                    'product_id' => $store->product->id,
                    'refno' => $invoice
                ], [
                    'store_id' => $store->store->id,
                    'product_id' => $store->product->id,
                    'cr' => 0,
                    'dr' => $content->quantity,
                    'refno' => $invoice,
                    'type' => 'Sale',
                    'date' => $request->order_date,
                    'user_id' => Auth::id(),
                    'status' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            //Upate the Order table with the discount
            DB::table('orders')->where('id', $order_id)->increment('discount', $total_discount);
            //DB::table('orders')->where('id', $order_id)->decrement('due', $total_discount);

            if ($payment_mode != "Cash") {
                if ($payment_mode == "Credit") {
                    DB::table('customer_ledgers')->where('order_id', $order->id)->update([
                        'customer_id' => $customer_id,
                        'order_id' => $order_id,
                        'systemid' => $invoice,
                        'description' => 'Credit sales',
                        'Ref' => 'Nil',
                        'cr' => $total,
                        'payment_mode' => $payment_mode,
                        'date' => $request->order_date,
                        //date('Y-m-d'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    //Undo for previous amount
                    DB::table('customers')->where(['id' => $customer_id])->decrement('opening_balance', ($order->total));
                    //Update with new amount
                    DB::table('customers')->where(['id' => $customer_id])->increment('opening_balance', ($total - $total_discount));
                }

            }
            if ($payment_mode == "Cash") {
                //Undo for the prevoius amount
                DB::table('bank_accounts')->where(['account_type' => 'Cash'])->where('branch_id', 'LIKE', User::userBranchAction())->decrement('account_balance', $order->total);
                //Update with the new amount
                DB::table('bank_accounts')->where(['account_type' => 'Cash'])->where('branch_id', 'LIKE', User::userBranchAction())->increment('account_balance', ($total - $total_discount));

                $bank_account = DB::table('bank_accounts')->where(['account_type' => 'Cash'])->where('branch_id', 'LIKE', User::userBranchAction())->first();
                DB::table('customer_ledgers')->updateOrInsert([
                    'order_id' => $order_id,
                    'systemid' => $invoice
                ], [
                    'customer_id' => $customer_id,
                    'order_id' => $order_id,
                    'systemid' => $invoice,
                    'description' => 'Cash sales',
                    'Ref' => 'Nil',
                    'cr' => $total,
                    'dr' => $total,
                    'payment_mode' => $payment_mode,
                    'bank_account_id' => $bank_account->id,
                    'date' => $request->order_date,
                    //date('Y-m-d'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                //Bank Deposit
                DB::table('bank_transactions')->updateOrInsert(['ref_no' => $invoice], [
                    'bank_account_id' => $bank_account->id,
                    'trans_date' => $request->order_date,
                    //date('Y-m-d'),
                    'cr' => $total,
                    'dr' => 0,
                    'ref_no' => $invoice,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            }
            $action = "Updated invoice $invoice: $total";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        \Cart::clear();

        session()->flash('Invoice created successfully');

        return redirect()->route('orders.show', $order_id);
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
    public function linkOrderInvoice(Request $request, OrderInvoice $order)
    {
        $user_branch = User::userBranchAction();
        $stores = StoreProduct::select('store_products.id', 'products.name', 'products.code', 'stores.name AS store', 'qty_available', 'selling_price', 'retail_selling_price', 'cost_price')->distinct()
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('products', 'products.id', 'store_products.product_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->join('branch_product_prices', function ($join) {
                $join->on('branch_product_prices.product_id', '=', 'products.id')
                    ->on('branch_product_prices.branch_id', '=', 'branches.id');

            })
            //->where('stores.branch_id', 'LIKE', $user_branch)
            ->where('branch_product_prices.status', 1)
            ->orderBy('products.name')->orderBy('stores.name')->get();
        //TODO:: remove limit here
        $customers = Customer::where('branch_id', 'LIKE', $user_branch)->orderBy('name');
        if (\Cart::getContent()->isEmpty())
            $this->loadOrderInvoiceToCart($order);
        $cart_products = \Cart::getContent();
        $categories = Category::orderBy('name', 'ASC')->get();
        $store = Store::where('id', 'LIKE', $user_branch)->get();
        return view('pages.pos.index', compact('stores', 'customers', 'cart_products', 'categories', 'store', 'order'));
    }
    public function editOrderInvoice(Request $request, OrderInvoice $order)
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

        $customers = Customer::where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();

        if (\Cart::getContent()->isEmpty()) {
            foreach ($order->order_items()->get() as $item) {
                $selling_price = $item->selling_price;
                $cost_price = $item->cost_price;
                $qty_available = $item->qty_available;
                $store = $item->storeProduct->store->name;
                $qty = $item->quantity == 0 ? 1 : $item->quantity;
                $add = \Cart::add([
                    'id' => $item->store_product_id,
                    'name' => $item->storeProduct->product->name,
                    'price' => $item->sold_price,
                    'quantity' => $qty,
                    'attributes' => array(
                        'cost_price' => $cost_price,
                        'code' => $item->storeProduct->product->code,
                        'selling_price' => $selling_price,
                        'qty_available' => $qty_available,
                        'discount' => 0,
                        'store' => $store,
                        'unit' => $item->storeProduct->product->unit
                    ),
                ]);

            }
        }

        $cart_products = \Cart::getContent();
        //dd($cart_products);
        $categories = Category::orderBy('name', 'ASC')->get();
        $store = Store::where('id', 'LIKE', $user_branch)->get();
        return view('pages.pos.order_invoice', compact('stores', 'customers', 'cart_products', 'categories', 'store', 'order'));
    }
    public function loadOrderInvoiceToCart(OrderInvoice $order)
    {
        foreach ($order->order_items()->get() as $item) {
            $selling_price = $item->selling_price;
            $cost_price = $item->cost_price;
            $qty_available = $item->qty_available;
            $store = $item->storeProduct->store->name;
            $qty = $item->quantity;
            $store_products = StoreProduct::find($item->store_product_id);
            if ($store_products && $store_products->qty_available > 0) {
                $add = \Cart::add([
                    'id' => $item->store_product_id,
                    'name' => $item->storeProduct->product->name,
                    'price' => $item->sold_price,
                    'quantity' => $qty <= $store_products->qty_available ? $qty : ceil($store_products->qty_available),
                    'attributes' => array(
                        'cost_price' => $cost_price,
                        'code' => $item->storeProduct->product->code,
                        'selling_price' => $selling_price,
                        'qty_available' => $qty_available,
                        'discount' => 0,
                        'store' => $store,
                        'unit' => $item->storeProduct->product->unit
                    ),
                ]);
            }
        }

        //dd(\Cart::getContent());
    }
    public function updateOrderInvoice(Request $request, OrderInvoice $order)
    {
        $invoice = $order->invoice_no;
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
            DB::table('order_invoices')->where('id', $order->id)->update([
                'customer_id' => $customer_id,
                'due' => $total,
                'order_date' => $request->order_date,
                'order_status' => 'pending',
                'total_products' => \Cart::getTotalQuantity(),
                'sub_total' => $sub_total,
                'vat' => $tax,
                'total' => $total,
                'invoice_no' => $invoice,
                'modified_by' => Auth::id(),
            ]);

            $contents = \Cart::getContent();
            $products = [];
            $total_discount = 0;
            DB::table('order_invoice_details')->where('order_id', $order->id)->delete();
            foreach ($contents as $content) {
                //Put back the previous quantity
                /*$restored_qty = $order->order_items()->where('store_product_id', $content->id)->first();

               if($restored_qty?->quantity >0)
                   DB::table('store_products')->where('id', $content->id)->increment('qty_available', $restored_qty->$restored_qty?->quantity);*/
                $total_discount += $content->attributes['discount'] * $content->quantity;
                $store = StoreProduct::find($content->id);
                $qtyAval = $store->qty_available;

                DB::table('order_invoice_details')->insert([
                    'store_product_id' => $content->id,
                    'order_id' => $order->id,
                    'quantity' => $content->quantity,
                    'selling_price' => $content->attributes['selling_price'],
                    'sold_price' => $content->price,
                    'cost_price' => $content->attributes['cost_price'],
                    'total' => $content->getPriceSum(),
                    'avail_qty_before_sale' => $qtyAval,
                    'status' => 1,
                    'last_modified_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $action = "Updated invoice $invoice: $total";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
        \Cart::clear();

        session()->flash('Order invoice updated successfully');
        return redirect()->route('order.invoice.show', $order_id);
    }
    public function loadProformaToCart(Proformer $order)
    {
        foreach ($order->order_items()->get() as $item) {
            $selling_price = $item->selling_price;
            $cost_price = $item->cost_price;
            $qty_available = $item->qty_available;
            $store = $item->storeProduct->store->name;
            $qty = $item->quantity;
            $store_products = StoreProduct::find($item->store_product_id);
            if ($store_products && $store_products->qty_available > 0) {
                $add = \Cart::add([
                    'id' => $item->store_product_id,
                    'name' => $item->storeProduct->product->name,
                    'price' => $item->sold_price,
                    'quantity' => $qty <= $store_products->qty_available ? $qty : ceil($store_products->qty_available),
                    'attributes' => array(
                        'cost_price' => $cost_price,
                        'code' => $item->storeProduct->product->code,
                        'selling_price' => $selling_price,
                        'qty_available' => $qty_available,
                        'discount' => 0,
                        'store' => $store,
                        'unit' => $item->storeProduct->product->unit
                    ),
                ]);
            }
        }

        //dd(\Cart::getContent());
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

        $customers = Customer::where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        if (\Cart::getContent()->isEmpty()) {
            foreach ($order->order_items()->get() as $item) {
                $selling_price = $item->selling_price;
                $cost_price = $item->cost_price;
                $qty_available = $item->qty_available;
                $store = $item->storeProduct->store->name;
                $qty = $item->quantity == 0 ? 1 : $item->quantity;
                $add = \Cart::add([
                    'id' => $item->store_product_id,
                    'name' => $item->storeProduct->product->name,
                    'price' => $item->sold_price,
                    'quantity' => $qty,
                    'attributes' => array(
                        'cost_price' => $cost_price,
                        'code' => $item->storeProduct->product->code,
                        'selling_price' => $selling_price,
                        'qty_available' => $qty_available,
                        'discount' => 0,
                        'store' => $store,
                        'unit' => $item->storeProduct->product->unit
                    ),
                ]);

            }
        }

        $cart_products = \Cart::getContent();
        //dd($cart_products);
        $categories = Category::orderBy('name', 'ASC')->get();
        $store = Store::where('id', 'LIKE', $user_branch)->get();
        return view('pages.pos.proformer', compact('stores', 'customers', 'cart_products', 'categories', 'store', 'order'));
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
                'pay' => 0,
                'due' => $total,
                'order_date' => $request->order_date,
                'order_status' => 'approved',
                'total_products' => \Cart::getTotalQuantity(),
                'sub_total' => $sub_total,
                'vat' => $tax,
                'total' => $total,
                'invoice_no' => $invoice,
                'sold_by' => Auth::id(),
                'status' => 1,
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
                $store = StoreProduct::find($content->id);
                $qtyAval = $store->qty_available;
                //$store->qty_available = $qtyAval - $content->quantity;
                $order_detail = new OrderDetail();
                DB::table('proformer_details')->insert([
                    'order_id' => $order_id,
                    'store_product_id' => $content->id,
                    'quantity' => $content->quantity,
                    'original_quantity_sold' => $content->quantity,
                    'selling_price' => $content->attributes['selling_price'],
                    'sold_price' => $content->price,
                    'cost_price' => $content->attributes['cost_price'],
                    'total' => $content->getPriceSum(),
                    'avail_qty_before_sale' => $qtyAval,
                    //get available product in stock before sale
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $action = "Updated invoice $invoice: $total";
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
    public function delete(Request $request, Order $invoice)
    {
        $method = $request->method();
        $invoice_id = $invoice->id;
        if ($method == 'POST') {
            if ($invoice->status == 0) {
                DB::beginTransaction();
                if ($invoice->delete()) {
                    OrderDetail::where('order_id', $invoice_id)->delete();
                    session()->flash('app_message', 'Invoice deleted successfully!');
                    DB::commit();
                }
            } else
                session()->flash('app_error', 'Invoice cannot be deleted!');
        } else {
            session()->flash('app_error', 'Invalid request methods.');
            DB::rollBack();
        }
        return redirect()->route('invoice.index');
    }
}
