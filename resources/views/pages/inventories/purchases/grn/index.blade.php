@extends('layouts.backend.app')

@section('title', 'Purchases')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Purchases(GRN)</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Purchases(GRN)</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('purchases.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('purchases.index') }}">
                    <span class="fa fa-list">View Purchases (GRN)</span>
                </a>
            @endcan
            @can('purchases.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('purchases.create') }}">
                    <span class="fa fa-plus-circle">New Purchase (GRN)</span>
                </a>
            @endcan
            <div class="container-fluid">
                @can('purchases.search')
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('purchases.search') }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="search" class="form-control rounded" required
                                        placeholder="Search by Reference number" name="refno" aria-label="Search"
                                        aria-describedby="search-addon" />
                                    <button type="submit" class="btn btn-outline-primary">search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endcan
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-striped" id="record1">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Reference </th>
                                    <th>Purchase Date </th>
                                    <th>Amount (&#8358;) </th>
                                    <th>Status </th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                    <tr class="@if ($record->status == 0) bg-warning @endif">
                                        <td> {{ optional($record->supplier)->code }} -
                                            {{ optional($record->supplier)->name }} </td>
                                        <td> {{ $record->reference }} </td>
                                        <td> {{ $record->purchase_date->toDayDateTimeString() }} </td>
                                        <td style="text-align: right">
                                            {{ number_format($record->totalProductCost()->total, 2) }} </td>
                                        <td> {{ $record->status == 1 ? 'Completed' : 'Pending' }} </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-default dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @can('purchases.show')
                                                        <a class="dropdown-item"
                                                            href="{{ route('purchases.show', $record->id) }}">
                                                            <span class="fa fa-eye"></span> View
                                                        </a>
                                                    @endcan
                                                    @can('purchase.print')
                                                        <a class="dropdown-item"
                                                            href="{{ route('purchase.print', $record->id) }}" title="Print GRN"
                                                            target="_BLANK">
                                                            <span class="fa fa-print"></span> Print
                                                        </a>
                                                    @endcan
                                                    @if ($record->status == 0)
                                                        @can('purchase.post')
                                                            <form action="{{ route('purchase.post', $record->id) }}"
                                                                method="post"
                                                                onsubmit="return confirm('Are you sure you want to post this order?')">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fa fa-check" aria-hidden="true"></i> Post
                                                                </button>
                                                            </form>
                                                        @endcan
                                                        @can('purchases.edit')
                                                            <a class="dropdown-item"
                                                                href="{{ route('purchases.edit', $record->id) }}">
                                                                <span class="fa fa-pencil"></span> Edit
                                                            </a>
                                                        @endcan
                                                        @can('purchases.destroy')
                                                            <form onsubmit="return confirm('Are you sure you want to cancel?')"
                                                                action="{{ route('purchases.destroy', $record->id) }}"
                                                                method="post" style="display: inline">
                                                                {{ csrf_field() }}
                                                                {{ method_field('DELETE') }}
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="text-danger fa fa-remove"></i> Delete
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    @endif
                                                    @if ($record->waybill_no != null)
                                                        @can('purchase.waybill.print')
                                                            <a class="dropdown-item"
                                                                href="{{ route('purchase.waybill.print', $record->id) }}"
                                                                title="Print Invoice" target="_BLANK">
                                                                <span class="fa fa-print"></span> Print (Waybill)
                                                            </a>
                                                        @endcan
                                                    @endif
                                                    @can('purchase.generate.waybill')
                                                        <a href="javascript:void(0)" data-toggle="modal"
                                                            data-target="#customermodal{{ $loop->index + 1 }}"
                                                            class="dropdown-item float-md-right"><i class="fa fa-truck"></i>
                                                            WayBill Fill</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <!--  modal create customer -->
                                    <div class="modal fade" id="customermodal{{ $loop->index + 1 }}" style="display: none;"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Generate Purchase Waybill with Invoice
                                                        ({{ $record->invoice }})
                                                        by {{ $record->supplier->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post"
                                                        action="{{ route('purchase.generate.waybill', $record->id) }}">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label>Way Bill No: </label>
                                                                    <input type="text" class="form-control"
                                                                        name="waybill_no"
                                                                        value="{{ old('waybill_no', $record->invoice) }}"
                                                                        placeholder="WayBill No">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Driver's Name:</label>
                                                                    <input type="text" class="form-control"
                                                                        name="driver_name"
                                                                        value="{{ old('driver_name', $record->driver_name) }}"
                                                                        placeholder="Driver name">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="transporter_phone">Phone No: </label>
                                                                    <input type="text" class="form-control"
                                                                        name="transporter_phone"
                                                                        value="{{ old('transporter_phone', $record->transporter_phone) }}"
                                                                        placeholder="Phone Number">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Warehouse</label>
                                                                    <input type="text" class="form-control"
                                                                        name="warehouse"
                                                                        value="{{ old('warehouse', $record->warehouse) }}"
                                                                        placeholder="Warehouse">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Vehicle Reg No.:</label>
                                                                    <input type="text" class="form-control"
                                                                        name="vehicle_reg_no"
                                                                        value="{{ old('vehicle_reg_no', $record->vehicle_reg_no) }}"
                                                                        placeholder="Vehicle Registration No">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Transporter:</label>
                                                                    <select class="form-control select2-single"
                                                                        name="transporter" id="transporter" required>
                                                                        <option value="">Select...</option>
                                                                        @foreach ($suppliers as $data)
                                                                            <option value="{{ $data->id }}"
                                                                                {{ $record->transporter == $data->id ? 'selected' : '' }}>
                                                                                {{ $data->code }}-{{ $data->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="modal" value="modal" />
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-dark"
                                                                data-dismiss="modal"><i class="fa fa-times"></i>
                                                                Close
                                                            </button>
                                                            <button type="submit" class="btn btn-info px-3"><i
                                                                    class="icon-trash"></i>
                                                                Generate
                                                            </button>
                                                        </div>
                                                        @method('post')
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- End modal delete -->
                                @endforeach
                            </tbody>
                        </table>

                        {{--                        @include('tables.purchase') --}}
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
{{--    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>--}}
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable({
                'iDisplayLength': 100
            });
            $('#record2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
@endpush
