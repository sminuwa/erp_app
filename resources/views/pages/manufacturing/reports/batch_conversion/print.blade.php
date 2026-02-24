<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Batch Conversion Report - {{ config('app.name', 'ERP') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <style>
        body { font-family: 'Source Sans Pro', Arial, sans-serif; font-size: 11px; }
        .print-header { text-align: center; margin-bottom: 20px; }
        .print-header h3 { margin: 5px 0; }
        .print-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .print-table th, .print-table td { border: 1px solid #ddd; padding: 4px 6px; }
        .print-table th { background-color: #343a40; color: #fff; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .row-shortage { background-color: #fff3cd; }
        .row-excess   { background-color: #f8d7da; }
        .row-within   { background-color: #d4edda; }
        .summary-row  { background-color: #f5f5f5; font-weight: bold; }
        .badge-within   { color: #155724; }
        .badge-shortage { color: #856404; }
        .badge-excess   { color: #721c24; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="print-header">
            <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:50px;height:50px;" alt="Logo">
            <h3>Batch Conversion Report</h3>
            <h5>Period: {{ date('d M Y', strtotime($dateFrom)) }} &ndash; {{ date('d M Y', strtotime($dateTo)) }}</h5>
        </div>

        <table class="print-table" style="margin-bottom: 20px;">
            <tr>
                <td><strong>Total Conversions:</strong> {{ $totals['total_conversions'] }}</td>
                <td><strong>Posted:</strong> {{ $totals['posted'] }}</td>
                <td><strong>Pending:</strong> {{ $totals['pending'] }}</td>
                <td><strong>Within Range:</strong> {{ $totals['within_count'] }}</td>
                <td><strong>Shortage:</strong> {{ $totals['shortage_count'] }}</td>
                <td><strong>Excess:</strong> {{ $totals['excess_count'] }}</td>
                <td><strong>Total Produced:</strong> {{ number_format($totals['total_produced'], 4) }}</td>
                <td><strong>Total Cost:</strong> {{ number_format($totals['total_cost'], 2) }}</td>
            </tr>
            <tr>
                <td colspan="6"><strong>Printed:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</td>
                <td colspan="2"><strong>By:</strong> {{ auth()->user()->name }}</td>
            </tr>
        </table>

        <table class="print-table">
            <tr>
                <th>#</th>
                <th>Reference</th>
                <th>Date</th>
                <th>Batch No.</th>
                <th>Product</th>
                <th class="text-right">Produced Qty</th>
                <th class="text-right">Expected Qty</th>
                <th class="text-right">Min Qty</th>
                <th class="text-right">Max Qty</th>
                <th class="text-center">Tolerance Status</th>
                <th class="text-right">Diff from Tolerance</th>
                <th class="text-right">WIP Cost</th>
                <th class="text-right">Unit Cost</th>
                <th class="text-right">Total Cost</th>
                <th class="text-center">Status</th>
            </tr>
            @foreach($conversions as $idx => $conv)
            @php
                $trClass = $conv->diff_type === 'shortage' ? 'row-shortage' : ($conv->diff_type === 'excess' ? 'row-excess' : 'row-within');
            @endphp
            <tr class="{{ $trClass }}">
                <td>{{ $idx + 1 }}</td>
                <td>{{ $conv->reference }}</td>
                <td>{{ date('d/m/Y', strtotime($conv->conversion_date)) }}</td>
                <td>{{ $conv->batchProduction->batch_number ?? 'N/A' }}</td>
                <td>{{ ($conv->finishProduct->code ?? '') }} {{ $conv->finishProduct->name ?? 'N/A' }}</td>
                <td class="text-right"><strong>{{ number_format($conv->produced_qty, 4) }}</strong></td>
                <td class="text-right">{{ number_format($conv->expected_qty, 4) }}</td>
                <td class="text-right">{{ number_format($conv->min_qty, 4) }}{{ $conv->accepted_shortage > 0 ? ' (-'.$conv->accepted_shortage.'%)' : '' }}</td>
                <td class="text-right">{{ number_format($conv->max_qty, 4) }}{{ $conv->accepted_excess > 0 ? ' (+'.$conv->accepted_excess.'%)' : '' }}</td>
                <td class="text-center">
                    @if($conv->diff_type === 'within')
                        <span class="badge-within">&#10003; Within</span>
                    @elseif($conv->diff_type === 'shortage')
                        <span class="badge-shortage">&#9660; Short</span>
                    @else
                        <span class="badge-excess">&#9650; Excess</span>
                    @endif
                </td>
                <td class="text-right">
                    @if($conv->diff_type === 'within')
                        &mdash;
                    @elseif($conv->diff_type === 'shortage')
                        {{ number_format($conv->tolerance_diff, 4) }}
                    @else
                        +{{ number_format($conv->tolerance_diff, 4) }}
                    @endif
                </td>
                <td class="text-right">{{ number_format($conv->wip_cost_deducted, 2) }}</td>
                <td class="text-right">{{ number_format($conv->unit_cost, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($conv->total_cost, 2) }}</strong></td>
                <td class="text-center">{{ ucfirst($conv->status) }}</td>
            </tr>
            @endforeach
            <tr class="summary-row">
                <td colspan="5">Grand Total</td>
                <td class="text-right">{{ number_format($totals['total_produced'], 4) }}</td>
                <td class="text-right">{{ number_format($totals['total_expected'], 4) }}</td>
                <td colspan="4"></td>
                <td class="text-right">{{ number_format($totals['total_wip_cost'], 2) }}</td>
                <td></td>
                <td class="text-right">{{ number_format($totals['total_cost'], 2) }}</td>
                <td></td>
            </tr>
        </table>
    </div>
    <script src="{{ asset('assets/backend/plugins/jquery/jquery.min.js') }}"></script>
    <script>window.print();</script>
</body>
</html>
