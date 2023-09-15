<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.stock.adjustment.reports', [$from_date,$to_date,$store_id, $category_id, $product_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">STOCK ADJUSTMENT REPORT </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>ITEM NAME</th>
            <th>QUANTITY</th>
            <th>STORE</th>
            <th>ADJUSTMENT NO</th>

        </tr>
    </thead>
    @foreach ($stores as $store)
        <tr>
            <td> {{ \Carbon\Carbon::parse($store->date)->toFormattedDateString() }} </td>
            <td> {{ $store->name }} </td>
            <td> {{ $store->adjusted_qty }} </td>
            <td>{{ $store->store }} </td>
            <td> {{ $store->refno }} </td>
        </tr>
    @endforeach
</table>
