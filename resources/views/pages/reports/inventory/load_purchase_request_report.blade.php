<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.purchase.request.report.print', [$from_date, $to_date, $branch_id, $category_id, $product_id, $supplier_id, $status]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">Purchase Requests
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="5">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="4">Pricessed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>DATE</th>
            <th>INVOICE</th>
            <th>ITEM</th>
            <th>ITEM PRICE</th>
            <th>QTY</th>
            <th>TOTAL COST</th>
            <th>WAYBILL</th>
            <th>SUPPLIER</th>
            <th>STATUS</th>
        </tr>
    </thead>
    @php
        $total_cost = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->purchase_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->reference }}</td>
            <td>{{ $sale->product }}</td>
            <td style="text-align: right">{{ number_format($sale->unit_price, 2) }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">
                {{ number_format($sale->unit_price * $sale->quantity, 2, '.', ',') }}</td>
            <td>{{ $sale->wbno }}</td>
            <td>{{ $sale->supplier }}</td>
            <td>{{ $sale->status == 1 ? 'Completed' : 'Pending' }}</td>

        </tr>
        @php
            $total_cost += $sale->unit_price * $sale->quantity;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="5" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_cost, 2, '.', ',') }}
            </th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
