 <div class="row">
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-info">
             <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">Today's Sale</span>
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
                 <span class="info-box-text">This Month's Sale</span>
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
                 <span class="info-box-text">This Year's Sale</span>
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
                 <span class="info-box-text mt-3 pb-1">Total Sale</span>
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
                 <span class="info-box-text">Today's Paid</span>
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
                 <span class="info-box-text">This Month's Paid</span>
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
                 <span class="info-box-text">This Year's Paid</span>
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
                 <span class="info-box-text mt-3 pb-1">Total Paid</span>
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
                 <span class="info-box-text">Today's Due</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($today_due->sum('due'), 2, '.', ',') }}</span>
                 @php

                     if ($yesterday_due->sum('due') != 0) {
                         $percentage =
                             (($today_due->sum('due') - $yesterday_due->sum('due')) / $yesterday_due->sum('due')) * 100;
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
                 <span class="info-box-text">This Month's Due</span>
                 <span class="info-box-number">&#8358;{{ number_format($month_due->sum('due'), 2, '.', ',') }}</span>
                 @php

                     if ($previous_month_due->sum('due') != 0) {
                         $percentage =
                             (($month_due->sum('due') - $previous_month_due->sum('due')) /
                                 $previous_month_due->sum('due')) *
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
                 <span class="info-box-text">This Year's Due</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($year_due->sum('due'), 2, '.', ',') }}</span>
                 @php
                     if ($previous_year_due->sum('due') != 0) {
                         $percentage =
                             (($year_due->sum('due') - $previous_year_due->sum('due')) /
                                 $previous_year_due->sum('due')) *
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
                 <span class="info-box-text mt-3 pb-1">Total Due</span>
                 <span class="info-box-number mb-3">&#8358;
                     {{ number_format($sales_due->sum('due'), 2, '.', ',') }}</span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">Today's Expenses</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($today_expenses->sum('amount'), 2, '.', ',') }}</span>

                 @php

                     if ($yesterday_expenses->sum('amount') != 0) {
                         $percentage =
                             (($today_expenses->sum('amount') - $yesterday_expenses->sum('amount')) /
                                 $yesterday_expenses->sum('amount')) *
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
                     {{ date('l', strtotime('-1 day')) }}
                 </span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
     </div>
     <!-- /.col -->
     <div class="col-md-3 col-sm-6 col-12">
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">This Month's Expenses</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($month_expenses->sum('amount'), 2, '.', ',') }}</span>
                 @php
                     if ($yesterday_expenses->sum('amount') != 0) {
                         $percentage =
                             (($today_expenses->sum('amount') - $yesterday_expenses->sum('amount')) /
                                 $yesterday_expenses->sum('amount')) *
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
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text">This Year's Expenses</span>
                 <span class="info-box-number">&#8358;
                     {{ number_format($year_expenses->sum('amount'), 2, '.', ',') }}</span>
                 @php
                     if ($previous_year_expenses->sum('amount') != 0) {
                         $percentage =
                             (($year_expenses->sum('amount') - $previous_year_expenses->sum('amount')) /
                                 $previous_year_expenses->sum('amount')) *
                             100;
                     } else {
                         $percentage = 0;
                     }
                 @endphp

                 <div class="progress">
                     <div class="progress-bar" style="width: {{ number_format(abs($percentage), 2) }}%">
                     </div>
                 </div>

                 <span class="progress-description {{ $percentage > 0 ? 'text-warning' : '' }}">
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
         <div class="info-box bg-danger">
             <span class="info-box-icon"><i class="fa fa-minus-square"></i></span>

             <div class="info-box-content">
                 <span class="info-box-text mt-3 pb-1">Total Expenses</span>
                 <span class="info-box-number mb-3">&#8358;
                     {{ number_format($expenses->sum('amount'), 2, '.', ',') }}</span>
             </div>
             <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
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
                     <div id="barchart_material" style="width: 900px; height: 500px;"></div>
                 </div>
             </div>
             <!-- /.card-body -->
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
                     <div id="barchart_material2" style="width: 900px; height: 500px;"></div>
                 </div>
             </div>
             <!-- /.card-body -->
         </div>
         <!-- /.card -->
     </div>
 </div>
 <!-- /.row -->
