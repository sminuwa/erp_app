<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Class </th>
            <th>Number </th>
            <th>Description </th>
            <th>Branch</th>
            <th>Is Control </th>
            <th>Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->class }} </td>
                <td> {{ $record->number }} </td>
                <td> {{ $record->description }} </td>
                <td> {{ $record->branch?->name }} </td>
                <td> {{ $record->is_control == 1 ? 'Yes' : 'No' }} </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Not active' }} </td>
                <td>
                    @can('general_accounts.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('general_accounts.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('general_accounts.destroy')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('general_accounts.destroy', $record->id) }}" method="post"
                            style="display: inline">
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
