<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Name </th>
            <th>Code </th>
            <th>Branch</th>
            <th>Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td> {{ $record->code }} </td>
                <td> {{ $record->branch->name }} </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Inactive' }} </td>
                <td>
                    @can('edit.store')
                        <a class="btn btn-secondary btn-sm" href="{{ route('stores.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.store')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('stores.destroy', $record->id) }}" method="post" style="display: inline">
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
