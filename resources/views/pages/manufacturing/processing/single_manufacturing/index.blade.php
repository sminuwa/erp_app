@extends('layouts.backend.app')

@section('title', 'Single Product Manufacturing')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Single Product Manufacturing</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item active">Single Product Manufacturing</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.single_manufacturing.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.single_manufacturing.create') }}">
                    <span class="fa fa-plus-circle"></span> New Manufacturing
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
                                    <th>BOM</th>
                                    <th>Finish Product</th>
                                    <th>Quantity</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->reference }}</td>
                                    <td>{{ date('d M Y', strtotime($record->manufacturing_date)) }}</td>
                                    <td>{{ $record->bom->reference ?? 'N/A' }}</td>
                                    <td>{{ $record->bom->finishProduct->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($record->quantity, 4) }}</td>
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
                                        @can('manufacturing.single_manufacturing.show')
                                        <a href="{{ route('manufacturing.single_manufacturing.show', $record->id) }}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('manufacturing.single_manufacturing.print')
                                        <a href="{{ route('manufacturing.single_manufacturing.print', $record->id) }}" class="btn btn-secondary btn-xs" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        @endcan
                                        @can('manufacturing.single_manufacturing.delete')
                                        @if($record->isPending())
                                        <form action="{{ route('manufacturing.single_manufacturing.destroy', $record->id) }}" method="POST" style="display:inline;">
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
