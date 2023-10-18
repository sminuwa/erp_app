<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Date </th>
            <th>Refno </th>
            <th>Adjusted By </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ \Carbon\Carbon::parse($record->date)->toFormattedDateString() }} </td>
                <td> {{ $record->refno }} </td>
                <td> {{ $record->user?->name }} </td>
                <td style="text-align: right">
                    @can('make.stock.adjustment')
                        <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('edit.stock.adjustment')
                        <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.stock.adjustment')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('stock_adjustments.destroy', $record->id) }}" method="post"
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
