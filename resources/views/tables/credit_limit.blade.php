<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Customer Name </th>
            <th>Amount </th>
            <th>Status </th>
            <th>Updated By </th>
            <th>Date Created </th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->customer->name }} </td>
                <td style="text-align: right;"> &#8358;{{ number_format($record->amount, 2) }} </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Not Active' }} </td>
                <td> {{ $record->user->name }} </td>
                <td> {{ $record->created_at->toDayDateTimeString() }} </td>
                <td>
                    @can('credit_limits.limit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('credit_limits.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('credit_limits.destroy')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('credit_limits.destroy', $record->id) }}" method="post" style="display: inline">
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
