<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Production Orders Report - {{ config('app.name', 'ERP') }}</title>
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
        .text-center { text-align: center; }
        .order-header { background-color: #e9ecef; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="print-header">
            <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:50px;height:50px;" alt="Logo">
            <h3>Production Orders Report</h3>
            <h5>Period: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</h5>
        </div>

        <table class="print-table">
            <tr>
                <td><strong>Total Orders:</strong> {{ $totals['total_orders'] }}</td>
                <td><strong>Total Qty:</strong> {{ number_format($totals['total_qty'], 4) }}</td>
                <td><strong>Total Processed:</strong> {{ number_format($totals['total_processed'], 4) }}</td>
                <td><strong>Printed:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</td>
                <td><strong>By:</strong> {{ auth()->user()->name }}</td>
            </tr>
        </table>

        @foreach($orders as $order)
        <table class="print-table">
            <tr class="order-header">
                <td colspan="7">
                    {{ $order->reference }} | {{ date('d M Y', strtotime($order->order_date)) }}
                    | Status: {{ ucfirst($order->status) }}
                    | Branch: {{ $order->branch->name ?? 'N/A' }}
                    | Period: {{ $order->start_date ? date('d/m/Y', strtotime($order->start_date)) : '-' }} to {{ $order->end_date ? date('d/m/Y', strtotime($order->end_date)) : '-' }}
                </td>
            </tr>
            <tr>
                <th>#</th>
                <th>BOM Reference</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th class="text-right">Qty to Produce</th>
                <th class="text-right">Scheduled</th>
                <th class="text-right">Produced</th>
            </tr>
            @php $orderTargetQty = 0; @endphp
            @foreach($order->items as $idx => $item)
            @php
                $isBatch = ($item->bom->bom_type ?? 'single') === 'batch';
                $targetQty = $isBatch
                    ? $item->quantity_to_produce * ($item->bom->actual_output ?? 1)
                    : $item->quantity_to_produce;
                $orderTargetQty += $targetQty;
                $progress = $targetQty > 0 ? round(($item->produced_qty / $targetQty) * 100, 1) : 0;
            @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $item->bom->reference ?? 'N/A' }}</td>
                <td>{{ $item->bom->finishProduct->code ?? 'N/A' }}</td>
                <td>{{ $item->bom->finishProduct->name ?? 'N/A' }}</td>
                <td class="text-right">
                    {{ number_format($item->quantity_to_produce, 4) }}
                    @if($isBatch)<br><small>({{ number_format($targetQty, 4) }} units)</small>@endif
                </td>
                <td class="text-right">{{ number_format($item->scheduled_qty ?? 0, 4) }}</td>
                <td class="text-right">{{ number_format($item->produced_qty ?? 0, 4) }} ({{ $progress }}%)</td>
            </tr>
            @endforeach
            @php $orderProgress = $orderTargetQty > 0 ? round(($order->processed_qty / $orderTargetQty) * 100, 1) : 0; @endphp
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td colspan="4">Order Total</td>
                <td class="text-right">{{ number_format($orderTargetQty, 4) }} units</td>
                <td class="text-right">{{ number_format($order->items->sum('scheduled_qty'), 4) }}</td>
                <td class="text-right">{{ number_format($order->processed_qty, 4) }} ({{ $orderProgress }}%)</td>
            </tr>
        </table>
        @endforeach
    </div>
    <script src="{{ asset('assets/backend/plugins/jquery/jquery.min.js') }}"></script>
    <script>window.print();</script>
</body>
</html>
