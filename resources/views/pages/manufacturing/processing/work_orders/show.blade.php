@extends('layouts.backend.app')

@section('title', 'View Work Order')

@push('css')
@endpush

@section('content')
<section class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Work Order: {{ $record->reference }}</h3>
                    <div class="card-tools">
                        @can('manufacturing.work_orders.post')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.work_orders.post', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this work order?')">
                                <i class="fa fa-check"></i> Post
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.work_orders.delete')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.work_orders.destroy', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this work order?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                        @endcan
                        <a href="{{ route('manufacturing.work_orders.index') }}" class="btn btn-secondary btn-sm">
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
                            <p>{{ date('d M Y', strtotime($record->work_order_date)) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Schedule:</strong>
                            <p>
                                @if($record->schedule)
                                    <a href="{{ route('manufacturing.schedules.show', $record->schedule->id) }}">{{ $record->schedule->reference }}</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <strong>Machine:</strong>
                            <p>{{ $record->machine ? $record->machine->code . ' - ' . $record->machine->description : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Team:</strong>
                            <p>{{ $record->team->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <p>
                                @if($record->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($record->status == 'posted')
                                    <span class="badge badge-success">Posted</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <strong>Created By:</strong>
                            <p>{{ $record->createdBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Posted By:</strong>
                            <p>{{ $record->postedBy->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Notes:</strong>
                            <p>{{ $record->notes ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr>
                    <h5>Work Order Items</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>BOM Reference</th>
                                <th>Finish Product</th>
                                <th>Planned Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($record->items as $index => $item)
                            @php $bom = $item->scheduleItem->productionOrderItem->bom ?? null; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $bom->reference ?? 'N/A' }}</td>
                                <td>{{ $bom->finishProduct->name ?? 'N/A' }}</td>
                                <td>{{ number_format($item->planned_qty, 4) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <hr>
                    <h5>Required Materials</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Store</th>
                                <th>Required Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materials as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material['product_name'] ?? 'N/A' }}</td>
                                <td>{{ $material['store_name'] ?? 'N/A' }}</td>
                                <td>{{ number_format($material['quantity'] ?? 0, 4) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No materials found</td>
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
