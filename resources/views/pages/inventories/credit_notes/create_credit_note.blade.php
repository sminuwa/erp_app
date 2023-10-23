@extends('layouts.backend.app')

@section('title', 'Customer')

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
                    <div class="col-sm-6" id="load">
                        @if (isset($order) && $order != null)
                            @include('pages.inventories.credit_notes.load_products')
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
                                                @foreach ($orders as $order)
                                                    <tr>
                                                        <td><a href="javascript:void(0)" class="invoice" onclick="load()"
                                                                data-val="{{ $order->invoice_no }}">{{ $order->invoice_no }}</a>
                                                        </td>
                                                        <td>{{ $order->customer->name }}</td>
                                                        <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}
                                                        </td>
                                                        <td style="text-align: right"><a href="javascript:void(0)"
                                                                onclick="load()" class="invoice"
                                                                data-val="{{ $order->invoice_no }}"><span
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
                            <table id="store_data" class="table table-bordered table-striped text-left"
                                style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th>Store</th>
                                        <th>Code</th>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th>QTY</th>
                                        {{-- <th>Price</th> --}}
                                        <th>Add To Cart</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Store</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th>QTY</th>
                                        {{-- <th>Price</th> --}}
                                        <th>Add To Cart</th>
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($stores as $key => $store)
                                        <tr>
                                            <form action="{{ route('credit.note.cart.store') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $store->id }}">
                                                <input type="hidden" name="order" value="{{ $order->id }}">
                                                <input type="hidden" name="name" value="{{ $store->name }}">
                                                <input type="hidden" name="code" value="{{ $store->code }}">
                                                <input type="hidden" name="store" value="{{ $store->store }}">
                                                <input type="hidden" name="qty" value="1">
                                                <input type="hidden" name="selling_price"
                                                    value="{{ $store->selling_price }}">
                                                <input type="hidden" name="qty_available"
                                                    value="{{ $store->qty_available }}">
                                                <input type="hidden" name="sold_price"
                                                    value="{{ $store->selling_price }}">
                                                <input type="hidden" name="cost_price"
                                                    value="{{ $store->cost_price }}">

                                                <td>{{ ucwords($store->store) }}</td>
                                                <td>{{ $store->code }}</td>
                                                <td>{{ $store->name }}</td>
                                                <td>{{ $store->unit }}</td>
                                                <td align="center">{{ $store->qty_available }}</td>
                                                {{-- <td align="right">
                                                    {{ number_format($store->selling_price, 2) }}
                                                </td> --}}
                                                @if ($store->qty_available > 0 && $store->selling_price > 0)
                                                    <td align="center">
                                                        <button type="submit" class="btn btn-sm btn-success px-2">
                                                            <i class="fa fa-cart-plus" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                @else
                                                    <td align="center">
                                                        <span class="fa fa-crosshairs text text-danger"></span>
                                                    </td>
                                                @endif
                                            </form>
                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

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
            if (parseFloat($('#quantity' + id.substr(1)).val()) > parseFloat($('#quantity' + id.substr(1)).attr(
                    'max-qty'))) {
                $("#valid_qty" + id.substr(1)).html("Selling QTY is more than the available QTY(" + $('#quantity' +
                    id.substr(1)).attr('max-qty') + ")");
                $('#quantity' + id.substr(1)).val($('#quantity' + id.substr(1)).attr('max-qty'));
                return false;
            }
            delay(function() {

                $.ajax({
                    url: $('#' + id).attr('action'),
                    type: $('#' + id).attr('method'),
                    //dataType: 'json',
                    data: $('#' + id).serialize(),
                    success: function(data) {
                        id = id.substr(1);

                        subtotal = $('#price' + id).val() * $('#quantity' + id).val();
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
