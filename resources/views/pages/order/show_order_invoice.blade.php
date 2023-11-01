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
                        <h1>Order Invoice Details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Order Invoice Details</li>
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
                        <div class="row no-print">
                            <div class="col-md-12 text-right">

                                <a href="javascript:history.back()" class="btn btn-primary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                                <a href="{{ route('order.invoice.index') }}" class="btn btn-secondary btn-sm ">
                                    <i class="fa fa-plus-circle" aria-hidden="true"> New Order</i>
                                </a>

                                <a href="{{ route('order.invoice.print', $order->id) }}"
                                   target="_BLANK" class="btn btn-dark btn-sm ">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print
                                </a>
                                @if ($order->status == 0)
                                    <a href="{{ route('order.invoice.edit', $order->id) }}"
                                       class="btn btn-primary btn-sm ">
                                        <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                    </a>
                                    <a href="{{ route('order.invoice.linking', $order->id) }}" title="Linking"
                                       class="btn btn-success btn-sm ">
                                        <i class="fa fa-link" aria-hidden="true"></i> Create Invoice
                                    </a>
                                    <form action="{{ route('order.invoice.close', $order->id) }}" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to close this order?')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-warning btn-sm">
                                            <i class="fa fa-close" aria-hidden="true"></i> Close
                                        </button>
                                    </form>
                                    <form action="{{ route('order.invoice.destroy', $order->id) }}" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </div>
                        <div class="invoice p-3 mt-3">
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
                                    <b>Reference No: {{ $order->reference }}</b><br><br>
                                    <b>Order Status:</b>
                                    {!!
                                        $order->status == 0 ? '<span class="badge badge-warning">Pending</span>':
                                        ($order->status == 1 ? '<span class="badge badge-success">Close</span>': '<span class="badge badge-success">Completed</span>' )
                                    !!}
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
                                                    <td>{{ $order_detail->storeProduct->product->name ?? "" }}</td>
                                                    <td align="center">{{ $order_detail->quantity }}</td>
                                                    <td align="right">{{ number_format($order_detail->sold_price, 2) }}
                                                    </td>
                                                    <td align="right">
                                                        {{ number_format($order_detail->sold_price * $order_detail->quantity, 2) }}
                                                    </td>
                                                </tr>
                                                @php $total += ($order_detail->sold_price * $order_detail->quantity);  @endphp
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
