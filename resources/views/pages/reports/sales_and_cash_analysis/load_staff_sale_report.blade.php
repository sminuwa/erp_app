<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.staff.sales.report.print', [$from_date, $to_date, $branch_id, $store_id, $category_id, $product_id, $staff_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">{{ ucfirst($user->name ?? 'All Users') }} <br />Sales
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
            <th style="width: 50%;text-align:right" colspan="5">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: right">TOTAL RECEIPTS: </th>
            <th style="text-align: right">{{ number_format($total_cash, 2, '.', ',') }}</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>

        <tr>
            <th>DATE</th>
            <th>NAME</th>
            <th>ATC</th>
            <th>INVOICE</th>
            <th>CUST ACCOUNT</th>
            <th>ITEM</th>
            <th>STORE</th>
            <th>QTY</th>
            <th>SELLING PRICE</th>
            <th>TOTAL SALES</th>
        </tr>
    </thead>
    @php

        $total_sold_price = 0;
        $total_sold = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->name }}</td>
            <td>{{ $sale->user }}</td>
            <td>{{ $sale->reference }}</td>
            <td>{{ $sale->customer }}</td>
            <td>{{ $sale->product }}</td>
            <td>{{ $sale->store }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">{{ number_format($sale->sold_price, 2, '.', ',') }}</td>
            <td style="text-align: right">
                {{ number_format($sale->sold_price * $sale->quantity, 2, '.', ',') }}</td>
        </tr>
        @php
            $total_sold_price += $sale->sold_price;
            $total_sold += $sale->sold_price * $sale->quantity;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="8" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_sold_price, 2, '.', ',') }}</th>
            <th style="text-align: right">
                {{ number_format($total_sold, 2, '.', ',') }}
            </th>
        </tr>
    </tfoot>
</table>
