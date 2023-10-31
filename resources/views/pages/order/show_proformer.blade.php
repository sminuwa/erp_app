@extends('layouts.backend.app')

@section('title', 'Order')

@push('css')
    <style>
        .modal-lg {
            max-width: 50% !important;
        }
    </style>
@endpush

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Proformer Details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Proformer Details</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Main content -->
                        <div class="invoice p-3 mb-3">
                            <!-- title row -->

                            <!-- info row -->
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    From
                                    <address>
                                        <strong>{{ config('app.name') }}</strong><br>
                                        {{ $company?->address }}<br>
                                        {{ $company?->city }} , {{ $company?->country }}<br>
                                        Phone:
                                        {{ $company?->mobile }}
                                        {{ $company?->phone !== null ? ', 0' . $company?->phone : '' }}
                                        <br>
                                        Email: {{ $company?->email }}
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    To
                                    <address>
                                        <strong>{{ $order->customer->name }}</strong><br>
                                        {{ $order->customer->address }}<br>
                                        {{ $order->customer->city }}<br>
                                        Phone: {{ $order->customer->phone }}<br>
                                        Email: {{ $order->customer->email }}
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    <b>Invoice
                                        No: {{ $order->invoice_no }}</b><br><br>
                                    </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-bordered text-left">
                                        <thead>
                                            <tr>
                                                <th>S.N</th>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Unit Cost</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total = 0;
                                                $total_discount = 0;
                                            @endphp
                                            @foreach ($order_details as $order_detail)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $order_detail->storeProduct->product->name }}</td>
                                                    <td align="center">{{ $order_detail->quantity }}</td>
                                                    <td align="right">{{ number_format($order_detail->selling_price, 2) }}
                                                    </td>
                                                    <td align="right">
                                                        {{ number_format($order_detail->selling_price * $order_detail->quantity, 2) }}
                                                    </td>
                                                </tr>
                                                @php $total += ($order_detail->selling_price * $order_detail->quantity);  @endphp
                                            @endforeach
                                            <tr>
                                                <th colspan="4" align="right">Total</th>
                                                <th style="text-align: right">{{ number_format($total, 2, '.', ',') }}</th>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->


                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <!-- this row will not appear when printing -->
                        <div class="row no-print">
                            <div class="col-12">

                                <a href="{{ route('proformer.list') }}" class="btn btn-primary btn-sm float-right"
                                    style="margin-right: 5px;">
                                    <i class="fa fa-list"></i> View Sales
                                </a>
                                &nbsp;
                                <a href="{{ route('proformer.index') }}" class="btn btn-secondary btn-sm float-right">
                                    <i class="fa fa-plus-circle" aria-hidden="true"> New Sales</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
    </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->





@endsection



@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/backend/plugins/fastclick/fastclick.js') }}"></script>

    <!-- Sweet Alert Js -->
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        function deleteItem(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
            })

            swalWithBootstrapButtons({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('delete-form-' + id).submit();
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons(
                        'Cancelled',
                        'Your data is safe :)',
                        'error'
                    )
                }
            })
        }
    </script>
@endpush
