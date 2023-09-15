<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.discount.granted.report.print', [$from_date, $to_date, $store_id, $category_id, $product_id, $customer_id, $credit_walkedin, $lower, $upper]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Discount Granted On Unit Price Report
            Between
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
        <h5>Discount Range: &#8358;{{ $lower }} - &#8358;{{ $upper }}</h5>
    </caption>
    <thead>
        <tr>
            <th>SALE DATE</th>
            <th>INVOICE NO</th>
            <th>ITEM NAME</th>
            <th>QTY</th>
            <th>ACTUAL</th>
            <th>SELLING</th>
            <th>DICOUNT</th>
            <th>STORE</th>
            <th>GROUP</th>
            <th>SOLD BY</th>
        </tr>
    </thead>
    @php
        $total_selling = 0;
        $total_sold = 0;
        $total_discount = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->invoice_no }}</td>
            <td>{{ $sale->item }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">&#8358;{{ number_format($sale->selling_price, 2, '.', ',') }}</td>
            <td style="text-align: right">&#8358;{{ number_format($sale->sold_price, 2, '.', ',') }}</td>
            <td style="text-align: right">
                &#8358;{{ number_format($sale->sold_price - $sale->sold_price, 2, '.', ',') }}</td>
            <td>{{ $sale->store }}</td>
            <td>{{ $sale->group }}</td>
            <td>{{ $sale->user }}</td>
        </tr>
        @php
            $total_selling += $sale->selling_price;
            $total_sold += $sale->sold_price;
            $total_discount += $sale->sold_price - $sale->sold_price;
            
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="4">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_selling, 0, '.', ',') }}</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_sold, 2, '.', ',') }}
            </th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_discount, 2, '.', ',') }}
            </th>
            <th colspan="3"></th>
        </tr>
    </tfoot>
</table>
