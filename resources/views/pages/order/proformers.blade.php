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
                            <li class="breadcrumb-item active">Proformers</li>
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
                                    Proformers
                                </h3>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    @can('proforma.index')
                                        <a href="{{ route('proforma.index') }}" class="btn btn-sm btn-secondary"
                                            style="margin-left: 2px;"><span class="fa fa-plus-circle"> </span> New Proformer</a>
                                    @endcan
                                </div>
                            </div>
                            @can('proformer.search')
                                <div class="row">
                                    <div class="col-md-6">
                                        <form action="{{ route('proformer.search') }}" method="POST">
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
                                    data-ordering="false">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Description</th>
                                            <th>Name</th>
                                            <th>Total</th>
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
                                            <tr>
                                                <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $order->reference }}</td>
                                                <td>{{ $order->description }}</td>
                                                <td>{{ $order->customer->name }}</td>
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
                                                            @can('proformer.show')
                                                                <a href="{{ route('proformer.show', $order->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i> View
                                                                </a>
                                                            @endcan
                                                            @can('proformer.edit')
                                                                <a href="{{ route('proformer.edit', $order->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                                                </a>
                                                            @endcan
                                                            @can('proformer.print')
                                                                <a href="{{ route('proformer.print', $order->id) }}"
                                                                    target="_BLANK" class="dropdown-item">
                                                                    <i class="fa fa-print" aria-hidden="true"></i> Print
                                                                </a>
                                                            @endcan
                                                            @can('proformer.delete')
                                                                <form action="{{ route('proformer.delete', $order->id) }}"
                                                                    method="post"
                                                                    onsubmit="return confirm('Are you sure want to delete this proforma invoice?')">
                                                                    @csrf
                                                                    @method('POST')
                                                                    <button class="dropdown-item" type="submit">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="order_detail_form{{ $order->id }}"
                                                style="display: none;" aria-hidden="true">
                                                @include('pages.order.proformer_modal')
                                            </div>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Name</th>
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
    <div class="modal fade" id="customer_ledgerform" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Ledger</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="get" action="{{ route('ajax.general.customer.ledger') }}" id="ledger_form"
                        target="_BLANK">
                        @csrf
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="text" class="form-control datepicker" name="from_date" id="from_date"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="text" class="form-control datepicker" name="to_date" id="to_date"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            &nbsp;&nbsp;
                            <label for="customer_id">Customer</label>
                            <select class="form-control select2-single" name="customer_id" id="customer_id" required>
                                {{-- <option value="all">All</option> --}}
                                <option value="">Select...</option>
                                @foreach (App\Models\Customer::where('type', 'credit')->get() as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}-{{ $data->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="print" value="print" />
                        <input type="hidden" name="modal" value="modal" />
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                                Close
                            </button>
                            <button type="submit" class="btn btn-info px-3"><i class="icon-trash"></i> Generate
                            </button>
                        </div>
                        @method('post')
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                        order_id: order_id,
                        type: 'proformer'
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
