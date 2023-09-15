<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.bank.withdraw.report.print', [$from_date, $to_date]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">BANK WITHDRAW BETWEEN
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>WITHD NAME</th>
            <th>A/C NUMBER (&#8358;)</th>
            <th>A/C NAME(&#8358;)</th>
            <th>WITHDRAW SLIP</th>
            <th>AMOUNT WITHDRAWN</th>
        </tr>
    </thead>
    @foreach ($withdraws as $withdraw)
        <tr>
            <td>{{ \Carbon\Carbon::parse($withdraw->date_withdraw)->toFormattedDateString() }}</td>
            <td> {{ optional($withdraw->withdrawer)->name }} </td>
            <td> {{ optional($withdraw->fromAccount)->account_name }} </td>
            <td> {{ optional($withdraw->fromAccount)->account_no }} </td>
            <td> {{ $withdraw->slip_no }} </td>
            <td style="text-align: right;font-weight:600"> &#8358;{{ number_format($withdraw->amount, 2) }} </td>
        </tr>
    @endforeach
</table>
