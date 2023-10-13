<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\JournalItem;
use App\Models\Supplier;
use Livewire\Component;

class EditJournal extends Component
{

    public $readyToLoad = false;
    public $accounts = [], $accounts_arr = [];
    public $journal;
    public $customers, $suppliers, $gls;
    public $journal_date, $description;
    public $type, $account, $debit, $credit, $desc, $result;
    public $total_credit = 0, $total_debit = 0;
    public $updateMode = false;
    public $inputs = [];
    public $i = 1;

    public function mount()
    {
        $customers = Customer::where('branch_id', auth()->user()->branch->id)->orderBy('code', 'asc')->get();
        $suppliers = Supplier::orderBy('code', 'asc')->get();
        $gls = GeneralAccount::orderBy('number', 'asc')->get();
        $c = $s = $g = [];
        foreach($customers as $customer){
            $c[] = [
                'id'=>$customer->id,
                'code' => $customer->code,
                'name' => $customer->name
            ];
        }
        foreach($suppliers as $supplier){
            $s[] = [
                'id'=>$supplier->id,
                'code'=>$supplier->code,
                'name' => $supplier->name
            ];
        }
        foreach($gls as $gl){
            $g[] = [
                'id'=>$gl->id,
                'code' => $gl->number,
                'name' => $gl->description
            ];
        }
        $this->accounts_arr['Customer'] = $c;
        $this->accounts_arr['Supplier'] = $s;
        $this->accounts_arr['GeneralAccount'] = $g;

        $this->description = $this->journal->description;
        $this->journal_date = $this->journal->date;
        foreach($this->journal->items as $key => $items){
            $this->inputs[] = "";
            $this->type[] = $items->account_type;
            $this->account[] = $items->account_id;
            $this->credit[] = $items->credit;
            $this->debit[] = $items->debit;
            $this->desc[] = $items->description;
            $this->accounts[] = '';
            $this->changeTypeEvent($items->account_type,$key);
        }
        $this->totals();

    }

    public function render()
    {

        return view('livewire.edit-journal');
    }


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

    public function changeTypeEvent($value, $key)
    {
        $this->result = $key;
        $this->accounts[$key] = $this->accounts_arr[$value];
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

            $this->journal->reference = \App\Models\Journal::generateNewNumber();
            $this->journal->description = $this->description;
            $this->journal->date = $this->journal_date;
            $this->journal->updated_by = auth()->id();
            $items = [];
            if($this->journal->save()){
                foreach ($this->type as $key => $value) {
                    $items[] = [
                        'journal_id' =>$this->journal->id,
                        'account_type' =>$this->type[$key],
                        'account_id' =>$this->account[$key],
                        'credit' =>$this->credit[$key],
                        'debit' =>$this->debit[$key],
                        'description' =>$this->desc[$key] ?? null,
                    ];
                }
            }
            if(JournalItem::upsert($items, ['journal_id', 'account_id'])){
                $this->inputs = [];
                $this->resetInputFields();
            }

            session()->flash('message', 'Journal created Successfully.');
            return $this->redirect(route('journal.index'));
        }catch (\Exception $e){
            session()->flash('error', $e->getMessage());
        }

    }

}
