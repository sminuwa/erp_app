<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>User</th>
            <th>Action </th>
            <th>Role</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->user->name }} </td>
                <td> {{ $record->action }} </td>
                <td> {{ $record->role->name }} </td>
                <td>
                    @can('audit_logs.show')
                        <a class="btn btn-secondary" href="{{ route('audit_logs.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('audit_logs.edit')
                        <a class="btn btn-secondary" href="{{ route('audit_logs.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('audit_logs.destroy')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('audit_logs.destroy', $record->id) }}" method="post" style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-secondary cursor-pointer">
                                <i class="text-danger fa fa-remove"></i>
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
