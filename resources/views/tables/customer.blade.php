<table class="table table-bordered table-striped" id='example1'>
    <thead>
        <tr>
            <th>SN</th>
            <th>Name </th>
            <th>Branch </th>
            <th>Phone </th>
            <th>Address </th>
            <th>Email </th>
            <th>Credit Limit </th>
            <th>Opening Balance </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $loop->index + 1 }} </td>
                <td> {{ $record->name }} </td>
                <td> {{ $record->branch?->name }} </td>
                <td> {{ $record->phone }} </td>
                <td> {{ $record->address }} </td>
                <td> {{ $record->email }} </td>
                <td> {{ number_format($record->credit_limit, 2) }} </td>
                <td>
                    @if ($record->runningBalance() < 0)
                        &#8358;({{ number_format(abs($record->runningBalance()), 2) }})
                    @else
                        &#8358;{{ number_format($record->runningBalance(), 2) }}
                    @endif
                </td>
                <td>
                    @can('view.customer.ledger')
                        <a class="btn btn-secondary btn-sm" title="View Ledger"
                            href="{{ route('customers.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('edit.customer')
                        <a class="btn btn-secondary btn-sm" href="{{ route('customers.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.customer')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('customers.destroy', $record->id) }}" method="post" style="display: inline">
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
