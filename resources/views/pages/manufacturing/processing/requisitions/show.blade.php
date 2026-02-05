@extends('layouts.backend.app')

@section('title', 'View Materials Requisition')

@push('css')
@endpush

@section('content')
<section class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Materials Requisition: {{ $record->reference }}</h3>
                    <div class="card-tools">
                        @can('manufacturing.requisitions.approve')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.requisitions.approve', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('Approve this requisition?')">
                                <i class="fa fa-check"></i> Approve
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.requisitions.issue')
                        @if($record->isApproved())
                        <form action="{{ route('manufacturing.requisitions.issue', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Issue materials for this requisition?')">
                                <i class="fa fa-truck"></i> Issue
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.requisitions.receive')
                        @if($record->isIssued())
                        <form action="{{ route('manufacturing.requisitions.receive', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Mark materials as received?')">
                                <i class="fa fa-check-double"></i> Receive
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.requisitions.delete')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.requisitions.destroy', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this requisition?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                        @endcan
                        <a href="{{ route('manufacturing.requisitions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Reference:</strong>
                            <p>{{ $record->reference }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Date:</strong>
                            <p>{{ date('d M Y', strtotime($record->requisition_date)) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Schedule:</strong>
                            <p>{{ $record->schedule->reference ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <p>
                                @if($record->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($record->status == 'approved')
                                    <span class="badge badge-info">Approved</span>
                                @elseif($record->status == 'issued')
                                    <span class="badge badge-primary">Issued</span>
                                @elseif($record->status == 'received')
                                    <span class="badge badge-success">Received</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Created By:</strong>
                            <p>{{ $record->createdBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Approved By:</strong>
                            <p>{{ $record->approvedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Issued By:</strong>
                            <p>{{ $record->issuedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Received By:</strong>
                            <p>{{ $record->receivedBy->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($record->notes)
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Notes:</strong>
                            <p>{{ $record->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <hr>
                    <h5>Requisition Items</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Store</th>
                                <th>Quantity</th>
                                <th>Issued Qty</th>
                                <th>Received Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($record->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->sourceStore->name ?? 'N/A' }}</td>
                                <td>{{ number_format($item->required_qty, 4) }}</td>
                                <td>{{ number_format($item->issued_qty ?? 0, 4) }}</td>
                                <td>{{ number_format($item->received_qty ?? 0, 4) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
