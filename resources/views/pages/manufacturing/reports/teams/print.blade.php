<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Manufacturing Teams Report - {{ config('app.name', 'ERP') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <style>
        body { font-family: 'Source Sans Pro', Arial, sans-serif; font-size: 11px; }
        .print-header { text-align: center; margin-bottom: 20px; }
        .print-header h3 { margin: 5px 0; }
        .print-header h5 { margin: 5px 0; }
        .print-header p { margin: 2px 0; }
        .print-summary { margin-bottom: 15px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 5px; border: 1px solid #ddd; background: #f9f9f9; }
        .print-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .print-table th, .print-table td { border: 1px solid #ddd; padding: 4px 6px; }
        .print-table th { background-color: #f5f5f5; font-weight: bold; }
        .print-table tbody tr:nth-child(even) { background-color: #fafafa; }
        .team-section { margin-bottom: 20px; page-break-inside: avoid; }
        .team-header { background-color: #e9ecef; font-weight: bold; }
        .team-header td { padding: 8px; border-left: 4px solid #007bff; }
        .team-summary { margin: 0 0 10px 0; font-size: 10px; color: #666; }
        .subtotal-row { background-color: #e9ecef !important; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .grand-total { margin-top: 20px; page-break-inside: avoid; }
        .grand-total-header { background-color: #28a745; color: white; padding: 8px; font-weight: bold; }
        .total-highlight { background-color: #d4edda !important; }
        .print-footer { margin-top: 30px; text-align: right; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }
        @media print {
            .no-print { display: none; }
            .team-section { page-break-inside: avoid; }
            body { font-size: 10px; }
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
                        <h3>Manufacturing Teams Report</h3>
                        <h5>Period: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</h5>
                        @if(!empty($filters['branch_id']))
                        <p>Factory: {{ \App\Models\Branch::find($filters['branch_id'])->name ?? 'All' }}</p>
                        @endif
                        @if(!empty($filters['team_id']))
                        <p>Team: {{ \App\Models\ManufacturingTeam::find($filters['team_id'])->name ?? 'All' }}</p>
                        @endif
                        @if(!empty($filters['category_id']))
                        <p>Category: {{ \App\Models\Category::find($filters['category_id'])->name ?? 'All' }}</p>
                        @endif
                    </div>

                    <div class="print-summary">
                        <table class="summary-table">
                            <tr>
                                <td><strong>Total Records:</strong> {{ $totals['total_records'] }}</td>
                                <td><strong>Total Quantity:</strong> {{ number_format($totals['total_qty'], 4) }}</td>
                                <td><strong>Total Cost:</strong> {{ number_format($totals['total_cost'], 2) }}</td>
                                <td><strong>Date Processed:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</td>
                                <td><strong>Processed By:</strong> {{ auth()->user()->name }}</td>
                            </tr>
                        </table>
                    </div>

                    @if($productions->count() > 0)
                        @foreach($productionsByTeam as $teamName => $teamProductions)
                        <div class="team-section">
                            <table class="print-table">
                                <thead>
                                    <tr class="team-header">
                                        <td colspan="7">Team: {{ $teamName }} ({{ $teamProductions->count() }} records | Qty: {{ number_format($teamProductions->sum('quantity'), 4) }} | Cost: {{ number_format($teamProductions->sum('total_cost'), 2) }})</td>
                                    </tr>
                                    <tr>
                                        <th style="width: 40px;">S/N</th>
                                        <th>Product Code</th>
                                        <th>Product Description</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Unit Cost</th>
                                        <th class="text-right">Total Cost</th>
                                        <th>Batch Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teamProductions as $index => $production)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $production['product_code'] }}</td>
                                        <td>{{ $production['product_name'] }}</td>
                                        <td class="text-right">{{ number_format($production['quantity'], 4) }}</td>
                                        <td class="text-right">{{ number_format($production['unit_cost'], 2) }}</td>
                                        <td class="text-right">{{ number_format($production['total_cost'], 2) }}</td>
                                        <td>{{ $production['batch_number'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="subtotal-row">
                                        <td colspan="3"><strong>Team Subtotal</strong></td>
                                        <td class="text-right"><strong>{{ number_format($teamProductions->sum('quantity'), 4) }}</strong></td>
                                        <td class="text-right">-</td>
                                        <td class="text-right"><strong>{{ number_format($teamProductions->sum('total_cost'), 2) }}</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @endforeach

                        <div class="grand-total">
                            <table class="print-table">
                                <tr>
                                    <td class="grand-total-header" colspan="7">GRAND TOTAL</td>
                                </tr>
                                <tr class="total-highlight">
                                    <td colspan="3"><strong>Total</strong></td>
                                    <td class="text-right"><strong>{{ number_format($totals['total_qty'], 4) }}</strong></td>
                                    <td class="text-right">-</td>
                                    <td class="text-right"><strong>{{ number_format($totals['total_cost'], 2) }}</strong></td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    @else
                        <p>No production records found for the selected criteria.</p>
                    @endif

                    <div class="print-footer">
                        <p>Printed on: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
                    </div>
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
