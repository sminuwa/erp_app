<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Name </th>
            <th>Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td> {{ $record->status==1?"Active" : "Inactive"}} </td>
                <td><a class="btn btn-secondary btn-sm" href="{{ route('payment_modes.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('payment_modes.destroy', $record->id) }}" method="post" style="display: inline">
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
    <tfoot>
        <tr>
            <td colspan="3">

            </td>
        </tr>
    </tfoot>
</table>
