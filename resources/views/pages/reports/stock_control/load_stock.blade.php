<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.current.stock.report.print', [$store_id, $category_id, $product_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">CURRENT STOCK REPORT </h5>
    </caption>
    <thead>
        <tr>
            <th>ITEM</th>
            <th>QTY</th>
            <th>COST PRICE</th>
            <th>SELLING PRICE</th>
            <th>TOTAL COST (&#8358;)</th>
            <th>TOTAL SEELING (&#8358;)</th>
            <th>STORE</th>
        </tr>
    </thead>
    @foreach ($stores as $store)
        <tr>
            <td> {{ $store->name }} </td>
            <td> {{ $store->qty_available }} </td>
            <td style="text-align: right;"> &#8358;{{ number_format($store->cost_price,2,'.',',') }} </td>
            <td style="text-align: right;"> &#8358;{{ number_format($store->selling_price,2,'.',',') }} </td>
            <td style="text-align: right;">{{ number_format($store->qty_available * $store->cost_price, 2, '.', ',') }} </td>
            <td style="text-align: right;">{{ number_format($store->qty_available * $store->selling_price, 2, '.', ',') }} </td>
            <td>{{$store->store }} </td>
        </tr>
    @endforeach
    <tfoot>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tfoot>
</table>
