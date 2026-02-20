<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Manufacturing History Report - {{ config('app.name', 'ERP') }}</title>

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
                        <h3>Manufacturing History Report</h3>
                        <h5>Period: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</h5>
                    </div>

                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-12">
                            <table class="print-table">
                                <tr class="summary-row">
                                    <td><strong>Total Records:</strong> {{ $totals['total_records'] }}</td>
                                    <td><strong>Total Quantity:</strong> {{ number_format($totals['total_qty'], 4) }}</td>
                                    <td><strong>Total Cost:</strong> {{ number_format($totals['total_cost'], 2) }}</td>
                                    <td><strong>Date Processed:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</td>
                                    <td><strong>Processed By:</strong> {{ auth()->user()->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($productions->count() > 0)
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="print-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">S/N</th>
                                        <th>Product Code</th>
                                        <th>Product Description</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Unit Cost</th>
                                        <th class="text-right">Total Cost</th>
                                        <th>Production No.</th>
                                        <th>Batch No.</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productions as $index => $record)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $record['product_code'] }}</td>
                                        <td>{{ $record['product_name'] }}</td>
                                        <td class="text-right">{{ number_format($record['quantity'], 4) }}</td>
                                        <td class="text-right">{{ number_format($record['unit_cost'], 2) }}</td>
                                        <td class="text-right">{{ number_format($record['total_cost'], 2) }}</td>
                                        <td>{{ $record['reference'] }}</td>
                                        <td>{{ $record['batch_number'] }}</td>
                                        <td>{{ $record['type'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight: bold; background-color: #f5f5f5;">
                                        <td colspan="3"><strong>Total</strong></td>
                                        <td class="text-right"><strong>{{ number_format($totals['total_qty'], 4) }}</strong></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"><strong>{{ number_format($totals['total_cost'], 2) }}</strong></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-12">
                            <p>No manufacturing records found for the selected criteria.</p>
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
