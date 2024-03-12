<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.product.valuation.report.print', [$from_date, $to_date, $branch_id, $category_id, $product_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">Product Valuation Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>STORE</th>
            <th>REFERENCE</th>
            <th>ITEM CODE</th>
            <th>ITEM NAME</th>
            <th>ITEM PRICE</th>
            <th>QTY</th>
            <th>TOTAL </th>
        </tr>
    </thead>
    @php
        $total_cost = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}</td>
            <td>{{ $sale->store->code ?? '' }}</td>
            <td>{{ $sale->reference }}</td>
            <td>{{ $sale->product->code ?? ''}}</td>
            <td>{{ $sale->product->name ?? ''}}</td>
            <td style="text-align: right">{{ number_format($sale->cost_price, 2) }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">
                {{ number_format($sale->cost_price * $sale->quantity, 2, '.', ',') }}</td>

        </tr>
        @php
            $total_cost += $sale->cost_price * $sale->quantity;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="5" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_cost, 2, '.', ',') }}
            </th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
