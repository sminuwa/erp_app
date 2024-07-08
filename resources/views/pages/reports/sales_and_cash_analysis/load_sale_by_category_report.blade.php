<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="text-right">
                    <a href="{{ route('ajax.category.sales.report.print', [$from_date, $to_date, $branch_id, $category_id1, $category_id2]) }}"
                        target="_BLANK" class="btn-success btn btn-sm">Print</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-3 table-responsive">
                <table class="table table-bordered table-striped caption" id="example1">
                    <caption style="caption-size:top">
                        <h3 style="text-align: center;">{{ $branch == null ? 'All Branches' : $branch->name . "($branch->code)" }} </h3>
                        <h5 style="text-align: center;">Sale Transactions By Categoery
                            From
                            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                            AND
                            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                        </h5>
                    </caption>
                    <thead>
                        <tr>
                            <th>CODE</th>
                            <th>CATEGORY</th>
                            <th>QTY AVAILABLE</th>
                            <th>QTY SOLD</th>
                            <th>AMOUNT</th>
                            <th>COST</th>
                            <th>MARGIN</th>
                            <th>MARGIN (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $total_amount = 0;
                        $total_cost = 0;
                        $total_profit = 0;
                        $grand_total_profit = 0;
                    @endphp
                    @foreach ($sales as $sale)
                        <tr>
                            <td>{{ $sale->code }}</td>
                            <td>{{ $sale->category }}</td>
                            <td style="text-align: right">{{ number_format($sale->qty_available,6) }}</td>
                            <td style="text-align: right">{{ number_format($sale->quantity,6) }}</td>
                            <td style="text-align: right">{{ number_format($sale->amount, 2, '.', ',') }}</td>
                            <td style="text-align: right">{{ number_format($sale->cost, 2, '.', ',') }}</td>
                            <td style="text-align: right">
                                @php
                                    $total_profit = $sale->amount - $sale->cost;
                                    $grand_total_profit += $total_profit;
                                    $total_amount += $sale->amount;
                                    $total_cost += $sale->cost;
                                @endphp
                                @if ($total_profit < 0)
                                    ({{ number_format(abs($total_profit), 2, '.', ',') }})
                                @else
                                    {{ number_format($total_profit, 2) }}
                                @endif
                            </td>
                            <td style="text-align: right">{{ round(($total_profit / $sale->amount) * 100, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="4" style="text-align: right">TOTAL</th>
                        <th style="text-align: right">
                            {{ number_format($total_amount, 2, '.', ',') }}</th>
                        <th style="text-align: right">
                            {{ number_format($total_cost, 2, '.', ',') }}</th>
                        <th style="text-align: right">
                            @if ($grand_total_profit < 0)
                                ({{ number_format(abs($grand_total_profit), 2, '.', ',') }})
                            @else
                                {{ number_format($grand_total_profit, 2) }}
                            @endif
                        </th>
                        <th style="text-align:right">
                            {{ $total_amount != 0 ? number_format(($grand_total_profit / $total_amount) * 100, 2) : 0 }}</th>
                    </tr>
                    </tfoot>

                </table>
            </div>
        </div>
    </div>
</div>
