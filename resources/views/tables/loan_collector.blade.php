<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Name </th>
            <th>Address </th>
            <th>Email </th>
            <th>Phone </th>
            <th>Reg Code </th>
            <th>Registered By </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td> {{ $record->address }} </td>
                <td> {{ $record->email }} </td>
                <td> {{ $record->phone }} </td>
                <td> {{ $record->reg_code }} </td>
                <td> {{ $record->registered->name }} </td>
                <td>
                    @can('edit.loan.collector')
                        <a class="btn btn-secondary btn-sm" href="{{ route('loan_collectors.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.loan.collector')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('loan_collectors.destroy', $record->id) }}" method="post"
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
