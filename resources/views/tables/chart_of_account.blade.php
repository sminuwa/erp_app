<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Prefix </th>
            <th>Class </th>
            <th>Description </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->prefix }} </td>
                <td> {{ $record->class }} </td>
                <td> {{ $record->description }} </td>
                <td>
                    <a class="btn btn-secondary btn-sm" href="{{ route('chart_of_accounts.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('chart_of_accounts.destroy', $record->id) }}" method="post"
                        style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                            <i class="text-danger fa fa-remove"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
