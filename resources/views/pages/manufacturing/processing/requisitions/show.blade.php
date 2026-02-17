@extends('layouts.backend.app')

@section('title', 'View Materials Requisition')

@push('css')
@endpush

@section('content')
<section class="content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Materials Requisition: {{ $record->reference }}</h3>
                    <div class="card-tools">
                        @can('manufacturing.requisitions.approve')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.requisitions.approve', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('Approve this requisition?')">
                                <i class="fa fa-check"></i> Approve
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.requisitions.issue')
                        @if($record->isApproved())
                        <form action="{{ route('manufacturing.requisitions.issue', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Issue materials for this requisition?')">
                                <i class="fa fa-truck"></i> Issue
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.requisitions.receive')
                        @if($record->isIssued())
                        <form action="{{ route('manufacturing.requisitions.receive', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Mark materials as received?')">
                                <i class="fa fa-check-double"></i> Receive
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('manufacturing.requisitions.delete')
                        @if($record->isPending())
                        <form action="{{ route('manufacturing.requisitions.destroy', $record->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this requisition?')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                        @endcan
                        <a href="{{ route('manufacturing.requisitions.index') }}" class="btn btn-secondary btn-sm">
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
                            <strong>Date:</strong>
                            <p>{{ date('d M Y', strtotime($record->requisition_date)) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Schedule:</strong>
                            <p>{{ $record->schedule->reference ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <p>
                                @if($record->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($record->status == 'approved')
                                    <span class="badge badge-info">Approved</span>
                                @elseif($record->status == 'issued')
                                    <span class="badge badge-primary">Issued</span>
                                @elseif($record->status == 'received')
                                    <span class="badge badge-success">Received</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Created By:</strong>
                            <p>{{ $record->createdBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Approved By:</strong>
                            <p>{{ $record->approvedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Issued By:</strong>
                            <p>{{ $record->issuedBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Received By:</strong>
                            <p>{{ $record->receivedBy->name ?? 'N/A' }}</p>
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

                    {{-- Production Details Section --}}
                    @if($record->bom)
                    <hr>
                    <h5><i class="fa fa-industry"></i> Production Details</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>BOM Reference:</strong>
                            <p>{{ $record->bom->reference ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>BOM Type:</strong>
                            <p><span class="badge badge-{{ $record->bom->bom_type === 'batch' ? 'info' : 'primary' }}">{{ ucfirst($record->bom->bom_type) }}</span></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Finish Product:</strong>
                            <p>{{ $record->bom->finishProduct->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Output Store:</strong>
                            <p>{{ $record->bom->outputStore->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Quantity to Produce:</strong>
                            <p>{{ number_format($record->quantity ?? 0, 4) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Already Manufactured:</strong>
                            <p>
                                <span class="{{ $totalManufactured > 0 ? 'text-success' : '' }}">
                                    {{ number_format($totalManufactured, 4) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <strong>Remaining:</strong>
                            <p>
                                <span class="{{ $remainingQty > 0 ? 'text-warning font-weight-bold' : 'text-success' }}">
                                    {{ number_format($remainingQty, 4) }}
                                </span>
                                @if($remainingQty <= 0 && ($record->quantity ?? 0) > 0)
                                    <span class="badge badge-success">Completed</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <strong>Expected Output per Unit:</strong>
                            <p>{{ number_format($record->bom->actual_output ?? 0, 4) }}</p>
                        </div>
                    </div>

                    {{-- Schedule Details --}}
                    @if($record->schedule)
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Schedule:</strong>
                            <p>{{ $record->schedule->reference ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Schedule Date:</strong>
                            <p>{{ $record->schedule->schedule_date ? date('d M Y', strtotime($record->schedule->schedule_date)) : 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Team:</strong>
                            <p>{{ $record->schedule->team->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Machine:</strong>
                            <p>{{ $record->schedule->machine->code ?? '' }} {{ $record->schedule->machine->description ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- BOM Cost Breakdown --}}
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>BOM Cost Breakdown (per unit):</strong>
                            <table class="table table-sm table-bordered mt-1">
                                <tr>
                                    <td>Labor Cost</td>
                                    <td class="text-right">{{ number_format($record->bom->labor_cost ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Power Cost</td>
                                    <td class="text-right">{{ number_format($record->bom->power_cost ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Other Cost</td>
                                    <td class="text-right">{{ number_format($record->bom->other_cost ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- BOM Materials --}}
                    @if($record->bom->materials && $record->bom->materials->count() > 0)
                    <h6 class="mt-3">BOM Materials (per unit)</h6>
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Raw Material</th>
                                <th class="text-right">Qty per Unit</th>
                                <th class="text-right">Total Required (x {{ number_format($record->quantity ?? 0, 4) }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($record->bom->materials as $index => $bomMat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $bomMat->product->name ?? 'N/A' }}</td>
                                <td class="text-right">{{ number_format($bomMat->quantity, 4) }}</td>
                                <td class="text-right">{{ number_format($bomMat->quantity * ($record->quantity ?? 0), 4) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                    {{-- Manufacturing Progress --}}
                    @php
                        $productions = $record->bom->bom_type === 'single'
                            ? $record->singleProductManufacturing
                            : $record->batchProductions;
                    @endphp
                    @if($productions && $productions->count() > 0)
                    <h6 class="mt-3">Manufacturing Progress</h6>
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Reference</th>
                                <th>Date</th>
                                <th class="text-right">Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productions as $index => $prod)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $prod->reference }}</td>
                                <td>{{ date('d M Y', strtotime($prod->manufacturing_date ?? $prod->production_date ?? $prod->created_at)) }}</td>
                                <td class="text-right">{{ number_format($prod->quantity, 4) }}</td>
                                <td>
                                    @if($prod->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($prod->status === 'posted')
                                        <span class="badge badge-success">Posted</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($prod->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary">
                                <td colspan="3" class="text-right"><strong>Total Manufactured:</strong></td>
                                <td class="text-right"><strong>{{ number_format($totalManufactured, 4) }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    @endif
                    @endif

                    <hr>
                    <h5>Requisition Items</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Store</th>
                                <th>Quantity</th>
                                <th>Issued Qty</th>
                                <th>Received Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($record->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->sourceStore->name ?? 'N/A' }}</td>
                                <td>{{ number_format($item->required_qty, 4) }}</td>
                                <td>{{ number_format($item->issued_qty ?? 0, 4) }}</td>
                                <td>{{ number_format($item->received_qty ?? 0, 4) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
