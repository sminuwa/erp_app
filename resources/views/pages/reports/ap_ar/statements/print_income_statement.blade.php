<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Ledger - {{ config('app.name', 'Inventory Management System') }}</title>

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

                            <img src="{{ asset('assets/backend/img/logo' . '.png') }}" style="width:80px;height:40px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{ $branch->name ?? 'All Branches' }}
                            </h3>
                            <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
                                INCOME STATEMENT FROM {{ $from_month == 'all' ? 'January' : monthName($from_month) }}
                                AND
                                {{ $to_month == 'all' ? 'December' : monthName($to_month) }}
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="display table table-bordered caption" id="example1" data-ordering="false">
                                <?php
                                $total_revenue = 0;
                                $credit_sum = 0;
                                $debit_sum = 0;
                                $other_income = 0;
                                ?>
                                <thead>
                                    <tr>

                                        <th colspan="4"></th>
                                        <th colspan="2" style="text-align: center; align-content: center">Balance
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Account No</th>
                                        <th>Description</th>
                                        <th style="text-align: center; align-content: center">Debit (Dr.)</th>
                                        <th style="text-align: center; align-content: center">Credit (Cr.)</th>
                                        <th style="text-align: center; align-content: center">Dr.</th>
                                        <th style="text-align: center; align-content: center">Cr.</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    <tr>
                                        <th colspan="6">
                                            <h3>SALES REVENUE</h3>
                                        </th>
                                    </tr>
                                    @foreach ($revenues as $revenue)
                                        @php
                                            $credit = number_format(abs($revenue->credit), 2);
                                            $debit = number_format(abs($revenue->debit), 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $revenue->number }}</td>
                                            <td>{{ $revenue->description }}</td>
                                            <td style="text-align: right">
                                                @if ($debit > 0.0)
                                                    {{ $debit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if ($credit > 0.0)
                                                    {{ $credit }}
                                                @endif
                                            </td>
                                            <?php
                                            $credit_sum += $revenue->credit;
                                            $debit_sum += $revenue->debit;
                                            $dif = $credit_sum - $debit_sum;
                                            ?>
                                            <td style="text-align: right">
                                                {{ $dif < 0 ? number_format(abs($dif), 2) : '' }}
                                            </td>
                                            <td style="text-align: right">
                                                {{ $dif > 0 ? number_format(abs($dif), 2) : '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: left">TOTAL REVENUE</th>
                                        @php $total_revenue = $credit_sum - $debit_sum;  @endphp
                                        <th colspan="3" style="text-align: right;">
                                            {{ number_format(abs($total_revenue), 2) }}</th>
                                    </tr>
                                    <?php
                                    $total_cost_of_sale = 0;
                                    $credit_sum = 0;
                                    $debit_sum = 0;
                                    ?>
                                    <tr>
                                        <th colspan="6">
                                            <h3>COST OF SALES</h3>
                                        </th>
                                    </tr>
                                    @foreach ($cost_of_sales as $sale)
                                        @php
                                            $credit = number_format(abs($sale->credit), 2);
                                            $debit = number_format(abs($sale->debit), 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $sale->number }}</td>
                                            <td>{{ $sale->description }}</td>
                                            <td style="text-align: right">
                                                @if ($debit > 0.0)
                                                    {{ $debit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if ($credit > 0.0)
                                                    {{ $credit }}
                                                @endif
                                            </td>

                                            <?php
                                            $credit_sum += $sale->credit;
                                            $debit_sum += $sale->debit;
                                            $dif = $credit_sum - $debit_sum;
                                            ?>
                                            <td style="text-align: right">
                                                {{ $dif < 0 ? number_format(abs($dif), 2) : '' }}
                                            </td>
                                            <td style="text-align: right">
                                                {{ $dif > 0 ? number_format(abs($dif), 2) : '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: left">TOTAL COST</th>
                                        @php $total_cost = $credit_sum - $debit_sum;  @endphp
                                        <th style="text-align: right;">
                                            {{ $total_cost < 0 ? number_format(abs($total_cost), 2) : '' }}</th>
                                        <th style="text-align: right;">
                                            {{ $total_cost > 0 ? number_format(abs($total_cost), 2) : '' }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: left">GROSS MARGIN</th>
                                        @php $gross_profit_loss = $total_revenue - abs($total_cost)  @endphp
                                        <th style="text-align: right;">
                                            {{ $gross_profit_loss < 0 ? number_format(abs($gross_profit_loss), 2) : '' }}
                                        </th>
                                        <th style="text-align: right;">
                                            {{ $gross_profit_loss > 0 ? number_format(abs($gross_profit_loss), 2) : '' }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="6">
                                            <h3>OTHER INCOME</h3>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="6">
                                            <h3>EXPENDITURE</h3>
                                        </th>
                                    </tr>
                                    <?php
                                    $total_expense = 0;
                                    $credit_sum = 0;
                                    $debit_sum = 0;
                                    ?>
                                    @foreach ($expenses as $expense)
                                        @php
                                            $credit = number_format(abs($expense->credit), 2);
                                            $debit = number_format(abs($expense->debit), 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $expense->number }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td style="text-align: right">
                                                @if ($debit > 0.0)
                                                    {{ $debit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if ($credit > 0.0)
                                                    {{ $credit }}
                                                @endif
                                            </td>
                                            <?php
                                            $credit_sum += $expense->credit;
                                            $debit_sum += $expense->debit;
                                            $dif = $credit_sum - $debit_sum;
                                            ?>
                                            <td style="text-align: right">
                                                {{ $dif < 0 ? number_format(abs($dif), 2) : '' }}
                                            </td>
                                            <td style="text-align: right">
                                                {{ $dif > 0 ? number_format(abs($dif), 2) : '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: left">TOTAL EXPENDITURE</th>
                                        @php $total_expense = $credit_sum - $debit_sum;  @endphp
                                        <th style="text-align: right;">
                                            {{ $total_expense < 0 ? number_format(abs($total_expense), 2) : '' }}</th>
                                        <th style="text-align: right;">
                                            {{ $total_expense > 0 ? number_format(abs($total_expense), 2) : '' }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: left">TAX DIVIDEND</th>
                                        <th style="text-align: right;">{{ number_format(0, 2) }}</th>
                                        <th style="text-align: right;">{{ number_format(0, 2) }}</th>
                                    </tr>

                                    {{-- <tr>
                                        <th colspan="4" style="text-align: left">NET MARGIN</th>
                                        @php $net_profit_loss = abs($gross_profit_loss) - abs($total_expense) + $other_income  @endphp
                                        <th style="text-align: right;">{{ $net_profit_loss < 0 ? number_format(abs($net_profit_loss), 2) : '' }}
                                        </th>
                                        <th style="text-align: right;">{{ $net_profit_loss > 0 ? number_format(abs($net_profit_loss), 2) : '' }}
                                        </th>
                                    </tr> --}}
                                    <tr>
                                        <th colspan="4" style="text-align: left">NET MARGIN</th>
                                        @php
                                            $net_profit_loss = $gross_profit_loss - $total_expense + $other_income;
                                        @endphp
                                        <th style="text-align: right;">
                                            {{ $net_profit_loss < 0 ? number_format(abs($net_profit_loss), 2) : '' }}
                                        </th>
                                        <th style="text-align: right;">
                                            {{ $net_profit_loss > 0 ? number_format(abs($net_profit_loss), 2) : '' }}
                                        </th>
                                    </tr>
                                </tbody>
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
