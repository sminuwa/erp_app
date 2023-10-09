<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowJournal extends Component
{
    public $journal;

    public function render()
    {
        return view('livewire.show-journal');
    }


}
