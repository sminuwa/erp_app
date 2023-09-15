<table class="table table-bordered table-striped" id="record2">
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
                    {{--<a class="btn btn-secondary btn-sm" href="{{ route('permissions.show', $record->id) }}">
                        <span class="fa fa-eye"></span>
                    </a><a class="btn btn-secondary btn-sm" href="{{ route('permissions.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('permissions.destroy', $record->id) }}" method="post"
                        style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                            <i class="text-danger fa fa-remove"></i>
                        </button>
                    </form>--}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
