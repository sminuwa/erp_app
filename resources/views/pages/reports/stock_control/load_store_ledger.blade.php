<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.store.ledger.reports', [$branch_id, $store_id, $category_id, $product_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;text-transform:uppercase">{{ $branch->name ?? 'All Branches' }}</h5>
        <h5 style="text-align: center;">STORE LEDGER REPORT </h5>
    </caption>
    <thead>
        <tr>
            <th>CODE</th>
            <th>PRODUCT NAME</th>
            <th>CATEGORY NAME</th>
            <th>STORE</th>
            <th>QUANTITY</th>
            <th>COST PRICE()</th>
            <th>TOTAL PRICE()</th>

        </tr>
    </thead>
    @foreach ($stores as $store)
        <tr>
            <td> {{ $store->code }} </td>
            <td> {{ $store->name }} </td>
            <td> {{ $store->category }} </td>
            <td>{{ $store->store }} </td>
            <td> {{ $store->qty_available }} </td>
            <td style="text-align: right;">
               {{ number_format(str_replace(',', '', $store->cost_price), 2, '.', ',') }} </td>
            <td style="text-align: right;">
                {{ number_format(str_replace(',', '', $store->cost_price * $store->qty_available), 2, '.', ',') }} </td>
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
