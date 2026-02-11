<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Employment ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Branch</th>
            <th>Status</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->employment_id }}</td>
                <td>{{ $record->name }}</td>
                <td>{{ $record->phone_number ?? '-' }}</td>
                <td>{{ $record->branch->name ?? '-' }}</td>
                <td>{{ $record->status == 1 ? 'Active' : 'Inactive' }}</td>
                <td>
                    @can('manufacturing.staff.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.staff.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('manufacturing.staff.delete')
                        <form onsubmit="return confirm('Are you sure you want to delete this staff member?')"
                            action="{{ route('manufacturing.staff.destroy', $record->id) }}" method="post" style="display: inline">
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
