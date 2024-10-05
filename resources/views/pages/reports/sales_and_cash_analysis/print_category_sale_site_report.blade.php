<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Sales Report by Category by Site- {{ config('app.name', 'Inventory Management System') }}</title>

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

                            <img src="{{ asset('assets/backend/img/logo' . '.png') }}" style="width:100px;height:60px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">

                            <h5 style="text-align: center;">Sale by Categoery by Site
                                From
                                {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                                AND
                                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                            </h5>
                        </div>
                        <!-- /.col -->
                    </div>

                    @php
                        $grand_total_amount = 0;
                        $grand_total_cost = 0;
                        $grand_total_profit = 0;
                    @endphp

                    @foreach ($salesByBranch as $branchId => $branchSales)
                        <div class="row">
                            <div class="col-md-12 mt-3 table-responsive">
                                <h4>{{ $branchSales->first()->branch_name }}
                                    ({{ $branchSales->first()->branch_code }})</h4>
                                <table class="display table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>CODE</th>
                                            <th>CATEGORY</th>
                                            <th>QTY AVAILABLE</th>
                                            <th>QTY SOLD</th>
                                            <th>AMOUNT</th>
                                            <th>COST</th>
                                            <th>MARGIN</th>
                                            <th>MARGIN (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $branch_total_amount = 0;
                                            $branch_total_cost = 0;
                                            $branch_total_profit = 0;
                                        @endphp
                                        @foreach ($branchSales as $sale)
                                            <tr>
                                                <td>{{ $sale->code }}</td>
                                                <td>{{ $sale->category }}</td>
                                                <td style="text-align: right">
                                                    {{ number_format($sale->qty_available, 6) }}</td>
                                                <td style="text-align: right">{{ number_format($sale->quantity, 6) }}
                                                </td>
                                                <td style="text-align: right">
                                                    {{ number_format($sale->amount, 2, '.', ',') }}</td>
                                                <td style="text-align: right">
                                                    {{ number_format($sale->cost, 2, '.', ',') }}</td>
                                                <td style="text-align: right">
                                                    @php
                                                        $profit = $sale->amount - $sale->cost;
                                                        $branch_total_profit += $profit;
                                                        $branch_total_amount += $sale->amount;
                                                        $branch_total_cost += $sale->cost;
                                                    @endphp
                                                    {{ $profit < 0 ? '(' . number_format(abs($profit), 2, '.', ',') . ')' : number_format($profit, 2) }}
                                                </td>
                                                <td style="text-align: right">
                                                    {{ $sale->amount != 0 ? number_format(($profit / $sale->amount) * 100, 2) : 0 }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" style="text-align: right">BRANCH TOTAL</th>
                                            <th style="text-align: right">
                                                {{ number_format($branch_total_amount, 2, '.', ',') }}
                                            </th>
                                            <th style="text-align: right">
                                                {{ number_format($branch_total_cost, 2, '.', ',') }}</th>
                                            <th style="text-align: right">
                                                {{ $branch_total_profit < 0 ? '(' . number_format(abs($branch_total_profit), 2, '.', ',') . ')' : number_format($branch_total_profit, 2) }}
                                            </th>
                                            <th style="text-align: right">
                                                {{ $branch_total_amount != 0 ? number_format(($branch_total_profit / $branch_total_amount) * 100, 2) : 0 }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        @php
                            $grand_total_amount += $branch_total_amount;
                            $grand_total_cost += $branch_total_cost;
                            $grand_total_profit += $branch_total_profit;
                        @endphp
                    @endforeach

                    <div class="row">
                        <div class="col-md-12 mt-3 table-responsive">
                            <table class="display table table-bordered table-striped">
                                <tfoot>
                                    <tr>
                                        <th colspan="4" style="text-align: right">GRAND TOTAL</th>
                                        <th style="text-align: right">
                                            {{ number_format($grand_total_amount, 2, '.', ',') }}</th>
                                        <th style="text-align: right">
                                            {{ number_format($grand_total_cost, 2, '.', ',') }}</th>
                                        <th style="text-align: right">
                                            {{ $grand_total_profit < 0 ? '(' . number_format(abs($grand_total_profit), 2, '.', ',') . ')' : number_format($grand_total_profit, 2) }}
                                        </th>
                                        <th style="text-align: right">
                                            {{ $grand_total_amount != 0 ? number_format(($grand_total_profit / $grand_total_amount) * 100, 2) : 0 }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
