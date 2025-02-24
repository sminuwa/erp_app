<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Customer Payment Report - {{ config('app.name', 'Inventory Management System') }}</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <!-- IonIcons -->
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />

</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12" style="text-align: center">

                            <img src="{{ asset('assets/backend/img/logo.png') }}"
                                style="width:100px;height:60px;" alt="Albabello Logo" class="img-circle elevation-3"
                                style="opacity: .8">
                            <h3>
                                {{ App\Models\User::UserBranchName()->long_name }}
                            </h3>
                            <h5 style="text-align: center;">Customer Payment Report
                                From
                                {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                                AND
                                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="display table table-bordered caption" id="example1" border="1" cellpadding="0"
                                cellspacing="0" data-ordering="true">
                                <thead>
                                    <tr>
                                        <th style="width: 50%" colspan="4">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
                                        </th>
                                        <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
                                    </tr>
                                    <tr>
                                        <th>DATE</th>
                                        <th>CUST NAME</th>
                                        <th>BALANCE</th>
                                        <th>AMOUNT PAID</th>
                                        <th>TELLER NO</th>
                                        <th>PAY MODE</th>
                                        <th>ACC. NAME</th>
                                        <th>DATE POSTED</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_sold = 0;
                                        $total_pay = 0;
                                        $total_discount = 0;
                                        $total_due = 0;
                                    @endphp
                                    @foreach ($sales as $sale)
                                        @php
                                            $sum_cr = \App\Models\CustomerLedger::where('id', '<', $sale->id)
                                                ->where('customer_id', $sale->customer_id)
                                                ->sum('cr');
                                            $sum_dr = \App\Models\CustomerLedger::where('id', '<', $sale->id)
                                                ->where('customer_id', $sale->customer_id)
                                                ->sum('dr');
                                            $balance = $sum_cr - $sum_dr;
                                            $total_pay += $sale->dr;
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}</td>
                                            <td>{{ $sale->customer }}</td>
                                            <td style="text-align: right">
                                                @if ($balance < 0)
                                                    ({{ number_format(abs($balance), 2) }})
                                                @else
                                                    {{ number_format($balance, 2) }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                {{ number_format($sale->dr, 2, '.', ',') }}</td>
                                            <td>{{ $sale->teller_no }}</td>
                                            <td>{{ $sale->payment_mode }}</td>
                                            <td>{{ $sale->account_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sale->created_at)->toFormattedDateString() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2"></th>
                                        <th style="text-align: right">B/F:
                                            @if ($balance < 0)
                                                ({{ number_format(abs($balance), 2) }})
                                            @else
                                                {{ number_format($balance, 2) }}
                                            @endif
                                        </th>
                                        <th style="text-align: right">
                                            {{ number_format($total_pay, 2, '.', ',') }}</th>
                                        <th colspan="4"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->

        <!-- /.content -->

        <!-- ./wrapper -->

        <!-- REQUIRED SCRIPTS -->
        <!-- jQuery -->
        <script src="{{ asset('assets/backend/plugins/jquery/jquery.min.js') }}"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('assets/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- AdminLTE -->
        <script src="{{ asset('assets/backend/js/adminlte.js') }}"></script>

        <script>
            window.print();
        </script>

</body>


</html>
