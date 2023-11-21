<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Code </th>
            <th>Name </th>
            <th>Email </th>
            <th>Phone </th>
            <th>Gender </th>
            <th>Branch</th>
            <th>Account Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->user_code }} </td>
                <td> {{ $record->name }} </td>
                <td> {{ $record->email }} </td>
                <td> {{ $record->phone }} </td>
                <td> {{ $record->gender }} </td>

                <td> {{ optional($record->branch)->name }} </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Blocked' }} </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @can('assign.user.role')
                                <a class="dropdown-item" href="{{ route('users.show', $record->id) }}">
                                    <span class="fa fa-eye"> View</span>
                                </a>
                            @endcan
                            @can('view.user.activity.log')
                                <a class="dropdown-item" href="{{ route('users.logs', $record->id) }}">
                                    <span class="fa fa-adjust"> Logs</span>
                                </a>
                            @endcan
                            @can('edit.user')
                                <a class="dropdown-item" href="{{ route('users.edit', $record->id) }}">
                                    <span class="fa fa-pencil"> Edit</span>
                                </a>
                            @endcan
                            @can('delete.user')
                                <form onsubmit="return confirm('Are you sure you want to delete?')"
                                    action="{{ route('users.destroy', $record->id) }}" method="post"
                                    style="display: inline">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="dropdown-item cursor-pointer">
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
