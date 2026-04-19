@extends('layouts.backend.app')

@section('title', 'Intersite Additional Costs')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Intersite Additional Costs</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Intersite Additional Costs</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @can('intersite.additional_cost.create')
                    <a class="btn btn-primary btn-sm mb-3" href="{{ route('intersite.additional_cost.create') }}">
                        <i class="fa fa-plus"></i> Create Additional Cost
                    </a>
                @endcan

                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped" id="records-table">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Reference</th>
                                    <th>Date</th>
                                    <th>Intersite Ref</th>
                                    <th>Supplier</th>
                                    <th>Cost Mode</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $record->reference }}</td>
                                        <td>{{ $record->date }}</td>
                                        <td>{{ $record->intersiteTransfer->reference ?? 'N/A' }}</td>
                                        <td>{{ $record->supplier->name ?? 'N/A' }}</td>
                                        <td>{{ $record->cost_mode == 'by_amount' ? 'By Amount' : 'By Quantity' }}</td>
                                        <td class="text-right">&#8358;{{ number_format($record->amount, 2) }}</td>
                                        <td>
                                            @if($record->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($record->status == 'posted')
                                                <span class="badge badge-success">Posted</span>
                                            @elseif($record->status == 'reversed')
                                                <span class="badge badge-danger">Reversed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('intersite.additional_cost.show')
                                                <a href="{{ route('intersite.additional_cost.show', $record->id) }}"
                                                    class="btn btn-info btn-sm" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endcan
                                            @if($record->isPending())
                                                @can('intersite.additional_cost.edit')
                                                    <a href="{{ route('intersite.additional_cost.edit', $record->id) }}"
                                                        class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $records->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#records-table').DataTable({
                paging: false,
                info: false,
            });
        });
    </script>
@endpush
