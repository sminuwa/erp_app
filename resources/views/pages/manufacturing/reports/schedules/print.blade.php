<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Schedules Report - {{ config('app.name', 'ERP') }}</title>
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
        .schedule-header { background-color: #e9ecef; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="print-header">
            <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:50px;height:50px;" alt="Logo">
            <h3>Daily Manufacturing Schedules Report</h3>
            <h5>Period: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</h5>
        </div>

        <table class="print-table">
            <tr>
                <td><strong>Total Schedules:</strong> {{ $totals['total_schedules'] }}</td>
                <td><strong>Total Scheduled Qty:</strong> {{ number_format($totals['total_scheduled_qty'], 4) }}</td>
                <td><strong>Printed:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</td>
                <td><strong>By:</strong> {{ auth()->user()->name }}</td>
            </tr>
        </table>

        @foreach($schedules as $schedule)
        <table class="print-table">
            <tr class="schedule-header">
                <td colspan="5">
                    {{ $schedule->reference }} | {{ date('d M Y', strtotime($schedule->schedule_date)) }}
                    | Status: {{ ucfirst($schedule->status) }}
                    | Order: {{ $schedule->productionOrder->reference ?? 'N/A' }}
                    | Team: {{ $schedule->team->name ?? 'N/A' }}
                    | Machine: {{ $schedule->machine->name ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <th>#</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th class="text-right">Scheduled Qty</th>
                <th class="text-right">Requisitioned Qty</th>
            </tr>
            @foreach($schedule->items as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $item->productionOrderItem->bom->finishProduct->code ?? 'N/A' }}</td>
                <td>{{ $item->productionOrderItem->bom->finishProduct->name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($item->scheduled_qty, 4) }}</td>
                <td class="text-right">{{ number_format($item->requisitioned_qty ?? 0, 4) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td colspan="3">Schedule Total</td>
                <td class="text-right">{{ number_format($schedule->items->sum('scheduled_qty'), 4) }}</td>
                <td class="text-right">{{ number_format($schedule->items->sum('requisitioned_qty'), 4) }}</td>
            </tr>
        </table>
        @endforeach
    </div>
    <script src="{{ asset('assets/backend/plugins/jquery/jquery.min.js') }}"></script>
    <script>window.print();</script>
</body>
</html>
