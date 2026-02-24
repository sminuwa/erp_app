<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daily Manufacturing Schedules Report Results</h3>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fa fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Schedules</span>
                        <span class="info-box-number">{{ $totals['total_schedules'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Scheduled Qty</span>
                        <span class="info-box-number">{{ number_format($totals['total_scheduled_qty'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fa fa-file-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Requisitioned Qty</span>
                        <span class="info-box-number">{{ number_format($totals['total_requisitioned_qty'], 4) }}</span>
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
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($schedules->count() > 0)
        @foreach($schedules as $schedule)
        @php
            // For older records, requisitioned_qty may be 0 even if a requisition exists.
            // Derive the effective value: if a requisition exists, the item is fully requisitioned.
            $scheduleIsRequisitioned = $schedule->requisitions->count() > 0;
        @endphp
        <div class="card card-outline card-{{ $schedule->status == 'pending' ? 'warning' : 'success' }} mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    {{ $schedule->reference }}
                    <span class="ml-2">{{ date('d M Y', strtotime($schedule->schedule_date)) }}</span>
                    @if($schedule->status == 'pending')
                        <span class="badge badge-warning ml-2">Pending</span>
                    @else
                        <span class="badge badge-success ml-2">Approved</span>
                    @endif
                    <span class="float-right text-sm">
                        <strong>Order:</strong> {{ $schedule->productionOrder->reference ?? 'N/A' }}
                        | <strong>Team:</strong> {{ $schedule->team->name ?? 'N/A' }}
                        | <strong>Machine:</strong> {{ $schedule->machine->description ?? 'N/A' }}
                        | <strong>Branch:</strong> {{ $schedule->branch->name ?? 'N/A' }}
                    </span>
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>BOM Type</th>
                            <th class="text-right">Scheduled Qty</th>
                            <th class="text-right">Requisitioned Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule->items as $idx => $item)
                        @php
                            // Use stored value if available; fall back to scheduled_qty when requisition exists
                            $reqQty = ($item->requisitioned_qty > 0)
                                ? $item->requisitioned_qty
                                : ($scheduleIsRequisitioned ? $item->scheduled_qty : 0);
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $item->productionOrderItem->bom->finishProduct->code ?? 'N/A' }}</td>
                            <td>{{ $item->productionOrderItem->bom->finishProduct->name ?? 'N/A' }}</td>
                            <td>
                                @if(($item->productionOrderItem->bom->bom_type ?? '') == 'single')
                                    <span class="badge badge-info">Single</span>
                                @else
                                    <span class="badge badge-primary">Batch</span>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($item->scheduled_qty, 4) }}</td>
                            <td class="text-right">{{ number_format($reqQty, 4) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="4"><strong>Schedule Total</strong></td>
                            <td class="text-right"><strong>{{ number_format($schedule->items->sum('scheduled_qty'), 4) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($scheduleIsRequisitioned ? $schedule->items->sum('scheduled_qty') : $schedule->items->sum('requisitioned_qty'), 4) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if($schedule->notes)
            <div class="card-footer text-muted">
                <small><strong>Notes:</strong> {{ $schedule->notes }}</small>
            </div>
            @endif
        </div>
        @endforeach
        @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No schedules found for the selected criteria.
        </div>
        @endif
    </div>
</div>
