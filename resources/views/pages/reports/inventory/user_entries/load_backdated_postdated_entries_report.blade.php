@if ($reports->isEmpty())
    <p class="text-center">No records found for the selected filters.</p>
@else
    <div class="table-responsive">
        <table class="display table table-bordered caption" id="example1" data-ordering="true">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Entry Date</th>
                    <th>Recorded Date</th>
                    <th>No Days</th>
                    @if (isset($reports[0]->reference))
                        <th>Reference</th>
                    @endif
                    @if (isset($reports[0]->amount))
                        <th>Amount</th>
                    @endif
                    {{-- @if (isset($reports[0]->description)) --}}
                    <th>Description</th>
                    {{-- @endif --}}
                    <th>Branch</th>
                    <th>Performed By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->date)->format('d M, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->created_at)->format('d M, Y h:i A') }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($record->date)->diffInDays(\Carbon\Carbon::parse($record->created_at)) }}
                            days
                        </td>
                        @if (isset($record->reference))
                            <td>
                                <a href="{{ route($route_name, $record->id) }}"
                                    target="_BLANK">{{ $record->reference }}</a>
                            </td>
                        @endif

                        @if ($type == 'journals')
                            <td style="text-align: right">
                                {{ number_format(amountFromJournalItems($record->reference), 2) }}</td>
                        @else
                            @if (isset($record->amount))
                                <td style="text-align: right">{{ number_format($record->amount, 2) }}</td>
                            @endif
                        @endif
                        {{-- @if (isset($record->description)) --}}
                        <td>{{ $record->description ?? '' }}</td>
                        {{-- @endif --}}
                        <td>{{ $record->branch_name }}</td>
                        <td>{{ $record->user_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
