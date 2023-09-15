<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.payment.overdue.report.print', [$from_date, $to_date, $customer_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Debts Due for Payment Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>TRANS DATE</th>
            <th>CUST NAME</th>
            <th>INVOICE</th>
            <th>TOTAL</th>
            <th>AMOUNT PAID</th>
            <th>AMOUNT DUE</th>
            <th>DUE DATE</th>

        </tr>
    </thead>
    <tbody>
        @php
            $total_sold = 0;
            $total_pay = 0;
            $total_discount = 0;
            $total_due = 0;
        @endphp
        @foreach ($sales as $sale)
            @php
                $total_pay += $sale->pay;
                $total_due += $sale->due;
                $total_sold +=$sale->total; 
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
                <td>{{ $sale->customer }}</td>
                <td>{{ $sale->invoice_no }}</td>
                <td style="text-align: right">&#8358;{{ number_format($sale->total, 2, '.', ',') }}</td>
                <td style="text-align: right">&#8358;{{ number_format($sale->pay, 2, '.', ',') }}</td>
                <td style="text-align: right">&#8358;{{ number_format($sale->due, 2, '.', ',') }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->due_date)->toFormattedDateString() }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right">TOTAL</th>
            <th style="text-align: right">&#8358;{{ number_format($total_sold, 2, '.', ',') }}</th>
            <th style="text-align: right">&#8358;{{ number_format($total_pay, 2, '.', ',') }}</th>
            <th style="text-align: right">&#8358;{{ number_format($total_due, 2, '.', ',') }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
