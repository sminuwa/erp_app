@extends('layouts.backend.app')

@section('title', 'Dashboard')
<link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
@push('css')
    <style>
        .clock {
            width: 25rem;
            height: 25rem;
            border: 7px solid #282828;
            box-shadow: -4px -4px 10px rgba(67, 67, 67, 0.5),
                inset 4px 4px 10px rgba(0, 0, 0, 0.5),
                inset -4px -4px 10px rgba(67, 67, 67, 0.5),
                4px 4px 10px rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            margin: 50px auto;
            position: relative;
            padding: 2rem;

        }

        .outer-clock-face {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 100%;
            background: #282828;


            overflow: hidden;
        }

        .outer-clock-face::after {
            -webkit-transform: rotate(90deg);
            -moz-transform: rotate(90deg);
            transform: rotate(90deg)
        }

        .outer-clock-face::before,
        .outer-clock-face::after,
        .outer-clock-face .marking {
            content: '';
            position: absolute;
            width: 5px;
            height: 100%;
            background: #1df52f;
            z-index: 0;
            left: 49%;
        }

        .outer-clock-face .marking {
            background: #bdbdcb;
            width: 3px;
        }

        .outer-clock-face .marking.marking-one {
            -webkit-transform: rotate(30deg);
            -moz-transform: rotate(30deg);
            transform: rotate(30deg)
        }

        .outer-clock-face .marking.marking-two {
            -webkit-transform: rotate(60deg);
            -moz-transform: rotate(60deg);
            transform: rotate(60deg)
        }

        .outer-clock-face .marking.marking-three {
            -webkit-transform: rotate(120deg);
            -moz-transform: rotate(120deg);
            transform: rotate(120deg)
        }

        .outer-clock-face .marking.marking-four {
            -webkit-transform: rotate(150deg);
            -moz-transform: rotate(150deg);
            transform: rotate(150deg)
        }

        .inner-clock-face {
            position: absolute;
            top: 10%;
            left: 10%;
            width: 80%;
            height: 80%;
            background: #282828;
            -webkit-border-radius: 100%;
            -moz-border-radius: 100%;
            border-radius: 100%;
            z-index: 1;
        }

        .inner-clock-face::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            border-radius: 18px;
            margin-left: -9px;
            margin-top: -6px;
            background: #4d4b63;
            z-index: 11;
        }

        .hand {
            width: 50%;
            right: 50%;
            height: 6px;
            background: #61afff;
            position: absolute;
            top: 50%;
            border-radius: 6px;
            transform-origin: 100%;
            transform: rotate(90deg);
            transition-timing-function: cubic-bezier(0.1, 2.7, 0.58, 1);
        }

        .hand.hour-hand {
            width: 30%;
            z-index: 3;
        }

        .hand.min-hand {
            height: 3px;
            z-index: 10;
            width: 40%;
        }

        .hand.second-hand {
            background: #ee791a;
            width: 45%;
            height: 2px;
        }
    </style>
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard</h1>
                        <marquee>
                            <span style="font-size: 24px;">
                                Customers who have not patronized for more than two weeks:
                                @foreach (App\Models\User::overTwoWeeks() as $customer)
                                    {{ $customer->name }}-{{ $customer->phone }},
                                @endforeach
                            </span>
                        </marquee>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <h3 style="text-shadow: 5px 5px #558ABB;font-weight:900;" id="clock" onload="currentTime()">
                            </h3>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Today's Sales Per User</h5>
                        <ul>
                            @foreach ($user_sales_per_branch as $sale)
                            <li>{{$sale->name}}: &#8358; {{number_format($sale->total,2,'.',',')}}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            
            <div class="container-fluid">
                @hasanyrole('Super-admin|Admin')
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
                                            $percentage = (($today->sum('total') - $yesterday->sum('total')) / $yesterday->sum('total')) * 100;
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
                                            $percentage = (($month->sum('total') - $previous_month->sum('total')) / $previous_month->sum('total')) * 100;
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
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fa fa-balance-scale"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">This Year's Sale</span>
                                    <span class="info-box-number">&#8358;
                                        {{ number_format($year->sum('total'), 2, '.', ',') }}</span>
                                    @php
                                        
                                        if ($previous_year->sum('total') != 0) {
                                            $percentage = (($year->sum('total') - $previous_year->sum('total')) / $previous_year->sum('total')) * 100;
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
                                            $percentage = (($month->sum('pay') - $previous_month->sum('pay')) / $previous_month->sum('pay')) * 100;
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
                                            $percentage = (($year->sum('pay') - $previous_year->sum('pay')) / $previous_year->sum('pay')) * 100;
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
                                            $percentage = (($today_due->sum('due') - $yesterday_due->sum('due')) / $yesterday_due->sum('due')) * 100;
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
                                    <span
                                        class="info-box-number">&#8358;{{ number_format($month_due->sum('due'), 2, '.', ',') }}</span>
                                    @php
                                        
                                        if ($previous_month_due->sum('due') != 0) {
                                            $percentage = (($month_due->sum('due') - $previous_month_due->sum('due')) / $previous_month_due->sum('due')) * 100;
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
                                            $percentage = (($year_due->sum('due') - $previous_year_due->sum('due')) / $previous_year_due->sum('due')) * 100;
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
                                            $percentage = (($today_expenses->sum('amount') - $yesterday_expenses->sum('amount')) / $yesterday_expenses->sum('amount')) * 100;
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
                                            $percentage = (($today_expenses->sum('amount') - $yesterday_expenses->sum('amount')) / $yesterday_expenses->sum('amount')) * 100;
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
                                            $percentage = (($year_expenses->sum('amount') - $previous_year_expenses->sum('amount')) / $previous_year_expenses->sum('amount')) * 100;
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
                @else
                    <div class="row">
                        <div class="col-2">
                            
                        </div>
                        <div class="col-8">
                            <h1 style="text-shadow: 5px 5px #558ABB;font-weight:900;font-size:60px;">{{App\Models\User::UserBranchName()->long_name}}
                            </h1>
                            <h2 style="text-shadow: 5px 5px #558ABB;font-weight:900;text-align:center">SALE AND INVENTORY
                                APPLICATION SYSTEM</h2>
                            <div class="clock">
                                <div class="outer-clock-face">
                                    <div class="marking marking-one"></div>
                                    <div class="marking marking-two"></div>
                                    <div class="marking marking-three"></div>
                                    <div class="marking marking-four"></div>
                                    <div class="inner-clock-face">
                                        <div class="hand hour-hand"></div>
                                        <div class="hand min-hand"></div>
                                        <div class="hand second-hand"></div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                const secondHand = document.querySelector('.second-hand');
                                const minsHand = document.querySelector('.min-hand');
                                const hourHand = document.querySelector('.hour-hand');

                                function setDate() {
                                    const now = new Date();

                                    const seconds = now.getSeconds();
                                    const secondsDegrees = ((seconds / 60) * 360) + 90;
                                    secondHand.style.transform = `rotate(${secondsDegrees}deg)`;

                                    const mins = now.getMinutes();
                                    const minsDegrees = ((mins / 60) * 360) + ((seconds / 60) * 6) + 90;
                                    minsHand.style.transform = `rotate(${minsDegrees}deg)`;

                                    const hour = now.getHours();
                                    const hourDegrees = ((hour / 12) * 360) + ((mins / 60) * 30) + 90;
                                    hourHand.style.transform = `rotate(${hourDegrees}deg)`;
                                }

                                setInterval(setDate, 1000);

                                setDate();
                            </script>
                        </div>
                        <div class="col-2">
                            
                        </div>

                    </div>
                @endhasanyrole

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection

@push('js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawStuff);

        function drawStuff() {
            var data = new google.visualization.arrayToDataTable([
                ['Month', "Sales {{ date('Y') }}"],
                @foreach ($current_sales as $sale)
                    ["{{ date('M', mktime('0', 0, 0, $sale->months, 10)) }}", {{ $sale['sums'] }}],
                @endforeach
            ]);

            var options = {
                title: 'Monthly Sales Report',
                width: 700,
                legend: {
                    position: 'none'
                },
                chart: {
                    title: "Monthly Sales Report {{ date('Y') }}",
                    subtitle: 'Monthly Sales Report'
                },
                bars: 'vertical', // Required for Material Bar Charts.
                axes: {
                    x: {
                        0: {
                            side: 'bottom',
                            label: 'Month'
                        } // Top x-axis.
                    }
                },
                bar: {
                    groupWidth: "90%"
                }
            };

            var chart = new google.charts.Bar(document.getElementById('barchart_material'));
            chart.draw(data, options);
        }
    </script>

    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawStuff);

        function drawStuff() {
            var data = new google.visualization.arrayToDataTable([
                ['Month', "Sales {{ date('Y') }}"],
                @foreach ($current_expenses as $expense)
                    ["{{ date('M', mktime('0', 0, 0, $expense['months'], 10)) }}", {{ $expense['sums'] }}],
                @endforeach
            ]);

            var options = {
                title: 'Monthly Expenses Report',
                width: 700,
                legend: {
                    position: 'none'
                },
                chart: {
                    title: "Monthly Expenses Report {{ date('Y') }}",
                    subtitle: 'Monthly Expenses Report'
                },
                bars: 'vertical', // Required for Material Bar Charts.
                axes: {
                    x: {
                        0: {
                            side: 'bottom',
                            label: 'Month'
                        } // Top x-axis.
                    }
                },
                bar: {
                    groupWidth: "90%"
                }
            };

            var chart = new google.charts.Bar(document.getElementById('barchart_material2'));
            chart.draw(data, options);
        }

        function currentTime() {
            let date = new Date();
            let hh = date.getHours();
            let mm = date.getMinutes();
            let ss = date.getSeconds();
            let session = "AM";

            if (hh === 0) {
                hh = 12;
            }
            if (hh > 12) {
                hh = hh - 12;
                session = "PM";
            }

            hh = (hh < 10) ? "0" + hh : hh;
            mm = (mm < 10) ? "0" + mm : mm;
            ss = (ss < 10) ? "0" + ss : ss;

            let time = hh + ":" + mm + ":" + ss + " " + session;

            document.getElementById("clock").innerText = time;
            let t = setTimeout(function() {
                currentTime()
            }, 1000);
        }

        currentTime();
    </script>
@endpush
