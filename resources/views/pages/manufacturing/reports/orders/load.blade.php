<div class="card">
    <div class="card-header">
        <h3 class="card-title">Production Orders Report Results</h3>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fa fa-clipboard-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Orders</span>
                        <span class="info-box-number">{{ $totals['total_orders'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Qty to Produce</span>
                        <span class="info-box-number">{{ number_format($totals['total_qty'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Processed</span>
                        <span class="info-box-number">{{ number_format($totals['total_processed'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fa fa-chart-pie"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Status Breakdown</span>
                        <span class="info-box-number" style="font-size: 14px;">
                            <span class="badge badge-warning">{{ $totals['pending'] }} Pending</span>
                            <span class="badge badge-success">{{ $totals['approved'] }} Approved</span>
                            <span class="badge badge-secondary">{{ $totals['closed'] }} Closed</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @php
            $today = now()->startOfDay();
            $onTimeCount  = 0; $lateCount = 0; $overdueCount = 0; $inProgressCount = 0;
            foreach ($orders as $o) {
                $oEnd    = $o->end_date   ? \Carbon\Carbon::parse($o->end_date)->startOfDay()   : null;
                $oClosed = $o->closed_at  ? \Carbon\Carbon::parse($o->closed_at)->startOfDay()  : null;
                if ($o->status === 'closed' && $oClosed && $oEnd) {
                    if ($oClosed->lte($oEnd)) $onTimeCount++; else $lateCount++;
                } elseif ($o->status !== 'closed' && $oEnd && $today->gt($oEnd)) {
                    $overdueCount++;
                } else {
                    $inProgressCount++;
                }
            }
        @endphp
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">On Time</span>
                        <span class="info-box-number">{{ $onTimeCount }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Completed Late</span>
                        <span class="info-box-number">{{ $lateCount }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fa fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Overdue (Open)</span>
                        <span class="info-box-number">{{ $overdueCount }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fa fa-spinner"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">In Progress</span>
                        <span class="info-box-number">{{ $inProgressCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($orders->count() > 0)
        @foreach($orders as $order)
        <div class="card card-outline card-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'approved' ? 'success' : 'secondary') }} mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    {{ $order->reference }}
                    <span class="ml-2">{{ date('d M Y', strtotime($order->order_date)) }}</span>
                    @if($order->status == 'pending')
                        <span class="badge badge-warning ml-2">Pending</span>
                    @elseif($order->status == 'approved')
                        <span class="badge badge-success ml-2">Approved</span>
                    @else
                        <span class="badge badge-secondary ml-2">Closed</span>
                    @endif
                    <span class="float-right text-sm">
                        <strong>Period:</strong> {{ $order->start_date ? date('d M Y', strtotime($order->start_date)) : '-' }}
                        to {{ $order->end_date ? date('d M Y', strtotime($order->end_date)) : '-' }}
                        | <strong>Branch:</strong> {{ $order->branch->name ?? 'N/A' }}
                        | <strong>Created By:</strong> {{ $order->createdBy->name ?? 'N/A' }}
                    </span>
                </h5>
            </div>
            <!-- Completion Timeline -->
            @php
                $oToday      = now()->startOfDay();
                $oStartDate  = $order->start_date ? \Carbon\Carbon::parse($order->start_date) : null;
                $oEndDate    = $order->end_date   ? \Carbon\Carbon::parse($order->end_date)->startOfDay()   : null;
                $oClosedDate = $order->closed_at  ? \Carbon\Carbon::parse($order->closed_at)->startOfDay()  : null;
                $oPlannedDays = ($oStartDate && $oEndDate) ? (int) $oStartDate->diffInDays($oEndDate) : null;

                if ($order->status === 'closed' && $oClosedDate && $oEndDate) {
                    $oStatus   = $oClosedDate->lte($oEndDate) ? 'on_time' : 'late';
                    $oVariance = (int) $oEndDate->diffInDays($oClosedDate);
                } elseif ($order->status !== 'closed' && $oEndDate && $oToday->gt($oEndDate)) {
                    $oStatus   = 'overdue';
                    $oVariance = (int) $oEndDate->diffInDays($oToday);
                } elseif ($order->status !== 'closed') {
                    $oStatus   = 'in_progress';
                    $oVariance = 0;
                } else {
                    $oStatus   = 'unknown';
                    $oVariance = 0;
                }
            @endphp
            <div class="px-3 py-1 border-top bg-light">
                <small>
                    <strong>Planned Duration:</strong> {{ $oPlannedDays !== null ? $oPlannedDays . ' days' : 'N/A' }}
                    &nbsp;|&nbsp;
                    @if($oStatus === 'on_time')
                        <span class="badge badge-success"><i class="fa fa-check"></i> Completed On Time</span>
                        <span class="text-muted ml-1">Closed: {{ $oClosedDate->format('d M Y') }}</span>
                    @elseif($oStatus === 'late')
                        <span class="badge badge-danger"><i class="fa fa-exclamation-triangle"></i> Completed Late (+{{ $oVariance }} day{{ $oVariance != 1 ? 's' : '' }})</span>
                        <span class="text-muted ml-1">Closed: {{ $oClosedDate->format('d M Y') }}</span>
                    @elseif($oStatus === 'overdue')
                        <span class="badge badge-danger"><i class="fa fa-clock"></i> Overdue ({{ $oVariance }} day{{ $oVariance != 1 ? 's' : '' }} past due)</span>
                    @elseif($oStatus === 'in_progress')
                        <span class="badge badge-info"><i class="fa fa-spinner"></i> In Progress</span>
                    @else
                        <span class="badge badge-secondary">No Timeline Set</span>
                    @endif
                </small>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">#</th>
                            <th>BOM Reference</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th class="text-right">Qty to Produce</th>
                            <th class="text-right">Scheduled Qty</th>
                            <th class="text-right">Produced Qty</th>
                            <th class="text-right">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $orderTargetQty = 0; @endphp
                        @foreach($order->items as $idx => $item)
                        @php
                            // For batch BOMs, quantity_to_produce = number of runs; target units = runs * actual_output
                            // For single BOMs, quantity_to_produce is already in finish goods units
                            $isBatch = ($item->bom->bom_type ?? 'single') === 'batch';
                            $targetQty = $isBatch
                                ? $item->quantity_to_produce * ($item->bom->actual_output ?? 1)
                                : $item->quantity_to_produce;
                            $orderTargetQty += $targetQty;
                            $progress = $targetQty > 0 ? round(($item->produced_qty / $targetQty) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $item->bom->reference ?? 'N/A' }}</td>
                            <td>{{ $item->bom->finishProduct->code ?? 'N/A' }}</td>
                            <td>{{ $item->bom->finishProduct->name ?? 'N/A' }}</td>
                            <td class="text-right">
                                {{ number_format($item->quantity_to_produce, 4) }}
                                @if($isBatch)
                                    <small class="text-muted d-block">({{ number_format($targetQty, 4) }} units)</small>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($item->scheduled_qty ?? 0, 4) }}</td>
                            <td class="text-right">{{ number_format($item->produced_qty ?? 0, 4) }}</td>
                            <td class="text-right">
                                <div class="progress" style="height: 20px; min-width: 80px;">
                                    <div class="progress-bar bg-{{ $progress >= 100 ? 'success' : ($progress > 0 ? 'info' : 'secondary') }}"
                                         style="width: {{ min($progress, 100) }}%; min-width: {{ $progress > 0 ? '2rem' : '0' }}">
                                        {{ $progress }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        @php $orderProgress = $orderTargetQty > 0 ? round(($order->processed_qty / $orderTargetQty) * 100, 1) : 0; @endphp
                        <tr>
                            <td colspan="4"><strong>Order Total</strong></td>
                            <td class="text-right"><strong>{{ number_format($orderTargetQty, 4) }} units</strong></td>
                            <td class="text-right"><strong>{{ number_format($order->items->sum('scheduled_qty'), 4) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($order->processed_qty, 4) }}</strong></td>
                            <td class="text-right">
                                <strong>{{ $orderProgress }}%</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if($order->notes)
            <div class="card-footer text-muted">
                <small><strong>Notes:</strong> {{ $order->notes }}</small>
            </div>
            @endif
        </div>
        @endforeach
        @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No production orders found for the selected criteria.
        </div>
        @endif
    </div>
</div>
