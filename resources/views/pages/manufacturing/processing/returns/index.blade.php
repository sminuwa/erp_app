@extends('layouts.backend.app')

@section('title', 'Manufacturing Returns')

@push('css')
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manufacturing Returns</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Returns</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @can('manufacturing.returns.create')
            <a class="btn btn-primary btn-sm mb-3" href="{{ route('manufacturing.returns.create') }}">
                <span class="fa fa-plus-circle"></span> New Return
            </a>
            @endcan

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manufacturing Returns List</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Production Type</th>
                                <th>Production Ref</th>
                                <th>Return Qty</th>
                                <th>Total Cost</th>
                                <th>Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                            <tr>
                                <td>{{ $record->reference }}</td>
                                <td>{{ date('d M Y', strtotime($record->return_date)) }}</td>
                                <td>
                                    @if($record->production_type == 'single_product')
                                        <span class="badge badge-info">Single Product</span>
                                    @elseif($record->production_type == 'batch_conversion')
                                        <span class="badge badge-primary">Batch Conversion</span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $production = $record->getProduction();
                                    @endphp
                                    {{ $production->reference ?? 'N/A' }}
                                </td>
                                <td>{{ number_format($record->return_qty, 4) }}</td>
                                <td>{{ number_format($record->total_cost_returned, 2) }}</td>
                                <td>
                                    @if($record->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($record->status == 'posted')
                                        <span class="badge badge-success">Posted</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @can('manufacturing.returns.show')
                                    <a href="{{ route('manufacturing.returns.show', $record->id) }}" class="btn btn-info btn-xs" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('manufacturing.returns.delete')
                                    @if($record->isPending())
                                    <form action="{{ route('manufacturing.returns.destroy', $record->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this return?')" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $records->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[0, "desc"]],
        "paging": false,
        "info": false
    });
});
</script>
@endpush
