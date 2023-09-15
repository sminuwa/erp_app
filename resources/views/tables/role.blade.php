<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Name </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td>
                <td>
                    @can('edit.role')
                        <a class="btn btn-secondary btn-sm" href="{{ route('roles.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.role')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('roles.destroy', $record->id) }}" method="post" style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                <i class="text-danger fa fa-remove"></i>
                            </button>
                        </form>
                    @endcan
                </td>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
