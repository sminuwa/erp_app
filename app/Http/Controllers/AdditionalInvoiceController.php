<?php

namespace App\Http\Controllers;

use App\Classes\CostPrice;
use App\Classes\Transaction;
use App\Models\AuditLog;
use App\Models\Purchase;
use App\Models\PurchaseExpense;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdditionalInvoiceController extends Controller
{
    //
    public function index(Request $request){
        $purchases = Purchase::where('branch_id',User::userBranchAction())->get()->pluck('id')->toArray();
        $invoices = PurchaseExpense::whereIn('purchase_id',$purchases)->orderBy('id','desc')->get();
        return view('pages.inventories.expenses.index', compact('invoices'));
    }

    public function create(){

        $purchases = Purchase::where('branch_id',User::userBranchAction())->orderBy('id','desc')->get();
        $suppliers = Supplier::orderBy('code','asc')->get();
        return view('pages.inventories.expenses.create',compact('purchases', 'suppliers'));
    }

    public function store(Request $request){
        $invoice_id = $request->invoice_id;
        $invoice_id = $request->invoice_id;
        $date = $request->date;
        $purchase_id = $request->purchase_id;
        $supplier_id = $request->supplier_id;
        $amount = $request->amount;
        $description = $request->description;
        $invoice = PurchaseExpense::find($invoice_id);
        if(!$invoice){
            $invoice = new PurchaseExpense();
            $invoice->reference = PurchaseExpense::generateNewNumber();
            $invoice->created_by = auth()->id();
            $invoice->supplier_id = $supplier_id;
            $invoice->purchase_id = $purchase_id;
        }else{

        }
        $invoice->date = $date;
        $invoice->amount = $amount;
        $invoice->description = $description;
        $invoice->description = $description;
        $invoice->status = 0;
        if($invoice->save()){
            session()->flash('app_message', 'Additional invoice saved successfully');
            return back();
        }
        session()->flash('app_error', 'Purchase successfully posted');
        return back();
    }

    public function edit(PurchaseExpense $invoice){
        $purchases = Purchase::orderBy('id','desc')->get();
        $suppliers = Supplier::orderBy('code','asc')->get();
        return view('pages.inventories.expenses.create', compact('invoice','purchases', 'suppliers'));
    }

    public function post(PurchaseExpense $invoice){
        if($invoice->status == 0) {
            $purchase = $invoice->purchase;
            $supplier = $invoice->supplier;
            DB::beginTransaction();
            $invoice->status = 1;
            $invoice->posted_by = auth()->id();
            if ($invoice->save()) {
                if (
                    CostPrice::additionalInvoiceCostPrice($invoice->purchase_id, $invoice->id, TRANSACTION_TYPE_GRN, 'A')['status']
                ) {
                    $action = "Posted additional invoice with reference $invoice->reference for supplier: " . $supplier->name;
                    AuditLog::auditLog(Auth::id(), $action);
                    DB::commit();
                    session()->flash('app_message', 'Additional invoice posted successfully');
                } else
                    DB::rollback();

            }
        }
        return back();
    }

    public function reverse(PurchaseExpense $invoice) {
        $invoice->status = 0;
        $invoice->updated_by = auth()->id();
        DB::beginTransaction();
        if($invoice->save()){
            if(CostPrice::additionalInvoiceCostPriceReversal($invoice->purchase_id,$invoice->id,TRANSACTION_TYPE_GRN, 'A')['status']){
                $action = "Invoice reversed : " . $invoice->reference;
                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Invoice reversed successfully');
                DB::commit();
            }else{
                DB::rollBack();
                session()->flash('app_message', 'Something went wrong.');
            }
        }
        return back();
    }

    public function print(PurchaseExpense $invoice){
        return view('pages.inventories.expenses.print',compact('invoice'));
    }
}
