<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>

    <style>
        /* PDF Page Settings */
        @page { margin: 20px; } /* Top, Bottom, Left, Right Margin */
        body { font-family: 'Arial', sans-serif; font-size: 12px; margin: 10px; }

        /* Title Styling */
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: right;
        }

        th {
            background-color: #f4f4f4;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        /* Alternating Row Colors */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Grand Total Row */
        .grand-total {
            font-weight: bold;
            background-color: #ddd;
        }

        /* Totals Row */
        .totals-row {
            font-weight: bold;
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>

    <!-- Report Title -->
    <div class="title">
        Sales Report <br>
        From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }} 
        To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
    </div>

    @php
        $grand_total_cost = 0;
        $grand_total_profit = 0;
        $grand_total_amount = 0;
    @endphp

    @foreach ($salesByGroup as $groupKey => $groupSales)
        @php
            $total_cost = 0;
            $total_profit = 0;
            $total_amount = 0;
        @endphp

        <!-- Section Header -->
        <h3>
            @if ($group_by_category)
                Category: {{ $groupSales->first()->category_code }} - {{ $groupSales->first()->category_name }}
            @elseif ($group_by_product)
                Product: {{ $groupSales->first()->product_code }} - {{ $groupSales->first()->product_name }}
            @else
                Branch: {{ $groupSales->first()->branch_code }} - {{ $groupSales->first()->branch_name }}
            @endif
        </h3>

        <!-- Data Table -->
        <table>
            <thead>
                <tr>
                    @if (!$group_by_category && !$group_by_product)
                        <th>Category Code</th>
                        <th>Category Name</th>
                    @endif
                    @if ($group_by_category)
                        <th>Branch Code</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                    @endif
                    @if ($group_by_product)
                        <th>Branch Code</th>
                    @endif
                    <th>QTY Available</th>
                    <th>QTY Sold</th>
                    <th>Amount</th>
                    <th>Cost</th>
                    <th>Margin</th>
                    <th>Margin (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupSales as $sale)
                    @php
                        $profit = $sale->amount - $sale->cost;
                        $total_cost += $sale->cost;
                        $total_profit += $profit;
                        $total_amount += $sale->amount;
                    @endphp
                    <tr>
                        @if (!$group_by_category && !$group_by_product)
                            <td>{{ $sale->category_code }}</td>
                            <td>{{ $sale->category_name }}</td>
                        @endif
                        @if ($group_by_category)
                            <td>{{ $sale->branch_code }}</td>
                            <td>{{ $sale->product_code }}</td>
                            <td>{{ $sale->product_name }}</td>
                        @endif
                        @if ($group_by_product)
                            <td>{{ $sale->branch_code }}</td>
                        @endif
                        <td>{{ number_format($sale->qty_available, 2, '.', ',') }}</td>
                        <td>{{ number_format($sale->quantity, 2, '.', ',') }}</td>
                        <td>{{ number_format($sale->amount, 2, '.', ',') }}</td>
                        <td>{{ number_format($sale->cost, 2, '.', ',') }}</td>
                        <td>{{ number_format($profit, 2, '.', ',') }}</td>
                        <td>{{ $sale->amount != 0 ? number_format(($profit / $sale->amount) * 100, 2) : '0.00' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <th colspan="{{ !$group_by_category && !$group_by_product ? 5 : 6 }}" style="text-align: right">TOTAL</th>
                    <th>{{ number_format($total_amount, 2, '.', ',') }}</th>
                    <th>{{ number_format($total_cost, 2, '.', ',') }}</th>
                    <th>{{ number_format($total_profit, 2, '.', ',') }}</th>
                    <th>
                        {{ $total_amount != 0 ? number_format(($total_profit / $total_amount) * 100, 2) : '0.00' }}
                    </th>
                </tr>
            </tfoot>
        </table>

        @php
            $grand_total_cost += $total_cost;
            $grand_total_profit += $total_profit;
            $grand_total_amount += $total_amount;
        @endphp
    @endforeach

    <!-- GRAND TOTAL -->
    <table>
        <tfoot>
            <tr class="grand-total">
                <th colspan="5" style="text-align: right">GRAND TOTAL</th>
                <th>{{ number_format($grand_total_amount, 2, '.', ',') }}</th>
                <th>{{ number_format($grand_total_cost, 2, '.', ',') }}</th>
                <th>{{ number_format($grand_total_profit, 2, '.', ',') }}</th>
                <th>
                    {{ $grand_total_amount != 0 ? number_format(($grand_total_profit / $grand_total_amount) * 100, 2) : '0.00' }}
                </th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
