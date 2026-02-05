@extends('layouts.print')

@section('title', 'Manufacturing History Report')

@section('content')
<div class="print-header">
    <h2>Manufacturing History Report</h2>
    <p>Period: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</p>
</div>

<div class="print-summary">
    <table class="summary-table">
        <tr>
            <td><strong>Single Manufacturing:</strong> {{ $totals['single_count'] }} records</td>
            <td><strong>Total Quantity:</strong> {{ number_format($totals['single_qty'], 4) }}</td>
            <td><strong>Total Cost:</strong> {{ number_format($totals['single_cost'], 2) }}</td>
        </tr>
        <tr>
            <td><strong>Batch Productions:</strong> {{ $totals['batch_count'] }} records</td>
            <td><strong>Total Batches:</strong> {{ number_format($totals['batch_qty'], 0) }}</td>
            <td><strong>WIP Value:</strong> {{ number_format($totals['batch_wip'], 2) }}</td>
        </tr>
    </table>
</div>

@if($singleManufacturing->count() > 0)
<h4>Single Product Manufacturing</h4>
<table class="print-table">
    <thead>
        <tr>
            <th>Reference</th>
            <th>Date</th>
            <th>BOM</th>
            <th>Finish Product</th>
            <th>Team</th>
            <th class="text-right">Quantity</th>
            <th class="text-right">Material Cost</th>
            <th class="text-right">Labor Cost</th>
            <th class="text-right">Total Cost</th>
            <th class="text-right">Unit Cost</th>
        </tr>
    </thead>
    <tbody>
        @foreach($singleManufacturing as $record)
        <tr>
            <td>{{ $record->reference }}</td>
            <td>{{ date('d M Y', strtotime($record->manufacturing_date)) }}</td>
            <td>{{ $record->bom->reference ?? 'N/A' }}</td>
            <td>{{ $record->bom->finishProduct->name ?? 'N/A' }}</td>
            <td>{{ $record->team->name ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($record->quantity, 4) }}</td>
            <td class="text-right">{{ number_format($record->material_cost, 2) }}</td>
            <td class="text-right">{{ number_format($record->labor_cost, 2) }}</td>
            <td class="text-right">{{ number_format($record->total_cost, 2) }}</td>
            <td class="text-right">{{ number_format($record->unit_cost, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5"><strong>Total</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['single_qty'], 4) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($singleManufacturing->sum('material_cost'), 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($singleManufacturing->sum('labor_cost'), 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['single_cost'], 2) }}</strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>
@endif

@if($batchProductions->count() > 0)
<h4>Batch Productions</h4>
<table class="print-table">
    <thead>
        <tr>
            <th>Batch Number</th>
            <th>Date</th>
            <th>BOM</th>
            <th>Finish Product</th>
            <th>Team</th>
            <th class="text-right">Batches</th>
            <th class="text-right">Material Cost</th>
            <th class="text-right">WIP Value</th>
            <th class="text-right">Converted Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach($batchProductions as $record)
        <tr>
            <td>{{ $record->batch_number }}</td>
            <td>{{ date('d M Y', strtotime($record->production_date)) }}</td>
            <td>{{ $record->bom->reference ?? 'N/A' }}</td>
            <td>{{ $record->bom->finishProduct->name ?? 'N/A' }}</td>
            <td>{{ $record->team->name ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($record->quantity, 0) }}</td>
            <td class="text-right">{{ number_format($record->material_cost, 2) }}</td>
            <td class="text-right">{{ number_format($record->wip_value, 2) }}</td>
            <td class="text-right">{{ number_format($record->converted_qty, 4) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5"><strong>Total</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['batch_qty'], 0) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($batchProductions->sum('material_cost'), 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['batch_wip'], 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['batch_converted'], 4) }}</strong></td>
        </tr>
    </tfoot>
</table>
@endif

<div class="print-footer">
    <p>Printed on: {{ date('d M Y H:i:s') }}</p>
</div>

<style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    .print-header { text-align: center; margin-bottom: 20px; }
    .print-header h2 { margin: 0; }
    .print-summary { margin-bottom: 20px; }
    .summary-table { width: 100%; border-collapse: collapse; }
    .summary-table td { padding: 5px; }
    .print-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .print-table th, .print-table td { border: 1px solid #ddd; padding: 5px; }
    .print-table th { background-color: #f5f5f5; }
    .text-right { text-align: right; }
    .print-footer { margin-top: 30px; text-align: right; font-size: 10px; }
    h4 { margin-top: 20px; margin-bottom: 10px; }
    @media print {
        .no-print { display: none; }
    }
</style>

<script>
    window.onload = function() { window.print(); }
</script>
@endsection
