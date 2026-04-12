@extends('layouts.backend.app')

@section('title', 'View Intersite Additional Cost')

@push('css')
<link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
<style>
    .intersite-details {
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
                    <h4>View Intersite Additional Cost</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('intersite.additional_cost.index') }}">Intersite Additional Costs</a></li>
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
                            @can('intersite.additional_cost.post')
                            @if($record->isPending())
                            <form action="{{ route('intersite.additional_cost.post', $record->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Post this additional cost? This will create accounting entries.')">
                                    <i class="fa fa-check"></i> Post
                                </button>
                            </form>
                            @endif
                            @if($record->canBeReversed())
                            <form action="{{ route('intersite.additional_cost.reverse', $record->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Reverse this additional cost? This will create reverse accounting entries.')">
                                    <i class="fa fa-undo"></i> Reverse
                                </button>
                            </form>
                            @endif
                            @endcan
                            @can('intersite.additional_cost.edit')
                            @if($record->isPending())
                            <a href="{{ route('intersite.additional_cost.edit', $record->id) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            @endif
                            @endcan
                            @can('intersite.additional_cost.delete')
                            @if($record->isPending())
                            <form action="{{ route('intersite.additional_cost.destroy', $record->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                            @endif
                            @endcan
                            <a href="{{ route('intersite.additional_cost.index') }}" class="btn btn-secondary btn-sm">
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
                                <strong>Date:</strong>
                                <p>{{ date('d M Y', strtotime($record->date)) }}</p>
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
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-3">
                                <strong>Cost Mode:</strong>
                                <p>{{ $record->cost_mode == 'by_amount' ? 'By Amount' : 'By Quantity' }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <strong>Supplier:</strong>
                                <p>{{ $record->supplier->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Amount:</strong>
                                <p class="text-primary"><strong>&#8358;{{ number_format($record->amount, 2) }}</strong></p>
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

                        <div class="row mb-4">
                            @if($record->truck_number)
                            <div class="col-md-3">
                                <strong>Truck Number:</strong>
                                <p>{{ $record->truck_number }}</p>
                            </div>
                            @endif
                            @if($record->waybill_no)
                            <div class="col-md-3">
                                <strong>Waybill No:</strong>
                                <p>{{ $record->waybill_no }}</p>
                            </div>
                            @endif
                            @if($record->description)
                            <div class="col-md-6">
                                <strong>Description:</strong>
                                <p>{{ $record->description }}</p>
                            </div>
                            @endif
                        </div>

                        <hr>

                        @if($record->intersiteTransfer)
                        <h5><i class="fa fa-exchange"></i> Intersite Transfer Details</h5>
                        <div class="intersite-details mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Reference:</strong>
                                    <p>
                                        <a href="{{ route('intersite.show', $record->intersiteTransfer->id) }}">
                                            {{ $record->intersiteTransfer->reference }}
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Source Branch:</strong>
                                    <p>{{ $record->intersiteTransfer->source->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Destination Branch:</strong>
                                    <p>{{ $record->intersiteTransfer->destination->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Transfer Date:</strong>
                                    <p>{{ $record->intersiteTransfer->date }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <h5><i class="fa fa-list"></i> Cost Distribution</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Original Unit Cost</th>
                                        <th>Allocated Amount</th>
                                        <th>Additional Unit Cost</th>
                                        <th>New Unit Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalAllocated = 0; @endphp
                                    @foreach($record->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product->code ?? 'N/A' }}</td>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($item->quantity, 2) }}</td>
                                        <td class="text-right">&#8358;{{ number_format($item->original_cost, 2) }}</td>
                                        <td class="text-right">&#8358;{{ number_format($item->allocated_amount, 2) }}</td>
                                        <td class="text-right">&#8358;{{ number_format($item->additional_unit_cost, 4) }}</td>
                                        <td class="text-right"><strong>&#8358;{{ number_format($item->original_cost + $item->additional_unit_cost, 2) }}</strong></td>
                                    </tr>
                                    @php $totalAllocated += $item->allocated_amount; @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total Allocated:</th>
                                        <th class="text-right">&#8358;{{ number_format($totalAllocated, 2) }}</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
@endpush
