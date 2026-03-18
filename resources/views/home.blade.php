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
        .dashboard-clock { font-size: 1.1rem; }
        .dashboard-inactive-alert { font-size: 0.9rem; }
        .dashboard-filters-card .form-control-sm { min-height: 31px; }
        @media (min-width: 768px) {
            .content .clock { width: 18rem; height: 18rem; margin: 1rem auto; padding: 1.25rem; }
            .content .clock .outer-clock-face .marking { width: 2px; }
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

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right mb-0 align-items-center flex-row">
                            <li class="breadcrumb-item mr-2 mb-0">
                                <span class="dashboard-clock text-primary font-weight-bold" id="clock" aria-label="Current time"></span>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        </ol>
                    </div>
                </div>
                @php $inactive_customers = App\Models\User::overTwoWeeks(); @endphp
                @if($inactive_customers->isNotEmpty())
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning py-2 mb-0 mt-2 dashboard-inactive-alert" role="alert">
                                <strong><i class="fa fa-info-circle mr-1"></i>Customers not patronized in 2+ weeks:</strong>
                                <span class="ml-1">{{ $inactive_customers->pluck('name')->implode(', ') }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @hasanyrole('Super-admin|Admin')
                    <div class="card card-outline card-primary shadow-sm mb-4 dashboard-filters-card">
                        <div class="card-header py-2">
                            <h5 class="card-title mb-0"><i class="fa fa-filter mr-1"></i> Summary filters</h5>
                        </div>
                        <div class="card-body py-3">
                            <div class="row align-items-end">
                                <div class="col-md-2 col-6 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-muted mb-1">Company</label>
                                    <select id="company_id" class="form-control form-control-sm">
                                        <option value="">Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-6 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-muted mb-1">Branch</label>
                                    <select id="branch_id" class="form-control form-control-sm select2-single">
                                        <option value="">Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-6 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-muted mb-1">Year</label>
                                    <input type="number" class="form-control form-control-sm" id="report_year" min="2020" max="2030" value="{{ now()->format('Y') }}">
                                </div>
                                <div class="col-md-2 col-6 mb-2 mb-md-0">
                                    <label class="small font-weight-bold text-muted mb-1">Quarter</label>
                                    <select id="report_quarter" class="form-control form-control-sm">
                                        <option value="">Select Quarter</option>
                                        <option value="Q1">Q1 (Jan – Mar)</option>
                                        <option value="Q2">Q2 (Apr – Jun)</option>
                                        <option value="Q3">Q3 (Jul – Sep)</option>
                                        <option value="Q4">Q4 (Oct – Dec)</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-6 mb-2 mb-md-0">
                                    <button class="btn btn-primary btn-sm btn-block" id="load_dashboard_summary">
                                        <i class="fa fa-refresh mr-1"></i> Load Summary
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-3">
                        <div class="card-header py-2 bg-light">
                            <h5 class="card-title mb-0"><i class="fa fa-users mr-1"></i> Today's sales per user</h5>
                        </div>
                        <div class="card-body py-2">
                            <ul class="list-unstyled mb-0 row">
                                @foreach ($user_sales_per_branch as $sale)
                                    <li class="col-md-4 col-6 mb-1"><span class="text-muted">{{ $sale->name }}:</span> &#8358;{{ number_format($sale->total, 2, '.', ',') }}</li>
                                @endforeach
                            </ul>
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
                        <p class="mt-3 text-muted">Generating summary, please wait...</p>
                    </div>
                @else
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-12">
                            <div class="card shadow-sm border-0 bg-light mb-4">
                                <div class="card-body text-center py-5">
                                    <h1 class="h3 font-weight-bold text-primary mb-2">{{ optional(App\Models\User::UserBranchName())->long_name ?? 'Branch' }}</h1>
                                    <p class="text-muted mb-4">Sale and Inventory Application System</p>
                                    <div class="clock mx-auto">
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
                                </div>
                            </div>
                            <div class="card shadow-sm">
                                <div class="card-header py-2 bg-light">
                                    <h5 class="card-title mb-0"><i class="fa fa-users mr-1"></i> Today's sales per user</h5>
                                </div>
                                <div class="card-body py-2">
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($user_sales_per_branch as $sale)
                                            <li class="py-1 border-bottom border-light"><span class="text-muted">{{ $sale->name }}:</span> &#8358;{{ number_format($sale->total, 2, '.', ',') }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        (function() {
                            const secondHand = document.querySelector('.second-hand');
                            const minsHand = document.querySelector('.min-hand');
                            const hourHand = document.querySelector('.hour-hand');
                            if (!secondHand || !minsHand || !hourHand) return;
                            function setDate() {
                                const now = new Date();
                                const seconds = now.getSeconds();
                                secondHand.style.transform = 'rotate(' + (((seconds / 60) * 360) + 90) + 'deg)';
                                const mins = now.getMinutes();
                                minsHand.style.transform = 'rotate(' + (((mins / 60) * 360) + ((seconds / 60) * 6) + 90) + 'deg)';
                                const hour = now.getHours();
                                hourHand.style.transform = 'rotate(' + (((hour / 12) * 360) + ((mins / 60) * 30) + 90) + 'deg)';
                            }
                            setInterval(setDate, 1000);
                            setDate();
                        })();
                    </script>
                @endhasanyrole
            </div>
        </section>
    </div>

@endsection

@push('js')
    <script>
        (function() {
                            var el = document.getElementById('clock');
                            if (!el) return;
                            function updateClock() {
                                var now = new Date();
                                el.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                            }
                            updateClock();
                            setInterval(updateClock, 1000);
                        })();
    </script>
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
