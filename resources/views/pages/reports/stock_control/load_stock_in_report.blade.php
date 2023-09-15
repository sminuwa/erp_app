<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.stock.in.reports', [$from_date, $to_date,$store_id,$category_id,$product_id])}}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Stock
            In Between
            {{ $from_date }} and
            {{ $to_date }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Date</th>
            <th>Return No</th>
            <th>Item Name</th>
            <th>Rate</th>
            <th>QTY</th>
            <th>Total Amount</th>
            <th>Store</th>
            <th>Branch</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Date</th>
            <th>Return No</th>
            <th>Item Name</th>
            <th>Rate</th>
            <th>QTY</th>
            <th>Total Amount</th>
            <th>Store</th>
            <th>Branch</th>
        </tr>
    </tfoot>
    @foreach ($purchases as $purchase)
        <tr>
            <td>{{ $purchase->purchase_date->toFormattedDateString() }}</td>
            <td>{{ $purchase->invoice }}</td>
            <td>{{ $purchase->name }}</td>
            <td style="text-align: right">{{ number_format($purchase->unit_price,2,'.',',') }}</td>
            <td style="text-align: center">{{ $purchase->qty_supplied }}</td>
            <td style="text-align: right">{{ number_format(($purchase->unit_price * $purchase->qty_supplied),2,'.',',') }}</td>
            <td>{{ $purchase->store }}</td>
            <td>{{ $purchase->branch }}</td>
        </tr>
    @endforeach
</table>
