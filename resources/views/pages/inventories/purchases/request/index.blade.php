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
                        <h4>Purchase (Requests)</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Purchase (Requests)</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a class="btn btn-secondary btn-sm" href="{{ route('purchases.request.create') }}">
                    <span class="fa fa-plus-circle"></span> New
                </a>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Purchase (Requests)</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form action="{{ route('purchases.request.search') }}" method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <input type="search" class="form-control rounded" required placeholder="Search by Reference number"
                                               name="refno" aria-label="Search" aria-describedby="search-addon" />
                                        <button type="submit" class="btn btn-outline-primary">search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-striped" id="record1">
                                    <thead>
                                    <tr>
                                        <th>Request No </th>
                                        <th>Supplier</th>
                                        <th>Reference </th>
                                        <th>Request Date </th>
                                        <th>Amount (&#8358;) </th>
                                        <th>Status </th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($records as $record)
                                        <tr class="@if ($record->status == 0) bg-warning @endif">
                                            <td> {{ $record->reference }} </td>
                                            <td> {{ optional($record->supplier)->code }} - {{ optional($record->supplier)->name }} </td>
                                            <td> {{ $record->invoice }} </td>
                                            <td> {{ $record->purchase_date->toDayDateTimeString() }} </td>
                                            <td style="text-align: right"> {{ number_format($record->totalProductCost()->total, 2) }} </td>
                                            <td> {{ $record->status == 0 ? 'Open' : ($record->status == 1 ? 'Completed' : 'Closed') }} </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('purchases.request.show', $record->id) }}">
                                                            <span class="fa fa-eye"> View</span>
                                                        </a>
                                                        @if ($record->status == 0)
                                                            <form action="{{ route('purchase.request.close', $record->id) }}" method="post"
                                                                  onsubmit="return confirm('Are you sure you want to close this order?')">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fa fa-close" aria-hidden="true"></i> Close
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('purchase.request.link', $record->id) }}" method="post"
                                                                  onsubmit="return confirm('Are you sure you want to post this order?')">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fa fa-link" aria-hidden="true"></i> Link to GRN
                                                                </button>
                                                            </form>

                                                            @can('edit.item.purchase')
                                                                <a class="dropdown-item" href="{{ route('purchases.request.edit', $record->id) }}">
                                                                    <span class="fa fa-pencil"> Edit</span>
                                                                </a>
                                                            @endcan
                                                        @endif
                                                        <a class="dropdown-item" href="{{ route('purchase.request.print', $record->id) }}"
                                                           title="Print Invoice" target="_BLANK">
                                                            <span class="fa fa-print"> Print</span>
                                                        </a>
                                                        @can('delete.item.purchase')
                                                            <form onsubmit="return confirm('Are you sure you want to cancel?')"
                                                                  action="{{ route('purchases.request.destroy', $record->id) }}" method="post"
                                                                  style="display: inline">
                                                                {{ csrf_field() }}
                                                                {{ method_field('DELETE') }}
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fa fa-remove"> Delete</i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                        {{-- @if ($record->waybill_no != null)
                                                            <a class="dropdown-item"
                                                                href="{{ route('purchase.request.waybill.print', $record->id) }}"
                                                                title="Print Invoice" target="_BLANK">
                                                                <span class="ion-printer"> Print Waybill</span>
                                                            </a>
                                                        @endif
                                                        <a href="javascript:void(0)" data-toggle="modal"
                                                            data-target="#customermodal{{ $loop->index + 1 }}"
                                                            class="dropdown-item">WayBill</a> --}}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <!--  modal create customer -->
                                        <div class="modal fade" id="customermodal{{ $loop->index + 1 }}" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Generate Purchase Waybill with Invoice
                                                            ({{ $record->invoice }})
                                                            by {{ $record->supplier->name }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post"
                                                              action="{{ route('purchase.request.generate.waybill', $record->id) }}">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <div class="form-group">
                                                                        <label>Way Bill No: </label>
                                                                        <input type="text" class="form-control" name="waybill_no"
                                                                               value="{{ old('waybill_no', $record->invoice) }}"
                                                                               placeholder="WayBill No">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Driver's Name:</label>
                                                                        <input type="text" class="form-control" name="driver_name"
                                                                               value="{{ old('driver_name', $record->driver_name) }}"
                                                                               placeholder="Driver name">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Location ID: </label>
                                                                        <input type="text" class="form-control" name="location_id"
                                                                               value="{{ old('location_id', $record->location_id) }}"
                                                                               placeholder="Enter Address">
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>Warehouse</label>
                                                                        <input type="text" class="form-control" name="warehouse"
                                                                               value="{{ old('warehouse', $record->warehouse) }}"
                                                                               placeholder="Warehouse">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Vehicle Reg No.:</label>
                                                                        <input type="text" class="form-control" name="vehicle_reg_no"
                                                                               value="{{ old('vehicle_reg_no', $record->vehicle_reg_no) }}"
                                                                               placeholder="Vehicle Registration No">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Transporter:</label>
                                                                        <input type="text" class="form-control" name="transporter"
                                                                               value="{{ old('transporter', $record->transporter) }}"
                                                                               placeholder="Transporter name">
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="modal" value="modal" />
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-dark" data-dismiss="modal"><i
                                                                        class="fa fa-times"></i>
                                                                    Close
                                                                </button>
                                                                <button type="submit" class="btn btn-info px-3"><i class="icon-trash"></i>
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

                                {{--                        @include('tables.purchase_request')--}}
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $(function() {
             $("#record1").DataTable({
                'iDisplayLength':100
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
