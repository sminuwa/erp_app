@extends('layouts.app')

@section('title', 'View Manufacturing Return')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manufacturing Return: {{ $record->reference }}</h3>
                    <div class="card-tools">
                        @can('manufacturing.returns.post')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.returns.post', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this return?')">
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
                            <strong>Return Date:</strong>
                            <p>{{ date('d M Y', strtotime($record->return_date)) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Return Type:</strong>
                            <p>{{ ucfirst($record->return_type) }}</p>
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
                            <strong>Single Manufacturing:</strong>
                            <p>{{ $record->singleManufacturing->reference ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Batch Production:</strong>
                            <p>{{ $record->batchProduction->batch_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Return Quantity:</strong>
                            <p>{{ number_format($record->return_qty, 4) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Created By:</strong>
                            <p>{{ $record->createdBy->name ?? 'N/A' }}</p>
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
                    <h5>Returned Materials</h5>
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
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
