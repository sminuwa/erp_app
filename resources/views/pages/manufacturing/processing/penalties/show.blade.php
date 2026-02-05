@extends('layouts.app')

@section('title', 'View Penalty')

@section('content')
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
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this penalty?')">
                                <i class="fa fa-check"></i> Post
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
                    <div class="row">
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
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="row">
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
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Reason:</strong>
                            <p>{{ $record->reason }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
