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
                    @can('companies.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('companies.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('companies.destroy')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('companies.destroy', $record->id) }}" method="post" style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-danger btn-sm cursor-pointer">
                                <i class="text-white fa fa-remove"></i>
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
</table>
