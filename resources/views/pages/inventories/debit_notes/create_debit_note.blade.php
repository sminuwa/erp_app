@extends('layouts.backend.app')

@section('title', 'Customer')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@push('css')
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
                        <h4>Debit Note</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.payments') }}">Supplier Payment</a>
                            </li>
                            <li class="breadcrumb-item active">Credit Note</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('suppliers.debit.note') }}">
                <span class="fa fa-list"> Debit Notes</span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6" id="load">
                        @if (isset($purchase) && $purchase != null)
                            @include('pages.inventories.debit_notes.load_expenses')
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <div class="container mt-5" style="max-height: 300px;overflow: scroll;">
                            <div class="card">
                                <div class="card-header">
                                    Previous Invoices
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Invoices</h5>
                                    <input type="text" id="search" class="form-control mt-3" placeholder="Search">
                                    <div id="table-container">
                                        <table class="table" id="example1" style="font-size: 12px;">
                                            <thead>
                                                <tr>
                                                    <th>Reference No</th>
                                                    <th>Customer</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">
                                                @foreach ($purchases as $order)
                                                    <tr>
                                                        <td><a href="javascript:void(0)" class="invoice" onclick="load()"
                                                                data-val="{{ $order->reference }}">{{ $order->reference }}</a>
                                                        </td>
                                                        <td>{{ $order->supplier->name ?? '' }}</td>
                                                        <td>{{ Carbon\Carbon::parse($order->purchase_date)->toFormattedDateString() }}
                                                        </td>
                                                        <td style="text-align: right"><a href="javascript:void(0)"
                                                                onclick="load()" class="invoice"
                                                                data-val="{{ $order->reference }}"><span
                                                                    class=""></span>Select</a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive" id="load">
                            <button class="btn btn-primary btn-sm text-right right" data-toggle="modal" id="add-product-btn"
                                data-target="#add-product-modal"><span class="fa fa-plus-circle"></span> Add Other Invoice
                            </button>


                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <div class="modal fade" id="add-product-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Additional Invoices</h5>
                    <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="create-form" action="{{ route('debit.note.cart.store') }}" method="post">
                        <input type="hidden" name="purchase_id" id="purchase_id" value="{{ $purchase?->id }}" />
                        <input type="hidden" name="reference" id="reference" value="" />
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Account</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control select2-single"
                                        required>
                                        <option value="">Select...</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">
                                                {{ $supplier->code }}-{{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" required></textarea>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="number" class="form-control" name="amount" id="amount"
                                        placeholder="Amount" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right ">
                            <input type="submit" class="btn btn-primary" value="Add Invoice"><span class="fa fa-plus-circle">
                                </span> 
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/backend/plugins/fastclick/fastclick.js') }}"></script>

    <!-- Sweet Alert Js -->
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(function() {
            $("#example1,#store_data").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
            $(document).on('keyup', '#search', function() {
                var searchText = $(this).val();
                $.ajax({
                    url: "{{ route('suppliers.load.order.invoices') }}",
                    method: 'GET',
                    data: {
                        search: searchText
                    },
                    success: function(response) {
                        if (response == null)
                            $('#table-body').html("");
                        $('#table-body').html(response);

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
            var delay = (function() {
                var timer = 0;
                return function(callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();

            // function load() {
            $(document).on('click', '.invoice', function() {
                var reference = $(this).attr('data-val');
                $('#reference').val(reference);
                $('#load').html("<h3>Please wait... while it is loading...</h3>");
                $.ajax({
                    url: "{{ route('suppliers.load.order.cart') }}",
                    method: 'GET',
                    data: {
                        reference: reference
                    },
                    success: function(response) {

                        $('#load').html(response);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            // }


            function formatMoney(n, c, d, t) {
                var c = isNaN(c = Math.abs(c)) ? 2 : c,
                    d = d == undefined ? "." : d,
                    t = t == undefined ? "," : t,
                    s = n < 0 ? "-" : "",
                    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ?
                    d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };
            $(document).on('click', '.delete', function() {
                var id = $(this).attr('data-val');
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
            });
            var delay = (function() {
                var timer = 0;
                return function(callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();
            $(document).on('keyup', '.price', function() {
                id = $(this).attr('data-value');

                delay(function() {

                    $.ajax({
                        url: $('#' + id).attr('action'),
                        type: $('#' + id).attr('method'),
                        header: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: $('#' + id).serialize(),
                        success: function(data) {
                            id = id.substr(1);

                            subtotal = $('#price' + id).val();
                            $('.subtotal' + id).text(formatMoney(subtotal));
                            $('#total').text(formatMoney(data));
                            $('#subtotal').text(formatMoney(data));
                        },
                        error: function(xhr, err) {
                            //$('#total').text(formatMoney(data));
                            //$('#subtotal').text(formatMoney(data));
                        }
                    });

                }, 500);
            });

            $(document).on('click', '#add-product-btn', function(e) {
                
                $('#purchase_id').val($('#purchase').val());
               
            });
            $('body').on('submit', '.create-form', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'post',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('.close-modal').trigger('click')
                    },
                    success: function(response) {
                        $('#load').html(response)
                        //console.log(response)
                    }
                })
            });
        });
    </script>
@endpush
