<?php

namespace App\Http\Controllers;

use App\Imports\SupplierImport;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Http\Requests\Suppliers\Index;
use App\Http\Requests\Suppliers\Show;
use App\Http\Requests\Suppliers\Create;
use App\Http\Requests\Suppliers\Store;
use App\Http\Requests\Suppliers\Edit;
use App\Http\Requests\Suppliers\Update;
use App\Http\Requests\Suppliers\Destroy;
use App\Models\Bank;
use Illuminate\Support\Facades\DB;
use App\Models\BankAccount;
use App\Models\PaymentMode;
use App\Models\SupplierLedger;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\AuditLog;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Description of SupplierController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
//        $user_branch = User::userBranchAction();
        return view('pages.suppliers.index', ['records' => Supplier::orderBy('name')->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  Supplier  $supplier
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, Supplier $supplier)
    {
        return view('pages.suppliers.show', [
            'record' => $supplier,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.suppliers.create', [
            'model' => new Supplier,
            'banks' => Bank::all(['id', 'name']),
            'code' => $this->supplierCode(),

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new Supplier;
        $model->fill($request->all());

        if ($model->save()) {
            $model->branch_id = Auth::user()->branch_id;
            $model->save();
            $action = "Added a new supplier " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Supplier saved successfully');
            if ($request->has('shortcut'))
                return redirect()->back();
            return redirect()->route('suppliers.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving Supplier');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  Supplier  $supplier
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, Supplier $supplier)
    {

        return view('pages.suppliers.edit', [
            'model' => $supplier,
            'banks' => Bank::all(['id', 'name']),
        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  Supplier  $supplier
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, Supplier $supplier)
    {
        $supplier->fill($request->all());

        if ($supplier->save()) {
            $action = "Updated supplier " . $supplier->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Supplier successfully updated');
            return redirect()->route('suppliers.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating Supplier');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  Supplier  $supplier
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, Supplier $supplier)
    {
        if ($supplier->supplierLedgers()->count() == 0) {
            if ($supplier->delete()) {
                $action = "Deleted supplier " . $supplier->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Supplier successfully deleted');
            }
            else {
                session()->flash('app_error', 'Error occurred while deleting Supplier');
            }
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting Supplier');
        }
        return redirect()->back();
    }
    public function supplierCode()
    {
        $no = DB::table('suppliers')->select('id')->orderBy('id', 'DESC')->first();
        if ($no == null)
            $no = 1;
        else {
            $no = $no->id + 1;
        }
        return 'S' . str_pad($no, 3, "0", STR_PAD_LEFT);
    }

    public function runninigBalance($supplier_id)
    {
        return Supplier::find($supplier_id)->opening_balance;
    }

    public function createSupplierPayment()
    {

        $user_branch = User::userBranchAction();
        $bank_accounts = BankAccount::where('branch_id', 'LIKE', $user_branch)->orderBy('account_name')->get();
        $suppliers = Supplier::where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $payment_modes = PaymentMode::orderBy('name')->get();
        $model = new SupplierLedger;
        return view('pages.suppliers.create_supplier_payment', compact('bank_accounts', 'suppliers', 'model', 'payment_modes'));
    }
    public function pay(Request $request)
    {
        if (SupplierLedger::where('teller_no', $request->teller_no)->count('teller_no') > 0) {
            session()->flash('app_error', 'Teller no already exists. Duplicate is not allowed!');
            return back();
        }
        $supplier_id = $request->supplier_id;
        $amount = $request->amount_paid;
        $bank_account_id = $request->bank_account_id;
        $ledger_id = 0;
        if ($request->payment_mode == "Cash") {
            $bank_account_id = BankAccount::where(['account_type' => 'Cash', 'status' => 1])->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        }
        DB::beginTransaction();
        try {
            DB::table('suppliers')->where(['id' => $supplier_id])->decrement('opening_balance', $amount);
            $ledger_id = DB::table('supplier_ledgers')->insertGetId([
                'supplier_id' => $supplier_id,
                'purchase_id' => 0,
                'description' => 'Payment',
                'ref' => $this->generateInvoice(),
                'teller_no' => $request->teller_no,
                'bank_account_id' => $bank_account_id,
                'date' => $request->payment_date,
                'payment_mode' => $request->payment_mode,
                'dr' => $amount,
                'user_id' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            DB::table('bank_accounts')->where('id', $bank_account_id)->decrement('account_balance', $amount);
            //Bank Withdrawal
            DB::table('bank_transactions')->insert(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->payment_date,
                'cr' => 0,
                'dr' => $amount,
                'ref_no' => $request->teller_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $action = "Posted  $amount to supplier ledger: " . Supplier::find($supplier_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Payment captured successfully');
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        $supplier = Supplier::find($supplier_id);

        //return redirect()->route('suppliers.show', $supplier)->with(['record' => Supplier::find($supplier_id)]);
        //return redirect()->route('supplier.payment.print', $ledger_id);
        return redirect()->back()->with(['prev_id' => $ledger_id]);
    }
    public function updatePayment(Request $request, SupplierLedger $ledger)
    {
        $supplier_id = $ledger->supplier_id;
        $amount = $request->amount_paid;
        $bank_account_id = $request->bank_account_id;
        if ($request->payment_mode == "Cash") {
            $bank_account_id = BankAccount::where(['account_type' => 'Cash', 'status' => 1])->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        }
        DB::beginTransaction();
        try {
            DB::table('suppliers')->where(['id' => $supplier_id])->increment('opening_balance', $ledger->dr);
            DB::table('suppliers')->where(['id' => $supplier_id])->decrement('opening_balance', $amount);

            DB::table('bank_accounts')->where('id', $bank_account_id)->increment('account_balance', $ledger->dr);
            DB::table('bank_accounts')->where('id', $bank_account_id)->decrement('account_balance', $amount);
            //Bank Withdrawal
            DB::table('bank_transactions')->where(['ref_no' => $ledger->teller_no, 'bank_account_id' => $ledger->bank_account_id])->update(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->payment_date,
                'cr' => 0,
                'dr' => $amount,
                'ref_no' => $request->teller_no,
                'updated_at' => Carbon::now(),
            ]);
            DB::table('supplier_ledgers')->where('id', $ledger->id)->update([
                'teller_no' => $request->teller_no,
                'bank_account_id' => $bank_account_id,
                'date' => $request->payment_date,
                'payment_mode' => $request->payment_mode,
                'dr' => $amount,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $action = "Modified payment of  $amount to supplier ledger: " . Supplier::find($supplier_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Payment updated successfully');
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Payment failed');
            throw $e;
        }
        return redirect()->route('suppliers.payments');
    }
    public function generateInvoice()
    {
        $invoice = DB::table('supplier_ledgers')->select(DB::raw('MAX(SUBSTR(ref,7,10)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where(DB::raw('MONTH(created_at)'), '=', date('m'))->where('dr', '>', 0)->where('cr', '=', 0)->where('payment_mode', '<>', 'Credit')->first();
        return 'PC' . date('y') . date('m') . str_pad(($invoice->max + 1), 4, "0", STR_PAD_LEFT);
    }
    public function generateCreditNoteInvoice()
    {
        $invoice = DB::table('supplier_ledgers')->select(DB::raw('MAX(SUBSTR(ref,7,10)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where(DB::raw('MONTH(created_at)'), '=', date('m'))->where('dr', '>', 0)->where('cr', '=', 0)->where('payment_mode', 'Credit')->first();
        return 'CN' . date('y') . date('m') . str_pad(($invoice->max + 1), 4, "0", STR_PAD_LEFT);
    }

    public function loadLedger(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $supplier_id = $request->supplier_id;
        $supplier = Supplier::find($request->supplier_id);

        $query = SupplierLedger::where('supplier_id', $supplier_id)->where('payment_mode', '<>', 'Credit Note')->whereBetween('date', [$from_date, $to_date]);
        $sum_cr_b_d = SupplierLedger::where('supplier_id', $supplier_id)->where('payment_mode', '<>', 'Credit Note')->where('date', '<', $from_date)->sum('cr');
        $sum_dr_b_d = SupplierLedger::where('supplier_id', $supplier_id)->where('payment_mode', '<>', 'Credit Note')->where('date', '<', $from_date)->sum('dr');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d; //balance before date
        $ledgers = $query->orderBy('date')->get();

        return view('pages.suppliers.load_ledger', compact('ledgers', 'supplier', 'from_date', 'to_date', 'balance_b_d','sum_cr_b_d','sum_dr_b_d'));
    }
    public function loadGeneralSupplierLedger(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $supplier_id = $request->supplier_id;
        $supplier = Supplier::find($supplier_id);
        $user_branch = User::userBranchAction();
        if ($supplier_id == 'all') {
            $supplier_id = '%';

        }
        if ($request->has('print')) {
            return $this->printLedger($from_date, $to_date, $supplier_id);
        }
        $query = SupplierLedger::where('supplier_id', 'LIKE', $supplier_id)
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('payment_mode', '<>', 'Credit Note')
            ->where('branch_id', 'LIKE', $user_branch)
            ->whereBetween('date', [$from_date, $to_date]);
        $sum_cr_b_d = SupplierLedger::where('supplier_id', $supplier_id)->where('payment_mode', '<>', 'Credit Note')->where('date', '<', $from_date)->sum('cr');
        $sum_dr_b_d = SupplierLedger::where('supplier_id', $supplier_id)->where('payment_mode', '<>', 'Credit Note')->where('date', '<', $from_date)->sum('dr');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d; //balance before date
        $ledgers = $query->orderBy('date')->get();
        return view('pages.suppliers.load_general_ledger', compact('ledgers', 'from_date', 'to_date', 'balance_b_d', 'supplier','sum_cr_b_d','sum_dr_b_d'));
    }
    public function generateSupplierLedger()
    {
        return view('pages.suppliers.general_ledger', [
            'suppliers' => Supplier::orderBy('name')->get()
        ]);
    }
    public function printLedger($from_date, $to_date, $supplier_id)
    {
        $supplier = Supplier::find($supplier_id);
        $user_branch = User::userBranchAction();
        //$query = SupplierLedger::where('supplier_id', $supplier_id)->where('payment_mode', '<>', 'Credit Note')->whereBetween('date', [$from_date, $to_date]);
        $query = SupplierLedger::where('supplier_id', 'LIKE', $supplier_id)
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('payment_mode', '<>', 'Credit Note')
            ->where('branch_id', 'LIKE', $user_branch)
            ->whereBetween('date', [$from_date, $to_date]);
        $sum_cr_b_d = SupplierLedger::where('supplier_id', $supplier_id)->where('payment_mode', '<>', 'Credit Note')->where('date', '<', $from_date)->sum('cr');
        $sum_dr_b_d = SupplierLedger::where('supplier_id', $supplier_id)->where('payment_mode', '<>', 'Credit Note')->where('date', '<', $from_date)->sum('dr');
        $balance_b_d = $sum_cr_b_d - $sum_dr_b_d; //balance before date
        $ledgers = $query->orderBy('date')->get();

        return view('pages.suppliers.print_ledger', compact('ledgers', 'supplier', 'from_date', 'to_date', 'balance_b_d','sum_cr_b_d','sum_dr_b_d'));
    }
    public function supplierPayments()
    {
        $payments = SupplierLedger::select('supplier_ledgers.*')
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('dr', '>', 0)
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('date', 'DESC')->take(10)->get();
        $accounts = BankAccount::where('branch_id', 'LIKE', User::userBranchAction());
        return view('pages.suppliers.supplier_payment', ['payments' => $payments, 'accounts' => $accounts]);
    }
    public function searchPayment(Request $request)
    {
        $search_value = $request->refno;
        $payments = SupplierLedger::select('supplier_ledgers.*')
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->where('teller_no', 'LIKE', "%$search_value%")
            ->orWhere('Ref', 'LIKE', "%$search_value%")
            ->orderBy('Ref', 'DESC')->get();
        $accounts = BankAccount::where('branch_id', 'LIKE', User::userBranchAction());
        return view('pages.suppliers.supplier_payment', ['payments' => $payments, 'accounts' => $accounts]);
    }
    public function printPaymentReceipt(SupplierLedger $payment)
    {
        return view('pages.suppliers.print_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function printPoSPaymentReceipt(SupplierLedger $payment)
    {
        return view('pages.suppliers.print_pos_payment_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function deletePayment(Request $request, SupplierLedger $ledger)
    {
        $supplier_id = $ledger->supplier_id;
        $bank_account_id = $ledger->bank_account_id;
        if ($ledger->payment_mode == "Cash") {
            $bank_account_id = BankAccount::where(['account_type' => 'Cash', 'status' => 1])->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        }
        DB::beginTransaction();
        try {
            DB::table('suppliers')->where(['id' => $supplier_id])->increment('opening_balance', $ledger->dr);

            DB::table('bank_accounts')->where('id', $bank_account_id)->increment('account_balance', $ledger->dr);

            DB::table('supplier_ledgers')->where('id', $ledger->id)->delete();
            //Bank Withdrawal
            DB::table('bank_transactions')->where(['ref_no' => $ledger->teller_no, 'bank_account_id' => $bank_account_id])->delete();
            session()->flash('app_message', 'Payment deleted successfully');
            $action = "Deleted payment of  $ledger->teller_no for supplier: " . Supplier::find($supplier_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Payment cannot be deleted');
            throw $e;
        }
        return redirect()->route('suppliers.payments');
    }
    public function supplierCreditNote()
    {
        $payments = SupplierLedger::where('payment_mode', '=', 'Credit Note')
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->orderBy('date', 'DESC')->take(10)->get();
        return view('pages.suppliers.credit_note', ['payments' => $payments, 'categories' => Category::orderBy('name')->get()]);
    }
    public function createCreditNote()
    {
        $user_branch = User::userBranchAction();
        $suppliers = Supplier::where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $receipt_no = $this->generateCreditNoteInvoice();
        $model = new SupplierLedger;
        return view('pages.suppliers.create_credit_note', compact('categories', 'suppliers', 'model', 'receipt_no'));
    }
    public function payCreditNote(Request $request)
    {
        if (SupplierLedger::where('teller_no', $request->teller_no)->count('teller_no') > 0) {
            session()->flash('app_error', 'Teller no already exists. Duplicate is not allowed!');
            return back();
        }
        $supplier_id = $request->supplier_id;
        $amount = $request->amount_paid;
        $bank_account_id = $request->group_id; // This will be use as bank_account_id
        $ledger_id = 0;

        DB::beginTransaction();
        try {
            DB::table('suppliers')->where(['id' => $supplier_id])->decrement('opening_balance', $amount);
            $ledger_id = DB::table('supplier_ledgers')->insertGetId([
                'supplier_id' => $supplier_id,
                'purchase_id' => 0,
                'description' => 'Credit note',
                'ref' => $this->generateCreditNoteInvoice(),
                'teller_no' => $request->teller_no,
                'bank_account_id' => $bank_account_id,
                'date' => $request->payment_date,
                'payment_mode' => "Credit Note",
                'dr' => $amount,
                'user_id' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            //Bank Withdrawal
            DB::table('bank_transactions')->insert(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->payment_date,
                'cr' => 0,
                'dr' => $amount,
                'ref_no' => $request->teller_no,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            session()->flash('app_message', 'Credit note captured successfully');
            $action = "Posted credit note $request->teller_no from supplier: " . Supplier::find($supplier_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        $supplier = Supplier::find($supplier_id);

        //return redirect()->route('suppliers.show', $supplier)->with(['record' => Supplier::find($supplier_id)]);
        return redirect()->route('supplier.credit.note.print', $ledger_id);
    }
    public function updateCreditNote(Request $request, SupplierLedger $ledger)
    {
        $supplier_id = $ledger->supplier_id;
        $amount = $request->amount_paid;
        $bank_account_id = $request->group_id; //This will be used as bank_account_id

        DB::beginTransaction();
        try {
            DB::table('suppliers')->where(['id' => $supplier_id])->increment('opening_balance', $ledger->dr);
            DB::table('suppliers')->where(['id' => $supplier_id])->decrement('opening_balance', $amount);

            //Bank Withdrawal
            DB::table('bank_transactions')->where(['ref_no' => $ledger->teller_no, 'bank_account_id' => $ledger->bank_account_id])->update(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->payment_date,
                'cr' => 0,
                'dr' => $amount,
                'ref_no' => $request->teller_no,
                'updated_at' => Carbon::now(),
            ]);
            DB::table('supplier_ledgers')->where('id', $ledger->id)->update([
                'teller_no' => $request->teller_no,
                'bank_account_id' => $bank_account_id,
                'date' => $request->payment_date,
                'payment_mode' => 'Credit Note',
                'dr' => $amount,
                'updated_at' => Carbon::now()
            ]);
            session()->flash('app_message', 'Credit note updated successfully');
            $action = "Updated credit note $request->teller_no from supplier: " . Supplier::find($supplier_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Credit note failed to update');
            throw $e;
        }
        return redirect()->route('supplier.credit.note.print', $ledger->id);
    }
    public function deleteCreditNote(Request $request, SupplierLedger $ledger)
    {
        $supplier_id = $ledger->supplier_id;
        $bank_account_id = $ledger->bank_account_id;

        DB::beginTransaction();
        try {
            DB::table('suppliers')->where(['id' => $supplier_id])->increment('opening_balance', $ledger->dr);

            DB::table('supplier_ledgers')->where('id', $ledger->id)->delete();
            //Bank Withdrawal
            DB::table('bank_transactions')->where(['ref_no' => $ledger->teller_no, 'bank_account_id' => $bank_account_id])->delete();
            session()->flash('app_message', 'Credit note deleted successfully');
            $action = "Deleted credit note $ledger->teller_no from supplier: " . Supplier::find($supplier_id)->name;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Credit note cannot be deleted');
            throw $e;
        }
        return redirect()->route('suppliers.credit.note');
    }
    public function searchCreditNote(Request $request)
    {
        $search_value = $request->refno;
        $payments = SupplierLedger::where('teller_no', 'LIKE', "%$search_value%")
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->join('suppliers', 'suppliers.id', 'supplier_ledgers.supplier_id')
            ->orWhere('Ref', 'LIKE', "%$search_value%")->orderBy('Ref', 'DESC')->get();
        $categories = Category::orderBy('name')->get();
        return view('pages.suppliers.credit_note', ['payments' => $payments, 'categories' => $categories]);
    }
    public function printCreditnoteReceipt(SupplierLedger $payment)
    {
        return view('pages.suppliers.print_credit_note_receipt', ['payment' => $payment, 'setting' => Setting::first()]);
    }
    public function createOpeningBalance()
    {
        $user_branch = User::userBranchAction();
        $suppliers = Supplier::where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        return view('pages.suppliers.create_opening_balance', [
            'suppliers' => $suppliers,
            'model' => null]);
    }
    public function openingBalanceStore(Request $request)
    {
        $supplier_id = $request->supplier_id;
        $amount = $request->amount;
        Supplier::where(['id' => $supplier_id])->update(['opening_balance' => $amount]);
        $ledger_id = DB::table('supplier_ledgers')->updateOrInsert([
            'supplier_id' => $supplier_id,
            'purchase_id' => 0,
            'description' => 'Opening Balance',
            'ref' => $this->generateInvoice(),
            'teller_no' => Supplier::find($supplier_id)->email,
            'bank_account_id' => 0,
            'date' => date('Y-m-d'),
            'payment_mode' => "Credit",
            'cr' => $amount,
            'user_id' => Auth::id(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $action = "Set opening balance to $amount for supplier: " . Supplier::find($supplier_id)->name;
        AuditLog::auditLog(Auth::id(), $action);
        session()->flash('app_message', 'Supplier opening balance successfully defined');
        return redirect()->route('suppliers.index');
    }
    public function importForm()
    {
        return view('pages.suppliers.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $file = $request->file('file');
        $import = new SupplierImport();
        $rows = Excel::toCollection($import, $file)->first();
        $user_branch = User::userBranchAction();
        $faileds = [];
        $count = 0;
        $data = array();
        try {
            foreach ($rows as $row) {
                Supplier::updateOrInsert(
                    ['code' => $row['code']],
                    [
                        'name' => trim($row['name']),
                        'email' => $row['email'],
                        'phone' => $row['phone'],
                        'address' => $row['address'],
                        'category' => Category::where('code', trim($row['category_code']))->first()->id ?? 0,
                        'branch_id' => Branch::where('code', trim($row['branch_code']))->first()->id ?? 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
                $count++;
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
        //dd($faileds);
        session()->flash('app_message', 'File imported and records updated/inserted successfully!');
        return view('pages.suppliers.import', ['count' => $count]);
    }
}
