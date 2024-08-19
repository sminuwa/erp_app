<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.payment.report.print', [$from_date, $to_date, $customer_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Customer Payment History Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>CUST NAME</th>
            <th>BALANCE</th>
            <th>AMOUNT PAID</th>
            <th>TELLER NO</th>
            <th>PAY MODE</th>
            <th>ACC. NAME</th>
            <th>DATE POSTED</th>

        </tr>
    </thead>
    <tbody>
        @php
            $total_sold = 0;
            $total_pay = 0;
            $total_discount = 0;
            $total_due = 0;
            $balance = 0;
        @endphp
        @foreach ($sales as $sale)
            @php
                $sum_cr = \App\Models\CustomerLedger::where('id', '<', $sale->id)
                    ->where('customer_id', $sale->customer_id)
                    ->sum('cr');
                $sum_dr = \App\Models\CustomerLedger::where('id', '<', $sale->id)
                    ->where('customer_id', $sale->customer_id)
                    ->sum('dr');
                $balance = $sum_cr - $sum_dr;
                $total_pay += $sale->dr;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}</td>
                <td>{{ $sale->customer }}</td>
                <td style="text-align: right">
                    @if ($balance < 0)
                        ({{ number_format(abs($balance), 2) }})
                    @else
                        {{ number_format($balance, 2) }}
                    @endif
                </td>
                <td style="text-align: right">{{ number_format($sale->dr, 2, '.', ',') }}</td>
                <td>{{ $sale->teller_no }}</td>
                <td>{{ $sale->payment_mode }}</td>
                <td>{{ $sale->account_name }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->created_at)->toFormattedDateString() }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2"></th>
            <th style="text-align: right">B/F:
                @if ($balance < 0)
                    ({{ number_format(abs($balance), 2) }})
                @else
                    {{ number_format($balance, 2) }}
                @endif
            </th>
            <th style="text-align: right">{{ number_format($total_pay, 2, '.', ',') }}</th>
            <th colspan="4"></th>
        </tr>
    </tfoot>
</table>
