@extends('layouts.backend.app')

@section('title', 'Manufacturing Penalties')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Manufacturing Penalties</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item active">Penalties</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.penalties.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.penalties.create') }}">
                    <span class="fa fa-plus-circle"></span> New Penalty
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
                                    <th>Type</th>
                                    <th>Team/Staff</th>
                                    <th>Production Doc</th>
                                    <th class="text-right">Amount Charged</th>
                                    <th class="text-right">Loss Incurred</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->reference }}</td>
                                    <td>{{ date('d M Y', strtotime($record->penalty_date)) }}</td>
                                    <td>{{ ucfirst($record->penalty_type) }}</td>
                                    <td>
                                        @if($record->penalty_type == 'team')
                                            {{ $record->team->name ?? 'N/A' }}
                                        @else
                                            {{ $record->staff->name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>{{ $record->production_reference ?? '-' }}</td>
                                    <td class="text-right">{{ number_format($record->amount_charged, 2) }}</td>
                                    <td class="text-right">{{ number_format($record->total_loss_incurred ?? 0, 2) }}</td>
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
                                        @can('manufacturing.penalties.show')
                                        <a href="{{ route('manufacturing.penalties.show', $record->id) }}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('manufacturing.penalties.delete')
                                        @if($record->isPending())
                                        <form action="{{ route('manufacturing.penalties.destroy', $record->id) }}" method="POST" style="display:inline;">
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
                        <div class="mt-3">
                            {{ $records->links() }}
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
        "order": [[0, "desc"]],
        "paging": false
    });
});
</script>
@endpush
