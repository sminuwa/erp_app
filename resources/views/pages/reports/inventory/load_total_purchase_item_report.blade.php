<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.total.purchase.item.report.print', [$from_date, $to_date, $store_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Sum of All Product Purchased
            Between
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            and
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
        <h5 style="text-align: center;">Store Name: {{$store_id=="all"?"All Stores":\App\Models\Store::find($store_id)->name}}</h5>
    </caption>
    <thead>
        <tr>
            <th>ITEM</th>
            <th>QTY PURCHASED</th>
        </tr>
    </thead>
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->product }}</td>
            <td style="text-align: center">{{ number_format($sale->quantity, 0, '.', ',') }}</td>
        </tr>
    @endforeach
</table>
