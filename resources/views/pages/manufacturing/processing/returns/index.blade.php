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
                            <li class="breadcrumb-item active">Returns</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.returns.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.returns.create') }}">
                    <span class="fa fa-plus-circle"></span> New Return
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
                                    <th>Return Type</th>
                                    <th>Manufacturing Ref</th>
                                    <th>Return Qty</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->reference }}</td>
                                    <td>{{ date('d M Y', strtotime($record->return_date)) }}</td>
                                    <td>{{ ucfirst($record->return_type) }}</td>
                                    <td>
                                        @if($record->single_manufacturing_id)
                                            {{ $record->singleManufacturing->reference ?? 'N/A' }}
                                        @elseif($record->batch_production_id)
                                            {{ $record->batchProduction->batch_number ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ number_format($record->return_qty, 4) }}</td>
                                    <td>{{ Str::limit($record->reason, 30) }}</td>
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
                                        <a href="{{ route('manufacturing.returns.show', $record->id) }}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('manufacturing.returns.delete')
                                        @if($record->isPending())
                                        <form action="{{ route('manufacturing.returns.destroy', $record->id) }}" method="POST" style="display:inline;">
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
