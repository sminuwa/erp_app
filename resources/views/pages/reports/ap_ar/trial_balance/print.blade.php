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

                            <img src="{{ asset('assets/backend/img/logo' . '.png') }}" style="width:50px;height:50px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
                            </h3>
                            <h5 style="text-align: center;">TRIAL BALANCE BETWEEN
                                {{ Carbon\Carbon::parse($from)->toFormattedDateString() }} AND
                                {{ Carbon\Carbon::parse($to)->toFormattedDateString() }}</h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered caption" id="example1" data-ordering="false">

                                <thead>
                                    <tr>
                                        <th>Account No</th>
                                        <th>Description</th>
                                        <th style="text-align: center; align-content: center">Total (Dr.)</th>
                                        <th style="text-align: center; align-content: center">Total (Cr.)</th>
                                        <th style="text-align: center; align-content: center">Balance</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @php
                                        $total_credit = $total_debit = 0;
                                    @endphp
                                    @foreach ($ledger1 as $ledger)
                                        @php
                                            $credit = number_format($ledger->credit, 2);
                                            $debit = number_format($ledger->debit, 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $ledger->number }}</td>
                                            <td>{{ $ledger->description }}</td>
                                            <td style="text-align: right">
                                                @if ($debit > 0.0)
                                                    &#8358; {{ $debit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if ($credit > 0.0)
                                                    &#8358; {{ $credit }}
                                                @endif
                                            </td>

                                            @php
                                                $total_credit += $ledger->credit;
                                                $total_debit += $ledger->debit;
                                                $diff = $ledger->debit - $ledger->credit;
                                            @endphp
                                            @if ($diff >= 0)
                                                <td style="text-align: right;">{{ number_format($diff, 2) }}</td>
                                            @else
                                                <td style="text-align: right;">({{ number_format(abs($diff), 2) }})
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align: right;">Total</th>
                                        <th style="text-align: right;">&#8358;{{ number_format($total_credit, 2) }}
                                        </th>
                                        <th style="text-align: right;">&#8358;{{ number_format($total_debit, 2) }}</th>
                                        <th style="text-align: right;">
                                            &#8358;{{ number_format($total_credit - $total_debit, 2) }}</th>
                                    </tr>
                                </tfoot>
                                </tbody>
                            </table>
                            <table class="table table-bordered caption" id="example1" data-ordering="false">

                                <thead>
                                    <tr>
                                        <th>Account No</th>
                                        <th>Description</th>
                                        <th style="text-align: center; align-content: center">Total (Dr.)</th>
                                        <th style="text-align: center; align-content: center">Total (Cr.)</th>
                                        <th style="text-align: center; align-content: center">Balance</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @php
                                        $total_credit = $total_debit = 0;
                                    @endphp
                                    @foreach ($ledger2 as $ledger)
                                        @php
                                            $credit = number_format($ledger->credit, 2);
                                            $debit = number_format($ledger->debit, 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $ledger->number }}</td>
                                            <td>{{ $ledger->description }}</td>
                                            <td style="text-align: right">
                                                @if ($debit > 0.0)
                                                    &#8358; {{ $debit }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if ($credit > 0.0)
                                                    &#8358; {{ $credit }}
                                                @endif
                                            </td>

                                            @php
                                                $total_credit += $ledger->credit;
                                                $total_debit += $ledger->debit;
                                                $diff = $ledger->debit - $ledger->credit;
                                            @endphp
                                            @if ($diff >= 0)
                                                <td style="text-align: right;">{{ number_format($diff, 2) }}</td>
                                            @else
                                                <td style="text-align: right;">({{ number_format(abs($diff), 2) }})
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align: right;">Total</th>
                                        <th style="text-align: right;">&#8358;{{ number_format($total_debit, 2) }}</th>
                                        <th style="text-align: right;">&#8358;{{ number_format($total_credit, 2) }}
                                        </th>

                                        <th style="text-align: right;">
                                            &#8358;{{ number_format($total_debit - $total_credit, 2) }}</th>
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
