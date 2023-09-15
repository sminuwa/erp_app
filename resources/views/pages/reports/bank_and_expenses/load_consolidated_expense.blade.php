<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.consolidated.expense.report.print', [$from_date, $to_date,$item_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">CONSOLIDATED EXPENSE HEAD REPORT BETWEEN
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>NAME</th>
            <th>AMOUNT(&#8358;)</th>
        </tr>
    </thead>
    @foreach ($expenses as $expense)
        <tr>
            <td> {{ $expense->name }} </td>
            <td style="text-align: right;font-weight:600"> &#8358;{{ number_format($expense->total, 2) }} </td>
        </tr>
    @endforeach
</table>
