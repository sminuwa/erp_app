<?php

namespace App\Http\Controllers;

use App\Classes\Transaction;
use App\Models\Journal;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    //
    public function index(){
        $journals = Journal::orderBy('id', 'desc')->get();
        return view('pages.journals.index', compact('journals'));
    }

    public function store(Request $request){

    }

    public function create(Request $request){

        return view('pages.journals.create');
    }

    public function show(Journal $journal){
        return view('pages.journals.show', compact('journal'));
    }

    public function post(Journal $journal){
        DB::beginTransaction();
        $journal->status = 1;
        $journal->posted_by = auth()->id();
        $account_details = [];
        $debit = $credit = 0;
        foreach($journal->items as $item){
            $debit += intval($item->debit);
            $credit += intval($item->credit);
            $account_details[] = [
                'account_id' => $item->account_id,
                'account_type' => $item->account_type,
                'debit' => $item->debit,
                'credit' => $item->credit,
            ];
        }
        if(($debit - $credit) != 0)
            return back()->with('error', 'Cannot post journal. Make sure total debit and credit are equal.');
//        return Transaction::journal($account_details,$journal->reference,$journal->date);
        if($journal->save()){

            if(Transaction::journal($account_details,$journal->reference,$journal->date)) {
                DB::commit();
                return back()->with('success', 'Reversed successfully');
            }
            else
                DB::rollback();
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function reverse(Journal $journal) {
        try{
            DB::beginTransaction();
            $journal->status = 0;
            $journal->updated_by = auth()->id();
            if($journal->save()){
                if(Transaction::reversal($journal->reference)['status']) {
                    DB::commit();
                    return back()->with('success', 'Reversed successfully');
                }
                else
                    DB::rollback();
                return back()->with('error', 'Something went wrong.');
            }
        }catch (\Exception $e){
            return back()->with('error', 'Something went wrong. '.$e->getMessage());
        }
    }

    public function print(Journal $journal)
    {
        return view('pages.journals.print', ['journal' => $journal, 'setting' => Setting::first()]);
    }

    public function edit(Journal $journal){
        return view('pages.journals.edit', compact('journal'));
    }

    public function delete(Journal $journal){
        if($journal->delete()){
            return back()->with('success', 'Reversed successfully');
        }
        return back()->with('error', 'Something went wrong.');
    }



}
