<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Code</th>
            <th>Description</th>
            <th>Capacity</th>
            <th>Branch</th>
            <th>Status</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->code }}</td>
                <td>{{ $record->description }}</td>
                <td>{{ $record->capacity ?? '-' }}</td>
                <td>{{ $record->branch->name ?? '-' }}</td>
                <td>{{ $record->status == 1 ? 'Active' : 'Inactive' }}</td>
                <td>
                    @can('manufacturing.machines.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.machines.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('manufacturing.machines.delete')
                        <form onsubmit="return confirm('Are you sure you want to delete this machine?')"
                            action="{{ route('manufacturing.machines.destroy', $record->id) }}" method="post" style="display: inline">
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
