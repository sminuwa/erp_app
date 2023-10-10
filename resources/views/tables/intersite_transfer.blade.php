@isset($records)
    <table class="table table-bordered table-striped" id="record1">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Product</th>
                <th>Source Store</th>
                <th>Destination Store</th>
                <th>Qty Transfered </th>
                {{-- <th>Stock In/Out </th> --}}
                <th>Transfered No </th>
                <th>Transfered By </th>
                <th>Date</th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td> {{ $record->product->name }} </td>
                    <td> {{ $record->source->name }} </td>
                    <td> {{ $record->destination->name }} </td>
                    <td> {{ $record->qty_transfered }} </td>
                    <td> {{ $record->refno }} </td>
                    {{-- <td> {{ $record->stock_in_out }} </td> --}}
                    <td> {{ optional($record->user)->name }} </td>
                    <td> {{ optional($record->updated_at)->toDayDateTimeString() }} </td>
                    <td> {{ $record->status == 1 ? 'Completed' : 'Cancelled' }} </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @can('edit.stock.transfer')
                                    <a class="btn btn-secondary btn-sm"
                                        href="{{ route('intersite.edit', $record->id) }}">
                                        <span class="fa fa-pencil"></span>
                                    </a>
                                @endcan
                                <a class="btn btn-secondary btn-sm" title="Stock Tranfer Report" target="_BLANK"
                                    href="{{ route('intersite.print', $record->transfer_id) }}">
                                    <span class="fa fa-print"></span>
                                </a>
                                @can('delete.stock.transfer')
                                    <form onsubmit="return confirm('Are you sure you want to cancel ?')"
                                        action="{{ route('intersite.destroy', $record->id) }}" method="post"
                                        style="display: inline">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-secondary  btn-sm cursor-pointer">
                                            <i class="text-danger fa fa-remove"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endisset
