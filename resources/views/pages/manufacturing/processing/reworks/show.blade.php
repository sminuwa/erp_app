@extends('layouts.backend.app')

@section('title', 'View Manufacturing Rework')

@push('css')
@endpush

@section('content')
<section class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manufacturing Rework: {{ $record->reference }}</h3>
                    <div class="card-tools">
                        @can('manufacturing.reworks.post')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.reworks.post', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this rework?')">
                                <i class="fa fa-check"></i> Post
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.reworks.delete')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.reworks.destroy', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this rework?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                        @endcan
                        <a href="{{ route('manufacturing.reworks.index') }}" class="btn btn-secondary btn-sm">
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
                            <strong>Rework Date:</strong>
                            <p>{{ date('d M Y', strtotime($record->rework_date)) }}</p>
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
                            <strong>Single Manufacturing:</strong>
                            <p>{{ $record->singleManufacturing->reference ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Batch Production:</strong>
                            <p>{{ $record->batchProduction->batch_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Additional Labor Cost:</strong>
                            <p>{{ number_format($record->additional_labor_cost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Additional Power Cost:</strong>
                            <p>{{ number_format($record->additional_power_cost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Additional Other Cost:</strong>
                            <p>{{ number_format($record->additional_other_cost, 2) }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Total Material Cost:</strong>
                            <p>{{ number_format($record->total_additional_material_cost, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Additional Cost:</strong>
                            <p><strong>{{ number_format($record->total_additional_cost, 2) }}</strong></p>
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
                            <strong>Reason:</strong>
                            <p>{{ $record->reason }}</p>
                        </div>
                    </div>

                    @if($record->materials->count() > 0)
                    <hr>
                    <h5>Additional Materials</h5>
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
                            @foreach($record->materials as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->product->name ?? 'N/A' }}</td>
                                <td>{{ $material->store->name ?? 'N/A' }}</td>
                                <td>{{ number_format($material->quantity, 4) }}</td>
                                <td>{{ number_format($material->unit_cost, 2) }}</td>
                                <td>{{ number_format($material->total_cost, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Total Material Cost:</strong></td>
                                <td><strong>{{ number_format($record->total_additional_material_cost, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
