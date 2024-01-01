<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Loan Collector</th>
            <th>Amount </th>
            <th>Payment Mode </th>
            <th>Bank Account</th>
            <th>Date </th>
            <th>Granted By </th>
            <th>Receipt No </th>
            <th>Due Date </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->collector->name }} </td>
                <td> {{ $record->amount }} </td>
                <td> {{ $record->payment_mode }} </td>
                <td> {{ $record->bankAccount->account_name }} </td>
                <td> {{ \Carbon\Carbon::parse($record->date)->toFormattedDateString() }} </td>
                <td> {{ $record->granted->name }} </td>
                <td> {{ $record->receipt_no }} </td>
                <td> {{ $record->due_date }} </td>
                <td>
                    @can('loans.show')
                        <a class="btn btn-secondary btn-sm" href="{{ route('loans.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('loans.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('loans.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('loans.destroy')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('loans.destroy', $record->id) }}" method="post" style="display: inline">
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
