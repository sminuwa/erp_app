<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Single Product Manufacturing - {{ $record->reference }}</title>
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
        .totals { margin-top: 10px; }
        .totals td { padding: 5px 10px; }
        .totals .label { font-weight: bold; text-align: right; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SINGLE PRODUCT MANUFACTURING</h1>
        <h2>{{ $record->reference }}</h2>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Manufacturing Date:</td>
            <td>{{ $record->manufacturing_date ? date('d-M-Y', strtotime($record->manufacturing_date)) : '-' }}</td>
            <td class="label">Status:</td>
            <td>{{ ucfirst($record->status) }}</td>
        </tr>
        <tr>
            <td class="label">Batch Number:</td>
            <td>{{ $record->batch_number }}</td>
            <td class="label">Quantity:</td>
            <td>{{ number_format($record->quantity, 4) }}</td>
        </tr>
        <tr>
            <td class="label">Requisition:</td>
            <td>{{ $record->requisition->reference ?? '-' }}</td>
            <td class="label">BOM:</td>
            <td>{{ $record->bom->reference ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Finish Product:</td>
            <td>{{ $record->bom->finishProduct->name ?? '-' }}</td>
            <td class="label">Team:</td>
            <td>{{ $record->team->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Machine:</td>
            <td>{{ $record->machine->description ?? '-' }}</td>
            <td class="label">Unit Cost:</td>
            <td>{{ number_format($record->unit_cost, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Total Material Cost:</td>
            <td>{{ number_format($record->total_material_cost, 2) }}</td>
            <td class="label">Total Cost:</td>
            <td>{{ number_format($record->total_cost, 2) }}</td>
        </tr>
    </table>

    <h3>Raw Materials Used</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Cost</th>
                <th class="text-right">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @php $totalMaterialCost = 0; @endphp
            @foreach($record->materials as $index => $material)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $material->product->code ?? '' }}</td>
                <td>{{ $material->product->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($material->quantity, 4) }}</td>
                <td class="text-right">{{ number_format($material->unit_cost, 2) }}</td>
                <td class="text-right">{{ number_format($material->total_cost, 2) }}</td>
            </tr>
            @php $totalMaterialCost += $material->total_cost; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">Total Material Cost:</th>
                <th class="text-right">{{ number_format($totalMaterialCost, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    @if($record->notes)
    <h3>Notes</h3>
    <p>{{ $record->notes }}</p>
    @endif

    <script>window.print();</script>
</body>
</html>
