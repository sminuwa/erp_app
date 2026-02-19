<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Team & Staff Ledger Report - {{ config('app.name', 'ERP') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <style>
        body { font-family: 'Source Sans Pro', Arial, sans-serif; font-size: 12px; }
        .print-header { text-align: center; margin-bottom: 20px; }
        .print-header h3 { margin: 5px 0; }
        .print-header h5 { margin: 5px 0; }
        .print-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .print-table th, .print-table td { border: 1px solid #ddd; padding: 5px; }
        .print-table th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: #dc3545; }
        .text-success { color: #28a745; }
        .summary-row { background-color: #f9f9f9; }
        .summary-row td { padding: 8px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="print-header">
                        <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:50px;height:50px;" alt="Logo" class="img-circle">
                        <h3>Team & Staff Ledger Report</h3>
                        <h5>Period: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</h5>
                    </div>

                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-12">
                            <table class="print-table">
                                <tr class="summary-row">
                                    <td><strong>Total Records:</strong> {{ $totals['total_records'] }}</td>
                                    <td><strong>Total Debit:</strong> {{ number_format($totals['total_debit'], 2) }}</td>
                                    <td><strong>Total Credit:</strong> {{ number_format($totals['total_credit'], 2) }}</td>
                                    <td><strong>Net Balance:</strong> {{ number_format($totals['total_debit'] - $totals['total_credit'], 2) }}</td>
                                    <td><strong>Processed By:</strong> {{ auth()->user()->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($teamBalances->count() > 0)
                    <h5>Current Team Balances</h5>
                    <table class="print-table" style="width: 50%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Team</th>
                                <th class="text-right">Ledger Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teamBalances as $index => $team)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $team->name }}</td>
                                <td class="text-right">{{ number_format($team->ledger_balance ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                    @if($entries->count() > 0)
                    <h5>Penalty Ledger Entries</h5>
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="print-table">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;">S/N</th>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Team/Staff</th>
                                        <th class="text-right">Debit</th>
                                        <th class="text-right">Credit</th>
                                        <th class="text-right">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entries as $index => $entry)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('d M Y', strtotime($entry['date'])) }}</td>
                                        <td>{{ $entry['reference'] }}</td>
                                        <td>{{ $entry['description'] }}</td>
                                        <td>{{ ucfirst($entry['penalty_type']) }}</td>
                                        <td>{{ $entry['penalty_type'] == 'team' ? $entry['team_name'] : $entry['staff_name'] }}</td>
                                        <td class="text-right">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}</td>
                                        <td class="text-right">{{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}</td>
                                        <td class="text-right">{{ number_format($entry['balance'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight: bold; background-color: #f5f5f5;">
                                        <td colspan="6"><strong>Totals</strong></td>
                                        <td class="text-right"><strong>{{ number_format($totals['total_debit'], 2) }}</strong></td>
                                        <td class="text-right"><strong>{{ number_format($totals['total_credit'], 2) }}</strong></td>
                                        <td class="text-right"><strong>{{ number_format($totals['total_debit'] - $totals['total_credit'], 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-12">
                            <p>No penalty records found for the selected criteria.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/backend/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/adminlte.js') }}"></script>

    <script>
        window.print();
    </script>
</body>

</html>
