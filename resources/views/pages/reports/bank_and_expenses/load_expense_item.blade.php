<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.expense.item.report.print', [$from_date, $to_date, $item_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <thead>
        <tr>
            <th>DATE</th>
            <th>EXPENSE</th>
            <th>REASON FOR EXPENSE</th>
            <th>AMOUNT PAID</th>
            <th>IMPRESS NO</th>
        </tr>
    </thead>
    @php
        $total = 0;
    @endphp
    @foreach ($expenses as $expense)
        <tr>
            <td> {{ $expense->date }} </td>
            <td> {{ $expense->item->name }} </td>
            <td> {{ $expense->reason }} </td>
            <td style="text-align: right;font-weight:600"> &#8358;{{ number_format($expense->amount, 2) }} </td>
            <td>{{ $expense->impress }}</td>
            @php
                $total += $expense->amount;
            @endphp
        </tr>
    @endforeach
    <caption style="caption-size:top">
        <h5 style="text-align: center;">TOTAL PAID FOR THE PERIOD
            {{ number_format($total, 2, '.', ',') }}
        </h5>
    </caption>
</table>
