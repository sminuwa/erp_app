@extends('layouts.backend.app')

@section('title', 'View Daily Manufacturing Schedule')

@push('css')
@endpush

@section('content')
<section class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daily Manufacturing Schedule: {{ $record->reference }}</h3>
                    <div class="card-tools">
                        @can('manufacturing.schedules.approve')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.schedules.approve', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this schedule?')">
                                <i class="fa fa-check"></i> Approve
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.schedules.delete')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.schedules.destroy', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this schedule?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                        @endcan
                        <a href="{{ route('manufacturing.schedules.index') }}" class="btn btn-secondary btn-sm">
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
                            <strong>Schedule Date:</strong>
                            <p>{{ date('d M Y', strtotime($record->schedule_date)) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Production Order:</strong>
                            <p>{{ $record->productionOrder->reference ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <p>
                                @if($record->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($record->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Team:</strong>
                            <p>{{ $record->team->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Machine:</strong>
                            <p>{{ $record->machine->code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Created By:</strong>
                            <p>{{ $record->createdBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Approved By:</strong>
                            <p>{{ $record->approvedBy->name ?? 'N/A' }}</p>
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
                    <h5>Schedule Items</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>BOM Reference</th>
                                <th>Finish Product</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($record->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->productionOrderItem->bom->reference ?? 'N/A' }}</td>
                                <td>{{ $item->productionOrderItem->bom->finishProduct->name ?? 'N/A' }}</td>
                                <td>{{ number_format($item->scheduled_qty, 4) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No items found</td>
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
