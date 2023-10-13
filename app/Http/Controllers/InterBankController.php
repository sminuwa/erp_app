<?php

namespace App\Http\Controllers;

use App\Classes\Transaction;
use App\Models\AuditLog;
use App\Models\GeneralAccount;
use App\Models\InterBank;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class InterBankController extends Controller
{
    public function list(Request $request)
    {
        $user_branch = User::userBranchAction();
        $interbanks = InterBank::select('inter_banks.*')
            ->where('branch_id', 'LIKE', $user_branch)
            ->orderBy('reference', 'DESC')->take(10)->get();
        return view('pages.interbanks.list', ['interbanks' => $interbanks]);
    }

    public function create(Request $request)
    {
        $user_branch = User::userBranchAction();
        $accounts = GeneralAccount::whereIn('class', ['A11','A12','A13'])->orderBy('number')->get();
        $model = new InterBank;
        return view('pages.interbanks.create', compact('accounts', 'model'));
    }
    public function store(Request $request)
    {
        $amount = $request->amount;
        $source_account_id = $request->source_account_id;
        $destination_account_id = $request->destination_account_id;
        $reference = InterBank::generateNewNumber();
        $date = $request->payment_date;
        $description = $request->payment_ref;
        $user_branch = User::userBranchAction();
        $status = false;
        $insert_id = 0;
        $ledger_id = 0;

        DB::beginTransaction();
        try {
            $supplier_id = $request->payer_id;
            $status = Transaction::interbank($source_account_id, 'GeneralAccount', $destination_account_id, 'GeneralAccount', $amount, $reference, $date);
            $ledger_id = DB::table('inter_banks')->insertGetId([
                'amount' => $amount,
                'date' => $date,
                'reference' => $reference,
                'description' => $description,
                'recieved_by' => auth()->id(),
                'source_account_id' => $source_account_id,
                'destination_account_id' => $destination_account_id,
                'branch_id' => $user_branch,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::commit();
            if ($status == true) {
                $action = "Posted payment of $amount for : " . $reference;
                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Transfered  successfully');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Failed to make payment');
            throw $e;
        }

        return redirect()->back()->with(['prev_id' => $ledger_id]);
    }

    public function reverse(InterBank $interbank) {
        $interbank->status = 0;
        $interbank->reversed_by = auth()->id();
        if($interbank->save()){
            Transaction::reversal($interbank->reference);
        }
        return back();
    }

    public function print(InterBank $interbank)
    {
        return view('pages.interbanks.print', ['interbank' => $interbank, 'setting' => Setting::first()]);
    }
    public function printPoS(InterBank $interbank)
    {
        return view('pages.interbanks.print_pos', ['interbank' => $interbank, 'setting' => Setting::first()]);
    }
    public function edit(Request $request, InterBank $interbank)
    {

        $accounts = GeneralAccount::orderBy('number', 'asc')->whereIn('class', ['A11','A12','A13'])->get();
        return view('pages.interbanks.create', [
            'model' => $interbank,
            'accounts' => $accounts
        ]);
    }
    public function update(Update $request, Branch $branch)
    {

    }
    public function destroy(Request $request, InterBank $interBank)
    {
        if ($interBank->delete()) {
            AuditLog::auditLog(Auth::id(), "Deleted interbank transfer: " . $interBank->receipt_no);
            session()->flash('app_message', 'Record successfully deleted');
        } else {
            session()->flash('app_error', 'Error occurred while deleting Branch');
        }

        return redirect()->back();
    }
    public function generateReceiptNo()
    {
        $invoice = DB::table('general_account_ledgers')->select(DB::raw('MAX(SUBSTR(receipt_no,8,17)) as max'))->where(DB::raw('SUBSTR(receipt_no,1,3)'), '=', 'RCT')->where(DB::raw('YEAR(created_at)'), '=', date('Y'))->first();
        $number = $invoice == null ? 1 : $invoice->max + 1;
        return 'ITB' . date('y') . str_pad($number, 10, "0", STR_PAD_LEFT);
    }
}
