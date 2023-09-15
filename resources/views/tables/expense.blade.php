<table class="table table-bordered table-striped" id="record2">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Date </th>
            <th>Impress </th>
            <th>Amount </th>
            <th>Payment Mode</th>
            <th>Account Name </th>
            <th>Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td> {{ $record->date }} </td>
                <td> {{ $record->impress }} </td>
                <td style="text-align: right">&#8358; {{ number_format($record->amount, 2) }} </td>
                <td> {{ $record->payment_mode }} </td>
                <td> {{ $record->account_name }} ({{ $record->account_no }}) </td>
                <td> {{ $record->status }} </td>
                <td>
                    @can('view.expenditure')
                        <a class="btn btn-secondary btn-sm" href="{{ route('expenses.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('edit.expenditure')
                        <a class="btn btn-secondary btn-sm" href="{{ route('expenses.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @if ($record->status != 'Cancelled')
                        @can('delete.expenditure')
                            <form onsubmit="return confirm('Are you sure you want to cancel?')"
                                action="{{ route('expenses.destroy', $record->id) }}" method="post"
                                style="display: inline">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-secondary  btn-sm cursor-pointer">
                                    <i class="text-danger fa fa-remove"></i>
                                </button>
                            </form>
                        @endcan
                    @else
                        <span class="fa fa-remove"></span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
