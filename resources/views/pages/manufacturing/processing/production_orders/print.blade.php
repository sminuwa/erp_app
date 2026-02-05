<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Production Order - {{ $record->reference }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .info-table .label { font-weight: bold; width: 150px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #333; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        .badge-warning { background-color: #ffc107; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>PRODUCTION ORDER</h1>
        <h2>{{ $record->reference }}</h2>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Order Date:</td>
            <td>{{ $record->order_date ? date('d-M-Y', strtotime($record->order_date)) : '-' }}</td>
            <td class="label">Status:</td>
            <td>{{ ucfirst($record->status) }}</td>
        </tr>
        <tr>
            <td class="label">Start Date:</td>
            <td>{{ $record->start_date ? date('d-M-Y', strtotime($record->start_date)) : '-' }}</td>
            <td class="label">End Date:</td>
            <td>{{ $record->end_date ? date('d-M-Y', strtotime($record->end_date)) : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Branch:</td>
            <td>{{ $record->branch->name ?? '-' }}</td>
            <td class="label">Created By:</td>
            <td>{{ $record->createdBy->name ?? '-' }}</td>
        </tr>
    </table>

    <h3>Order Items</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>BOM Reference</th>
                <th>Finish Product</th>
                <th>Type</th>
                <th class="text-right">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->bom->reference ?? '-' }}</td>
                <td>{{ $item->bom->finishProduct->name ?? '-' }}</td>
                <td>{{ ucfirst($item->bom->bom_type ?? '') }}</td>
                <td class="text-right">{{ number_format($item->quantity, 4) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Required Raw Materials</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Source Store</th>
                <th class="text-right">Required Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->materials as $index => $material)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $material->product->code ?? '' }} - {{ $material->product->name ?? '-' }}</td>
                <td>{{ $material->store->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($material->required_qty, 4) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>window.print();</script>
</body>
</html>
