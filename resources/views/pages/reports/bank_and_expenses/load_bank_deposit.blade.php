<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.bank.deposit.report.print', [$from_date, $to_date]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">BANK DEPOSIT BETWEEN
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>DEPOSITOR</th>
            <th>A/C NUMBER (&#8358;)</th>
            <th>A/C NAME(&#8358;)</th>
            <th>DEPOSIT SLIP</th>
            <th>AMOUNT DEPOSIT</th>
        </tr>
    </thead>
    @foreach ($deposits as $deposit)
        <tr>
            <td>{{ \Carbon\Carbon::parse($deposit->date_deposit)->toFormattedDateString() }}</td>
            <td> {{ optional($deposit->depositor)->name }} </td>
            <td> {{ optional($deposit->fromAccount)->account_name }} </td>
            <td> {{ optional($deposit->fromAccount)->account_no }} </td>
            <td> {{ $deposit->slip_no }} </td>
            <td style="text-align: right;font-weight:600"> &#8358;{{ number_format($deposit->amount, 2) }} </td>
        </tr>
    @endforeach
</table>
