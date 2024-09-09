<?php

namespace App\Http\Controllers;

use App\Classes\Transaction;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderInvoice;
use App\Models\OrderInvoiceDetail;
use App\Models\Proformer;
use App\Models\ProformerDetail;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade as PDF;
use Brian2694\Toastr\Facades\Toastr;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\CustomerLedger;
use App\Models\StoreProduct;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function pending_order()
    {
        $pendings = Order::latest()->with('customer')->where('branch_id', 'LIKE', User::userBranchAction())->where('order_status', 'pending')->get();
        return view('pages.order.pending_orders', compact('pendings'));
    }

    public function search(Request $request)
    {
        $search_value = $request->refno;

        $orders = Order::with('customer')->select('orders.*', 'customers.name')->latest('order_date')->join(
            'customers',
            'customers.id',
            'orders.customer_id'
        );
        if (Auth::user()->hasRole('Sales-Manager'))
            $orders = $orders->where('sold_by', Auth::id());
        $orders = $orders->where('orders.branch_id', 'LIKE', User::userBranchAction())
            ->where(
                'orders.branch_id',
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

        return view('pages.order.index', compact('orders'));
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
    public function order_invoice_search(Request $request)
    {
        $search_value = $request->refno;

        $orders = OrderInvoice::with('customer')->select('order_invoices.*', 'customers.name')->latest('order_date')->join(
            'customers',
            'customers.id',
            'order_invoices.customer_id'
        );
        if (Auth::user()->hasRole('Sales-Manager'))
            $orders = $orders->where('sold_by', Auth::id());
        $orders = $orders->where('order_invoices.branch_id', 'LIKE', User::userBranchAction())
            ->where(
                'order_invoices.branch_id',
                'LIKE',
                User::userBranchAction()
            )
            ->where(
                function ($query) use ($search_value) {
                    $query->where('reference', 'LIKE', "%$search_value%")
                        ->orWhere(
                            'customers.name',
                            'LIKE',
                            "%$search_value%"
                        )
                        ->orWhere('invoice_no', 'LIKE', "%$search_value%")
                        ->orWhere(
                            'customers.phone',
                            'LIKE',
                            "%$search_value%"
                        );
                }
            )->get(
            );

        return view('pages.order.order_invoices', compact('orders'));
    }
    public function load(Request $request)
    {
        $order = Order::find($request->order_id);
        if ($request->type == 'order')
            $order = OrderInvoice::find($request->order_id);
        if ($request->type == 'proformer')
            $order = Proformer::find($request->order_id);
        return view('pages.order.view_orders', compact('order'));
    }

   
    public function destroy_order_invoice(Request $request, Order $order)
    {

        DB::beginTransaction();
        try {

            $invoice_no = $order->invoice_no;
            DB::table('order_invoices')->where('id', $order->id)->delete();
            DB::table('order_invoice_details')->where('order_id', $order->id)->delete();
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
    public function destroy_proformer(Request $request, Order $order)
    {

        DB::beginTransaction();
        try {

            $invoice_no = $order->invoice_no;
            DB::table('proformers')->where('id', $order->id)->delete();
            DB::table('proformer_details')->where('order_id', $order->id)->delete();
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
    public function download($order_id)
    {
        $order = Order::with('customer')->where('id', $order_id)->first();
        //return $order;
        $order_details = OrderDetail::with('storeProduct')->where(['order_id' => $order_id, 'status' => 1])->get();
        //return $order_details;
        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();

        set_time_limit(300);

        $pdf = PDF::loadView('pages.order.pdf', ['order' => $order, 'order_details' => $order_details, 'company' => $company]);

        $content = $pdf->download()->getOriginalContent();

        Storage::put('public/pdf/' . $order->customer->name . '-' . str_pad($order->id, 9, "0", STR_PAD_LEFT) . '.pdf', $pdf->output());
        //PDF::loadHTML('pages.order.pdf')->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf');

        session()->flash('PDF successfully saved');
        return redirect()->back();

    }

    // for sales report
    public function today_sales()
    {
        $today = date('Y-m-d');

        $balance = Order::where('order_date', $today)->get();
        $user_branch = User::userBranchAction();

        $orders = DB::table('orders')
            ->join(
                'order_details',
                'orders.id',
                '=',
                'order_details.order_id'
            )
            ->join(
                'store_products',
                'store_products.id',
                '=',
                'order_details.store_product_id'
            )
            ->join(
                'stores',
                'stores.id',
                '=',
                'store_products.store_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'store_products.product_id'
            )
            ->join(
                'customers',
                'orders.customer_id',
                '=',
                'customers.id'
            )
            ->join(
                'branches',
                'branches.id',
                'stores.branch_id'
            )
            ->select(
                'customers.name as customer_name',
                'products.name AS product_name',
                'order_details.*',
                'stores.name as store',
                'orders.order_date',
                'orders.invoice_no'
            )
            ->whereDate(
                'orders.order_date',
                $today
            )
            ->where(
                'order_details.status',
                1
            )
            ->where(
                'stores.branch_id',
                'LIKE',
                $user_branch
            );
        if (Auth::user()->hasRole('Sale-Manager'))
            $orders = $orders->where('sold_by', Auth::id());
        $orders = $orders->orderBy('order_details.created_at', 'desc')
            ->get(
            );

        return view('pages.sales.today', compact('orders', 'balance'));
    }
    public function monthly_sales($month = null)
    {

        if ($month == null) {
            $month = date('m');
        } else {
            $month = date('m', strtotime($month));
        }

        $balance = Order::whereMonth('order_date', $month)->get();
        $user_branch = User::userBranchAction();

        $orders = DB::table('orders')
            ->join(
                'order_details',
                'orders.id',
                '=',
                'order_details.order_id'
            )
            ->join(
                'store_products',
                'store_products.id',
                '=',
                'order_details.store_product_id'
            )
            ->join(
                'stores',
                'stores.id',
                '=',
                'store_products.store_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'store_products.product_id'
            )
            ->join(
                'customers',
                'orders.customer_id',
                '=',
                'customers.id'
            )
            ->join(
                'branches',
                'branches.id',
                'stores.branch_id'
            )
            ->select(
                'customers.name as customer_name',
                'products.name AS product_name',
                'order_details.*',
                'stores.name as store',
                'orders.invoice_no'
            )
            ->whereMonth(
                'orders.order_date',
                $month
            )
            ->where(
                'order_details.status',
                1
            )
            ->where(
                'stores.branch_id',
                'LIKE',
                $user_branch
            );
        if (Auth::user()->hasRole('Sale-Manager'))
            $orders = $orders->where('sold_by', Auth::id());
        $orders = $orders->orderBy('order_details.created_at', 'desc')
            ->get(
            );

        return view('pages.sales.month', compact('orders', 'month', 'balance'));
    }
    public function total_sales()
    {
        $balance = Order::all();
        $user_branch = User::userBranchAction();
        $orders = DB::table('orders')
            ->join(
                'order_details',
                'orders.id',
                '=',
                'order_details.order_id'
            )
            ->join(
                'store_products',
                'store_products.id',
                '=',
                'order_details.store_product_id'
            )
            ->join(
                'stores',
                'stores.id',
                '=',
                'store_products.store_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'store_products.product_id'
            )
            ->join(
                'customers',
                'orders.customer_id',
                '=',
                'customers.id'
            )
            ->join(
                'branches',
                'branches.id',
                'stores.branch_id'
            )
            ->select(
                'customers.name as customer_name',
                'products.name AS product_name',
                'order_details.*',
                'stores.name as store',
                'orders.invoice_no'
            )
            ->where(
                'stores.branch_id',
                'LIKE',
                $user_branch
            )
            ->orderBy(
                'order_details.created_at',
                'desc'
            )
            ->where(
                ['order_details.status' => 1]
            );
        if (Auth::user()->hasRole('Sale-Manager'))
            $orders = $orders->where('sold_by', Auth::id());
        $orders = $orders->orderBy('order_details.created_at', 'desc')
            ->get(
            );

        return view('pages.sales.index', compact('balance', 'orders'));
    }
    public function printPayment($customerid)
    {

        $customer = Customer::find($customerid);
        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        return view('pages.customer.print', compact('dates', 'customer', 'company'));
    }
    public function transfer(Request $request)
    {
        if (Auth::user()->can('transfer.sale.to.user')) {
            Order::where('id', $request->order_id)->update(['sold_by' => $request->user_id]);
            $user = User::find($request->user_id);
            $action = "Transfered sale to $user->name of invoice  $request->invoice_no";
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Sale transfered successfully');
            return redirect()->route('orders.approved');
        } else {
            session()->flash('app_error', 'Unauthorized access!!');
            return redirect()->route('orders.approved');
        }
    }

    //invoice
    public function invoice_list()
    {
        \Cart::clear();
        $user = Auth::user();
        $orders = Order::selectRaw("
            orders.*,
            (SELECT id FROM credit_notes WHERE credit_notes.order_id = orders.id LIMIT 1) as has_credit_note    
        ")->with('customer')->where('branch_id', User::userBranchAction());
        if ($user->hasRole('Sales-Manager'))
            $orders = $orders->where('sold_by', Auth::id());
        //$orders = $orders->whereBetween('order_date', [date('Y-m-d', strtotime(Carbon::now()->subDays(7))), date('Y-m-d')])
        $orders = $orders->orderBy('posted_by', 'asc')->orderBy('order_date', 'DESC')->get()->take(300);
        return view('pages.order.index', compact('orders'));
    }
    public function post(Order $invoice)
    {
        if($invoice->status == 0) {
            $invoice->status = 1;
            $invoice->posted_by = auth()->id();
            $items = $invoice->order_items;
            $is_out_of_stock = false;
            $out_of_stock_products = "";
            DB::beginTransaction();
            if ($invoice->save()) {
                $products = [];
                foreach ($items as $item) {

                    $store = StoreProduct::find($item->store_product_id);

                    //get unit of measure
                    $quantity_sold = Transaction::quantity_sold($store->product->id, $item->quantity, $item->unit);
                    if ($quantity_sold > $store->qty_available) {
                        $is_out_of_stock = true;
                        $out_of_stock_products .= $store->product->code . ",";
                    }
                    DB::table('store_products')->where('id', $item->store_product_id)->update([
                        'qty_available' => str_replace(',', '', $store->qty_available) - $quantity_sold,
                        'updated_at' => Carbon::now()
                    ]);

                    DB::table('stock_cards')->insert([
                        'store_id' => $store->store->id,
                        'product_id' => $store->product->id,
                        'cr' => 0,
                        'dr' => $quantity_sold,
                        'refno' => $invoice->reference,
                        'type' => 0,
                        'date' => $invoice->order_date,
                        'charged_account' => $invoice->customer->code ?? '',
                        'user_id' => auth()->id(),
                        'priority' => 2,
                    ]);
                    $products[$item->store_product_id] = ['quantity' => $item->quantity, 'cost_price' => $item->cost_price, 'sold_price' => $item->sold_price];
                }
                if ($is_out_of_stock == true) {
                    session()->flash('app_error', 'Please check, the following products are out of stock\n' . $out_of_stock_products);
                    DB::rollBack();
                    return redirect()->back()->withInput();
                }
                /*return Transaction::sale(
                    $products,
                    $invoice->customer_id,
                    $invoice->reference,
                    $invoice->order_date);*/
                if (
                    Transaction::sale(
                        $products,
                        $invoice->customer_id,
                        $invoice->reference,
                        $invoice->order_date
                    )['status']
                ) {

                    $action = "Invoice of $invoice->total for : " . $invoice->reference;
                    AuditLog::auditLog(auth()->id(), $action);
                    session()->flash('app_message', 'Sale posted successfully');
                    DB::commit();
                } else {
                    DB::rollBack();
                    session()->flash('app_message', 'Something went wrong.');
                }
            }
        }
        return back();
    }
    public function show($id)
    {
        $order = Order::with('customer')->where('branch_id', 'LIKE', User::userBranchAction())->where('id', $id)->first();
        $order_details = OrderDetail::with('storeProduct')->where(['order_id' => $id, 'status' => 1])->get();
        //return $order_details;
        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        return view('pages.order.show', compact('order_details', 'order', 'company'));
    }
    public function loadEdit(Request $request)
    {
        $order = Order::find($request->order_id);
        return view('pages.order.load_edit_order', compact('order'));
    }
    public function order_confirm($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 1;
        $order->save();

        session()->flash('Order has been Approved! Please deliver the products', 'Success');
        return redirect()->back();
    }
    public function verify(Request $request) // to be verified by store keeper
    {
        $order_id = $request->order_id;
        $comment = $request->comment;
        $invoice_no = $request->invoice;
        Order::where('id', $order_id)->update(['comment' => $comment, 'issued_by' => Auth::id()]);
        session()->flash('app_message', 'Invoice confirmed successfully');
        $action = "Issued order with invoice $invoice_no out of stock";
        AuditLog::auditLog(Auth::id(), $action);
        $orders = Order::latest('order_date')->with('customer')->where('branch_id', 'LIKE', User::userBranchAction())->where('order_status', 'approved')->get();
        return view('pages.order.index', compact('orders'));
    }


    //order
    public function order_invoice_list()
    {
        //        return date('Y-m-d', strtotime(Carbon::now()->subDays(7)));
        \Cart::clear();
        $user = Auth::user();
        $orders = OrderInvoice::latest('order_date')->with('customer')->where('branch_id', 'LIKE', User::userBranchAction());
        if ($user->hasRole('Sales-Manager'))
            $orders = $orders->where('sold_by', Auth::id());
        $orders = $orders->whereBetween('order_date', [date('Y-m-d', strtotime(Carbon::now()->subDays(7))), date('Y-m-d')])->orderBy('status', 'asc')->get();
        return view('pages.order.order_invoices', compact('orders'));
    }
    public function order_invoice_show($id)
    {
        $order = OrderInvoice::with('customer')->where('branch_id', 'LIKE', User::userBranchAction())->where('id', $id)->first();
        $order_details = OrderInvoiceDetail::with('storeProduct')->where(['order_id' => $id, 'status' => 1])->get();
        //return $order_details;
        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        return view('pages.order.show_order_invoice', compact('order_details', 'order', 'company'));
    }
    public function approveOrderInvoice(Request $request, OrderInvoice $order)
    {
        $comment = $request->comment;
        $order_status = $request->order_status;
        $order->order_status = $order_status;
        $order->comment = $comment;
        $order->modified_by = auth()->id();
        $order->updated_at = Carbon::now();
        if ($order->save()) {
            AuditLog::auditLog(Auth::id(), "$order_status Invoice with " . $request->reference);
            session()->flash('app_message', "Invoice $order_status successfully");
            return redirect()->back();
        } else {
            session()->flash('app_error', "Failed to $order_status Order Invoice");
            return back();
        }

    }
    public function orderInvoiceClose(Request $request, OrderInvoice $order)
    {
        $order->status = 1;
        if ($order->save()) {
            return back()->with('success', 'Order closed successfully');
        }
        return back()->with('error', 'Something went wrong.');
    }

    //proforma
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
        $order_details = ProformerDetail::with('storeProduct')->where(['order_id' => $id, 'status' => 1])->get();
        //return $order_details;
        $company = Setting::where('branch_id', 'LIKE', User::userBranchAction())->latest()->first();
        return view('pages.order.show_proformer', compact('order_details', 'order', 'company'));
    }

    public function getAvailableQuantity(Request $request, StoreProduct $storeproduct)
    {
        $unit = $request->unit;
        //Get the value equavalant for the selected unit
        $record = $storeproduct->product->productUnitMeasure()->where('code', $unit)->first();

        if ($record != null && $storeproduct->product->unit != $unit) {
            //Do the coversion to the small unit of higher as the case maybe
            return $record->type == 'division' ? ($storeproduct->qty_available * $record->value) : ($storeproduct->qty_available / $record->value);
        } else {
            // Incase the selected unit is not diffent from the base unit
            return $storeproduct->qty_available;
        }

    }

}
