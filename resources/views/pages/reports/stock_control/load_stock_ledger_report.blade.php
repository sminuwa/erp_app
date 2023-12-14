<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.stock.ledger.report.print', [$from_date, $to_date, $branch_id, $store_id, $product_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
{{-- <h3>{{ $product->item ?? '' }}</h3> --}}
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <thead>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Store</th>
            <th>Branch</th>
            <th>QTY</th>
            <th>Account</th>
            <th>QTY Before</th>
            <th>QTY After</th>
        </tr>
    </thead>
    <tbody>
        @php
            $qty_in_stock = 0;
            $available_qty = 0;
            $qty_after = $qty_before = $qty_available;
            //$available_qty = $qty_available;
        @endphp

        @foreach ($records as $record)
            <tr>
                <td>{{ Carbon\Carbon::parse($record->date)->toFormattedDateString() }}</td>
                <td>{{ $record->refno }}</td>
                <td>{{ $record->store_code }}</td>
                <td>{{ $record->branch_code }}</td>
                <td>
                    @php
                        $qty_after = $qty_before;
                    @endphp
                    @if ($record->cr > 0)
                        {{ $quantity = $record->cr }}
                        @php
                            $qty_before = $qty_after - $quantity;
                        @endphp
                    @else
                        -{{ $quantity = $record->dr }}
                        @php
                            $qty_before = $qty_after + $quantity;
                        @endphp
                    @endif
                </td>


                <td>
                    @if ($record->model_name == 'Customer')
                        {{ App\Models\Customer::find($record->model_id)->code }}
                        {{-- @elseif($record->model_name == 'GeneralAccount')
                        {{ App\Models\GeneralAccount::find($record->model_id)->description }} --}}
                    @elseif($record->model_name == 'Supplier')
                        {{ App\Models\Supplier::find($record->model_id)->code }}
                    @endif
                </td>

                <td>{{ $qty_before }}</td>
                <td>{{ $qty_after }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" align="right">Current Stock Balance B/F</td>
            <td>{{ number_format($qty_after, 0) }}</td>
        </tr>
    </tfoot>
</table>
