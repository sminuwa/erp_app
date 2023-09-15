<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.most.sold.item.print', [$from_date, $to_date, $number_limit]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ $number_limit }} Most Sold Items Report
            Between
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>ITEM</th>
            <th>QUANTITY</th>
            <th>TOTAL AMOUNT</th>
        </tr>
    </thead>
    @php
        $total_quantity = 0;
        $total_amount = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->item }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">&#8358;{{ number_format($sale->total, 2, '.', ',') }}</td>
        </tr>
        @php
            $total_quantity += $sale->quantity;
            $total_amount += $sale->total;
            
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_quantity, 0, '.', ',') }}</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_amount, 2, '.', ',') }}
            </th>
        </tr>
    </tfoot>
</table>
