@extends('layouts.backend.app')

@section('title', 'Materials Requisitions')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Materials Requisitions</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item active">Materials Requisitions</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.requisitions.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.requisitions.create') }}">
                    <span class="fa fa-plus-circle"></span> New Requisition
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
                                    <th>Source</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->reference }}</td>
                                    <td>{{ date('d M Y', strtotime($record->requisition_date)) }}</td>
                                    <td>
                                        @if($record->workOrder)
                                            WO: {{ $record->workOrder->reference }}
                                        @elseif($record->schedule)
                                            {{ $record->schedule->reference }}
                                        @elseif($record->bom)
                                            BOM: {{ $record->bom->reference }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>{{ $record->createdBy->name ?? 'N/A' }}</td>
                                    <td>
                                        @can('manufacturing.requisitions.show')
                                        <a href="{{ route('manufacturing.requisitions.show', $record->id) }}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('manufacturing.requisitions.delete')
                                        @if($record->isPending())
                                        <form action="{{ route('manufacturing.requisitions.destroy', $record->id) }}" method="POST" style="display:inline;">
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
