<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.current.stock.report.print', [$branch_id, $store_id, $category_id, $product_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch == null ? 'All Branches' : $branch->name . "($branch->code)" }} </h3>
        <h5 style="text-align: center;">CURRENT STOCK REPORT </h5>
    </caption>
    <thead>
        <tr>
            <th>BRANCH</th>
            <th>STORE</th>
            <th>PRODUCT</th>
            <th>QTY</th>
            <th>COST PRICE ()</th>
            <th>R PRICE ()</th>
            <th>W PRICE ()</th>
            <th>TOTAL COST ()</th>
            <th>TOTAL R ()</th>
            <th>TOTAL W ()</th>
            <th>R MARGIN</th>
            <th>W MARGIN</th>
        </tr>
    </thead>
    @foreach ($stores as $store)
        <tr>
            <td> {{ $store->branch_code }} </td>
            <td>{{ $store->store_code }} </td>
            <td> {{ $store->product_code }} - {{ $store->name }} </td>
            <td> {{ $store->qty_available }} </td>
            @php $cost_price = str_replace(',', '', $store->cost_price); @endphp
            <td style="text-align: right;"> {{ number_format($cost_price, 2, '.', ',') }}</td>
            <td style="text-align: right;">
                @php $retail_price = str_replace(',', '', $store->retail_selling_price); @endphp
                {{ number_format($retail_price, 2, '.', ',') }}
            </td>
            <td style="text-align: right;">
                @php $whole_price = str_replace(',', '', $store->whole_selling_price); @endphp
                {{ number_format($whole_price, 2, '.', ',') }}
            </td>
            <td style="text-align: right;">
                @php $total_cost = str_replace(',', '', $store->qty_available) * str_replace(',', '', $store->cost_price); @endphp
                {{ number_format($total_cost, 2, '.', ',') }}
            </td>
            <td style="text-align: right;">
                @php $total_r_price = str_replace(',', '', $store->qty_available) * str_replace(',', '', $store->retail_selling_price); @endphp
                {{ number_format($total_r_price, 2, '.', ',') }}
            </td>
            <td style="text-align: right;">
                @php $total_w_price = str_replace(',', '', $store->qty_available) * str_replace(',', '', $store->whole_selling_price); @endphp
                {{ number_format($total_w_price, 2, '.', ',') }}
            </td>
            <td style="text-align: right;">
                {{ number_format($retail_price - $cost_price, 2, '.', ',') }}
            </td>
            <td style="text-align: right;">
                {{ number_format($whole_price - $cost_price, 2, '.', ',') }}
            </td>
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
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tfoot>
</table>
