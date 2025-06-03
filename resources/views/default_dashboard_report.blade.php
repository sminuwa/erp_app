 {{-- <div class="row">
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-info">
             <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">Today's Sales</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($today->sum('total'), 2, '.', ',') }}</span>

                 @php

                     if ($yesterday->sum('total') != 0) {
                         $percentage =
                             (($today->sum('total') - $yesterday->sum('total')) / $yesterday->sum('total')) * 100;
                     } else {
                         $percentage = 0;
                     }
                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>

                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }} %
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From
                     {{ date('l', strtotime('-1 day')) }}
                 </span>

             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-info">
             <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">This Month's Sales</span>
                 <span class="info-box-number"> &#8358;
                     {{ number_format($month->sum('total'), 2, '.', ',') }}</span>
                 @php

                     if ($previous_month->sum('total') != 0) {
                         $percentage =
                             (($month->sum('total') - $previous_month->sum('total')) / $previous_month->sum('total')) *
                             100;
                     } else {
                         $percentage = 0;
                     }
                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }} %
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From
                     {{ date('F', strtotime('-1 month')) }}
                 </span>
                 <span class="text-white">Budget: &#8358;{{ number_format($month_budget, 2) }}</span><br>
                 <span class="text-white">
                     Variance:
                     {{ $month_budget > 0 ? number_format((($month->sum('total') - $month_budget) / $month_budget) * 100, 2) : '0' }}%
                 </span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-info">
             <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">This Year's Sales</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($year->sum('total'), 2, '.', ',') }}</span>
                 @php

                     if ($previous_year->sum('total') != 0) {
                         $percentage =
                             (($year->sum('total') - $previous_year->sum('total')) / $previous_year->sum('total')) *
                             100;
                     } else {
                         $percentage = 0;
                     }
                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }} %
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From
                     {{ date('Y', strtotime('-1 year')) }}
                 </span>
                 <span class="text-white">Budget: &#8358;{{ number_format($year_budget, 2) }}</span><br>
                 <span class="text-white">
                     Variance:
                     {{ $year_budget > 0 ? number_format((($year->sum('total') - $year_budget) / $year_budget) * 100, 2) : '0' }}%
                 </span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-info">
             <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text mt-3 pb-1">Total Sales</span>
                 <span
                     class="info-box-number mb-3">&#8358;{{ number_format($sales->sum('total'), 2, '.', ',') }}</span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-success">
             <span class="info-box-icon"><i class="fa fa-money"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">Suppliers: Today's Paid</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($today->sum('pay'), 2, '.', ',') }}</span>
                 @php

                     if ($yesterday->sum('pay') != 0) {
                         $percentage = (($today->sum('pay') - $yesterday->sum('pay')) / $yesterday->sum('pay')) * 100;
                     } else {
                         $percentage = 0;
                     }

                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>

                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }} %
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From
                     {{ date('l', strtotime('-1 day')) }}
                 </span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-success">
             <span class="info-box-icon"><i class="fa fa-money"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">Suppliers: This Month's Paid</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($month->sum('pay'), 2, '.', ',') }}</span>
                 @php
                     if ($previous_month->sum('pay') != 0) {
                         $percentage =
                             (($month->sum('pay') - $previous_month->sum('pay')) / $previous_month->sum('pay')) * 100;
                     } else {
                         $percentage = 0;
                     }
                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>

                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }} %
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From
                     {{ date('F', strtotime('-1 month')) }}
                 </span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-success">
             <span class="info-box-icon"><i class="fa fa-money"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">Suppliers: This Year's Paid</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($year->sum('pay'), 2, '.', ',') }}</span>
                 @php

                     if ($previous_year->sum('pay') != 0) {
                         $percentage =
                             (($year->sum('pay') - $previous_year->sum('pay')) / $previous_year->sum('pay')) * 100;
                     } else {
                         $percentage = 0;
                     }
                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>

                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }} %
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From
                     {{ date('Y', strtotime('-1 year')) }}
                 </span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-success">
             <span class="info-box-icon"><i class="fa fa-money"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text mt-3 pb-1">Suppliers: Total Paid</span>
                 <span class="info-box-number mb-3">&#8358;
                     {{ number_format($sales->sum('pay'), 2, '.', ',') }}</span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-warning">
             <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">Invoices: Today's Due</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($today_due->sum('debit'), 2, '.', ',') }}</span>
                 @php

                     if ($yesterday_due->sum('debit') != 0) {
                         $percentage =
                             (($today_due->sum('debit') - $yesterday_due->sum('debit')) / $yesterday_due->sum('debit')) * 100;
                     } else {
                         $percentage = 0;
                     }
                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>

                 <span class="progress-description {{ $percentage < 0 ? 'text-success' : '' }}">
                     {{ number_format(abs($percentage), 2) }} %
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From
                     {{ date('l', strtotime('-1 day')) }}
                 </span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-warning">
             <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">Invoices: This Month's Due</span>
                 <span class="info-box-number">&#8358;{{ number_format($month_due->sum('debit'), 2, '.', ',') }}</span>
                 @php

                     if ($previous_month_due->sum('debit') != 0) {
                         $percentage =
                             (($month_due->sum('debit') - $previous_month_due->sum('debit')) /
                                 $previous_month_due->sum('debit')) *
                             100;
                     } else {
                         $percentage = 0;
                     }
                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>

                 <span class="progress-description {{ $percentage < 0 ? 'text-success' : '' }}">
                     {{ number_format(abs($percentage), 2) }} %
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From
                     {{ date('F', strtotime('-1 month')) }}
                 </span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-warning">
             <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">Invoices: This Year's Due</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($year_due->sum('debit'), 2, '.', ',') }}</span>
                 @php
                     if ($previous_year_due->sum('debit') != 0) {
                         $percentage =
                             (($year_due->sum('debit') - $previous_year_due->sum('debit')) /
                                 $previous_year_due->sum('debit')) *
                             100;
                     } else {
                         $percentage = 0;
                     }
                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>

                 <span class="progress-description {{ $percentage < 0 ? 'text-success' : '' }}">
                     {{ number_format(abs($percentage), 2) }} %
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From
                     {{ date('Y', strtotime('-1 year')) }}
                 </span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-warning">
             <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text mt-3 pb-1">Invoices: Total Due</span>
                 <span class="info-box-number mb-3">&#8358;
                     {{ number_format($sales_due->sum('debit'), 2, '.', ',') }}</span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">Today's Expenses</span>
                 <span class="info-box-number">&#8358;{{ number_format($today_expenses->sum('debit'), 2) }}</span>
                 @php
                     $today_sum = $today_expenses->sum('debit');
                     $yesterday_sum = $yesterday_expenses->sum('debit');
                     $percentage = $yesterday_sum != 0 ? (($today_sum - $yesterday_sum) / $yesterday_sum) * 100 : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-white' : 'text-warning' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('l', strtotime('-1 day')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- This Month's Expenses + Budget -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">This Month's Expenses</span>
                 @php
                     $month_sum = $month_expenses->sum('debit');
                     $budget_month = $expense_budgets/12 ?? 0;
                     $budget_util_percent = $budget_month > 0 ? ($month_sum / $budget_month) * 100 : 0;
                     $prev_month_sum = $previous_month_expenses->sum('debit');
                     $trend_percent =
                         $prev_month_sum != 0 ? (($month_sum - $prev_month_sum) / $prev_month_sum) * 100 : 0;
                 @endphp
                 <span class="info-box-number">&#8358;{{ number_format($month_sum, 2) }}</span>
                 <small class="text-white">Budget: &#8358;{{ number_format($budget_month, 2) }}
                     ({{ number_format($budget_util_percent, 1) }}% used)</small>
                 <div class="progress mt-1">
                     <div class="progress-bar"
                         style="width: {{ min(100, number_format($budget_util_percent, 1)) }}%"></div>
                 </div>
                 <span class="progress-description {{ $trend_percent < 0 ? 'text-white' : 'text-warning' }}">
                     {{ number_format(abs($trend_percent), 2) }}%
                     {{ $trend_percent > 0 ? 'Increase' : 'Decrease' }} From {{ date('F', strtotime('-1 month')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- This Year's Expenses + Budget -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">This Year's Expenses</span>
                 @php
                     $year_sum = $year_expenses->sum('debit');
                     $year_budget_amount = $expense_budgets ?? 0;
                     $year_util_percent = $year_budget_amount > 0 ? ($year_sum / $year_budget_amount) * 100 : 0;
                     $prev_year_sum = $previous_year_expenses->sum('debit');
                     $trend_year = $prev_year_sum != 0 ? (($year_sum - $prev_year_sum) / $prev_year_sum) * 100 : 0;
                 @endphp
                 <span class="info-box-number">&#8358;{{ number_format($year_sum, 2) }}</span>
                 <small class="text-white">Budget: &#8358;{{ number_format($year_budget_amount, 2) }}
                     ({{ number_format($year_util_percent, 1) }}% used)</small>
                 <div class="progress mt-1">
                     <div class="progress-bar" style="width: {{ min(100, number_format($year_util_percent, 1)) }}%">
                     </div>
                 </div>
                 <span class="progress-description {{ $trend_year < 0 ? 'text-white' : 'text-warning' }}">
                     {{ number_format(abs($trend_year), 2) }}%
                     @if ($trend_year > 0)
                         Increase From {{ date('Y', strtotime('-1 year')) }}
                     @elseif ($trend_year < 0)
                         Decrease From {{ date('Y', strtotime('-1 year')) }}
                     @else
                         Same As {{ date('Y', strtotime('-1 year')) }}
                     @endif
                 </span>
             </div>
         </div>
     </div>

     <!-- Total Expenses -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text mt-3 pb-1">Total Expenses</span>
                 <span class="info-box-number mb-3">&#8358;{{ number_format($expenses->sum('debit'), 2) }}</span>
             </div>
         </div>
     </div>

     <!-- /.col -->
 </div>
 <div class="row">
     <div class="col-md-6">
         <!-- AREA CHART -->
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Sales Report</h3>
             </div>
             <div class="card-body">
                 <div class="chart">
                     <div id="barchart_material" style="width: 100%; height: 500px;"></div>
                 </div>
             </div>
         </div>
         <!-- /.card -->
     </div>
     <div class="col-md-6">
         <!-- AREA CHART -->
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Expenses Report</h3>
             </div>
             <div class="card-body">
                 <div class="chart">
                     <div id="barchart_material2" style="width: 100%; height: 500px;"></div>
                 </div>
             </div>
             <!-- /.card-body -->
         </div>
         <!-- /.card -->
     </div>
 </div>
 <!-- /.row --> --}}
 <div class="row">
     <!-- Today's Sales -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-info">
             <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">Today's Sales</span>
                 <span class="info-box-number">₦{{ number_format($today->sum('total'), 2, '.', ',') }}</span>
                 @php
                     $yesterday_total = $yesterday->sum('total');
                     $percentage =
                         $yesterday_total != 0
                             ? (($today->sum('total') - $yesterday_total) / $yesterday_total) * 100
                             : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('l', strtotime('-1 day')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- This Month's Sales -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-info">
             <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">This Month's Sales</span>
                 <span class="info-box-number">₦{{ number_format($month->sum('total'), 2, '.', ',') }}</span>
                 @php
                     $prev_month_total = $previous_month->sum('total');
                     $percentage =
                         $prev_month_total != 0
                             ? (($month->sum('total') - $prev_month_total) / $prev_month_total) * 100
                             : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('F', strtotime('-1 month')) }}
                 </span>
                 <span class="text-white">Budget: ₦{{ number_format($month_budget, 2, '.', ',') }}</span><br>
                 <span class="text-white">
                     Variance:
                     {{ $month_budget > 0 ? number_format((($month->sum('total') - $month_budget) / $month_budget) * 100, 2) : '0' }}%
                 </span>
             </div>
         </div>
     </div>

     <!-- This Year's Sales -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-info">
             <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">This Year's Sales</span>
                 <span class="info-box-number">₦{{ number_format($year->sum('total'), 2, '.', ',') }}</span>
                 @php
                     $prev_year_total = $previous_year->sum('total');
                     $percentage =
                         $prev_year_total != 0
                             ? (($year->sum('total') - $prev_year_total) / $prev_year_total) * 100
                             : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('Y', strtotime('-1 year')) }}
                 </span>
                 <span class="text-white">Budget: ₦{{ number_format($year_budget, 2, '.', ',') }}</span><br>
                 <span class="text-white">
                     Variance:
                     {{ $year_budget > 0 ? number_format((($year->sum('total') - $year_budget) / $year_budget) * 100, 2) : '0' }}%
                 </span>
             </div>
         </div>
     </div>

     <!-- Total Sales (Placeholder, as 'sales' is not defined in controller) -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-info">
             <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text mt-3 pb-1">Total Sales</span>
                 <span class="info-box-number mb-3">₦{{ number_format($year->sum('total'), 2, '.', ',') }}</span>
             </div>
         </div>
     </div>

     <!-- Suppliers: Today's Paid -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-success">
             <span class="info-box-icon"><i class="fa fa-money"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">Suppliers: Today's Paid</span>
                 <span class="info-box-number">₦{{ number_format($today_paid, 2, '.', ',') }}</span>
                 @php
                     $percentage = $today_paid != 0 ? (($today_paid - ($yesterday_paid ?? 0)) / $today_paid) * 100 : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('l', strtotime('-1 day')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- Suppliers: This Month's Paid -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-success">
             <span class="info-box-icon"><i class="fa fa-money"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">Suppliers: This Month's Paid</span>
                 <span class="info-box-number">₦{{ number_format($month_paid, 2, '.', ',') }}</span>
                 @php
                     $percentage =
                         $month_paid != 0 ? (($month_paid - ($previous_month_paid ?? 0)) / $month_paid) * 100 : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('F', strtotime('-1 month')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- Suppliers: This Year's Paid -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-success">
             <span class="info-box-icon"><i class="fa fa-money"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">Suppliers: This Year's Paid</span>
                 <span class="info-box-number">₦{{ number_format($year_paid, 2, '.', ',') }}</span>
                 @php
                     $percentage = $year_paid != 0 ? (($year_paid - ($previous_year_paid ?? 0)) / $year_paid) * 100 : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-warning' : '' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('Y', strtotime('-1 year')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- Suppliers: Total Paid -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-success">
             <span class="info-box-icon"><i class="fa fa-money"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text mt-3 pb-1">Suppliers: Total Paid</span>
                 <span class="info-box-number mb-3">₦{{ number_format($total_paid, 2, '.', ',') }}</span>
             </div>
         </div>
     </div>

     <!-- Invoices: Today's Due -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-warning">
             <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">Invoices: Today's Due</span>
                 <span class="info-box-number">₦{{ number_format($today_due->sum('debit'), 2, '.', ',') }}</span>
                 @php
                     $yesterday_due_total = $yesterday_due->sum('debit');
                     $percentage =
                         $yesterday_due_total != 0
                             ? (($today_due->sum('debit') - $yesterday_due_total) / $yesterday_due_total) * 100
                             : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-success' : '' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('l', strtotime('-1 day')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- Invoices: This Month's Due -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-warning">
             <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">Invoices: This Month's Due</span>
                 <span class="info-box-number">₦{{ number_format($month_due->sum('debit'), 2, '.', ',') }}</span>
                 @php
                     $prev_month_due_total = $previous_month_due->sum('debit');
                     $percentage =
                         $prev_month_due_total != 0
                             ? (($month_due->sum('debit') - $prev_month_due_total) / $prev_month_due_total) * 100
                             : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-success' : '' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('F', strtotime('-1 month')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- Invoices: This Year's Due -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-warning">
             <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">Invoices: This Year's Due</span>
                 <span class="info-box-number">₦{{ number_format($year_due->sum('debit'), 2, '.', ',') }}</span>
                 @php
                     $prev_year_due_total = $previous_year_due->sum('debit');
                     $percentage =
                         $prev_year_due_total != 0
                             ? (($year_due->sum('debit') - $prev_year_due_total) / $prev_year_due_total) * 100
                             : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-success' : '' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('Y', strtotime('-1 year')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- Invoices: Total Due -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-warning">
             <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text mt-3 pb-1">Invoices: Total Due</span>
                 <span class="info-box-number mb-3">₦{{ number_format($total_due->sum('debit'), 2, '.', ',') }}</span>
             </div>
         </div>
     </div>

     <!-- Today's Expenses -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">Today's Expenses</span>
                 <span class="info-box-number">₦{{ number_format($today_expenses->sum('debit'), 2, '.', ',') }}</span>
                 @php
                     $today_sum = $today_expenses->sum('debit');
                     $yesterday_sum = $yesterday_expenses->sum('debit');
                     $percentage = $yesterday_sum != 0 ? (($today_sum - $yesterday_sum) / $yesterday_sum) * 100 : 0;
                 @endphp
                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%"></div>
                 </div>
                 <span class="progress-description {{ $percentage < 0 ? 'text-white' : 'text-warning' }}">
                     {{ number_format(abs($percentage), 2) }}%
                     {{ $percentage > 0 ? 'Increase' : 'Decrease' }} From {{ date('l', strtotime('-1 day')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- This Month's Expenses -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">This Month's Expenses</span>
                 @php
                     $month_sum = $month_expenses->sum('debit');
                     $budget_month = $expense_budgets / 12 ?? 0;
                     $budget_util_percent = $budget_month > 0 ? ($month_sum / $budget_month) * 100 : 0;
                     $prev_month_sum = $previous_month_expenses->sum('debit');
                     $trend_percent =
                         $prev_month_sum != 0 ? (($month_sum - $prev_month_sum) / $prev_month_sum) * 100 : 0;
                 @endphp
                 <span class="info-box-number">₦{{ number_format($month_sum, 2, '.', ',') }}</span>
                 <small class="text-white">Budget: ₦{{ number_format($budget_month, 2, '.', ',') }}
                     ({{ number_format($budget_util_percent, 1) }}% used)</small>
                 <div class="progress mt-1">
                     <div class="progress-bar"
                         style="width: {{ min(100, number_format($budget_util_percent, 1)) }}%"></div>
                 </div>
                 <span class="progress-description {{ $trend_percent < 0 ? 'text-white' : 'text-warning' }}">
                     {{ number_format(abs($trend_percent), 2) }}%
                     {{ $trend_percent > 0 ? 'Increase' : 'Decrease' }} From {{ date('F', strtotime('-1 month')) }}
                 </span>
             </div>
         </div>
     </div>

     <!-- This Year's Expenses -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text">This Year's Expenses</span>
                 @php
                     $year_sum = $year_expenses->sum('debit');
                     $year_budget_amount = $expense_budgets ?? 0;
                     $year_util_percent = $year_budget_amount > 0 ? ($year_sum / $year_budget_amount) * 100 : 0;
                     $prev_year_sum = $previous_year_expenses->sum('debit');
                     $trend_year = $prev_year_sum != 0 ? (($year_sum - $prev_year_sum) / $prev_year_sum) * 100 : 0;
                 @endphp
                 <span class="info-box-number">₦{{ number_format($year_sum, 2, '.', ',') }}</span>
                 <small class="text-white">Budget: ₦{{ number_format($year_budget_amount, 2, '.', ',') }}
                     ({{ number_format($year_util_percent, 1) }}% used)</small>
                 <div class="progress mt-1">
                     <div class="progress-bar" style="width: {{ min(100, number_format($year_util_percent, 1)) }}%">
                     </div>
                 </div>
                 <span class="progress-description {{ $trend_year < 0 ? 'text-white' : 'text-warning' }}">
                     {{ number_format(abs($trend_year), 2) }}%
                     @if ($trend_year > 0)
                         Increase From {{ date('Y', strtotime('-1 year')) }}
                     @elseif ($trend_year < 0)
                         Decrease From {{ date('Y', strtotime('-1 year')) }}
                     @else
                         Same As {{ date('Y', strtotime('-1 year')) }}
                     @endif
                 </span>
             </div>
         </div>
     </div>

     <!-- Total Expenses -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>
             <div class="info-box-content">
                 <span class="info-box-text mt-3 pb-1">Total Expenses</span>
                 <span class="info-box-number mb-3">₦{{ number_format($expenses->sum('debit'), 2, '.', ',') }}</span>
             </div>
         </div>
     </div>
 </div>

 <!-- Charts -->
 <div class="row">
     <div class="col-md-6">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Sales Report</h3>
             </div>
             <div class="card-body">
                 <div class="chart">
                     <div id="barchart_material" style="width: 100%; height: 500px;"></div>
                 </div>
             </div>
         </div>
     </div>
     <div class="col-md-6">
         <div class="card card-primary">
             <div class="card-header">
                 <h3 class="card-title">Expenses Report</h3>
             </div>
             <div class="card-body">
                 <div class="chart">
                     <div id="barchart_material2" style="width: 100%; height: 500px;"></div>
                 </div>
             </div>
         </div>
     </div>
 </div>
