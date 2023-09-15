<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.user.loan.history.report.print', [$collector->id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Loan Collection History Report<br>
            NAME: {{$collector->name}}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE COLLECTED</th>
            <th>AMOUNT COLLECTED</th>
            <th>AMOUNT PAID</th>
            <th>COLLECTION RECEIPT NO</th>
            <th>PAYMENT RECEIPT NO</th>
            <th>DUE DATE</th>
            <th>ACCOUNT NAME</th>
        </tr>
    </thead>
    @php
        $total_balance = 0;
        $total_collected = 0;
        $total_paid = 0;
    @endphp
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ \Carbon\Carbon::parse($record->date)->toformattedDateString() }} </td>
                <td style="text-align: right;"> &#8358;{{ number_format($record->amount_collected, 2, '.', ',') }} </td>
                <td style="text-align: right;"> &#8358;{{ number_format($record->amount_paid, 2, '.', ',') }} </td>
                <td> {{ $record->c_receipt_no }} </td>
                <td> {{ $record->p_receipt_no }} </td>
                <td> {{ \Carbon\Carbon::parse($record->due_date)->toformattedDateString() }} </td>
                <td> {{ $record->account_name }} </td>

            </tr>
            @php
                $total_paid +=$record->amount_paid;
                $total_collected +=$record->amount_collected;
                $total_balance += $total_paid - $total_collected;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: right;">Total</td>
            <td style="text-align: right;" colspan="6">&#8358;{{ number_format($total_balance, 2, '.', ',') }}</td>
        </tr>
    </tfoot>
</table>
