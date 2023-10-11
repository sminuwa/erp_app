<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use App\Models\GeneralAccount;
use App\Models\JournalItem;
use Livewire\Component;


class CreateJournal extends Component
{


    public $accounts;
    public $customers, $suppliers, $gls;
    public $journal_date, $description;
    public $type, $account, $debit, $credit, $desc, $result;
    public $total_credit = 0, $total_debit = 0;
    public $updateMode = false;
    public $inputs = [], $items =  [];
    public $i = 1;

    protected $listeners = ['accounts' => 'changeTypeEvent'];

    public function add()
    {
        $this->inputs[] = '';
        $this->debit[] = 0.00;
        $this->credit[] = 0.00;
    }

    public function remove($i)
    {
        unset($this->inputs[$i]);
    }

    public function render()
    {
        $this->customers = Customer::where('branch_id', auth()->user()->branch->id)->orderBy('code', 'asc')->get();
        $this->suppliers = Supplier::orderBy('code', 'asc')->get();
        $this->gls = GeneralAccount::orderBy('number', 'asc')->get();

        return view('livewire.create-journal');
    }

    public function totals(){
        try{
            $this->total_credit = $this->total_debit = 0;
            foreach ($this->credit as $key => $val) {
                $this->total_credit += intval($this->credit[$key]);
                $this->total_debit += intval($this->debit[$key]);
            }
        }catch (\Exception $e){
            session()->flash('error', $e->getMessage());
        }
    }

    public function changeTypeEvent($value)
    {
        $this->result = $value;
//        foreach ($this->type as $key => $val) {
            if ($this->type[$value] == 'Customer')
                $this->accounts[$value] = $this->customers;
            if ($this->type[$value]  == 'Supplier')
                $this->accounts[$value] = $this->suppliers;
            if ($this->type[$value]  == 'GeneralAccount')
                $this->accounts[$value] = $this->gls;
//        }
    }

    private function resetInputFields(){
        $this->journal_date = '';
        $this->description = '';
        $this->type = '';
        $this->account = '';
        $this->credit = '';
        $this->debit = '';
        $this->desc = '';
    }

    public function store()
    {
        try{
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
            DB::beginTransaction();
            $journal = new \App\Models\Journal();
            $journal->reference = \App\Models\Journal::generateNewNumber();
            $journal->description = $this->description;
            $journal->date = $this->journal_date;
            $journal->created_by = auth()->id();
            if($journal->save()){
                foreach ($this->inputs as $key => $value) {
                    $this->items[] = [
                        'journal_id' =>$journal->id,
                        'account_type' =>$this->type[$key],
                        'account_id' =>$this->account[$key],
                        'credit' =>$this->credit[$key],
                        'debit' =>$this->debit[$key],
                        'description' =>$this->desc[$key] ?? null,
                    ];
                }
            }
            if(JournalItem::upsert($this->items, ['journal_id', 'account_id'])){
                DB::commit();
                $this->inputs = [];
                $this->resetInputFields();
            }else{
                DB::rollback();
            }
            session()->flash('message', 'Journal created Successfully.');
            return $this->redirect(route('journal.index'));
        }catch (\Exception $e){
            session()->flash('error', $e->getMessage());
        }

    }
}
