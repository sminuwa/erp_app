<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.store.ledger.reports', [$store_id, $category_id, $product_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">STORE LEDGER REPORT </h5>
    </caption>
    <thead>
        <tr>
            <th>ITEM NAME</th>
            <th>GROUP NAME</th>
            <th>STORE</th>
            <th>QUANTITY</th>
            <th>COST PRICE</th>
           
        </tr>
    </thead>
    @foreach ($stores as $store)
        <tr>
            <td> {{ $store->name }} </td>
            <td> {{ $store->category }} </td>
            <td>{{ $store->store }} </td>
            <td> {{ $store->qty_available }} </td>
            <td style="text-align: right;"> &#8358;{{ number_format(str_replace(',','',$store->cost_price), 2, '.', ',') }} </td>
        </tr>
    @endforeach
    <tfoot>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tfoot>
</table>
