@isset($records)
    <table class="table table-bordered table-striped" id="record1">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Date</th>
                <th>Reference</th>
                <th>Product</th>
                <th>Source Store</th>
                <th>Destination Store</th>
                <th>Qty Transfered </th>
                {{-- <th>Stock In/Out </th> --}}
                <th>Transfered By </th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td> {{ $record->transfer_date }} </td>
                    <td> {{ $record->reference }} </td>
                    <td>{{ $record->product->code }} - {{ $record->product->name }} </td>
                    <td> {{ $record->source->code }} - {{ $record->source->name }} </td>
                    <td> {{ $record->destination->code }} - {{ $record->destination->name }} </td>
                    <td> {{ $record->qty_transfered }} </td>

                    {{-- <td> {{ $record->stock_in_out }} </td> --}}
                    <td> {{ optional($record->user)->name }} </td>
                    <td> {{ $record->status == 1 ? 'Completed' : 'Cancelled' }} </td>
                    <td>
                        @can('interstore.print')
                            <a class="btn btn-secondary btn-sm" title="Stock Tranfer Report" target="_BLANK"
                                href="{{ route('interstore.print', [$record->transfer_id,'A4']) }}">
                                <span class="fa fa-print"></span> Print A4
                            </a>
                            <a class="btn btn-secondary btn-sm" title="Stock Tranfer Report" target="_BLANK"
                                href="{{ route('interstore.print', [$record->transfer_id,'A5']) }}">
                                <span class="fa fa-print"></span> Print A5
                            </a>
                        @endcan

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endisset
