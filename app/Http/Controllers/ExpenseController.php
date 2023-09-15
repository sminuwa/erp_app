<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\PaymentMode;
use App\Models\BankAccount;
use App\Models\ExpenseItem;
use Illuminate\Support\Facades\DB;
use Yoeunes\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;


/**
 * Description of ExpenseController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        \Cart::clear();
        $records = Expense::select('impress', 'date', 'expenses.id', 'expenses.status', 'payment_mode', DB::raw('SUM(amount) AS amount'), 'account_name', 'account_no')
            ->join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('date', 'DESC')->groupBy('impress')
            ->take(10)->get();
        return view('pages.expenses.index', ['records' => $records]);
    }
    public function search(Request $request)
    {
        $search_value = $request->refno;

        $records = Expense::select('impress', 'date', 'expenses.id', 'expenses.status', 'payment_mode', DB::raw('SUM(amount) AS amount'), 'account_name', 'account_no')
            ->join('bank_accounts', 'bank_accounts.id', 'expenses.bank_account_id')
            ->where('bank_accounts.branch_id', 'LIKE', User::userBranchAction())
            ->where('impress', 'LIKE', "%$search_value%")
            ->orderBy('date', 'DESC')->groupBy('impress')
            ->get();
        return view('pages.expenses.index', ['records' => $records]);
    }
    /**
     * Display the specified resource.
     *
     * @param  Request  $request
     * @param  Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Expense $expense)
    {
        if (\Cart::isEmpty())
            $this->loadToCart($expense->impress);
        return view('pages.expenses.show', [
            'record' => $expense,
            'cart_expenses' => \Cart::getContent(),
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Request  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Request $request)
    {

        $bank_acoounts = BankAccount::where('branch_id', 'LIKE', User::userBranchAction());
        $expense_items = ExpenseItem::all(['id', 'name']);
        $cart_expenses = \Cart::getContent();
        return view('pages.expenses.create', [
            'model' => new Expense,
            'bank_accounts' => $bank_acoounts,
            'expense_items' => $expense_items,
            'cart_expenses' => $cart_expenses,
            'impress' => $this->generateImpress(),
        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Request  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Request $request)
    {
        $model = new Expense;
        $expenses = \Cart::getContent();

        $month = date('m', \strtotime($request->date));
        $year = date('Y', \strtotime($request->date));
        $day = date('d', \strtotime($request->date));
        $bank_account_id = $request->bank_account_id;
        if ($request->has('payment_mode') and $request->payment_mode == "Cash")
            $bank_account_id = BankAccount::where('account_type', 'Cash')->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;

        $payment_mode = $request->payment_mode;
        DB::beginTransaction();
        try {
            //$model->fill($request->all());
            foreach ($expenses as $expense) {
                DB::table('expenses')->insertGetId(['expense_item_id' => $expense->id,
                    'captured_by' => Auth::id(),
                    'amount' => $expense->price,
                    'month' => $month,
                    'year' => $year,
                    'day' => $day,
                    'payment_mode' => $payment_mode,
                    'impress' => $request->impress,
                    'bank_account_id' => $bank_account_id,
                    'reason' => $expense->attributes['reason'],
                    'date' => $request->date,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            $total = \Cart::getTotal();
            BankAccount::where('id', $bank_account_id)->decrement('account_balance', $total);
            //Bank Withdrawal
            DB::table('bank_transactions')->insert(['bank_account_id' => $bank_account_id,
                'trans_date' => $request->date,
                'cr' => 0,
                'dr' => $total,
                'ref_no' => $request->impress,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $action = "Captured expenses of $request->impress: $total ";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Expense saved successfully');
            \Cart::clear();
            return redirect()->route('expenses.index');

        }
        catch (\Exception $ex) {
            session()->flash('app_error', 'Something is wrong while saving Expense');
            DB::rollBack();
            throw $ex;
        }
        \Cart::clear();
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Request  $request
  * @param  Expense  $expense
  * @return \Illuminate\Http\Response
  */
    public function edit(Request $request, Expense $expense)
    {
        if (\Cart::isEmpty())
            $this->loadToCart($expense->impress);
        $bank_acoounts = BankAccount::where('branch_id', 'LIKE', User::userBranchAction());
        $expense_items = ExpenseItem::all(['id', 'name']);
        return view('pages.expenses.edit', [
            'model' => $expense,
            'bank_accounts' => $bank_acoounts,
            'expense_items' => $expense_items,
            'cart_expenses' => \Cart::getContent(),
        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Request  $request
  * @param  Expense  $expense
  * @return \Illuminate\Http\Response
  */
    public function update(Request $request, Expense $expense)
    {

        $month = date('m', \strtotime($request->date));
        $year = date('Y', \strtotime($request->date));
        $day = date('d', \strtotime($request->date));

        $amount = 0;
        $total = \Cart::getTotal();
        $expenses = \Cart::getContent();
        $bank_account_id = $request->bank_account_id;
        if ($request->has('payment_mode') and $request->payment_mode == "Cash")
            $bank_account_id = BankAccount::where('account_type', 'Cash')->where('branch_id', 'LIKE', User::userBranchAction())->first()->id;
        $payment_mode = $request->payment_mode;
        $amount = $total;
        DB::beginTransaction();
        try {
            DB::table('expenses')->where('impress', $request->impress)->delete();
            foreach ($expenses as $expense) {
                DB::table('expenses')->insertGetId(['expense_item_id' => $expense->id,
                    'captured_by' => Auth::id(),
                    'amount' => $expense->price,
                    'month' => $month,
                    'year' => $year,
                    'day' => $day,
                    'payment_mode' => $payment_mode,
                    'impress' => $request->impress,
                    'bank_account_id' => $bank_account_id,
                    'reason' => $expense->attributes['reason'],
                    'date' => $request->date,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }


            //Undo the previus expense
            BankAccount::where('id', $request->bank_account_id)->increment('account_balance', $amount);
            //Capture the new modified expense
            BankAccount::where('id', $request->bank_account_id)->decrement('account_balance', $amount);
            DB::table('bank_transactions')->updateOrInsert(['ref_no' => $request->impress], ['bank_account_id' => $bank_account_id,
                'trans_date' => $request->date,
                'cr' => 0,
                'dr' => $amount,
                'updated_at' => Carbon::now(),
            ]);
            $action = "Modified expenses of $request->impress: $amount ";
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Expense saved successfully');
            \Cart::clear();
            return redirect()->route('expenses.index');
        }
        catch (\Exception $ex) {
            session()->flash('app_error', 'Something is wrong while saving Expense');
            DB::rollBack();
            throw $ex;
        }
        \Cart::clear();
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Request  $request
  * @param  Expense  $expense
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Request $request, Expense $expense)
    {

        DB::beginTransaction();
        try {
            $total = $expense->where('impress', $expense->impress)->sum('amount');
            //$expense->status = 'Cancelled';
            BankAccount::where('id', $expense->bank_account_id)->increment('account_balance', $total);
            DB::table('bank_transactions')->where(['ref_no' => $expense->impress])->delete();
            $expense->where('impress', $expense->impress)->delete();
            DB::commit();
            if ($expense->save()) {
                $action = "Deleted expenses of $expense->impress: $total ";
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Expense cancelled successfully');
                return redirect()->route('expenses.index');
            }
            else {
                session()->flash('app_error', 'Something is wrong while saving Expense');
            }
        }
        catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

        return redirect()->back();
    }
    public function today_expense()
    {
        $today = date('Y-m-d');
        $expenses = Expense::latest()
        ->join('bank_accounts','bank_accounts.id','expenses.bank_account_id')
        ->where('bank_accounts.branch_id','LIKE',User::userBranchAction())
        ->where('date', $today)->get();
        return view('pages.expenses.today', compact('expenses'));
    }

    public function month_expense($month = null)
    {
        if ($month == null) {
            $month = date('F');
        }
        $expenses = Expense::latest()
        ->where('month', $this->monthValue(ucfirst($month)))
        ->join('bank_accounts','bank_accounts.id','expenses.bank_account_id')
        ->where('bank_accounts.branch_id','LIKE',User::userBranchAction())
        ->get();
        return view('pages.expenses.month', compact('expenses', 'month'));
    }

    public function yearly_expense($year = null)
    {
        if ($year == null) {
            $year = date('Y');
        }
        $expenses = Expense::latest()
        ->join('bank_accounts','bank_accounts.id','expenses.bank_account_id')
        ->where('bank_accounts.branch_id','LIKE',User::userBranchAction())
        ->where('year', $year)->get();
        $years = Expense::select('year')->distinct()->take(12)->get();
        return view('pages.expenses.year', compact('expenses', 'year', 'years'));
    }
    public function monthValue($month)
    {
        switch ($month) {
            case 'January':
                return "1";
            case 'Fabruary':
                return "2";
            case 'March':
                return "3";
            case 'April':
                return "4";
            case 'May':
                return "5";
            case 'June':
                return "6";
            case 'July':
                return "7";
            case 'August':
                return "8";
            case 'September':
                return "9";
            case 'October':
                return "10";
            case 'November':
                return "11";
            case 'December':
                return "12";
            default:
                return "-1";
        }
    }
    public function addToCart(Request $request)
    {
        //return $request;
        $validated = $request->validate([
            'expense_item_id' => 'required',
            'amount' => 'required',
            'reason' => 'required',
        ]);
        $bank_account_id = $request->bank_account_id;
        if (!$request->has('bank_account_id'))
            $bank_account_id = BankAccount::where('account_type', 'Cash')->where('branch_id','LIKE',User::userBranchAction())->first()->id;
        $add = \Cart::add([
            'id' => $request->expense_item_id,
            'name' => ExpenseItem::find($request->expense_item_id)->name,
            'price' => $request->amount,
            'quantity' => 1,
            'attributes' => array('payment_mode' => $request->payment_mode, 'reason' => $request->reason, 'bank_account_id' => $bank_account_id),
        ]);
        //dd(\Cart::getContent());
        if ($add) {
            session()->flash('app_message', 'Expense is Added to Cart Successfully !');
            return redirect()->back()->withInput();
        }
        else {

            session()->flash('app_error', 'Expense not added to cart');
            return redirect()->back()->withInput();
        }
    }
    public function removeCart(Request $request, $id)
    {

        \Cart::remove($id);
        session()->flash('app_message', 'Item Cart Remove Successfully !');

        return redirect()->back();
    }

    public function clearAllCart()
    {
        \Cart::clear();

        session()->flash('app_message', 'All Item Cart Clear Successfully !');

        return redirect()->back();
    }
    public function loadToCart($impress)
    {
        \Cart::clear();
        foreach (Expense::where('impress', $impress)->get() as $data) {
            \Cart::add([
                'id' => $data->id,
                'name' => $data->item->name,
                'price' => $data->amount,
                'quantity' => 1,
                'attributes' => array('reason' => $data->reason),
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
                'value' => 1
            ],
            'price' => $request->amount,
            'attributes' => array('reason' => $request->reason),
        ]
        );

        session()->flash('success', 'Item Cart is Updated Successfully !');

        return redirect()->back();
    }
    public function generateImpress()
    {
        $invoice = DB::table('expenses')->select(DB::raw('MAX(SUBSTR(impress,7,10)) as max'))->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->where(DB::raw('MONTH(created_at)'), '=', date('m'))->first();

        return 'EXP' . date('y') . date('m') . str_pad((intval($invoice->max) + 1), 3, "0", STR_PAD_LEFT);
    }
}
