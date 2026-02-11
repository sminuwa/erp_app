@extends('layouts.backend.app')

@section('title', 'Batch Conversions')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Batch Conversions</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item active">Batch Conversions</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.batch_conversion.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.batch_conversion.create') }}">
                    <span class="fa fa-plus-circle"></span> New Conversion
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive mt-3">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Date</th>
                                    <th>Batch Number</th>
                                    <th>Finish Product</th>
                                    <th>Output Qty</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->reference }}</td>
                                    <td>{{ date('d M Y', strtotime($record->conversion_date)) }}</td>
                                    <td>{{ $record->batchProduction->batch_number ?? 'N/A' }}</td>
                                    <td>{{ $record->batchProduction->bom->finishProduct->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($record->produced_qty, 4) }}</td>
                                    <td>{{ number_format($record->total_cost, 2) }}</td>
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
                                        @can('manufacturing.batch_conversion.show')
                                        <a href="{{ route('manufacturing.batch_conversion.show', $record->id) }}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('manufacturing.batch_conversion.delete')
                                        @if($record->isPending())
                                        <form action="{{ route('manufacturing.batch_conversion.destroy', $record->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')">
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
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[0, "desc"]]
    });
});
</script>
@endpush
