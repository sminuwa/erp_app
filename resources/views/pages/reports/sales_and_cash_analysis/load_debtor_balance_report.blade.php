<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.debtor.balance.report.print', [$from_date, $to_date, $customer_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Debtor Balance Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>INVOICE</th>
            <th>CUST NAME</th>
            <th>TOTAL AMOUNT</th>
            <th>TOTAL PAID</th>
            <th>DISCOUNT</th>
            <th>BALANCE</th>
        </tr>
    </thead>
    @php
        $total_sold = 0;
        $total_pay = 0;
        $total_discount = 0;
        $total_due = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->invoice_no }}</td>
            <td>{{ $sale->customer }}</td>
            <td style="text-align: right">{{ number_format($sale->total, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->pay, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->discount, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->due, 2, '.', ',') }}</td>
        </tr>
        @php
            $total_sold += $sale->total;
            $total_pay += $sale->pay;
            $total_discount += $sale->discount;
            $total_due += $sale->due;

        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_sold, 2, '.', ',') }}</th>
            <th style="text-align: right">
                {{ number_format($total_pay, 2, '.', ',') }}</th>
            <th style="text-align: right">
                {{ number_format($total_discount, 2, '.', ',') }}
            </th>
            <th style="text-align: right">
                {{ number_format($total_due, 2, '.', ',') }}
            </th>
        </tr>
    </tfoot>
</table>
