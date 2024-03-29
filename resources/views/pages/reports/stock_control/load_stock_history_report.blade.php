<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.stock.history.reports', [$from_date, $to_date, $type, $branch_id, $store_id, $category_id, $product_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Stock History
            Between
            {{ Carbon\Carbon::parse($from_date)->toFormattedDateString() }} and
            {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Code</th>
            <th>Item</th>
            <th>QTY</th>
            <th>Store</th>
            <th>Branch</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Code</th>
            <th>Item</th>
            <th>QTY</th>
            <th>Store</th>
            <th>Branch</th>
        </tr>
    </tfoot>
    @foreach ($records as $record)
        <tr>
            <td>{{ Carbon\Carbon::parse($record->date)->toFormattedDateString() }}</td>
            <td>{{ $record->refno }}</td>
            <td>{{ $record->product_code }}</td>
            <td>{{ $record->product_name }}</td>
            <td>
                @if ($record->cr > 0)
                    {{ $quantity = $record->cr }}
                @else
                    -{{ $quantity = $record->dr }}
                @endif
            </td>
            <td>{{ $record->store_code }}</td>
            <td>{{ $record->branch_code }}</td>
        </tr>
    @endforeach
</table>
