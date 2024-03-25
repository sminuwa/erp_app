<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Account Statements - {{ config('app.name', 'Inventory Management System') }}</title>

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
                                {{App\Models\User::UserBranchName()->long_name}}
                            </h3>
                            <h5 style="text-align: center;">{{ strtoupper($customer->name) }} LEDGER LISTING BETWEEN
                                {{ $from_date }}
                                AND
                                {{ $to_date }}<br /> Runining Balance B/d Before this date {{ $from_date }}
                                was = @if ($balance_b_d < 0)
                                    &#8358;({{ number_format(abs($balance_b_d), 2) }})
                                @else
                                    &#8358;{{ number_format($balance_b_d, 2) }}
                                @endif
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered caption" id="example1" border="1" data-ordering="false" cellpadding="0"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="width: 5%;">S/N</th>
                                        <th rowspan="2">Date</th>
                                        <th rowspan="2">Account No</th>
                                        <th rowspan="2">Description</th>
                                        <th rowspan="2">Reference</th>
                                        <th colspan="2" style="text-align: center; align-content: center">Balance</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center; align-content: center">Debit (Dr.)</th>
                                        <th style="text-align: center; align-content: center">Credit (Cr.)</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ledgers as $ledger)
                                        @php
                                            $credit = number_format($ledger->credit, 2);
                                            $debit = number_format($ledger->debit, 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $ledger->date->toFormattedDateString() }}</td>
                            
                                            <td>{{ $ledger->payer()->code ?? $ledger->payer()->number ?? '' }}</td>
                                            <td>{{ transactionDecription($ledger->reference) != '' ? transactionDecription($ledger->reference) : $ledger->description }}</td>
                                            <td>{{ $ledger->reference }}</td>
                                            <td style="text-align: right">
                                                @if ($credit > 0.0)
                                                    {{ abs($credit) }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if ($debit > 0.0)
                                                    {{ abs($debit) }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                <?php $sum_cr += $ledger->credit;
                                                $sum_dr += $ledger->debit;
                                                $dif = $sum_dr-$sum_cr;
                                                ?>
                                                @if ($dif < 0)
                                                   ({{ number_format(abs($dif), 2) }})
                                                @else
                                                    {{ number_format($dif, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                            
                                        <th colspan="5" style="text-align: right">Total</th>
                                        <th style="text-align: right;">&#8358;{{ number_format($debit_sum, 2) }}</th>
                                        <th style="text-align: right;">&#8358;{{ number_format($credit_sum, 2) }}</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right">Balance C/F</th>
                                        <th colspan="3" style="text-align: right;">&#8358;{{ number_format($dif, 2) }}</th>
                                        <th></th>
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
