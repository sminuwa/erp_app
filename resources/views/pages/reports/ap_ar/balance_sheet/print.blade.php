<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Balance Sheet - {{ config('app.name', 'Inventory Management System') }}</title>

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
                            <h5 style="text-align: center;">BALANCE SHEET AS AT
                                {{ Carbon\Carbon::parse($to)->toFormattedDateString() }}</h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            {{-- <table class="display table table-bordered caption" id="example1" data-ordering="false">

                                <thead>
                                    <tr>
                                        <th colspan="4"></th>
                                        <th style="text-align: center; align-content: center" colspan="2">Balance
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Account No</th>
                                        <th>Description</th>
                                        <th style="text-align: center; align-content: center">Total (Dr.)</th>
                                        <th style="text-align: center; align-content: center">Total (Cr.)</th>
                                        <th>Dr.</th>
                                        <th>Cr.</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @php
                                        $total_credit = $total_debit = 0;
                                    @endphp
                                    @foreach ($ledger1 as $ledger)
                                        @php
                                            $credit = number_format(abs($ledger->credit), 2);
                                            $debit = number_format(abs($ledger->debit), 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $ledger->number }}</td>
                                            <td>{{ $ledger->description }}</td>
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

                                            @php
                                                $total_credit += $ledger->credit;
                                                $total_debit += $ledger->debit;
                                                $diff = $ledger->debit - $ledger->credit;
                                            @endphp
                                            <td style="text-align: right;">
                                                @if ($diff < 0)
                                                    {{ number_format(abs($diff), 2) }}
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($diff > 0)
                                                    {{ number_format(abs($diff), 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align: right;">Total</th>
                                        <th style="text-align: right;">{{ number_format($total_credit, 2) }}</th>
                                        <th style="text-align: right;">{{ number_format($total_debit, 2) }}</th>
                                        <th style="text-align: right;">
                                            {{ $total_credit - $total_debit < 0 ? number_format(abs($total_credit - $total_debit), 2) : '' }}
                                        </th>
                                        <th style="text-align: right;">
                                            {{ $total_credit - $total_debit > 0 ? number_format($total_credit - $total_debit, 2) : '' }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table> --}}
                            <div class="row">
                                <div class="offset-10">
                                    <a href="{{ route('ajax.print.balance.sheet.report', [$to_date, $branch_id]) }}" target="_BLANK"
                                        class="btn-success btn btn-sm">Print</a>
                                </div>
                            </div>
                            {{-- <table class="display table table-bordered caption" id="example1" data-ordering="true">
                                <caption style="caption-size:top">
                                    <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
                                        BALANCE SHEET AS AT {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                                    </h5>
                                </caption>
                                <thead>
                                    <tr>
                                        <th colspan="2"></th>
                                        <th style="text-align: center; align-content: center" colspan="2"></th>
                                        <th style="text-align: center; align-content: center" colspan="2">Balance</th>
                                    </tr>
                                    <tr>
                                        <th>Account No</th>
                                        <th>Description</th>
                                        <th style="text-align: center; align-content: center">Total (Dr.)</th>
                                        <th style="text-align: center; align-content: center">Total (Cr.)</th>
                                        <th>Dr.</th>
                                        <th>Cr.</th>
                                    </tr>
                            
                                </thead>
                                <tbody>
                                    @php
                                        $total_credit = $total_debit = $dr = $cr =  0;
                                    @endphp
                                    @foreach ($ledger1 as $ledger)
                                        @php
                                            $credit = number_format(abs($ledger->credit), 2);
                                            $debit = number_format(abs($ledger->debit), 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $ledger->number }}</td>
                                            <td>{{ $ledger->description }}</td>
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
                            
                                            @php
                                                $total_credit += $ledger->credit;
                                                $total_debit += $ledger->debit;
                                                $diff = $ledger->credit - $ledger->debit;
                                            @endphp
                                            <td style="text-align: right;">
                                                @if ($diff < 0)
                                                    @php $dr+=$diff @endphp
                                                    {{ number_format(abs($diff), 2) }}
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($diff > 0)
                                                    @php $cr+=$diff @endphp
                                                    {{ number_format(abs($diff), 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach ($ledger2 as $ledger)
                                    @php
                                        $credit = number_format(abs($ledger->credit), 2);
                                        $debit = number_format(abs($ledger->debit), 2);
                                    @endphp
                                    <tr>
                                        <td>A150001</td>
                                        <td>General Customer Control Account</td>
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
                            
                                        @php
                                            $total_credit += $ledger->credit;
                                            $total_debit += $ledger->debit;
                                            $diff = $ledger->credit - $ledger->debit;
                                        @endphp
                                        <td style="text-align: right">
                                            @if ($diff < 0)
                                                @php $dr+=$diff @endphp
                                                {{ number_format(abs($diff), 2) }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if ($diff > 0)
                                                @php $cr+=$diff @endphp
                                                {{ number_format($diff, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach ($ledger3 as $ledger)
                                    @php
                                        $credit = number_format(abs($ledger->credit), 2);
                                        $debit = number_format(abs($ledger->debit), 2);
                                    @endphp
                                    <tr>
                                        <td>L220010</td>
                                        <td>Accounts Payable Control</td>
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
                            
                                        @php
                                            $total_credit += $ledger->credit;
                                            $total_debit += $ledger->debit;
                                            $diff = $ledger->credit - $ledger->debit;
                                        @endphp
                                        <td style="text-align: right">
                                            @if ($diff < 0)
                                                @php $dr+=$diff @endphp
                                                {{ number_format(abs($diff), 2) }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if ($diff > 0)
                                                @php $cr+=$diff @endphp
                                                {{ number_format($diff, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align: right;">Total</th>
                                        <th style="text-align: right;">{{ number_format($total_credit, 2) }}</th>
                                        <th style="text-align: right;">{{ number_format($total_debit, 2) }}</th>
                                        <th style="text-align: right;">
                                            {{ number_format(abs($dr), 2) }}</th>
                                        <th style="text-align: right;">
                                            {{ number_format(abs($cr), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table> --}}
                            <table class="display table table-bordered caption" id="example1" data-ordering="true">
                                <caption style="caption-size:top">
                                    <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
                                        BALANCE SHEET AS AT {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                                    </h5>
                                </caption>
                                <thead>
                                    <tr>
                                        <th>Account No</th>
                                        <th>Description</th>
                                        <th style="text-align: center;">Debit (Dr.)</th>
                                        <th style="text-align: center;">Credit (Cr.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Assets -->
                                    <tr>
                                        <th colspan="4">Assets</th>
                                    </tr>
                                    @php
                                        $total_assets = 0;
                                    @endphp
                                    @foreach ($assets as $asset)
                                        <tr>
                                            <td>{{ $asset->number }}</td>
                                            <td>{{ $asset->description }}</td>
                                            <td style="text-align: right;">{{ number_format(abs($asset->debit - $asset->credit), 2) }}</td>
                                            <td></td>
                                        </tr>
                                        @php
                                            $total_assets += $asset->debit - $asset->credit;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <th colspan="2">Total Assets</th>
                                        <th style="text-align: right;">{{ number_format(abs($total_assets), 2) }}</th>
                                        <td></td>
                                    </tr>
                            
                                    <!-- Liabilities -->
                                    <tr>
                                        <th colspan="4">Liabilities</th>
                                    </tr>
                                    @php
                                        $total_liabilities = 0;
                                    @endphp
                                    @foreach ($liabilities as $liability)
                                        <tr>
                                            <td>{{ $liability->number }}</td>
                                            <td>{{ $liability->description }}</td>
                                            <td></td>
                                            <td style="text-align: right;">{{ number_format(abs($liability->credit - $liability->debit), 2) }}</td>
                                        </tr>
                                        @php
                                            $total_liabilities += $liability->credit - $liability->debit;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <th colspan="2">Total Liabilities</th>
                                        <td></td>
                                        <th style="text-align: right;">{{ number_format(abs($total_liabilities), 2) }}</th>
                                    </tr>
                            
                                    <!-- Equity -->
                                    <tr>
                                        <th colspan="4">Equity</th>
                                    </tr>
                                    @php
                                        $total_equity = 0;
                                    @endphp
                                    @foreach ($equity as $eq)
                                        <tr>
                                            <td>{{ $eq->number }}</td>
                                            <td>{{ $eq->description }}</td>
                                            <td></td>
                                            <td style="text-align: right;">{{ number_format(abs($eq->credit - $eq->debit), 2) }}</td>
                                        </tr>
                                        @php
                                            $total_equity += $eq->credit - $eq->debit;
                                        @endphp
                                    @endforeach
                                    <!-- Retained Earnings -->
                                    <tr>
                                        <td>AC.xxxxxx</td>
                                        <td>Retained Earnings</td>
                                        <td></td>
                                        <td style="text-align: right;">{{ number_format(abs($net_income), 2) }}</td>
                                    </tr>
                                    @php
                                        $total_equity += $net_income;
                                    @endphp
                                    <tr>
                                        <th colspan="2">Total Equity</th>
                                        <td></td>
                                        <th style="text-align: right;">{{ number_format(abs($total_equity), 2) }}</th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total Liabilities and Equity</th>
                                        <td></td>
                                        <th style="text-align: right;">{{ number_format(abs($total_liabilities + $total_equity), 2) }}</th>
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
