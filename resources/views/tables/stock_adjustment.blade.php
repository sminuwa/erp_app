<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Date </th>
            <th>Reference </th>
            <th>QTY Adjusted </th>
            <th>Adjusted By </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ \Carbon\Carbon::parse($record->date)->toFormattedDateString() }} </td>
                <td> {{ $record->reference }} </td>
                <td> {{ $record->adjusted_qty }} </td>
                <td> {{ $record->user?->name }} </td>
                <td style="text-align: right">
                    @can('stock_adjustments.show')
                        <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan

                </td>
            </tr>
        @endforeach
    </tbody>
</table>
