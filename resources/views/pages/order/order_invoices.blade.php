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
                            <li class="breadcrumb-item active">Order Invoices</li>
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
                                    Order List
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <div class="row">
                                    <div class="col-sm-4">
                                        @can('view.daily.sale')
                                            <a href="{{ route('order.invoice.index') }}" class="btn btn-sm btn-secondary"
                                               style="margin-left: 2px;"><span class="fa fa-list"> </span> View Orders </a>
                                        @endcan
                                        @can('make.daily.sale')
                                            <a href="{{ route('order.invoice.index') }}" class="btn btn-sm btn-secondary"
                                               style="margin-left: 2px;"><span class="fa fa-plus-circle"> </span> New Order
                                                Invoice</a>
                                        @endcan
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <form action="{{ route('order.invoice.search') }}" method="POST">
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
                                <table id="example1"
                                    class="table table-bordered table-striped text-left table-responsive-xl"
                                    data-ordering="false">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach ($orders as $order)
                                            @php $total = $total + $order->total; @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $order->reference }}</td>
                                                <td>{{ $order->customer->name }}</td>
                                                <td>
                                                    {!!
                                                        $order->status == 0 ? '<span class="badge badge-warning">Pending</span>':
                                                        ($order->status == 1 ? '<span class="badge badge-success">Close</span>': '<span class="badge badge-success">Completed</span>' )
                                                    !!}
                                                </td>
                                                <td align="right">&#8358;{{ number_format($order->total, 2, '.', ',') }}
                                                </td>
                                                <td align="center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                            id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="{{ route('order.invoice.show', $order->id) }}"
                                                                class="dropdown-item">
                                                                <i class="fa fa-eye" aria-hidden="true"></i> View
                                                            </a>
                                                            <a href="{{ route('order.invoice.print', $order->id) }}"
                                                               target="_BLANK" class="dropdown-item">
                                                                <i class="fa fa-print" aria-hidden="true"></i> Print
                                                            </a>
                                                            @if ($order->status == 0)
                                                                <a href="{{ route('order.invoice.edit', $order->id) }}"
                                                                   class="dropdown-item">
                                                                    <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                                                </a>
                                                                <a href="{{ route('order.invoice.linking', $order->id) }}" title="Linking"
                                                                   class="dropdown-item">
                                                                    <i class="fa fa-link" aria-hidden="true"></i> Create Invoice
                                                                </a>
                                                                <form action="{{ route('order.invoice.close', $order->id) }}" method="post">
                                                                    @csrf
                                                                    <button type="submit"
                                                                       class="dropdown-item">
                                                                        <i class="fa fa-close" aria-hidden="true"></i> Close
                                                                    </button>
                                                                </form>

                                                                <button class="dropdown-item" type="button"
                                                                        onclick="deleteItem({{ $order->id }})">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                                </button>
                                                                <form id="delete-form-{{ $order->id }}"
                                                                      action="{{ route('order.invoice.destroy', $order->id) }}"
                                                                      method="post" style="display:none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>

                                                            @endif

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="order_detail_form{{ $order->id }}"
                                                style="display: none;" aria-hidden="true">
                                                @include('pages.order.order_invoice_modal')
                                            </div>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th style="text-align:right">&#8358;{{ number_format($total, 2, '.', ',') }}
                                            </th>
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
                            Invoice:
                            {{ optional($order)->invoice_no }}</h5>
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

            $("#example1").DataTable();
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
                        order_id: order_id,
                        type: 'order'
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
