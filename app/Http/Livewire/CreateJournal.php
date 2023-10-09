<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Models\Supplier;
use Livewire\Attributes\Rule;
use App\Models\GeneralAccount;
use App\Models\JournalItem;
use Livewire\Component;


class CreateJournal extends Component
{


    public $accounts;
    public $type, $account, $debit, $credit, $description, $result;
    public $updateMode = false;
    public $inputs = [];
    public $i = 1;

    protected $listeners = ['accounts' => 'changeTypeEvent'];

    public function add($i)
    {
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs ,$i);
        $this->debit[$i] = 0.00;
        $this->credit[$i] = 0.00;
        $this->accounts[$i] = Customer::where('branch_id', auth()->user()->branch->id)->orderBy('code', 'asc')->get();
    }

    public function remove($i)
    {
        unset($this->inputs[$i]);
    }

    public function render()
    {
//        $this->accounts = GeneralAccount::all();
        return view('livewire.create-journal');
    }

    public function changeTypeEvent($value)
    {
        foreach ($this->type as $key => $val) {
            if ($this->type[$key] == 'Customer')
                $this->accounts[$key] = Customer::where('branch_id', auth()->user()->branch->id)->orderBy('code', 'asc')->get();
            if ($this->type[$key]  == 'Supplier')
                $this->accounts[$key] = Supplier::orderBy('code', 'asc')->get();
            if ($this->type[$key]  == 'GeneralAccount')
                $this->accounts[$key] = GeneralAccount::orderBy('number', 'asc')->get();
        }
    }

    private function resetInputFields(){
        $this->type = '';
        $this->account = '';
        $this->credit = '';
        $this->debit = '';
    }

    public function store()
    {
        $validatedDate = $this->validate([
            'type.*' => ['required'],
            'account.*' => ['required'],
            'debit.*' => ['required'],
            'credit.*' => ['required'],
            ],
            [
                'type.*' => 'Type is required',
                'account.*' => 'account field is required',
                'debit.*' => 'Debit field is required',
                'credit.*' => 'credit field is required',
            ]
        );

        $journal = new \App\Models\Journal();
        $journal->reference = \App\Models\Journal::generateNewNumber();
        $journal->description = $this->description;
        if($journal->save()){
            foreach ($this->type as $key => $value) {
                JournalItem::create([
                    'journal_id' =>$journal->id,
                    'account_type' =>$this->type[$key],
                    'account_id' =>$this->account[$key],
                    'credit' =>$this->credit[$key],
                    'debit' =>$this->debit[$key],
                ]);
            }
        }

        $this->inputs = [];
        $this->resetInputFields();

        session()->flash('message', 'Journal created Successfully.');
//        return $this->redirect(route('journal.index'))->with('message', 'Journal created Successfully.');
    }
}
