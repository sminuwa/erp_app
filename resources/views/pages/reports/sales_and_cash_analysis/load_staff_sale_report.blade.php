<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.staff.sales.report.print', [$from_date, $to_date, $store_id, $category_id, $product_id, $staff_id, $payment_mode]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ ucfirst($user->name) }} Sales Transactions
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>

    <thead>
        <tr>
            <th colspan="4" style="text-align: right">TOTAL CASH SALES: </th>
            <th style="text-align: right">&#8358;{{ number_format($total_cash, 2, '.', ',') }}</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: right">TOTAL DEBTOR PAYMENT: </th>
            <th style="text-align: right">&#8358;{{ number_format($total_debtors, 2, '.', ',') }}</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: right">TOTAL CASH: </th>
            <th style="text-align: right">&#8358;{{ number_format($total_debtors + $total_cash, 2, '.', ',') }}</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th>DATE</th>
            <th>INVOICE</th>
            <th>CUST NAME</th>
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
            <td>{{ $sale->invoice_no }}</td>
            <td>{{ $sale->customer }}</td>
            <td>{{ $sale->product }}</td>
            <td>{{ $sale->store }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">&#8358;{{ number_format($sale->sold_price, 2, '.', ',') }}</td>
            <td style="text-align: right">
                &#8358;{{ number_format($sale->sold_price * $sale->quantity, 2, '.', ',') }}</td>
        </tr>
        @php
            $total_sold_price += $sale->sold_price;
            $total_sold += $sale->sold_price * $sale->quantity;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="6" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_sold_price, 2, '.', ',') }}</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_sold, 2, '.', ',') }}
            </th>
        </tr>
    </tfoot>
</table>
