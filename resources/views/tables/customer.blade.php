<table class="table table-bordered table-striped" id='example1'>
    <thead>
        <tr>
            <th>SN</th>
            <th>Code </th>
            <th>Name </th>
            <th>Branch </th>
            <th>Phone </th>
            <th>Address </th>
            <th>Email </th>
            <th>Credit Limit </th>
            <th>Current Balance </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $loop->index + 1 }} </td>
                <td> {{ $record->code }} </td>
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
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @can('customers.show')
                                <a class="dropdown-item" title="View Ledger"
                                    href="{{ route('customers.show', $record->id) }}">
                                    <span class="fa fa-eye"> View</span>
                                </a>
                            @endcan
                            @can('customers.edit')
                                <a class="dropdown-item" href="{{ route('customers.edit', $record->id) }}">
                                    <span class="fa fa-pencil"> Edit</span>
                                </a>
                            @endcan
                            @can('customers.destroy')
                                <form onsubmit="return confirm('Are you sure you want to delete?')"
                                    action="{{ route('customers.destroy', $record->id) }}" method="post"
                                    style="display: inline">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="dropdown-item">
                                        <i class="text-danger fa fa-remove"> Delete</i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
