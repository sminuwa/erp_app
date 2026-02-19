@if($teamBalances->count() > 0)
<div class="card">
    <div class="card-header bg-info">
        <h3 class="card-title text-white">Current Team Ledger Balances</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-sm mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Team Name</th>
                    <th class="text-right">Ledger Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teamBalances as $index => $team)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $team->name }}</td>
                    <td class="text-right {{ $team->ledger_balance > 0 ? 'text-danger' : 'text-success' }}">
                        <strong>{{ number_format($team->ledger_balance ?? 0, 2) }}</strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Penalty Ledger Entries</h3>
        <div class="card-tools">
            <span class="badge badge-primary">{{ $totals['total_records'] }} Records</span>
            <span class="badge badge-danger">Total Debit: {{ number_format($totals['total_debit'], 2) }}</span>
            <span class="badge badge-success">Total Credit: {{ number_format($totals['total_credit'], 2) }}</span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($entries->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Team/Staff</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $index => $entry)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ date('d M Y', strtotime($entry['date'])) }}</td>
                        <td>{{ $entry['reference'] }}</td>
                        <td>{{ $entry['description'] }}</td>
                        <td>{{ ucfirst($entry['penalty_type']) }}</td>
                        <td>{{ $entry['penalty_type'] == 'team' ? $entry['team_name'] : $entry['staff_name'] }}</td>
                        <td class="text-right">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}</td>
                        <td class="text-right">{{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}</td>
                        <td class="text-right {{ $entry['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                            <strong>{{ number_format($entry['balance'], 2) }}</strong>
                        </td>
                        <td>
                            @if($entry['status'] == 'posted')
                                <span class="badge badge-success">Posted</span>
                            @elseif($entry['status'] == 'reversed')
                                <span class="badge badge-danger">Reversed</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="6" class="text-right">Totals:</th>
                        <th class="text-right text-danger">{{ number_format($totals['total_debit'], 2) }}</th>
                        <th class="text-right text-success">{{ number_format($totals['total_credit'], 2) }}</th>
                        <th class="text-right">{{ number_format($totals['total_debit'] - $totals['total_credit'], 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="alert alert-info m-3">
            <i class="fa fa-info-circle"></i> No penalty records found for the selected period.
        </div>
        @endif
    </div>
</div>
