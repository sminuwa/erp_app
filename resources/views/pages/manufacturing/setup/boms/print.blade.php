<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bill of Materials - {{ $record->reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 150px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f0f0f0;
        }
        .text-right {
            text-align: right;
        }
        .cost-summary {
            width: 50%;
            float: right;
        }
        .cost-summary td {
            padding: 5px;
        }
        .cost-summary .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BILL OF MATERIALS</h1>
        <h2>{{ $record->reference }} - {{ ucfirst($record->bom_type) }} Manufacturing</h2>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Description:</td>
            <td>{{ $record->description }}</td>
            <td class="label">Approval Document:</td>
            <td>{{ $record->approval_document_no ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Finish Product:</td>
            <td>{{ $record->finishProduct->code ?? '' }} - {{ $record->finishProduct->name ?? '-' }}</td>
            <td class="label">Category:</td>
            <td>{{ $record->category->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Output Store:</td>
            <td>{{ $record->outputStore->name ?? '-' }}</td>
            <td class="label">Branch:</td>
            <td>{{ $record->branch->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Actual Output:</td>
            <td>{{ number_format($record->actual_output, 4) }}</td>
            <td class="label">Status:</td>
            <td>{{ $record->status == 1 ? 'Active' : 'Inactive' }}</td>
        </tr>
        @if($record->bom_type == 'batch')
        <tr>
            <td class="label">Main Raw Material:</td>
            <td>{{ $record->mainRawMaterial->name ?? '-' }}</td>
            <td class="label">Accepted Excess/Shortage:</td>
            <td>{{ number_format($record->accepted_excess, 4) }} / {{ number_format($record->accepted_shortage, 4) }}</td>
        </tr>
        @endif
    </table>

    <h3>Materials</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Source Store</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Cost</th>
                <th class="text-right">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->materials as $index => $material)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $material->product->code ?? '-' }}</td>
                <td>{{ $material->product->name ?? '-' }}</td>
                <td>{{ $material->sourceStore->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($material->quantity, 4) }}</td>
                <td class="text-right">{{ number_format($material->unit_cost, 2) }}</td>
                <td class="text-right">{{ number_format($material->total_cost, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">Total Material Cost:</th>
                <th class="text-right">{{ number_format($record->total_material_cost, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="clearfix">
        <table class="cost-summary">
            <tr>
                <td class="label">Labor Cost:</td>
                <td class="text-right">{{ number_format($record->labor_cost, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Power Cost:</td>
                <td class="text-right">{{ number_format($record->power_cost, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Other Cost:</td>
                <td class="text-right">{{ number_format($record->other_cost, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="label">Total Other Cost:</td>
                <td class="text-right">{{ number_format($record->total_other_cost, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Total Material Cost:</td>
                <td class="text-right">{{ number_format($record->total_material_cost, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="label">Total Cost:</td>
                <td class="text-right">{{ number_format($record->total_material_cost + $record->total_other_cost, 2) }}</td>
            </tr>
            <tr class="total-row" style="background-color: #d4edda;">
                <td class="label">Cost Per Unit:</td>
                <td class="text-right">{{ number_format($record->total_cost_per_unit, 2) }}</td>
            </tr>
        </table>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
