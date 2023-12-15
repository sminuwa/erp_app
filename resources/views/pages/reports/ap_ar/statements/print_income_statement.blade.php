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

                            <img src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="width:50px;height:50px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{$branch->name}}
                            </h3>
                            <h5 style="text-align: center;">{{ strtoupper($branch->name) }} <br>
                                INCOME STATEMENT FROM {{ $from_month=='all'?'January':monthName($from_month) }} AND
                                {{ $to_month=='all'?'December':monthName($to_month) }}
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered caption" id="example1" data-ordering="false">
                                <?php
                                $total_revenue = 0;
                                $credit_sum = 0;
                                $debit_sum = 0;
                                $other_income = 0;
                                ?>
                                <thead>
                                    <tr>
                                        <th rowspan="2">Account No</th>
                                        <th rowspan="2">Description</th>
                                        <th colspan="2" style="text-align: center; align-content: center">Balance</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center; align-content: center">Credit (Cr.)</th>
                                        <th style="text-align: center; align-content: center">Debit (Dr.)</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th colspan="6">
                                            <h3>REVENUES</h3>
                                        </th>
                                    </tr>
                                    @foreach ($revenues as $revenue)
                                        @php
                                            $credit = number_format($revenue->credit, 2);
                                            $debit = number_format($revenue->debit, 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $revenue->number }}</td>
                                            <td>{{ $revenue->description }}</td>
                                            <td style="text-align: right">
                                                @if ($credit > 0.0)
                                                    &#8358; {{ $credit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if ($debit > 0.0)
                                                    &#8358; {{ $debit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                <?php
                                                $credit_sum += $revenue->credit;
                                                $debit_sum += $revenue->debit;
                                                $dif = $credit_sum - $debit_sum;
                                                ?>
                                                @if ($dif < 0)
                                                    &#8358;({{ number_format(abs($dif), 2) }})
                                                @else
                                                    &#8358;{{ number_format($dif, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: right">TOTAL REVENUE</th>
                                        @php $total_revenue = $credit_sum - $debit_sum  @endphp
                                        <th colspan="3" style="text-align: right;">&#8358;{{ number_format($total_revenue, 2) }}</th>
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
                                            $credit = number_format($sale->credit, 2);
                                            $debit = number_format($sale->debit, 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $sale->number }}</td>
                                            <td>{{ $sale->description }}</td>
                                            <td style="text-align: right">
                                                @if ($credit > 0.0)
                                                    &#8358; {{ $credit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if ($debit > 0.0)
                                                    &#8358; {{ $debit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                <?php
                                                $credit_sum += $sale->credit;
                                                $debit_sum += $sale->debit;
                                                $dif = $credit_sum - $debit_sum;
                                                ?>
                                                @if ($dif < 0)
                                                    &#8358;({{ number_format(abs($dif), 2) }})
                                                @else
                                                    &#8358;{{ number_format($dif, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: right">TOTAL COST</th>
                                        @php $total_cost = $credit_sum - $debit_sum  @endphp
                                        <th colspan="3" style="text-align: right;">&#8358;{{ number_format($total_cost, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right">GROSS PROFIT/LOSS</th>
                                        @php $gross_profit_loss = $total_revenue - $total_cost  @endphp
                                        <th colspan="3" style="text-align: right;">&#8358;{{ number_format($gross_profit_loss, 2) }}</th>
                                    </tr>
                                     
                                    <tr>
                                        <th colspan="6">
                                            <h3>EXPENSES</h3>
                                        </th>
                                    </tr>
                                    <?php
                                    $total_expense = 0;
                                    $credit_sum = 0;
                                    $debit_sum = 0;
                                    ?>
                                    @foreach ($expenses as $expense)
                                        @php
                                            $credit = number_format($expense->credit, 2);
                                            $debit = number_format($expense->debit, 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $expense->number }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td style="text-align: right">
                                                @if ($credit > 0.0)
                                                    &#8358; {{ $credit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if ($debit > 0.0)
                                                    &#8358; {{ $debit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                <?php
                                                $credit_sum += $expense->credit;
                                                $debit_sum += $expense->debit;
                                                $dif = $credit_sum - $debit_sum;
                                                ?>
                                                @if ($dif < 0)
                                                    &#8358;({{ number_format(abs($dif), 2) }})
                                                @else
                                                    &#8358;{{ number_format($dif, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: right">TOTAL EXPENSE</th>
                                        @php $total_expense = $credit_sum - $debit_sum  @endphp
                                        <th colspan="3" style="text-align: right;">&#8358;{{ number_format($total_expense, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right">NET PROFIT /LOSS</th>
                                        @php $net_profit_loss = $gross_profit_loss - $total_expense+ $other_income  @endphp
                                        <th colspan="3" style="text-align: right;">&#8358;{{ number_format($net_profit_loss, 2) }}</th>
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
