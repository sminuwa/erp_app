@extends('layouts.backend.app')

@section('title', 'View Penalty')

@push('css')
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>View Penalty</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item"><a href="{{ route('manufacturing.penalties.index') }}">Penalties</a></li>
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
                        <h3 class="card-title">Manufacturing Penalty: {{ $record->reference }}</h3>
                        <div class="card-tools">
                            @can('manufacturing.penalties.post')
                            @if($record->isPending())
                            <form action="{{ route('manufacturing.penalties.post', $record->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this penalty? This will add the amount to the team ledger.')">
                                    <i class="fa fa-check"></i> Post
                                </button>
                            </form>
                            @endif
                            @if($record->isPosted())
                            <form action="{{ route('manufacturing.penalties.reverse', $record->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Reverse this penalty? This will subtract the amount from the team ledger.')">
                                    <i class="fa fa-undo"></i> Reverse
                                </button>
                            </form>
                            @endif
                            @endcan
                            @can('manufacturing.penalties.delete')
                            @if($record->isPending())
                            <form action="{{ route('manufacturing.penalties.destroy', $record->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this penalty?')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                            @endif
                            @endcan
                            <a href="{{ route('manufacturing.penalties.index') }}" class="btn btn-secondary btn-sm">
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
                                <strong>Penalty Date:</strong>
                                <p>{{ date('d M Y', strtotime($record->penalty_date)) }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Penalty Type:</strong>
                                <p>{{ ucfirst($record->penalty_type) }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <p>
                                    @if($record->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($record->status == 'posted')
                                        <span class="badge badge-success">Posted</span>
                                    @elseif($record->status == 'reversed')
                                        <span class="badge badge-danger">Reversed</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                @if($record->penalty_type == 'team')
                                <strong>Team:</strong>
                                <p>{{ $record->team->name ?? 'N/A' }}</p>
                                @else
                                <strong>Staff:</strong>
                                <p>{{ $record->staff->name ?? 'N/A' }}</p>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <strong>Production Document:</strong>
                                <p>{{ $record->production_reference ?? '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Amount Charged:</strong>
                                <p class="text-danger"><strong>{{ number_format($record->amount_charged, 2) }}</strong></p>
                            </div>
                            <div class="col-md-3">
                                <strong>Total Loss Incurred:</strong>
                                <p>{{ number_format($record->total_loss_incurred ?? 0, 2) }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <strong>Created By:</strong>
                                <p>{{ $record->createdBy->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Posted By:</strong>
                                <p>{{ $record->postedBy->name ?? ($record->isPosted() ? 'N/A' : '-') }}</p>
                            </div>
                            @if($record->posted_at)
                            <div class="col-md-3">
                                <strong>Posted At:</strong>
                                <p>{{ date('d M Y H:i', strtotime($record->posted_at)) }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <strong>Description:</strong>
                                <p>{{ $record->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
@endpush
