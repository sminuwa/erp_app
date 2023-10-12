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
        if($journal->save()){
//            if(Transaction::journal('','','','','')) {
                DB::commit();
                return back()->with('success', 'Reversed successfully');
//            }
//            else
//                DB::rollback();
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function reverse(Journal $journal) {
        DB::beginTransaction();
        $journal->status = 0;
        if($journal->save()){
           if(Transaction::reversal($journal->reference)) {
               DB::commit();
               return back()->with('success', 'Reversed successfully');
           }
            else
               DB::rollback();
            return back()->with('error', 'Something went wrong.');
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
