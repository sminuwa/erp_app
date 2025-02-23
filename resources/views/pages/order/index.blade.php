@extends('layouts.backend.app')

@section('title', 'Approved Orders')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 offset-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Approved Orders</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Daily Sales
                                </h3>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-sm-2">
                                    @can('invoice.index')
                                        <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-secondary"
                                            style="margin-left: 2px;"><span class="fa fa-list"> </span> List </a>
                                    @endcan
                                    @can('pos.index')
                                        <a href="{{ route('pos.index') }}" class="btn btn-sm btn-secondary"
                                            style="margin-left: 2px;"><span class="fa fa-plus-circle"> </span> New </a>
                                    @endcan
                                </div>
                            </div>
                            <br />
                            @can('pos.index')
                                <div class="row">
                                    <div class="col-md-6">
                                        <form action="{{ route('sales_products.search') }}" method="POST">
                                            @csrf
                                            <div class="input-group">
                                                <input type="search" class="form-control rounded" required
                                                    placeholder="Search by name, phone or invoice number" name="refno"
                                                    aria-label="Search" aria-describedby="search-addon" />
                                                <button type="submit" class="btn btn-outline-primary">search</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endcan
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1"
                                    class="table table-bordered table-striped text-left table-responsive-xl"
                                    data-ordering="true">
                                    <thead>
                                        <tr>
                                            <th>Processed Date</th>
                                            <th>Name</th>
                                            <th>Invoice No</th>
                                            <th>Total</th>
                                            {{-- <th>Amount Paid</th> --}}
                                            <th>Created By</th>
                                            <th>Date Created</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total = 0;
                                            $total_pay = 0;
                                            $total_due = 0;
                                        @endphp
                                        @foreach ($orders as $order)
                                            @php
                                                $total = $total + $order->total;
                                                $total_pay = $total_pay + $order->pay;
                                                $total_due = $total_due + $order->due;
                                            @endphp
                                            <tr class="@if ($order->status == 0) bg-warning @endif">
                                                <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $order->customer->name }}</td>
                                                <td>{{ $order->reference }}</td>
                                                <td align="right">{{ number_format($order->total, 2, '.', ',') }}
                                                </td>
                                                {{-- <td align="right">&#8358;{{ number_format($order->pay, 2, '.', ',') }}</td> --}}
                                                <td>{{ $order->createdBy->name ?? '' }}</td>
                                                <td>{{ Carbon\Carbon::parse($order->created_at)->toFormattedDateString() }}
                                                </td>

                                                <td>
                                                    @if ($order->has_credit_note > 0)
                                                        Reversed
                                                    @elseif($order->status == 1)
                                                        Completed
                                                    @else
                                                        Pending
                                                    @endif
                                                </td>
                                                <td align="center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                            id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            @can('orders.show')
                                                                <a href="{{ route('orders.show', $order->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i> View
                                                                </a>
                                                            @endcan

                                                            @if ($order->status == 1)
                                                                @can('sales_products.verify')
                                                                    <a href="javascript:void(0)" data-toggle="modal"
                                                                        data-target="#order_detail_form{{ $order->id }}"
                                                                        data-val="{{ $order->id }}"
                                                                        class="dropdown-item show">
                                                                        <i class="fa fa-check" aria-hidden="true"></i> Confirm
                                                                    </a>
                                                                @endcan
                                                                @can('waybill.order_print')
                                                                    <a href="{{ route('waybill.order_print', $order->id) }}"
                                                                        target="_BLANK" class="dropdown-item">
                                                                        <i class="fa fa-print" aria-hidden="true"></i> Waybill
                                                                    </a>
                                                                @endcan
                                                                @can('invoice.print')
                                                                    <a href="{{ route('invoice.print', [$order->id, 'A4']) }}"
                                                                        target="_BLANK" class="dropdown-item">
                                                                        <i class="fa fa-print" aria-hidden="true"></i> Print A4
                                                                    </a>
                                                                    <a href="{{ route('invoice.print', [$order->id, 'A5']) }}"
                                                                        target="_BLANK" class="dropdown-item">
                                                                        <i class="fa fa-print" aria-hidden="true"></i> Print A5
                                                                    </a>
                                                                @endcan
                                                                @can('invoice.print-with-vat')
                                                                    <a href="{{ route('invoice.print', $order->id) }}"
                                                                        target="_BLANK" class="dropdown-item">
                                                                        <i class="fa fa-print" aria-hidden="true"></i> Print
                                                                        (VAT)
                                                                    </a>
                                                                @endcan
                                                                @can('pos.order_print')
                                                                    <a href="{{ route('pos.order_print', $order->id) }}"
                                                                        target="_BLANK" class="dropdown-item">
                                                                        <i class="fa fa-print" aria-hidden="true"></i> Print
                                                                        (PoS)
                                                                    </a>
                                                                @endcan
                                                            @endif
                                                            @if ($order->status == 0)
                                                                @can('pos.edit')
                                                                    <a href="{{ route('pos.edit', $order->id) }}"
                                                                        class="dropdown-item">
                                                                        <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                                                    </a>
                                                                @endcan
                                                                @can('invoice.post')
                                                                    <form action="{{ route('invoice.post', $order->id) }}"
                                                                        method="post"
                                                                        onsubmit="return confirm('Are you sure you want to post this invoice?')">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                                            Post
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                                @can('invoice.delete')
                                                                    <form id="delete-form-{{ $order->id }}"
                                                                        action="{{ route('invoice.delete', $order->id) }}"
                                                                        method="post"
                                                                        onsubmit="return confirm('Are you sure you want to close this invoice?')">
                                                                        @csrf
                                                                        <button class="dropdown-item" type="submit">
                                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                                            Delete
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            @endif

                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                            <div class="modal fade" id="order_detail_form{{ $order->id }}"
                                                style="display: none;" aria-hidden="true">
                                                @include('pages.order.modal')
                                            </div>
                                            <div class="modal fade" id="sale_transfer_form{{ $order->id }}"
                                                style="display: none;" aria-hidden="true">
                                                @include('pages.order.transfer_modal')
                                            </div>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Invoice No</th>
                                            <th style="text-align:right">&#8358;{{ number_format($total, 2, '.', ',') }}
                                            </th>
                                            <th style="text-align:right">
                                                Created By
                                            </th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->

                        </div>
                        <!-- /.card -->

                    </div>
                    <!--/.col (left) -->

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div> <!-- Content Wrapper end -->
    <div class="modal fade order_edit" style="display: none;" aria-hidden="true">
        @isset($order)
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Invoice Verification/Stock out Panel: {{ optional($order)->customer->name }} |
                            Invoice: {{ optional($order)->reference }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="display">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endisset
    </div>
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


    <script>
        $(function() {

            $("#example1").DataTable({
                'iDisplayLength': 100
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
            $(".show").on('click', function() {
                order_id = $(this).attr('data-val');
                $.ajax({
                    type: 'get',
                    url: "{{ route('orders.load') }}",
                    data: {
                        order_id: order_id
                    }
                }).done(function(data) {
                    $('.display').html();
                    $('.display').html(data);
                });
            });
            $(".edit").on('click', function() {
                order_id = $(this).attr('data-val');
                $.ajax({
                    type: 'get',
                    url: "{{ route('orders.detail.edit') }}",
                    data: {
                        order_id: order_id
                    }
                }).done(function(data) {
                    $('.display').html();
                    $('.display').html(data);
                    /* $('.btnForm').on('click', function() {
                         id = $(this).attr('data-val');
                         //$('#'+id).submit();
                         qty = $('#qty' + id).val();

                         unit_cost = $('#unit_cost' + id).val();
                         alert(unit_cost)
                         store_product_id = $('#store_product_id' + id).val();
                         var url = '{{ route('orders.update', ':id') }}';
                         url = url.replace(':id', id);
                         alert(qty);
                         $.ajax({
                             type: 'get',
                             url: url,
                             data: {
                                 qty: qty,
                                 new_cost: unit_cost,
                                 store_product_id: store_product_id
                             }
                         }).done(function(data) {
                             alert(data);
                             console.log(data);
                             //$('#unit_cost' + id).val(data['unit_cost']);
                             //$('#qty' + id).val(data['quantity']);
                             //$('#store_product_id' + id).val(data['quantity']);
                         });
                     });*/
                });
            });
        });
    </script>


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
