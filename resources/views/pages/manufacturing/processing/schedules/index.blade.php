@extends('layouts.backend.app')

@section('title', 'Daily Manufacturing Schedules')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Daily Manufacturing Schedules</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Schedules</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daily Manufacturing Schedules</h3>
                    <div class="card-tools">
                        @can('manufacturing.schedules.create')
                        <a href="{{ route('manufacturing.schedules.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> New Schedule
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Schedule Date</th>
                                <th>Production Order</th>
                                <th>Team</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                            <tr>
                                <td>{{ $record->reference }}</td>
                                <td>{{ date('d M Y', strtotime($record->schedule_date)) }}</td>
                                <td>{{ $record->productionOrder->reference ?? 'N/A' }}</td>
                                <td>{{ $record->team->name ?? 'N/A' }}</td>
                                <td>
                                    @if($record->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($record->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($record->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $record->createdBy->name ?? 'N/A' }}</td>
                                <td>
                                    @can('manufacturing.schedules.show')
                                    <a href="{{ route('manufacturing.schedules.show', $record->id) }}" class="btn btn-info btn-xs">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('manufacturing.schedules.edit')
                                    @if($record->isPending())
                                    <a href="{{ route('manufacturing.schedules.edit', $record->id) }}" class="btn btn-warning btn-xs">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    @endif
                                    @endcan
                                    @can('manufacturing.schedules.delete')
                                    @if($record->isPending())
                                    <form action="{{ route('manufacturing.schedules.destroy', $record->id) }}" method="POST" style="display:inline;">
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
