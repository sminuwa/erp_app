<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.cheque.collected.report.print', [$from_date, $to_date]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">LIST IF CHEQUES COLLECTED BETWEEN
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
        <h6>Cheque Collected B/d Before this Date {{ $from_date }}
            was {{ number_format($balance_b_d, 2, '.', ',') }} </h6>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>FOLIO</th>
            <th>CUSTOMER NAME</th>
            <th>AMOUNT(&#8358;)</th>
        </tr>
    </thead>
    @foreach ($ledgers as $ledger)
        <tr>
            <td>{{ \Carbon\Carbon::parse($ledger->date)->toFormattedDateString() }}</td>
            <td> {{ $ledger->receipt_no }} </td>
            <td> {{ optional($ledger->customer)->name }} </td>
            <td style="text-align: right;font-weight:600"> &#8358;{{ number_format($ledger->dr, 2) }} </td>
        </tr>
    @endforeach
</table>
