<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.stock.balances.report.print', [$from_date,$to_date,$store_id, $category_id, $product_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">PREVIOUS STOCK BALANCES REPORT </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="3">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="2">Pricessed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>DATE</th>
            <th>GROUP NAME</th>
            <th>ITEM NAME</th>
            <th>QUANTITY</th>
            <th>STORE</th>
        </tr>
    </thead>
    @foreach ($stores as $store)
        <tr>
            <td> {{ \Carbon\Carbon::parse($store->date)->toFormattedDateString() }} </td>
            <td>{{ $store->group }} </td>
            <td> {{ $store->name }} </td>
            <td> {{ $store->qty }} </td>
            <td>{{ $store->store }} </td>
        </tr>
    @endforeach
</table>
