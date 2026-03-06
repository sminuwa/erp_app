<?php

namespace App\Exports\Manufacturing;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ManufacturingReportExport implements FromView, WithTitle
{
    protected $viewName;
    protected $data;
    protected $sheetTitle;

    public function __construct(string $viewName, array $data, string $sheetTitle = 'Report')
    {
        $this->viewName = $viewName;
        $this->data = $data;
        $this->sheetTitle = $sheetTitle;
    }

    public function view(): View
    {
        return view($this->viewName, $this->data);
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }
}
