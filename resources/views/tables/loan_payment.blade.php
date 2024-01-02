<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Loan Collector</th>
            <th>Amount </th>
            <th>Payment Mode </th>
            <th>Bank Account</th>
            <th>Cheque No </th>
            <th>Receipt No </th>
            <th>Received By </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->loan->collector->name }} ({{ $record->loan->collector->reg_code }}) </td>
                <td style="text-align: right"> {{ number_format($record->amount, 2, '.', ',') }} </td>
                <td> {{ $record->payment_mode }} </td>
                <td> {{ $record->bankAccount->account_name }} </td>
                <td> {{ $record->cheque_no }} </td>
                <td> {{ $record->receipt_no }} </td>
                <td> {{ $record->received->name }} </td>
                <td>
                    @can('loan_payments.print')
                        <a class="btn btn-secondary btn-sm" href="{{ route('loan_payments.print', $record->id) }}"
                            target="_BLANK">
                            <span class="fa fa-print"></span>
                        </a>
                    @endcan
                    @can('loan_payments.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('loan_payments.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('loan_payments.destroy')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('loan_payments.destroy', $record->id) }}" method="post"
                            style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                <i class="text-danger fa fa-remove"></i>
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
