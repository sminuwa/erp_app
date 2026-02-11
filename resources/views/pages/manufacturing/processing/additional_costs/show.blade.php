@extends('layouts.backend.app')

@section('title', 'View Additional Cost')

@push('css')
<style>
    .production-details {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>View Additional Cost</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item"><a href="{{ route('manufacturing.additional_costs.index') }}">Additional Costs</a></li>
                        <li class="breadcrumb-item active">{{ $record->reference }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Additional Cost: {{ $record->reference }}</h3>
                        <div class="card-tools">
                            @can('manufacturing.additional_costs.post')
                            @if($record->isPending())
                            <form action="{{ route('manufacturing.additional_costs.post', $record->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this additional cost? This will update the product cost.')">
                                    <i class="fa fa-check"></i> Post
                                </button>
                            </form>
                            @endif
                            @endcan
                            @can('manufacturing.additional_costs.delete')
                            @if($record->isPending())
                            <form action="{{ route('manufacturing.additional_costs.destroy', $record->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                            @endif
                            @endcan
                            <a href="{{ route('manufacturing.additional_costs.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <strong>Reference:</strong>
                                <p>{{ $record->reference }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Cost Date:</strong>
                                <p>{{ date('d M Y', strtotime($record->cost_date)) }}</p>
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
                                <strong>Production Type:</strong>
                                <p>{{ ucfirst(str_replace('_', ' ', $record->production_type)) }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <strong>Account:</strong>
                                <p>{{ $record->account->number ?? '' }} - {{ $record->account->description ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Amount:</strong>
                                <p class="text-primary"><strong>{{ number_format($record->amount, 2) }}</strong></p>
                            </div>
                            <div class="col-md-3">
                                <strong>Created By:</strong>
                                <p>{{ $record->createdBy->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Posted By:</strong>
                                <p>{{ $record->postedBy->name ?? ($record->isPosted() ? 'N/A' : '-') }}</p>
                            </div>
                        </div>

                        @if($record->description)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <strong>Description:</strong>
                                <p>{{ $record->description }}</p>
                            </div>
                        </div>
                        @endif

                        <hr>

                        @php $production = $record->getProduction(); @endphp
                        @if($production)
                        <h5><i class="fa fa-industry"></i> Production Details</h5>
                        <div class="production-details">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Reference:</strong>
                                    <p>{{ $production->reference }}</p>
                                </div>
                                @if($record->production_type == 'single_product')
                                <div class="col-md-3">
                                    <strong>Finish Product:</strong>
                                    <p>{{ $production->bom->finishProduct->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Quantity:</strong>
                                    <p>{{ number_format($production->quantity, 4) }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Total Cost:</strong>
                                    <p>{{ number_format($production->total_cost, 2) }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Batch Number:</strong>
                                    <p>{{ $production->batch_number }}</p>
                                </div>
                                @else
                                <div class="col-md-3">
                                    <strong>Finish Product:</strong>
                                    <p>{{ $production->finishProduct->name ?? ($production->batchProduction->bom->finishProduct->name ?? 'N/A') }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Produced Qty:</strong>
                                    <p>{{ number_format($production->produced_qty, 4) }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Total Cost:</strong>
                                    <p>{{ number_format($production->total_cost, 2) }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Batch Number:</strong>
                                    <p>{{ $production->batchProduction->batch_number ?? '-' }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> Production record not found.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
@endpush
