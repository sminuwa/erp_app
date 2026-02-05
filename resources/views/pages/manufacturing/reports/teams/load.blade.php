<div class="card">
    <div class="card-header">
        <h3 class="card-title">Teams Performance Report</h3>
    </div>
    <div class="card-body">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Teams</span>
                        <span class="info-box-number">{{ $teams->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-industry"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Single Manufacturing</span>
                        <span class="info-box-number">{{ $totals['total_single_count'] }}</span>
                        <span class="progress-description">Cost: {{ number_format($totals['total_single_cost'], 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fa fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Batch Productions</span>
                        <span class="info-box-number">{{ $totals['total_batch_count'] }}</span>
                        <span class="progress-description">WIP: {{ number_format($totals['total_batch_wip'], 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Penalties</span>
                        <span class="info-box-number">{{ number_format($totals['total_penalties'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($teams->count() > 0)
        <h5>Team Performance Details</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Members</th>
                    <th class="text-center">Single Mfg Count</th>
                    <th class="text-right">Single Mfg Qty</th>
                    <th class="text-right">Single Mfg Cost</th>
                    <th class="text-center">Batch Count</th>
                    <th class="text-right">Batch Qty</th>
                    <th class="text-right">Batch WIP</th>
                    <th class="text-right">Penalties</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teams as $team)
                <tr>
                    <td>
                        <strong>{{ $team->name }}</strong>
                        @if($team->supervisors->count() > 0)
                        <br><small class="text-muted">
                            Supervisors: {{ $team->supervisors->pluck('employee.full_name')->filter()->implode(', ') }}
                        </small>
                        @endif
                    </td>
                    <td class="text-center">{{ $team->members->count() }}</td>
                    <td class="text-center">{{ $team->single_count ?? 0 }}</td>
                    <td class="text-right">{{ number_format($team->single_qty ?? 0, 4) }}</td>
                    <td class="text-right">{{ number_format($team->single_cost ?? 0, 2) }}</td>
                    <td class="text-center">{{ $team->batch_count ?? 0 }}</td>
                    <td class="text-right">{{ number_format($team->batch_qty ?? 0, 0) }}</td>
                    <td class="text-right">{{ number_format($team->batch_wip ?? 0, 2) }}</td>
                    <td class="text-right text-danger">{{ number_format($team->total_penalties ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="2">Grand Total</td>
                    <td class="text-center">{{ $totals['total_single_count'] }}</td>
                    <td class="text-right">{{ number_format($totals['total_single_qty'], 4) }}</td>
                    <td class="text-right">{{ number_format($totals['total_single_cost'], 2) }}</td>
                    <td class="text-center">{{ $totals['total_batch_count'] }}</td>
                    <td class="text-right">{{ number_format($totals['total_batch_qty'], 0) }}</td>
                    <td class="text-right">{{ number_format($totals['total_batch_wip'], 2) }}</td>
                    <td class="text-right text-danger">{{ number_format($totals['total_penalties'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No teams found for the selected criteria.
        </div>
        @endif
    </div>
</div>
