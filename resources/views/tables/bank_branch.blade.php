<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Name </th>
            <th>Sortcode </th>
            <th>Bank</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td> {{ $record->sortcode }} </td>
                <td> {{ $record->bank->name }} </td>
                <td>
                    @can('edit.bank.branch')
                        <a class="btn btn-secondary btn-sm" href="{{ route('bank_branches.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.bank.branch')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('bank_branches.destroy', $record->id) }}" method="post" style="display: inline">
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
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
