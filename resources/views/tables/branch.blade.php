<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Name </th>
            <th>Phone </th>
            <th>Email </th>
            <th>Address </th>
            <th>Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td> {{ $record->phone }} </td>
                <td> {{ $record->email }} </td>
                <td> {{ $record->address }} </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Inactive' }} </td>
                <td>
                    @can('edit.office.branch')
                        <a class="btn btn-secondary btn-sm" href="{{ route('branches.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.office.branch')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('branches.destroy', $record->id) }}" method="post" style="display: inline">
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
