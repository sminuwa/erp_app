<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Name </th>
            <th>Email </th>
            <th>Phone </th>
            <th>Gender </th>
            <th>User Code </th>
            <th>Branch</th>
            <th>Account Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td> {{ $record->email }} </td>
                <td> {{ $record->phone }} </td>
                <td> {{ $record->gender }} </td>
                <td> {{ $record->user_code }} </td>
                <td> {{ optional($record->branch)->name }} </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Blocked' }} </td>
                <td>
                    @can('assign.user.role')
                        <a class="btn btn-secondary btn-sm" href="{{ route('users.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('view.user.activity.log')
                        <a class="btn btn-secondary btn-sm" href="{{ route('users.logs', $record->id) }}">
                            <span class="fa fa-adjust">Logs</span>
                        </a>
                    @endcan
                    @can('edit.user')
                        <a class="btn btn-secondary btn-sm" href="{{ route('users.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.user')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('users.destroy', $record->id) }}" method="post" style="display: inline">
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
