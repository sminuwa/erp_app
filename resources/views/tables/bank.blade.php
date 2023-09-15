<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Name </th>
            <th>Abbreviation </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td> {{ $record->name }} </td>
                <td> {{ $record->abbreviation }} </td>
                <td>
                    @can('edit.bank')
                        <a class="btn btn-secondary btn-sm" href="{{ route('banks.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.bank')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('banks.destroy', $record->id) }}" method="post" style="display: inline">
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
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
