<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.deleted.sales.report.print', [$from_date, $to_date, $customer_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Deleted Items Sold Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>TRANS DATE</th>
            <th>CUST NAME</th>
            <th>ITEM</th>
            <th>INVOICE</th>
            <th>PRICE</th>
            <th>QTY</th>
            <th>AMOUNT</th>
            <th>AMT PAID</th>
            <th>SALESMODE</th>
            <th>STORE</th>
            <th>DELETED BY</th>
            <th>DELETED DATE</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
                <td>{{ $sale->customer }}</td>
                <td>{{ $sale->item }}</td>
                <td>{{ $sale->invoice_no }}</td>
                <td style="text-align: right">&#8358;{{ number_format($sale->sold_price, 2, '.', ',') }}</td>
                <td style="text-align: right">{{ number_format($sale->quantity, 0) }}</td>
                <td style="text-align: right">&#8358;{{ number_format($sale->sold_price * $sale->quantity, 2, '.', ',') }}</td>
                <td style="text-align: right">&#8358;{{ number_format($sale->pay, 0) }}</td>
                <td>{{ $sale->payment_mode }}</td>
                <td>{{ $sale->payment_mode }}</td>
                <td></td>
                <td>{{ \Carbon\Carbon::parse($sale->updated_at)->toFormattedDateString() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
