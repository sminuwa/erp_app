<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.stock.adjustment.reports', [$from_date, $to_date, $branch_id, $store_id, $category_id, $product_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ $branch->name ?? 'All Branches' }} </h5>
        <h5 style="text-align: center;">STOCK ADJUSTMENT REPORT </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="5">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>PROCESSED DATE</th>
            <th>PRODUCT CODE</th>
            <th>PRODUCT NAME</th>
            <th>QUANTITY</th>
            <th>TYPE</th>
            <th>STORE</th>
            <th>ADJUSTMENT NO</th>
            <th>CREATED BY</th>
            <th>DATE CREATED</th>
            <th>POSTED By</th>

        </tr>
    </thead>
    @foreach ($stores as $store)
        <tr>
            <td> {{ \Carbon\Carbon::parse($store->date)->toFormattedDateString() }} </td>
            <td> {{ $store->code }} </td>
            <td> {{ $store->name }} </td>
            <td> {{ $store->operation == 'in'? $store->quantity:(-$store->quantity) }} </td>
            <td> {{ $store->operation }} </td>
            <td>{{ $store->store }} </td>
            <td> {{ $store->reference }} </td>
            <td> {{ $store->created_by ?? null }} </td>
            <td> {{ $store->posted_by ?? null }} </td>
            <td> {{ \Carbon\Carbon::parse($store->created_at)->toFormattedDateString() }} </td>
        </tr>
    @endforeach
</table>
