@extends('layouts.print')

@section('title', 'Manufacturing Teams Report')

@section('content')
<div class="print-header">
    <h2>Manufacturing Teams Report</h2>
    <p>Period: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</p>
</div>

<div class="print-summary">
    <table class="summary-table">
        <tr>
            <td><strong>Total Teams:</strong> {{ $teams->count() }}</td>
            <td><strong>Single Manufacturing:</strong> {{ $totals['total_single_count'] }} records</td>
            <td><strong>Batch Productions:</strong> {{ $totals['total_batch_count'] }} records</td>
        </tr>
        <tr>
            <td><strong>Total Penalties:</strong> {{ number_format($totals['total_penalties'], 2) }}</td>
            <td><strong>Total Single Cost:</strong> {{ number_format($totals['total_single_cost'], 2) }}</td>
            <td><strong>Total WIP Value:</strong> {{ number_format($totals['total_batch_wip'], 2) }}</td>
        </tr>
    </table>
</div>

@if($teams->count() > 0)
<h4>Team Performance Details</h4>
<table class="print-table">
    <thead>
        <tr>
            <th>Team Name</th>
            <th>Members</th>
            <th class="text-center">Single Mfg Count</th>
            <th class="text-right">Single Mfg Qty</th>
            <th class="text-right">Single Mfg Cost</th>
            <th class="text-center">Batch Count</th>
            <th class="text-right">Batch Qty</th>
            <th class="text-right">Batch WIP</th>
            <th class="text-right">Penalties</th>
        </tr>
    </thead>
    <tbody>
        @foreach($teams as $team)
        <tr>
            <td>{{ $team->name }}</td>
            <td class="text-center">{{ $team->members->count() }}</td>
            <td class="text-center">{{ $team->single_count ?? 0 }}</td>
            <td class="text-right">{{ number_format($team->single_qty ?? 0, 4) }}</td>
            <td class="text-right">{{ number_format($team->single_cost ?? 0, 2) }}</td>
            <td class="text-center">{{ $team->batch_count ?? 0 }}</td>
            <td class="text-right">{{ number_format($team->batch_qty ?? 0, 0) }}</td>
            <td class="text-right">{{ number_format($team->batch_wip ?? 0, 2) }}</td>
            <td class="text-right">{{ number_format($team->total_penalties ?? 0, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><strong>Grand Total</strong></td>
            <td class="text-center"><strong>{{ $totals['total_single_count'] }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['total_single_qty'], 4) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['total_single_cost'], 2) }}</strong></td>
            <td class="text-center"><strong>{{ $totals['total_batch_count'] }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['total_batch_qty'], 0) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['total_batch_wip'], 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totals['total_penalties'], 2) }}</strong></td>
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
    .text-center { text-align: center; }
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
