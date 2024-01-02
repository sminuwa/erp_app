@isset($records)
    <table class="table table-bordered table-striped" id="record1">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Reference</th>
                <th>Requested By</th>
                <th>Date Requsted</th>
                <th>Approved By</th>
                <th>Date Approved</th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td> {{ $record->reference }} </td>
                    <td> {{ $record->requestedBy->name ?? '' }} </td>
                    <td> {{ optional($record->created_at)->toDayDateTimeString() }} </td>
                    <td> {{ $record->approvedBy->name ?? '' }} </td>
                    <td> {{ $record->approvedBy != null ? optional($record->updated_at)->toDayDateTimeString() : '' }}
                    </td>
                    <td> {{ $record->status }} </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @can('intersite.show')
                                    <a class="btn btn-secondary btn-sm" href="{{ route('intersite.show', $record->id) }}">
                                        <span class="fa fa-eye"></span>
                                    </a>
                                @endcan
                                @can('intersite.edit')
                                    <a class="btn btn-secondary btn-sm" href="{{ route('intersite.edit', $record->id) }}">
                                        <span class="fa fa-pencil"></span>
                                    </a>
                                @endcan
                                @if (App\Models\User::find($record->approved_by) != null)
                                    @can('intersite.print')
                                        <a class="btn btn-secondary btn-sm" title="Stock Tranfer Report" target="_BLANK"
                                            href="{{ route('intersite.print', $record->transfer_id) }}">
                                            <span class="fa fa-print"></span>
                                        </a>
                                    @endcan
                                @endif
                                @can('intersite.destroy')
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
