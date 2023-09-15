<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.cash.trasnfer.report.print', [$from_date, $to_date, $user_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">CASH TRANSFER TO {{$user_id=="all"?" ALL USERS ":"USER (App\Models\User::find($user_id)->name)"}} BY CUSTOMER WITHDRWAN REPORT BETWEEN
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>WITHD NAME</th>
            <th>A/C NAME</th>
            <th>WITHDRAW SLIP</th>
            <th>AMOUNT WITHDRAWN</th>
            <th>A/C NAME</th>
            <th>AMOUNT DEPOSIT</th>
            <th>USER NAME</th>
        </tr>
    </thead>
    @foreach ($withdraws as $withdraw)
        <tr>
            <td>{{ \Carbon\Carbon::parse($withdraw->date_withdraw)->toFormattedDateString() }}</td>
            <td> {{ optional($withdraw->withdrawer)->name }} </td>
            <td> {{ optional($withdraw->fromAccount)->account_name }} </td>
            <td> {{ $withdraw->slip_no }} </td>
            <td style="text-align: right;font-weight:600"> &#8358;{{ number_format($withdraw->amount, 2) }} </td>
            <td> {{ optional($withdraw->toAccount)->account_name }} </td>
            <td style="text-align: right;font-weight:600"> &#8358;{{ number_format($withdraw->amount, 2) }} </td>
            <td> {{ $withdraw->sender->name }} </td> 
        </tr>
    @endforeach
</table>
