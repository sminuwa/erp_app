<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.order.lines.report.print', [$from_date, $to_date, $branch_id, $status]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">List of Order Lines Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>REFERENCE</th>
            <th>ORDER ACCOUNT</th>
            <th>ITEME</th>
            <th>QTY</th>
            <th>UNIT PRICE</th>
            <th>TOTAL</th>
            <th>STATUS</th>
        </tr>
    </thead>
    @php
        $total = 0;
    @endphp
    @foreach ($sales as $sale)
        @foreach ($sale->order_items as $item)
            @php
                $total += $sale->total;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
                <td>{{ $sale->reference }}</td>
                <td>{{ $sale->customer->code }}</td>
                <td>{{ $item->storeProduct->product->code }}</td>
                <td>{{ $item->quantity }}</td>
                <td style="text-align: right">{{ number_format($item->sold_price, 2, '.', ',') }}</td>
                <td style="text-align: right">{{ number_format($item->total, 2, '.', ',') }}</td>
                <td>{{ $sale->status == 1 ? 'Completed' : 'Pending' }}</td>
            </tr>
        @endforeach
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="6">Total</th>
            <th style="text-align: right">&#8358;{{ number_format($total, 2, '.', ',') }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
