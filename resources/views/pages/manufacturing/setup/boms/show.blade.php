@extends('layouts.backend.app')

@section('title', 'View Bill of Materials')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Bill of Materials Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.boms.index') }}">BOM</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.boms.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.boms.create') }}">
                    <span class="fa fa-plus-circle"></span>
                </a>
            @endcan
            <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.boms.index') }}">
                <span class="fa fa-list"></span>
            </a>
            @can('manufacturing.boms.edit')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.boms.edit', $record->id) }}">
                    <span class="fa fa-pencil"></span>
                </a>
            @endcan
            <a class="btn btn-info btn-sm" href="{{ route('manufacturing.boms.print', $record->id) }}" target="_blank">
                <span class="fa fa-print"></span> Print
            </a>

            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">BOM Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Reference</th>
                                        <td>{{ $record->reference }}</td>
                                    </tr>
                                    <tr>
                                        <th>Type</th>
                                        <td>
                                            <span class="badge badge-{{ $record->bom_type == 'batch' ? 'primary' : 'success' }}">
                                                {{ ucfirst($record->bom_type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $record->description }}</td>
                                    </tr>
                                    <tr>
                                        <th>Approval Document No</th>
                                        <td>{{ $record->approval_document_no ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td>{{ $record->category->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Finish Product</th>
                                        <td>{{ $record->finishProduct->code ?? '' }} - {{ $record->finishProduct->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Output Store</th>
                                        <td>{{ $record->outputStore->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Actual Output</th>
                                        <td>{{ number_format($record->actual_output, 4) }}</td>
                                    </tr>
                                    @if($record->bom_type == 'batch')
                                        <tr>
                                            <th>Main Raw Material</th>
                                            <td>{{ $record->mainRawMaterial->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Accepted Excess</th>
                                            <td>{{ number_format($record->accepted_excess, 4) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Accepted Shortage</th>
                                            <td>{{ number_format($record->accepted_shortage, 4) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Branch</th>
                                        <td>{{ $record->branch->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $record->status == 1 ? 'Active' : 'Inactive' }}</td>
                                    </tr>
                                    @if($record->margin_per_piece)
                                    <tr>
                                        <th>Margin per Piece</th>
                                        <td>{{ number_format($record->margin_per_piece, 4) }}</td>
                                    </tr>
                                    @endif
                                    @if($record->marginGlAccount)
                                    <tr>
                                        <th>Margin GL Account</th>
                                        <td>{{ $record->marginGlAccount->number }} - {{ $record->marginGlAccount->name }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Cost Summary</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="50%">Labor Cost</th>
                                        <td class="text-right">{{ number_format($record->labor_cost, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Power Cost</th>
                                        <td class="text-right">{{ number_format($record->power_cost, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Other Cost</th>
                                        <td class="text-right">{{ number_format($record->other_cost, 2) }}</td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th>Total Other Cost</th>
                                        <td class="text-right"><strong>{{ number_format($record->total_other_cost, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Total Material Cost</th>
                                        <td class="text-right">{{ number_format($record->total_material_cost, 2) }}</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <th>Total Cost</th>
                                        <td class="text-right"><strong>{{ number_format($record->total_material_cost + $record->total_other_cost, 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <th>Cost Per Unit</th>
                                        <td class="text-right"><strong>{{ number_format($record->total_cost_per_unit, 2) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Materials</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Code</th>
                                            <th>Product Name</th>
                                            <th>Source Store</th>
                                            <th class="text-right">Quantity</th>
                                            <th class="text-right">Unit Cost</th>
                                            <th class="text-right">Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($record->materials as $index => $material)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $material->product->code ?? '-' }}</td>
                                                <td>{{ $material->product->name ?? '-' }}</td>
                                                <td>{{ $material->sourceStore->name ?? '-' }}</td>
                                                <td class="text-right">{{ number_format($material->quantity, 4) }}</td>
                                                <td class="text-right">{{ number_format($material->unit_cost, 2) }}</td>
                                                <td class="text-right">{{ number_format($material->total_cost, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No materials found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary">
                                            <th colspan="6" class="text-right">Total Material Cost:</th>
                                            <th class="text-right">{{ number_format($record->total_material_cost, 2) }}</th>
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
