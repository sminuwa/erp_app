<table class="table table-bordered table-striped" id="record1">
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
                <td><a class="btn btn-secondary btn-sm" href="{{ route('dosage_forms.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('dosage_forms.destroy', $record->id) }}" method="post" style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-danger btn-sm cursor-pointer">
                            <i class="text-white fa fa-remove"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
</table>
