<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Materials Requisitions Report - {{ config('app.name', 'ERP') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <style>
        body { font-family: 'Source Sans Pro', Arial, sans-serif; font-size: 12px; }
        .print-header { text-align: center; margin-bottom: 20px; }
        .print-header h3 { margin: 5px 0; }
        .print-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .print-table th, .print-table td { border: 1px solid #ddd; padding: 5px; }
        .print-table th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
        .req-header { background-color: #e9ecef; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="print-header">
            <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:50px;height:50px;" alt="Logo">
            <h3>Materials Requisitions Report</h3>
            <h5>Period: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</h5>
        </div>

        <table class="print-table">
            <tr>
                <td><strong>Total Requisitions:</strong> {{ $totals['total_requisitions'] }}</td>
                <td><strong>Total Required Qty:</strong> {{ number_format($totals['total_required_qty'], 4) }}</td>
                <td><strong>Total Cost:</strong> {{ number_format($totals['total_cost'], 2) }}</td>
                <td><strong>Printed:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</td>
                <td><strong>By:</strong> {{ auth()->user()->name }}</td>
            </tr>
        </table>

        @foreach($requisitions as $requisition)
        <table class="print-table">
            <tr class="req-header">
                <td colspan="8">
                    {{ $requisition->reference }} | {{ date('d M Y', strtotime($requisition->requisition_date)) }}
                    | Status: {{ ucfirst($requisition->status) }}
                    @if($requisition->schedule)
                        | Schedule: {{ $requisition->schedule->reference }}
                        | Order: {{ $requisition->schedule->productionOrder->reference ?? 'N/A' }}
                    @elseif($requisition->bom)
                        | BOM: {{ $requisition->bom->reference }}
                        | Product: {{ $requisition->bom->finishProduct->name ?? 'N/A' }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>#</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Source Store</th>
                <th class="text-right">Required Qty</th>
                <th class="text-right">Issued Qty</th>
                <th class="text-right">Received Qty</th>
                <th class="text-right">Total Cost</th>
            </tr>
            @foreach($requisition->items as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $item->product->code ?? 'N/A' }}</td>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td>{{ $item->sourceStore->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->required_qty, 4) }}</td>
                <td class="text-right">{{ number_format($item->issued_qty ?? 0, 4) }}</td>
                <td class="text-right">{{ number_format($item->received_qty ?? 0, 4) }}</td>
                <td class="text-right">{{ number_format($item->total_cost ?? 0, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td colspan="4">Requisition Total</td>
                <td class="text-right">{{ number_format($requisition->items->sum('required_qty'), 4) }}</td>
                <td class="text-right">{{ number_format($requisition->items->sum('issued_qty'), 4) }}</td>
                <td class="text-right">{{ number_format($requisition->items->sum('received_qty'), 4) }}</td>
                <td class="text-right">{{ number_format($requisition->items->sum('total_cost'), 2) }}</td>
            </tr>
        </table>
        @endforeach
    </div>
    <script src="{{ asset('assets/backend/plugins/jquery/jquery.min.js') }}"></script>
    <script>window.print();</script>
</body>
</html>
