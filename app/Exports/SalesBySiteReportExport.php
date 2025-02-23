<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesBySiteReportExport implements FromView
{
    protected $salesByGroup;
    protected $from_date;
    protected $to_date;
    protected $group_by_category;
    protected $group_by_product;

    public function __construct($salesByGroup, $from_date, $to_date, $group_by_category, $group_by_product)
    {
        $this->salesByGroup = $salesByGroup;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->group_by_category = $group_by_category;
        $this->group_by_product = $group_by_product;
    }

    public function view(): View
    {
        return view('pages.reports.sales_and_cash_analysis.load_sale_by_category_by_site_report', [
            'salesByGroup' => $this->salesByGroup,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'group_by_category' => $this->group_by_category,
            'group_by_product' => $this->group_by_product
        ]);
    }
}
