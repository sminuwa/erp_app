<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.current.stock.report.print', [$branch_id,$store_id,$category_id, $product_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch == null ? 'All Branches' : $branch->name."($branch->code)" }} </h3>
        <h5 style="text-align: center;">CURRENT STOCK REPORT </h5>
    </caption>
    <thead>
        <tr>
            <th>CODE</th>
            <th>ITEM</th>
            <th>STORE</th>
            <th>QTY</th>
            <th>COST PRICE (&#8358;)</th>
            <th>R PRICE (&#8358;)</th>
            <th>W PRICE (&#8358;)</th>
            <th>TOTAL COST (&#8358;)</th>
            {{-- <th>TOTAL R Price (&#8358;)</th>
            <th>TOTAL W Price (&#8358;)</th> --}}
        </tr>
    </thead>
    @foreach ($stores as $store)
        <tr>
            <td> {{ $store->product_code }} </td>
            <td> {{ $store->name }} </td>
            <td>{{ $store->store_code }} </td>
            <td> {{ $store->qty_available }} </td>
            <td style="text-align: right;"> {{ number_format(str_replace(',', '', $store->cost_price), 2, '.', ',') }}
            </td>
            <td style="text-align: right;">
                {{ number_format(str_replace(',', '', $store->retail_selling_price), 2, '.', ',') }} </td>
            <td style="text-align: right;">
                {{ number_format(str_replace(',', '', $store->whole_selling_price), 2, '.', ',') }} </td>
            <td style="text-align: right;">
                {{ number_format(str_replace(',', '', $store->qty_available) * str_replace(',', '', $store->cost_price), 2, '.', ',') }}
            </td>
            {{-- <td style="text-align: right;">
                {{ number_format(str_replace(',', '', $store->qty_available) * str_replace(',', '', $store->retail_selling_price), 2, '.', ',') }}
            </td>
            <td style="text-align: right;">
                {{ number_format(str_replace(',', '', $store->qty_available) * str_replace(',', '', $store->whole_selling_price), 2, '.', ',') }}
            </td> --}}

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
        <th></th>
        {{-- <th></th> --}}
    </tfoot>
</table>
