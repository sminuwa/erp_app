<div class="card">
    <div class="card-header">
        <h3 class="card-title">Batch Conversion Report Results</h3>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fa fa-exchange-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Conversions</span>
                        <span class="info-box-number">{{ $totals['total_conversions'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Produced</span>
                        <span class="info-box-number">{{ number_format($totals['total_produced'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fa fa-bullseye"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Expected</span>
                        <span class="info-box-number">{{ number_format($totals['total_expected'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fa fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Cost</span>
                        <span class="info-box-number">{{ number_format($totals['total_cost'], 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fa fa-chart-pie"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tolerance Breakdown</span>
                        <span class="info-box-number" style="font-size: 13px;">
                            <span class="badge badge-success">{{ $totals['within_count'] }} Within</span>
                            <span class="badge badge-warning ml-1">{{ $totals['shortage_count'] }} Short</span>
                            <span class="badge badge-danger ml-1">{{ $totals['excess_count'] }} Excess</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="badge badge-warning badge-lg">{{ $totals['pending'] }} Pending</span>
                <span class="badge badge-success badge-lg ml-1">{{ $totals['posted'] }} Posted</span>
            </div>
        </div>

        @if($conversions->count() > 0)
        <table class="table table-bordered table-striped table-sm">
            <thead class="thead-dark">
                <tr>
                    <th width="40">#</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Batch No.</th>
                    <th>Product</th>
                    <th class="text-right">Produced Qty</th>
                    <th class="text-right">Expected Qty</th>
                    <th class="text-right">Min (Shortage)</th>
                    <th class="text-right">Max (Excess)</th>
                    <th class="text-right">Shortage Qty</th>
                    <th class="text-right">Excess Qty</th>
                    <th class="text-center">Tolerance Status</th>
                    <th class="text-right">WIP Cost</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Total Cost</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conversions as $idx => $conv)
                @php
                    $rowClass = '';
                    if ($conv->diff_type === 'shortage') $rowClass = 'table-warning';
                    elseif ($conv->diff_type === 'excess')   $rowClass = 'table-danger';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $conv->reference }}</td>
                    <td>{{ date('d M Y', strtotime($conv->conversion_date)) }}</td>
                    <td>{{ $conv->batchProduction->batch_number ?? 'N/A' }}</td>
                    <td>
                        <small class="text-muted">{{ $conv->finishProduct->code ?? '' }}</small><br>
                        {{ $conv->finishProduct->name ?? 'N/A' }}
                    </td>
                    <td class="text-right font-weight-bold">{{ number_format($conv->produced_qty, 4) }}</td>
                    <td class="text-right">{{ number_format($conv->expected_qty, 4) }}</td>
                    <td class="text-right text-warning">
                        {{ number_format($conv->min_qty, 4) }}
                        @if($conv->accepted_shortage > 0)
                            <small class="d-block text-muted">-{{ $conv->accepted_shortage }}%</small>
                        @endif
                    </td>
                    <td class="text-right text-danger">
                        {{ number_format($conv->max_qty, 4) }}
                        @if($conv->accepted_excess > 0)
                            <small class="d-block text-muted">+{{ $conv->accepted_excess }}%</small>
                        @endif
                    </td>
                    <td class="text-right {{ $conv->actual_shortage > 0 ? 'text-warning font-weight-bold' : '' }}">
                        {{ $conv->actual_shortage > 0 ? number_format($conv->actual_shortage, 4) : '-' }}
                    </td>
                    <td class="text-right {{ $conv->actual_excess > 0 ? 'text-danger font-weight-bold' : '' }}">
                        {{ $conv->actual_excess > 0 ? number_format($conv->actual_excess, 4) : '-' }}
                    </td>
                    <td class="text-center">
                        @if($conv->diff_type === 'within')
                            <span class="badge badge-success">Within Range</span>
                        @elseif($conv->diff_type === 'shortage')
                            <span class="badge badge-warning">Short {{ number_format($conv->tolerance_diff, 4) }}</span>
                        @else
                            <span class="badge badge-danger">Excess +{{ number_format($conv->tolerance_diff, 4) }}</span>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($conv->wip_cost_deducted, 2) }}</td>
                    <td class="text-right">{{ number_format($conv->unit_cost, 2) }}</td>
                    <td class="text-right font-weight-bold">{{ number_format($conv->total_cost, 2) }}</td>
                    <td class="text-center">
                        @if($conv->status === 'posted')
                            <span class="badge badge-success">Posted</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-secondary">
                <tr>
                    <td colspan="5"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($totals['total_produced'], 4) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totals['total_expected'], 4) }}</strong></td>
                    <td colspan="5"></td>
                    <td class="text-right"><strong>{{ number_format($totals['total_wip_cost'], 2) }}</strong></td>
                    <td></td>
                    <td class="text-right"><strong>{{ number_format($totals['total_cost'], 2) }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No batch conversions found for the selected criteria.
        </div>
        @endif
    </div>
</div>
