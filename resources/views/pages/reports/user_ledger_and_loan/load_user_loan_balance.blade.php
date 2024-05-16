<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.user.loan.balance.report.print', [$user_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Loan Collector Balances Report
        </h5>
    </caption>
    <thead>
        <tr>
            <th>NAME OF COLLECTOR</th>
            <th>BALANCE</th>
        </tr>
    </thead>
    @php
        $total_balance = 0;
    @endphp
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td style="text-align: right;"> {{ number_format($record->balance, 2, '.', ',') }} </td>
            </tr>
            @php
                $total_balance +=$record->balance;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: right;">Total</td>
            <td style="text-align: right;">{{ number_format($total_balance, 2, '.', ',') }}</td>
        </tr>
    </tfoot>
</table>
