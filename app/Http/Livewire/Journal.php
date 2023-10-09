<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Journal extends Component
{

    public $hello = 'Hello world 4';
    public $records;

    public function render()
    {
        return view('livewire.journal');
    }
}
