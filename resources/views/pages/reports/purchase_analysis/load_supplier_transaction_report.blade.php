<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.supplier.transaction.report.print', [$from_date, $to_date, $store_id, $category_id, $product_id, $supplier_id, $purchase_mode]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ ucfirst($purchase_mode) }} Purchases Transactions
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>DATE EDITED OR SAVE</th>
            <th>INVOICE</th>
            <th>ITEM</th>
            <th>ITEM PRICE</th>
            <th>QTY</th>
            <th>TOTAL COST</th>
            <th>Purchase Mode</th>
            <th>STORE</th>
            <th>SUPP NAME</th>
        </tr>
    </thead>
    @php
        $total_cost = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->purchase_date)->toFormattedDateString() }}</td>
            <td>{{ \Carbon\Carbon::parse($sale->updated_at)->toFormattedDateString() }}</td>
            <td>{{ $sale->invoice }}</td>
            <td>{{ $sale->product }}</td>
            <td>{{ $sale->unit_price }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">
                &#8358;{{ number_format($sale->unit_price * $sale->quantity, 2, '.', ',') }}</td>
            <td>{{ $sale->purchase_mode }}</td>
            <td>{{ $sale->store }}</td>
            <td>{{ $sale->supplier }}</td>

        </tr>
        @php
            $total_cost += $sale->unit_price * $sale->quantity;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="6" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_cost, 2, '.', ',') }}
            </th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
