@extends('layouts.backend.app')

@section('title', 'View Production Order')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Production Order: {{ $record->reference }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.production_orders.index') }}">Production Orders</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.production_orders.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.production_orders.create') }}">
                    <span class="fa fa-plus-circle"></span>
                </a>
            @endcan
            <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.production_orders.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <a class="btn btn-info btn-sm" href="{{ route('manufacturing.production_orders.print', $record->id) }}" target="_blank">
                <span class="fa fa-print"></span> Print
            </a>

            @if($record->isPending())
                @can('manufacturing.production_orders.create')
                    <a href="{{ route('manufacturing.production_orders.edit', $record->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                @endcan
                @can('manufacturing.production_orders.approve')
                    <form action="{{ route('manufacturing.production_orders.approve', $record->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Are you sure you want to approve this order?')">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-check"></i> Approve
                        </button>
                    </form>
                @endcan
            @endif

            @if($record->isApproved())
                @can('manufacturing.production_orders.close')
                    <form action="{{ route('manufacturing.production_orders.close', $record->id) }}" method="POST" style="display: inline" onsubmit="return confirm('Are you sure you want to close this order?')">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fa fa-times-circle"></i> Close Order
                        </button>
                    </form>
                @endcan
            @endif

            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Order Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Reference</th>
                                        <td>{{ $record->reference }}</td>
                                    </tr>
                                    <tr>
                                        <th>Order Date</th>
                                        <td>{{ $record->order_date ? date('d-M-Y', strtotime($record->order_date)) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Start Date</th>
                                        <td>{{ $record->start_date ? date('d-M-Y', strtotime($record->start_date)) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>End Date</th>
                                        <td>{{ $record->end_date ? date('d-M-Y', strtotime($record->end_date)) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Branch</th>
                                        <td>{{ $record->branch->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($record->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($record->status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($record->status == 'closed')
                                                <span class="badge badge-secondary">Closed</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <td>{{ $record->createdBy->name ?? '-' }}</td>
                                    </tr>
                                    @if($record->approved_by)
                                        <tr>
                                            <th>Approved By</th>
                                            <td>{{ $record->approvedBy->name ?? '-' }} at {{ $record->approved_at ? date('d-M-Y H:i', strtotime($record->approved_at)) : '' }}</td>
                                        </tr>
                                    @endif
                                    @if($record->notes)
                                        <tr>
                                            <th>Notes</th>
                                            <td>{{ $record->notes }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Order Items (Finish Goods to Produce)</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>BOM Reference</th>
                                            <th>Finish Product</th>
                                            <th>BOM Type</th>
                                            <th class="text-right">Quantity</th>
                                            <th class="text-right">Scheduled</th>
                                            <th class="text-right">Produced</th>
                                            <th class="text-right">Remaining</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($record->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->bom->reference ?? '-' }}</td>
                                                <td>{{ $item->bom->finishProduct->name ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $item->bom->bom_type == 'batch' ? 'primary' : 'success' }}">
                                                        {{ ucfirst($item->bom->bom_type ?? '') }}
                                                    </span>
                                                </td>
                                                <td class="text-right">{{ number_format($item->quantity_to_produce, 4) }}</td>
                                                <td class="text-right">{{ number_format($item->scheduled_qty, 4) }}</td>
                                                <td class="text-right">{{ number_format($item->produced_qty, 4) }}</td>
                                                <td class="text-right">{{ number_format($item->quantity_to_produce - $item->scheduled_qty, 4) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Required Raw Materials</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Source Store</th>
                                            <th class="text-right">Required Qty</th>
                                            <th class="text-right">Unit Cost</th>
                                            <th class="text-right">Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalCost = 0; @endphp
                                        @foreach($record->materials as $index => $material)
                                            @php $totalCost += $material->total_cost; @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $material->product->code ?? '' }} - {{ $material->product->name ?? '-' }}</td>
                                                <td>{{ $material->sourceStore->name ?? '-' }}</td>
                                                <td class="text-right">{{ number_format($material->required_qty, 4) }}</td>
                                                <td class="text-right">{{ number_format($material->unit_cost, 2) }}</td>
                                                <td class="text-right">{{ number_format($material->total_cost, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary">
                                            <th colspan="5" class="text-right">Total Estimated Material Cost:</th>
                                            <th class="text-right">{{ number_format($totalCost, 2) }}</th>
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
@endpush
