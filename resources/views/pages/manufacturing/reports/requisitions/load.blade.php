<div class="card">
    <div class="card-header">
        <h3 class="card-title">Materials Requisitions Report Results</h3>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fa fa-file-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Requisitions</span>
                        <span class="info-box-number">{{ $totals['total_requisitions'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Required Qty</span>
                        <span class="info-box-number">{{ number_format($totals['total_required_qty'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fa fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Issued Qty</span>
                        <span class="info-box-number">{{ number_format($totals['total_issued_qty'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fa fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Material Cost</span>
                        <span class="info-box-number">{{ number_format($totals['total_cost'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <span class="badge badge-warning badge-lg">{{ $totals['pending'] }} Pending</span>
                <span class="badge badge-info badge-lg ml-1">{{ $totals['approved'] }} Approved</span>
                <span class="badge badge-primary badge-lg ml-1">{{ $totals['issued'] }} Issued</span>
                <span class="badge badge-success badge-lg ml-1">{{ $totals['received'] }} Received</span>
            </div>
        </div>

        @if($requisitions->count() > 0)
        @foreach($requisitions as $requisition)
        @php
            $statusColor = match($requisition->status) {
                'pending' => 'warning',
                'approved' => 'info',
                'issued' => 'primary',
                'received' => 'success',
                default => 'secondary'
            };
        @endphp
        <div class="card card-outline card-{{ $statusColor }} mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    {{ $requisition->reference }}
                    <span class="ml-2">{{ date('d M Y', strtotime($requisition->requisition_date)) }}</span>
                    <span class="badge badge-{{ $statusColor }} ml-2">{{ ucfirst($requisition->status) }}</span>
                    <span class="float-right text-sm">
                        @if($requisition->schedule)
                            <strong>Schedule:</strong> {{ $requisition->schedule->reference }}
                            | <strong>Order:</strong> {{ $requisition->schedule->productionOrder->reference ?? 'N/A' }}
                        @elseif($requisition->bom)
                            <strong>BOM:</strong> {{ $requisition->bom->reference }}
                            | <strong>Product:</strong> {{ $requisition->bom->finishProduct->name ?? 'N/A' }}
                        @endif
                        | <strong>Created By:</strong> {{ $requisition->createdBy->name ?? 'N/A' }}
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
                            <th>Source Store</th>
                            <th class="text-right">Required Qty</th>
                            <th class="text-right">Issued Qty</th>
                            <th class="text-right">Received Qty</th>
                            <th class="text-right">Unit Cost</th>
                            <th class="text-right">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requisition->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $item->product->code ?? 'N/A' }}</td>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td>{{ $item->sourceStore->name ?? '-' }}</td>
                            <td class="text-right">{{ number_format($item->required_qty, 4) }}</td>
                            <td class="text-right">{{ number_format($item->issued_qty ?? 0, 4) }}</td>
                            <td class="text-right">{{ number_format($item->received_qty ?? 0, 4) }}</td>
                            <td class="text-right">{{ number_format($item->unit_cost ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($item->required_qty * ($item->unit_cost ?? 0), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="4"><strong>Requisition Total</strong></td>
                            <td class="text-right"><strong>{{ number_format($requisition->items->sum('required_qty'), 4) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($requisition->items->sum('issued_qty'), 4) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($requisition->items->sum('received_qty'), 4) }}</strong></td>
                            <td class="text-right">-</td>
                            <td class="text-right"><strong>{{ number_format($requisition->items->sum(fn($i) => $i->required_qty * ($i->unit_cost ?? 0)), 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if($requisition->notes)
            <div class="card-footer text-muted">
                <small><strong>Notes:</strong> {{ $requisition->notes }}</small>
            </div>
            @endif
        </div>
        @endforeach
        @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No requisitions found for the selected criteria.
        </div>
        @endif
    </div>
</div>
