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
                        <h4>Credit Note</h4>
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
            <a class="btn btn-secondary btn-sm" href="{{ route('customers.credit.note') }}">
                <span class="fa fa-list"> Credit Notes</span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Credit Notes
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        @hasanyrole('Super-admin|Admin')
                                        <div class="form-group">
                                            <label for="order_date">Date</label>
                                            <input type="text" name="order_date" class="form-control datepicker"
                                                   value="{{ $order ? $order->order_date : date('Y-m-d') }}" />
                                        </div>
                                        @else
                                            <input type="hidden" name="order_date" class="form-control datepicker"
                                                   value="{{ date('Y-m-d') }}" />
                                            @endhasanyrole
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Customer Type</label>
                                            <select name="account_type" id="account_type" class="form-control" required>
                                                <option value="" disabled selected>Select...</option>
                                                <option value="Retail">Retail</option>
                                                <option value="Wholesale">WholeSale</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <div class="form-group">
                                                <select name="customer_id" id="customer_record" class="form-control customer_id "></select>
                                                <div class="form-group">
                                                        <span class="text text-danger ion-android-alert" id="credit_balance"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Invoices</h3>
                            </div>
                            <div class="card-body">
                                <input type="text" id="search" class="form-control" placeholder="Search">
                                <table class="table" id="" style="font-size: 12px;">
                                    <thead>
                                    <tr>
                                        <th>Reference No</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody id="table-body" class="customer-orders">
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>
                                                <a href="javascript:void(0)" class="invoice" onclick="load()"
                                                   data-val="{{ $order->invoice_no }}">{{ $order->invoice_no }}</a>
                                            </td>
                                            <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-8" id="load">
                        @if (isset($order) && $order != null)
                            @include('pages.inventories.credit_notes.load_products')
                        @endif
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
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

            $('#account_type').on("change", function() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customers') }}",
                    data: {
                        type: $(this).val()
                    }
                }).done(function(data) {
                    $("#customer_record").html(data);
                });
            });

            $('.customer_id').on('change',function() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customer-orders') }}",
                    data: {
                        customer_id: $(this).val()
                    }
                }).done(function(data) {
                    console.log(data)
                    $(".customer-orders").html(data);
                });
            });

            $(document).on('keyup', '#search', function() {
                var searchText = $(this).val();
                $.ajax({
                    url: "{{ route('load.order.invoices') }}",
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
                var invoice_no = $(this).attr('data-val');

                $('#load').html("<h3>Please wait... while it is loading...</h3>");
                $.ajax({
                    url: "{{ route('load.order.cart') }}",
                    method: 'GET',
                    data: {
                        invoice_no: invoice_no
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
            $(document).on('keyup', '.quantity,.price', function() {
                id = $(this).attr('data-value');
                $("#valid_qty" + id.substr(1)).html("");
                if (parseFloat($('#quantity' + id.substr(1)).val()) > parseFloat($('#quantity' + id.substr(
                        1)).attr(
                        'max-qty'))) {
                    $("#valid_qty" + id.substr(1)).html("Selling QTY is more than the available QTY(" + $(
                        '#quantity' +
                        id.substr(1)).attr('max-qty') + ")");
                    $('#quantity' + id.substr(1)).val($('#quantity' + id.substr(1)).attr('max-qty'));
                    return false;
                }
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

                            subtotal = $('#price' + id).val() * $('#quantity' + id)
                                .val();
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
        });
    </script>
@endpush
