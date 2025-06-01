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
    <style>
        #loading-indicator {
            display: none;
            text-align: center;
            padding: 30px;
        }

        .lds-ring {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }

        .lds-ring div {
            box-sizing: border-box;
            display: block;
            position: absolute;
            width: 64px;
            height: 64px;
            margin: 8px;
            border: 8px solid #007bff;
            border-radius: 50%;
            animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            border-color: #007bff transparent transparent transparent;
        }

        .lds-ring div:nth-child(1) {
            animation-delay: -0.45s;
        }

        .lds-ring div:nth-child(2) {
            animation-delay: -0.3s;
        }

        .lds-ring div:nth-child(3) {
            animation-delay: -0.15s;
        }

        @keyframes lds-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
                                <li>{{ $sale->name }}: &#8358; {{ number_format($sale->total, 2, '.', ',') }}</li>
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
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="company_id" class="form-control">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="branch_id" class="form-control select2-single">
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="year" class="form-control" id="report_year" value="{{ now()->format('Y') }}">
                        </div>
                        <div class="col-md-2">
                            <select id="report_quarter" class="form-control">
                                <option value="">Select Quarter</option>
                                <option value="Q1">Q1 (Jan - Mar)</option>
                                <option value="Q2">Q2 (Apr - Jun)</option>
                                <option value="Q3">Q3 (Jul - Sep)</option>
                                <option value="Q4">Q4 (Oct - Dec)</option>
                            </select>
                        </div>
                        {{-- <div class="col-md-2">
                            <input type="month" class="form-control" id="report_month" value="{{ now()->format('Y-m') }}">
                        </div> --}}

                        <div class="col-md-2">
                            <button class="btn btn-sm btn-primary" id="load_dashboard_summary">Load Summary</button>
                        </div>
                    </div>

                    <div id="dashboard-summary-placeholder">
                        @include('default_dashboard_report')
                    </div>
                    <div id="loading-indicator">
                        <div class="lds-ring">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                        <p class="mt-3">Generating summary, please wait...</p>
                    </div>
                @else
                    <div class="row">
                        <div class="col-2">

                        </div>
                        <div class="col-8">
                            <h1 style="text-shadow: 5px 5px #558ABB;font-weight:900;font-size:60px;">
                                {{ App\Models\User::UserBranchName()->long_name }}
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
        google.charts.load("current", {
            packages: ['corechart', 'bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            const data = google.visualization.arrayToDataTable([
                ['Month', 'Expenses'],
                @foreach ($months as $index => $month)
                    ['{{ $month }}', {{ $amounts[$index] }}],
                @endforeach
            ]);

            const options = {
                title: 'Monthly Expenses Report (₦)',
                chartArea: {
                    width: '70%'
                },
                hAxis: {
                    title: 'Amount (₦)',
                    minValue: 0
                },
                vAxis: {
                    title: 'Month'
                },
                bars: 'horizontal',
                colors: ['#61afff']
            };

            const chart = new google.visualization.BarChart(document.getElementById('barchart_material2'));
            chart.draw(data, options);
        }
    </script>
    <script type="text/javascript">
        google.charts.load("current", {
            packages: ['corechart', 'bar']
        });
        google.charts.setOnLoadCallback(drawSalesChart);

        function drawSalesChart() {
            const data = google.visualization.arrayToDataTable([
                ['Month', 'Sales'],
                @foreach ($sale_months as $index => $month)
                    ['{{ $month }}', {{ $sale_amounts[$index] }}],
                @endforeach
            ]);

            const options = {
                title: 'Monthly Sales Report (₦)',
                chartArea: {
                    width: '70%'
                },
                hAxis: {
                    title: 'Amount (₦)',
                    minValue: 0
                },
                vAxis: {
                    title: 'Month'
                },
                bars: 'horizontal',
                colors: ['#007bff']
            };

            const chart = new google.visualization.BarChart(document.getElementById('barchart_material'));
            chart.draw(data, options);
        }
    </script>

    <script>
        $('#load_dashboard_summary').on('click', function() {
            $('#dashboard-summary-placeholder').hide();
            $('#loading-indicator').show();

            $.ajax({
                url: '{{ route('dashboard.summary.ajax') }}',
                data: {
                    company_id: $('#company_id').val(),
                    branch_id: $('#branch_id').val(),
                    year: $('#report_year').val(),
                    quarter: $('#report_quarter').val()
                },
                success: function(response) {
                    $('#loading-indicator').hide();
                    $('#dashboard-summary-placeholder').html(response).fadeIn();
                },
                error: function() {
                    $('#loading-indicator').hide();
                    $('#dashboard-summary-placeholder').html(
                        '<p class="text-danger">Failed to load summary.</p>').fadeIn();
                }
            });
        });
    </script>
@endpush
