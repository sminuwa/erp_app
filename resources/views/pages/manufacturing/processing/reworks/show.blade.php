@extends('layouts.backend.app')

@section('title', 'View Manufacturing Rework')

@push('css')
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manufacturing Rework: {{ $record->reference }}</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item"><a href="{{ route('manufacturing.reworks.index') }}">Reworks</a></li>
                        <li class="breadcrumb-item active">{{ $record->reference }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rework Details</h3>
                    <div class="card-tools">
                        @can('manufacturing.reworks.qc_verify')
                        @if($record->canBeQcVerified())
                        <form action="{{ route('manufacturing.reworks.qc_verify', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('QC verify this rework?')">
                                <i class="fa fa-check-circle"></i> QC Verify
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.reworks.post')
                        @if($record->canBePosted())
                        <form action="{{ route('manufacturing.reworks.post', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this rework? This will debit materials and add costs to the finished product.')">
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
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Reference</label>
                                <p class="font-weight-bold">{{ $record->reference }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Rework Date</label>
                                <p class="font-weight-bold">{{ date('d M Y', strtotime($record->rework_date)) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Status</label>
                                <p>
                                    @if($record->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($record->status == 'qc_verified')
                                        <span class="badge badge-info">QC Verified</span>
                                    @elseif($record->status == 'posted')
                                        <span class="badge badge-success">Posted</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Created By</label>
                                <p class="font-weight-bold">{{ $record->createdBy->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Production Type</label>
                                <p>
                                    @if($record->production_type == 'single_product')
                                        <span class="badge badge-info">Single Product Manufacturing</span>
                                    @elseif($record->production_type == 'batch_conversion')
                                        <span class="badge badge-primary">Batch Conversion</span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Production Reference</label>
                                @php
                                    $production = $record->getProduction();
                                @endphp
                                <p class="font-weight-bold">{{ $production->reference ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Finish Product</label>
                                @php
                                    $finishProduct = null;
                                    if ($production) {
                                        if ($record->production_type == 'single_product') {
                                            $finishProduct = $production->bom->finishProduct ?? null;
                                        } else {
                                            // Batch conversion has its own finish product
                                            $finishProduct = $production->finishProduct ?? ($production->batchProduction->bom->finishProduct ?? null);
                                        }
                                    }
                                @endphp
                                <p class="font-weight-bold">{{ $finishProduct->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($record->isPosted())
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Posted By</label>
                                <p class="font-weight-bold">{{ $record->postedBy->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <hr>
                    <h5>Costs</h5>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Labor Cost</label>
                                <p class="font-weight-bold">{{ number_format($record->labor_cost, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Power Cost</label>
                                <p class="font-weight-bold">{{ number_format($record->power_cost, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Other Cost</label>
                                <p class="font-weight-bold">{{ number_format($record->other_cost, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Total Material Cost</label>
                                <p class="font-weight-bold">{{ number_format($record->total_material_cost, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Grand Total Cost</label>
                                <p class="font-weight-bold text-primary" style="font-size: 1.2em;">{{ number_format($record->total_cost, 2) }}</p>
                            </div>
                        </div>
                        @if($record->isPosted())
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Posted At</label>
                                <p class="font-weight-bold">{{ $record->posted_at ? date('d M Y H:i', strtotime($record->posted_at)) : 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="text-muted">Reason for Rework</label>
                                <p>{{ $record->reason }}</p>
                            </div>
                        </div>
                    </div>

                    @if($record->materials->count() > 0)
                    <hr>
                    <h5>Additional Materials Used</h5>
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Store</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($record->materials as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->product->name ?? 'N/A' }}</td>
                                <td>{{ $material->store->name ?? 'N/A' }}</td>
                                <td class="text-right">{{ number_format($material->quantity, 4) }}</td>
                                <td class="text-right">{{ number_format($material->unit_cost, 2) }}</td>
                                <td class="text-right">{{ number_format($material->total_cost, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary">
                                <td colspan="5" class="text-right"><strong>Total Material Cost:</strong></td>
                                <td class="text-right"><strong>{{ number_format($record->total_material_cost, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    @endif

                    @if($record->isPosted())
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i>
                        This rework has been posted. The additional materials have been debited from inventory and the costs have been added to the finished product.
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fa fa-info-circle"></i>
                        This rework is pending. Upon posting:
                        <ul class="mb-0 mt-1">
                            <li>Additional raw materials will be debited from inventory</li>
                            <li>All costs (materials + labor + power + other) will be added to the finished product</li>
                            <li>Average cost will be recalculated for the finished product</li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
@endpush
