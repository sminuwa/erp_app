@extends('layouts.backend.app')

@section('title', 'View Batch Production')

@push('css')
@endpush

@section('content')
<section class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Batch Production: {{ $record->batch_number }}</h3>
                    <div class="card-tools">
                        @can('manufacturing.batch_production.post')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.batch_production.post', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this batch production?')">
                                <i class="fa fa-check"></i> Post
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.batch_production.print')
                        <a href="{{ route('manufacturing.batch_production.print', $record->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                            <i class="fa fa-print"></i> Print
                        </a>
                        @endcan
                        @can('manufacturing.batch_production.delete')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.batch_production.destroy', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this batch?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                        @endcan
                        <a href="{{ route('manufacturing.batch_production.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Batch Number:</strong>
                            <p>{{ $record->batch_number }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Production Date:</strong>
                            <p>{{ date('d M Y', strtotime($record->production_date)) }}</p>
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
                            <strong>Quantity (Batches):</strong>
                            <p>{{ number_format($record->quantity, 0) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Team:</strong>
                            <p>{{ $record->team->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Material Cost:</strong>
                            <p>{{ number_format($record->computed_material_cost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Labor Cost:</strong>
                            <p>{{ number_format($record->computed_labor_cost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Power Cost:</strong>
                            <p>{{ number_format($record->computed_power_cost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Other Cost:</strong>
                            <p>{{ number_format($record->computed_other_cost, 2) }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>WIP Value:</strong>
                            <p><strong>{{ number_format($record->computed_wip_value, 2) }}</strong></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Converted Qty:</strong>
                            <p>{{ number_format($record->converted_qty, 4) }}</p>
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

                    <hr>
                    <h5>Raw Materials Consumed</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Store</th>
                                <th>Quantity</th>
                                <th>Unit Cost (current)</th>
                                <th>Total Cost (current)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($record->materials as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->product->name ?? 'N/A' }}</td>
                                <td>{{ $material->store->name ?? 'N/A' }}</td>
                                <td>{{ number_format($material->quantity, 4) }}</td>
                                <td>{{ number_format($material->current_unit_cost, 2) }}</td>
                                <td>{{ number_format($material->current_total_cost, 2) }}</td>
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
                                <td><strong>{{ number_format($record->computed_material_cost, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
