<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.most.sold.item.print', [$from_date, $to_date, $branch_id, $type, $number_limit]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">{{ $number_limit }} Most Sold Items Report by
            {{ $type == 'qty' ? 'Amount' : 'Quantity' }}
            Between
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>CODE</th>
            <th>PRODUCT</th>
            <th>QUANTITY</th>
            <th>COST ()</th>
            <th>SALES ()</th>
            <th>MARGIN ()</th>

        </tr>
    </thead>
    @php
        $total_quantity = 0;
        $total_amount = 0;
        $total_cost = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->item }}</td>
            <td style="text-align: right">{{ $sale->quantity }}</td>
            <td style="text-align: right">{{ number_format($sale->total_cost, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->total, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->total - $sale->total_cost, 2, '.', ',') }}</td>
        </tr>
        @php
            $total_quantity += $sale->quantity;
            $total_amount += $sale->total;
            $total_cost += $sale->total_cost;

        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right"  colspan="3">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_cost, 0, '.', ',') }}</th>
            <th style="text-align: right">
                {{ number_format($total_amount, 2, '.', ',') }}
            </th>
            <th style="text-align: right">
                {{ number_format($total_amount-$total_cost, 2, '.', ',') }}
            </th>
        </tr>
    </tfoot>
</table>
