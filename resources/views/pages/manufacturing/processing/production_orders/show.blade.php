@extends('layouts.backend.app')

@section('title', 'View Production Order')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Production Order: {{ $record->reference }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.production_orders.index') }}">Production Orders</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.production_orders.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.production_orders.create') }}">
                    <span class="fa fa-plus-circle"></span>
                </a>
            @endcan
            <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.production_orders.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <a class="btn btn-info btn-sm" href="{{ route('manufacturing.production_orders.print', $record->id) }}" target="_blank">
                <span class="fa fa-print"></span> Print
            </a>

            @if($record->canBeEdited())
                @can('manufacturing.production_orders.create')
                    <a href="{{ route('manufacturing.production_orders.edit', $record->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                @endcan
            @endif

            @if($record->isRejected() || $record->isAdjustmentNeeded())
                @can('manufacturing.production_orders.create')
                    <form action="{{ route('manufacturing.production_orders.resubmit', $record->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Resubmit this order for review?')">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-refresh"></i> Resubmit
                        </button>
                    </form>
                @endcan
            @endif

            @if($record->isPending())
                @can('manufacturing.production_orders.reject')
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal">
                        <i class="fa fa-times"></i> Reject
                    </button>
                @endcan
                @can('manufacturing.production_orders.adjust')
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#adjustModal">
                        <i class="fa fa-exclamation-triangle"></i> Needs Adjustment
                    </button>
                @endcan
                @can('manufacturing.production_orders.confirm')
                    @if($record->canBeConfirmed())
                    <form action="{{ route('manufacturing.production_orders.confirm', $record->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Are you sure you want to confirm this order?')">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="fa fa-check-circle"></i> Confirm
                        </button>
                    </form>
                    @endif
                @endcan
            @endif

            @if($record->isConfirmed())
                @can('manufacturing.production_orders.approve')
                    <form action="{{ route('manufacturing.production_orders.approve', $record->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Are you sure you want to approve this order?')">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-check"></i> Approve
                        </button>
                    </form>
                @endcan
            @endif

            @if($record->isApproved())
                @can('manufacturing.production_orders.close')
                    <form action="{{ route('manufacturing.production_orders.close', $record->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Are you sure you want to close this order?')">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fa fa-times-circle"></i> Close Order
                        </button>
                    </form>
                @endcan
            @endif

            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Order Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Reference</th>
                                        <td>{{ $record->reference }}</td>
                                    </tr>
                                    <tr>
                                        <th>Order Date</th>
                                        <td>{{ $record->order_date ? date('d-M-Y', strtotime($record->order_date)) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Start Date</th>
                                        <td>{{ $record->start_date ? date('d-M-Y', strtotime($record->start_date)) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>End Date</th>
                                        <td>{{ $record->end_date ? date('d-M-Y', strtotime($record->end_date)) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Branch</th>
                                        <td>{{ $record->branch->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($record->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($record->status == 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @elseif($record->status == 'adjustment_needed')
                                                <span class="badge badge-warning">Adjustment Needed</span>
                                            @elseif($record->status == 'confirmed')
                                                <span class="badge badge-info">Confirmed</span>
                                            @elseif($record->status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($record->status == 'closed')
                                                <span class="badge badge-secondary">Closed</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <td>{{ $record->createdBy->name ?? '-' }}</td>
                                    </tr>
                                    @if($record->confirmed_by)
                                        <tr>
                                            <th>Confirmed By</th>
                                            <td>{{ $record->confirmedBy->name ?? '-' }} at {{ $record->confirmed_at ? date('d-M-Y H:i', strtotime($record->confirmed_at)) : '' }}</td>
                                        </tr>
                                    @endif
                                    @if($record->approved_by)
                                        <tr>
                                            <th>Approved By</th>
                                            <td>{{ $record->approvedBy->name ?? '-' }} at {{ $record->approved_at ? date('d-M-Y H:i', strtotime($record->approved_at)) : '' }}</td>
                                        </tr>
                                    @endif
                                    @if($record->notes)
                                        <tr>
                                            <th>Notes</th>
                                            <td>{{ $record->notes }}</td>
                                        </tr>
                                    @endif
                                    @if($record->rejected_by)
                                        <tr>
                                            <th>Rejected By</th>
                                            <td>{{ $record->rejectedBy->name ?? '-' }} at {{ $record->rejected_at ? date('d-M-Y H:i', strtotime($record->rejected_at)) : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Rejection Reason</th>
                                            <td class="text-danger">{{ $record->rejection_reason }}</td>
                                        </tr>
                                    @endif
                                    @if($record->adjusted_by)
                                        <tr>
                                            <th>Adjustment By</th>
                                            <td>{{ $record->adjustedBy->name ?? '-' }} at {{ $record->adjusted_at ? date('d-M-Y H:i', strtotime($record->adjusted_at)) : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Adjustment Reason</th>
                                            <td class="text-warning">{{ $record->adjustment_reason }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Checklist Panel --}}
                    @if($record->isPending() && $record->checklist && $record->checklist->count() > 0)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Pre-Confirmation Checklist</h5>
                            </div>
                            <div class="card-body">
                                @foreach($record->checklist as $item)
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input checklist-toggle"
                                           id="checklist_{{ $item->id }}"
                                           data-id="{{ $item->id }}"
                                           {{ $item->is_checked ? 'checked' : '' }}
                                           @cannot('manufacturing.production_orders.confirm') disabled @endcannot>
                                    <label class="custom-control-label" for="checklist_{{ $item->id }}">
                                        {{ $item->label }}
                                        @if($item->is_checked && $item->checkedBy)
                                            <small class="text-muted">({{ $item->checkedBy->name }} at {{ date('d-M-Y H:i', strtotime($item->checked_at)) }})</small>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                                <hr>
                                <div id="checklist-status">
                                    @if($record->checklistComplete())
                                        <span class="text-success"><i class="fa fa-check-circle"></i> All items verified - Confirm button is available</span>
                                    @else
                                        <span class="text-warning"><i class="fa fa-exclamation-circle"></i> Complete all checklist items to enable Confirm</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Order Items (Finish Goods to Produce)</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>BOM Reference</th>
                                            <th>Finish Product</th>
                                            <th>BOM Type</th>
                                            <th class="text-right">Quantity</th>
                                            <th class="text-right">Scheduled</th>
                                            <th class="text-right">Produced</th>
                                            <th class="text-right">Unscheduled</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($record->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->bom->reference ?? '-' }}</td>
                                                <td>{{ $item->bom->finishProduct->name ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $item->bom->bom_type == 'batch' ? 'primary' : 'success' }}">
                                                        {{ ucfirst($item->bom->bom_type ?? '') }}
                                                    </span>
                                                </td>
                                                <td class="text-right">{{ number_format($item->quantity_to_produce, 4) }}</td>
                                                <td class="text-right">{{ number_format($item->scheduled_qty, 4) }}</td>
                                                <td class="text-right">{{ number_format($item->produced_qty, 4) }}</td>
                                                <td class="text-right">{{ number_format($item->quantity_to_produce - $item->scheduled_qty, 4) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Required Raw Materials</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Source Store</th>
                                            <th class="text-right">Required Qty</th>
                                            <th class="text-right">Unit Cost</th>
                                            <th class="text-right">Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalCost = 0; @endphp
                                        @foreach($record->materials as $index => $material)
                                            @php $totalCost += $material->total_cost; @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $material->product->code ?? '' }} - {{ $material->product->name ?? '-' }}</td>
                                                <td>{{ $material->sourceStore->name ?? '-' }}</td>
                                                <td class="text-right">{{ number_format($material->required_qty, 4) }}</td>
                                                <td class="text-right">{{ number_format($material->unit_cost, 2) }}</td>
                                                <td class="text-right">{{ number_format($material->total_cost, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary">
                                            <th colspan="5" class="text-right">Total Estimated Material Cost:</th>
                                            <th class="text-right">{{ number_format($totalCost, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('manufacturing.production_orders.reject', $record->id) }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Reject Production Order</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Adjust Modal --}}
    <div class="modal fade" id="adjustModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('manufacturing.production_orders.adjust', $record->id) }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Request Adjustment</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Adjustment Details <span class="text-danger">*</span></label>
                            <textarea name="adjustment_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Request Adjustment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('.checklist-toggle').on('change', function() {
        var checkbox = $(this);
        var checklistId = checkbox.data('id');

        $.ajax({
            url: '{{ route("manufacturing.production_orders.toggle_checklist", $record->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                checklist_id: checklistId
            },
            success: function(response) {
                if (response.checklist_complete) {
                    $('#checklist-status').html('<span class="text-success"><i class="fa fa-check-circle"></i> All items verified - Confirm button is available</span>');
                    location.reload();
                } else {
                    $('#checklist-status').html('<span class="text-warning"><i class="fa fa-exclamation-circle"></i> Complete all checklist items to enable Confirm</span>');
                    location.reload();
                }
            },
            error: function() {
                checkbox.prop('checked', !checkbox.prop('checked'));
                alert('Error updating checklist item.');
            }
        });
    });
});
</script>
@endpush
