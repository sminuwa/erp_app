<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Code </th>
            <th>Name </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->code }} </td>
                <td> {{ $record->name }} </td>
                <td>
                    @can('categories.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('categories.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @if (!$record->has_record())
                        @can('categories.destroy')
                            <form onsubmit="return confirm('Are you sure you want to delete?')"
                                action="{{ route('categories.destroy', $record->id) }}" method="post" style="display: inline">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                    <i class="text-danger fa fa-remove"></i>
                                </button>
                            </form>
                        @endcan
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
