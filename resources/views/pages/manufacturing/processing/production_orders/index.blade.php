@extends('layouts.backend.app')

@section('title', 'Production Orders')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Production Orders</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item active">Production Orders</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.production_orders.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.production_orders.create') }}">
                    <span class="fa fa-plus-circle"></span> Create New Order
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive mt-3">
                        <table class="table table-bordered table-striped" id="record1">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Order Date</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Branch</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                    <tr>
                                        <td>{{ $record->reference }}</td>
                                        <td>{{ $record->order_date ? date('d-M-Y', strtotime($record->order_date)) : '-' }}</td>
                                        <td>{{ $record->start_date ? date('d-M-Y', strtotime($record->start_date)) : '-' }}</td>
                                        <td>{{ $record->end_date ? date('d-M-Y', strtotime($record->end_date)) : '-' }}</td>
                                        <td>{{ $record->branch->name ?? '-' }}</td>
                                        <td>
                                            @if($record->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($record->status == 'confirmed')
                                                <span class="badge badge-info">Confirmed</span>
                                            @elseif($record->status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($record->status == 'closed')
                                                <span class="badge badge-secondary">Closed</span>
                                            @endif
                                        </td>
                                        <td>{{ $record->createdBy->name ?? '-' }}</td>
                                        <td>
                                            @can('manufacturing.production_orders.show')
                                                <a class="btn btn-info btn-sm" href="{{ route('manufacturing.production_orders.show', $record->id) }}">
                                                    <span class="fa fa-eye"></span>
                                                </a>
                                            @endcan
                                            @if($record->isPending())
                                                @can('manufacturing.production_orders.delete')
                                                    <form onsubmit="return confirm('Are you sure you want to delete this order?')"
                                                        action="{{ route('manufacturing.production_orders.destroy', $record->id) }}" method="post" style="display: inline">
                                                        {{ csrf_field() }}
                                                        {{ method_field('DELETE') }}
                                                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                                            <i class="text-danger fa fa-remove"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
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
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable({
                'iDisplayLength': 100,
                'order': [[0, 'desc']]
            });
        });
    </script>
@endpush
