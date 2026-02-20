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

        @if($orders->count() > 0)
        @foreach($orders as $order)
        <div class="card card-outline card-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'approved' ? 'success' : 'secondary') }} mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <code>{{ $order->reference }}</code>
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
                        @foreach($order->items as $idx => $item)
                        @php
                            $progress = $item->quantity_to_produce > 0 ? round(($item->produced_qty / $item->quantity_to_produce) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><code>{{ $item->bom->reference ?? 'N/A' }}</code></td>
                            <td><code>{{ $item->bom->finishProduct->code ?? 'N/A' }}</code></td>
                            <td>{{ $item->bom->finishProduct->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($item->quantity_to_produce, 4) }}</td>
                            <td class="text-right">{{ number_format($item->scheduled_qty ?? 0, 4) }}</td>
                            <td class="text-right">{{ number_format($item->produced_qty ?? 0, 4) }}</td>
                            <td class="text-right">
                                <div class="progress" style="height: 20px; min-width: 80px;">
                                    <div class="progress-bar bg-{{ $progress >= 100 ? 'success' : ($progress > 0 ? 'info' : 'secondary') }}"
                                         style="width: {{ min($progress, 100) }}%">
                                        {{ $progress }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="4"><strong>Order Total</strong></td>
                            <td class="text-right"><strong>{{ number_format($order->total_finish_goods_qty, 4) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($order->items->sum('scheduled_qty'), 4) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($order->processed_qty, 4) }}</strong></td>
                            <td class="text-right">
                                @php $orderProgress = $order->total_finish_goods_qty > 0 ? round(($order->processed_qty / $order->total_finish_goods_qty) * 100, 1) : 0; @endphp
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
