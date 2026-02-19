@extends('layouts.backend.app')

@section('title', 'View Single Product Manufacturing')

@push('css')
@endpush

@section('content')
<section class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Single Product Manufacturing: {{ $record->reference }}</h3>
                    <div class="card-tools">
                        @can('manufacturing.single_manufacturing.post')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.single_manufacturing.post', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this manufacturing record?')">
                                <i class="fa fa-check"></i> Post
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.single_manufacturing.print')
                        <a href="{{ route('manufacturing.single_manufacturing.print', $record->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                            <i class="fa fa-print"></i> Print
                        </a>
                        @endcan
                        @can('manufacturing.single_manufacturing.delete')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.single_manufacturing.destroy', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                        @endcan
                        <a href="{{ route('manufacturing.single_manufacturing.index') }}" class="btn btn-secondary btn-sm">
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
                            <strong>Manufacturing Date:</strong>
                            <p>{{ date('d M Y', strtotime($record->manufacturing_date)) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>BOM:</strong>
                            <p>{{ $record->bom->reference ?? 'N/A' }}</p>
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
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Finish Product:</strong>
                            <p>{{ $record->bom->finishProduct->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Output Store:</strong>
                            <p>{{ $record->bom->outputStore->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Quantity:</strong>
                            <p>{{ number_format($record->quantity, 4) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Team:</strong>
                            <p>{{ $record->team->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @php
                        $bom = $record->bom;
                        $laborCost = ($bom->labor_cost ?? 0) * $record->quantity;
                        $powerCost = ($bom->power_cost ?? 0) * $record->quantity;
                        $otherCost = ($bom->other_cost ?? 0) * $record->quantity;
                    @endphp
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Material Cost:</strong>
                            <p>{{ number_format($record->total_material_cost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Labor Cost:</strong>
                            <p>{{ number_format($laborCost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Power Cost:</strong>
                            <p>{{ number_format($powerCost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Other Cost:</strong>
                            <p>{{ number_format($otherCost, 2) }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Total Other Cost:</strong>
                            <p>{{ number_format($record->total_other_cost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Cost:</strong>
                            <p><strong>{{ number_format($record->total_cost, 2) }}</strong></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Unit Cost:</strong>
                            <p>{{ number_format($record->unit_cost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Requisition:</strong>
                            <p>{{ $record->requisition->reference ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Created By:</strong>
                            <p>{{ $record->createdBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Posted By:</strong>
                            <p>{{ $record->postedBy->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Manufacturing Progress for this Requisition --}}
                    @if($record->requisition)
                    @php
                        $reqQty = $record->requisition->quantity ?? 0;
                        $totalManufactured = \App\Models\SingleProductManufacturing::where('requisition_id', $record->requisition_id)->sum('quantity');
                        $remainingQty = max(0, $reqQty - $totalManufactured);
                    @endphp
                    <div class="alert alert-info mt-2">
                        <strong>Requisition Progress:</strong>
                        Total Qty: {{ number_format($reqQty, 4) }} |
                        Manufactured: {{ number_format($totalManufactured, 4) }} |
                        Remaining: <strong>{{ number_format($remainingQty, 4) }}</strong>
                        @if($remainingQty <= 0 && $reqQty > 0)
                            <span class="badge badge-success ml-2">Completed</span>
                        @endif
                    </div>
                    @endif

                    <hr>
                    <h5>Raw Materials Consumed</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Store</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($record->materials as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->product->name ?? 'N/A' }}</td>
                                <td>{{ $material->store->name ?? 'N/A' }}</td>
                                <td>{{ number_format($material->quantity, 4) }}</td>
                                <td>{{ number_format($material->unit_cost, 2) }}</td>
                                <td>{{ number_format($material->total_cost, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No materials found</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Total Material Cost:</strong></td>
                                <td><strong>{{ number_format($record->total_material_cost, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
