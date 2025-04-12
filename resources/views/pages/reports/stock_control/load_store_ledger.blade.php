<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.store.ledger.reports', [$company_id,$branch_id, $store_id, is_array($category_id) ? implode(',', $category_id) : $category_id, $product_id]) }}"
           target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;text-transform:uppercase">{{ $branch->name ?? 'All Branches' }}</h5>
        <h5 style="text-align: center;">STORE QUANTITY REPORT </h5>
    </caption>
    <thead>
    <tr>
        <th style="width: 50%" colspan="4">Date
            Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
        </th>
        <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
    </tr>
    <tr>
        <th>BRANCH</th>
        <th>STORE</th>
        <th>PRODUCT NAME</th>
        <th>CATEGORY NAME</th>
        <th>QUANTITY</th>
        <th>UNIT</th>
        <th>COST PRICE</th>
        <th>TOTAL PRICE</th>
    </tr>
    </thead>
    @php $grantTotal=0.0;
    $total_quantity = 0;
    @endphp
    @foreach ($stores as $store)
        @php
            $total = remove_non_numeric($store->cost_price) * remove_non_numeric(round($store->qty_available, 6));
            $grantTotal += $total;
            $total_quantity +=$store->qty_available;
        @endphp
        <tr>
            <td> {{ $store->branch_code }} </td>
            <td>{{ $store->store }} </td>
            <td>{{ $store->code }} - {{ $store->name }} </td>
            <td> {{ $store->category }} </td>
            <td> {{ number_format(round($store->qty_available, 6), 6) }} </td>
            <td>{{ $store->product_unit }}</td>
            <td style="text-align: right;">
                {{ number_format(remove_non_numeric($store->cost_price), 2) }} </td>
            <td style="text-align: right;">
                {{ number_format($total, 2) }}
            </td>
        </tr>
    @endforeach
    <tfoot>
    <tr>
        <th style="text-align: right" colspan="4">TOTAL</th>
        <th>{{ number_format($total_quantity,2) }}</th>
        <th></th>
        <th></th>
        <th>{{ currency_sign() . number_format($grantTotal,2) }}</th>
    </tr>
    </tfoot>
</table>
