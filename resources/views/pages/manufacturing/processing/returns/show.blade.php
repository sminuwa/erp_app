@extends('layouts.backend.app')

@section('title', 'View Manufacturing Return')

@push('css')
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manufacturing Return: {{ $record->reference }}</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item"><a href="{{ route('manufacturing.returns.index') }}">Returns</a></li>
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
                    <h3 class="card-title">Return Details</h3>
                    <div class="card-tools">
                        @can('manufacturing.returns.post')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.returns.post', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this return? This will credit raw materials and debit finished goods.')">
                                <i class="fa fa-check"></i> Post
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.returns.delete')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.returns.destroy', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this return?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                        @endcan
                        <a href="{{ route('manufacturing.returns.index') }}" class="btn btn-secondary btn-sm">
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
                                <label class="text-muted">Return Date</label>
                                <p class="font-weight-bold">{{ date('d M Y', strtotime($record->return_date)) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Status</label>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Return Quantity</label>
                                <p class="font-weight-bold text-danger">{{ number_format($record->return_qty, 4) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Total Cost Returned</label>
                                <p class="font-weight-bold text-danger">{{ number_format($record->total_cost_returned, 2) }}</p>
                            </div>
                        </div>
                        @if($record->isPosted())
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-muted">Posted By</label>
                                <p class="font-weight-bold">{{ $record->postedBy->name ?? 'N/A' }}</p>
                            </div>
                        </div>
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
                                <label class="text-muted">Reason for Return</label>
                                <p>{{ $record->reason }}</p>
                            </div>
                        </div>
                    </div>

                    @if($record->materials && $record->materials->count() > 0)
                    <div class="card card-outline card-info mt-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-cubes"></i> Raw Materials to be {{ $record->isPosted() ? 'Credited' : 'Credited Back' }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-striped table-sm mb-0">
                                <thead class="bg-light">
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
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="5" class="text-right">Total Materials Cost:</th>
                                        <th class="text-right">{{ number_format($record->materials->sum('total_cost'), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($record->isPosted())
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i>
                        This return has been posted. The raw materials have been credited back to inventory and the finished goods have been debited.
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fa fa-info-circle"></i>
                        This return is pending. Upon posting:
                        <ul class="mb-0 mt-1">
                            <li>Raw materials listed above will be credited back to inventory</li>
                            <li>Finished goods will be debited from inventory</li>
                            <li>Average cost will be recalculated for affected products</li>
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
