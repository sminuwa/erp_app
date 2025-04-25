{{-- <div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">
                    Sales Report From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="display table table-bordered table-striped" data-ordering="true" id="example1">
                    <thead>
                        <tr>
                            @if ($group_by_category)
                                <th>CODE</th>
                                <th>CATEGORY</th>
                            @endif
                            @if ($group_by_product)
                                <th>CODE</th>
                                <th>PRODUCT</th>
                            @endif
                            <th>QTY AVAILABLE</th>
                            <th>QTY SOLD</th>
                            <th>UNIT</th>
                            <th>AMOUNT</th>
                            <th>COST</th>
                            <th>PROFIT</th>
                            <th>MARGIN (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            @php
                                $profit = $sale->amount - $sale->cost;
                            @endphp
                            <tr>
                                @if ($group_by_category)
                                    <td>{{ $sale->category_code }}</td>
                                    <td>{{ $sale->category }}</td>
                                @endif
                                @if ($group_by_product)
                                    <td>{{ $sale->product_code ?? '-' }}</td>
                                    <td>{{ $sale->product_name ?? '-' }}</td>
                                @endif
                                <td style="text-align: right">{{ number_format($sale->qty_available, 6) }}</td>
                                <td style="text-align: right">{{ number_format($sale->quantity, 6) }}</td>
                                <td>{{ $sale->product_unit }}</td>
                                <td style="text-align: right">{{ number_format($sale->amount, 2, '.', ',') }}</td>
                                <td style="text-align: right">{{ number_format($sale->cost, 2, '.', ',') }}</td>
                                <td style="text-align: right">{{ number_format($profit, 2, '.', ',') }}</td>
                                <td style="text-align: right">
                                    {{ $sale->amount != 0 ? number_format(($profit / $sale->amount) * 100, 2) : 0 }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> --}}
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">
                    Sales Report From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="display table table-bordered table-striped" data-ordering="true" id="example1">
                    <thead>
                        <tr>
                            @if ($group_by_category)
                                <th>CODE</th>
                                <th>CATEGORY</th>
                            @endif
                            @if ($group_by_product)
                                <th>CODE</th>
                                <th>PRODUCT</th>
                            @endif
                            <th>QTY AVAILABLE</th>
                            <th>QTY SOLD</th>
                            <th>UNIT</th>
                            <th>AMOUNT</th>
                            <th>COST</th>
                            <th>PROFIT</th>
                            <th>MARGIN (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_qty_available = 0;
                            $total_qty_sold = 0;
                            $total_amount = 0;
                            $total_cost = 0;
                            $total_profit = 0;
                        @endphp
                        
                        @foreach ($sales as $sale)
                            @php
                                $profit = $sale->amount - $sale->cost;
                                $total_qty_available += $sale->qty_available;
                                $total_qty_sold += $sale->quantity;
                                $total_amount += $sale->amount;
                                $total_cost += $sale->cost;
                                $total_profit += $profit;
                            @endphp
                            <tr>
                                @if ($group_by_category)
                                    <td>{{ $sale->category_code }}</td>
                                    <td>{{ $sale->category }}</td>
                                @endif
                                @if ($group_by_product)
                                    <td>{{ $sale->product_code ?? '-' }}</td>
                                    <td>{{ $sale->product_name ?? '-' }}</td>
                                @endif
                                <td style="text-align: right">{{ number_format($sale->qty_available, 6) }}</td>
                                <td style="text-align: right">{{ number_format($sale->quantity, 6) }}</td>
                                <td>{{ $sale->product_unit }}</td>
                                <td style="text-align: right">{{ number_format($sale->amount, 2, '.', ',') }}</td>
                                <td style="text-align: right">{{ number_format($sale->cost, 2, '.', ',') }}</td>
                                <td style="text-align: right">{{ number_format($profit, 2, '.', ',') }}</td>
                                <td style="text-align: right">
                                    {{ $sale->amount != 0 ? number_format(($profit / $sale->amount) * 100, 2) : 0 }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; background-color: #f5f5f5;">
                            @if ($group_by_category)
                                <td colspan="2" style="text-align: right">TOTAL</td>
                            @endif
                            @if ($group_by_product)
                                <td colspan="2" style="text-align: right">TOTAL</td>
                            @endif
                            <td style="text-align: right">{{ number_format($total_qty_available, 6) }}</td>
                            <td style="text-align: right">{{ number_format($total_qty_sold, 6) }}</td>
                            <td></td>
                            <td style="text-align: right">{{ number_format($total_amount, 2, '.', ',') }}</td>
                            <td style="text-align: right">{{ number_format($total_cost, 2, '.', ',') }}</td>
                            <td style="text-align: right">{{ number_format($total_profit, 2, '.', ',') }}</td>
                            <td style="text-align: right">
                                {{ $total_amount != 0 ? number_format(($total_profit / $total_amount) * 100, 2) : 0 }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
