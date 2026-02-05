@extends('layouts.backend.app')

@section('title', 'View Additional Cost')

@push('css')
@endpush

@section('content')
<section class="content-wrapper">
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
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this additional cost?')">
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
                    <div class="row">
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
                            <strong>Production:</strong>
                            <p>
                                @php $production = $record->getProduction(); @endphp
                                {{ $production->reference ?? 'N/A' }}
                                <small class="text-muted">({{ ucfirst(str_replace('_', ' ', $record->production_type)) }})</small>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Account:</strong>
                            <p>{{ $record->account->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Amount:</strong>
                            <p><strong>{{ number_format($record->amount, 2) }}</strong></p>
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
                    @if($record->description)
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Description:</strong>
                            <p>{{ $record->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
