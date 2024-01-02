<table class="table table-bordered table-striped" id="example1">
    <thead>
        <tr>
            <th>Name </th>
            <th>Email </th>
            <th>Phone </th>
            <th>Address </th>
            <th>Experience </th>
            <th>Salary </th>
            <th>City </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td> {{ $record->email }} </td>
                <td> {{ $record->phone }} </td>
                <td> {{ $record->address }} </td>
                <td> {{ $record->experience }} </td>
                <td> {{ $record->salary }} </td>
                <td> {{ $record->city }} </td>
                <td>
                    @can('employees.show')
                        <a class="btn btn-secondary btn-sm" href="{{ route('employees.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('employees.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('employees.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('employees.destroy')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('employees.destroy', $record->id) }}" method="post" style="display: inline">
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
