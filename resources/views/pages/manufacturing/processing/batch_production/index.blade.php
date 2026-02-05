@extends('layouts.backend.app')

@section('title', 'Batch Production')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Batch Production</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item active">Batch Production</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.batch_production.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.batch_production.create') }}">
                    <span class="fa fa-plus-circle"></span> New Batch Production
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive mt-3">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Batch Number</th>
                                    <th>Date</th>
                                    <th>BOM</th>
                                    <th>Finish Product</th>
                                    <th>Quantity</th>
                                    <th>WIP Value</th>
                                    <th>Converted</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->batch_number }}</td>
                                    <td>{{ date('d M Y', strtotime($record->production_date)) }}</td>
                                    <td>{{ $record->bom->reference ?? 'N/A' }}</td>
                                    <td>{{ $record->bom->finishProduct->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($record->quantity, 4) }}</td>
                                    <td>{{ number_format($record->wip_value, 2) }}</td>
                                    <td>{{ number_format($record->converted_qty, 4) }}</td>
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
                                        @can('manufacturing.batch_production.show')
                                        <a href="{{ route('manufacturing.batch_production.show', $record->id) }}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('manufacturing.batch_production.print')
                                        <a href="{{ route('manufacturing.batch_production.print', $record->id) }}" class="btn btn-secondary btn-xs" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        @endcan
                                        @can('manufacturing.batch_production.delete')
                                        @if($record->isPending())
                                        <form action="{{ route('manufacturing.batch_production.destroy', $record->id) }}" method="POST" style="display:inline;">
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
