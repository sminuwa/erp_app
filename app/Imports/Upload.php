<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToCollection;

class Upload implements FromCollection
{

    public function collection()
    {
        return Upload::all();
    }

}
