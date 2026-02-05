@extends('layouts.backend.app')

@section('title', 'View Batch Conversion')

@push('css')
@endpush

@section('content')
<section class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Batch Conversion: {{ $record->reference }}</h3>
                    <div class="card-tools">
                        @can('manufacturing.batch_conversion.post')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.batch_conversion.post', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this conversion?')">
                                <i class="fa fa-check"></i> Post
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.batch_conversion.delete')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.batch_conversion.destroy', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this conversion?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                        @endcan
                        <a href="{{ route('manufacturing.batch_conversion.index') }}" class="btn btn-secondary btn-sm">
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
                            <strong>Conversion Date:</strong>
                            <p>{{ date('d M Y', strtotime($record->conversion_date)) }}</p>
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
                            <strong>Batch Number:</strong>
                            <p>{{ $record->batchProduction->batch_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Finish Product:</strong>
                            <p>{{ $record->batchProduction->bom->finishProduct->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Output Store:</strong>
                            <p>{{ $record->batchProduction->bom->outputStore->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Output Quantity:</strong>
                            <p>{{ number_format($record->output_qty, 4) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Unit Cost:</strong>
                            <p>{{ number_format($record->unit_cost, 2) }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Total Cost:</strong>
                            <p><strong>{{ number_format($record->total_cost, 2) }}</strong></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Created By:</strong>
                            <p>{{ $record->createdBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Posted By:</strong>
                            <p>{{ $record->postedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Posted At:</strong>
                            <p>{{ $record->posted_at ? date('d M Y H:i', strtotime($record->posted_at)) : 'N/A' }}</p>
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
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
