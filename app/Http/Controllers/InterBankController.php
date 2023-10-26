<?php

namespace App\Http\Controllers;

use App\Classes\Transaction;
use App\Models\AuditLog;
use App\Models\GeneralAccount;
use App\Models\InterBank;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $interbank_id = $request->interbank_id;
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
        $interbank = InterBank::find($interbank_id);
        if(!$interbank)

        DB::beginTransaction();
        try {
            if(!$interbank) {
                $interbank = new InterBank();
                $interbank->reference = $reference;
                $interbank->created_by = auth()->id();
            }else{
                $interbank->updated_by = auth()->id();
            }
            $interbank->amount = $amount;
            $interbank->date = $date;

            $interbank->description = $description;
            $interbank->source_account_id = $source_account_id;
            $interbank->destination_account_id = $destination_account_id;
            $interbank->branch_id = $user_branch;
            $interbank->status = 0;
            if($interbank->save()) {
//                if(Transaction::interbank($source_account_id, 'GeneralAccount', $destination_account_id, 'GeneralAccount', $amount, $reference, $date)) {
                    $action = "Posted payment of $amount for : " . $reference;
                    AuditLog::auditLog(auth()->id(), $action);
                    session()->flash('app_message', 'Transfered  successfully');
                    DB::commit();
//                } else {
//                    DB::rollBack();
//                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('app_error', 'Failed to make payment');
            throw $e;
        }

        return redirect()->back()->with(['prev_id' => $ledger_id]);
    }

    public function post(InterBank $interbank) {
        $interbank->status = 1;
        $interbank->posted_by = auth()->id();
        DB::beginTransaction();
        if($interbank->save()){
            if(Transaction::interbank( $interbank->destination_account_id, 'GeneralAccount',$interbank->source_account_id, 'GeneralAccount', $interbank->amount, $interbank->reference, $interbank->date)){
                $action = "Made payment of $interbank->amount for : " . $interbank->receipt_no;
                AuditLog::auditLog(auth()->id(), $action);
                session()->flash('app_message', 'Payment generated successfully');
                DB::commit();
            }else {
                DB::rollBack();
                session()->flash('app_message', 'Something went wrong');
            }
        }
        return back();
    }

    public function reverse(InterBank $interbank) {
        $interbank->status = 0;
        $interbank->updated_by = auth()->id();
        if($interbank->save()){
            Transaction::reversal($interbank->reference);
        }
        return back();
    }

    public function delete(InterBank $interbank){
        $interbank->delete();
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
