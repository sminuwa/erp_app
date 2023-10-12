<?php

namespace App\Http\Livewire;

use App\Classes\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Journal extends Component
{

    public $hello = 'Hello world 4';
    public $records;

    public function render()
    {
        return view('livewire.journal');
    }


    public function post(\App\Models\Journal $journal){
        DB::beginTransaction();
        $journal->status = 1;
        if($journal->save()){
            if(Transaction::journal('','','','','')) {
                DB::commit();
                return back()->with('success', 'Reversed successfully');
            }
            else
                DB::rollback();
            return back()->with('error', 'Something went wrong.');
        }
    }
}
