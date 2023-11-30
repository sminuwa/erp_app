@extends('layouts.backend.app')

@section('title', 'Pos')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')
    <input name="cart_page_type" type="hidden" value="proforma">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 offset-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">PoS</a></li>
                            <li class="breadcrumb-item active">Proforma Invoice</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a href="{{ route('proformer.list') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                        class="fa fa-list"> </span> Proforma List</a>
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <div class="card">
                            <form
                                action="{{ isset($order) ? route('proformer.update', $order->id) : route('proformer.create') }}"
                                method="post">
                                @csrf
                                @isset($order)
                                    @method('PUT')
                                @endisset
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Proforma Details {{ isset($order) ? ": Edit Mode $order->reference" : '' }}
                                    </h3>

                                </div>
                                <div class="card-body">

                                    <input type="hidden" name="order_date" class="form-control datepicker"
                                        value="{{ date('Y-m-d') }}" />

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Customer Type</label>
                                                <select name="account_type" id="account_type" class="form-control" required>
                                                    <option value="" disabled selected>Select...</option>
                                                    <option value="Retail"
                                                        {{ (isset($order) && $order->customer->type == 'Retail') || (session()->has('customer') && session('customer')->type == 'Retail') ? 'selected' : '' }}>
                                                        Retail</option>
                                                    <option value="Wholesale"
                                                        {{ (isset($order) && $order->customer->type == 'Wholesale') || (session()->has('customer') && session('customer')->type == 'Wholesale') ? 'selected' : '' }}>
                                                        WholeSale</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <div class="form-group">
                                                    <select onchange="$('.customer').val($(this).val())" name="customer_id"
                                                        id="customer_record" class="form-control select2-single">
                                                        @if (session()->has('customer'))
                                                            <option value="{{ session('customer')->id }}">
                                                                {{ session('customer')->code }} -
                                                                {{ session('customer')->name }}</option>
                                                        @endif
                                                        @if (isset($order))
                                                            <option value="{{ $order->customer->id }}"
                                                                @if (session()->has('customer') && session('customer')->id == $order->customer?->id) selected @endif>
                                                                {{ $order->customer->code }} -
                                                                {{ $order->customer->name }}</option>
                                                        @endif
                                                    </select>

                                                    <div class="form-group">
                                                        <span class="text text-danger ion-android-alert"
                                                            id="credit_balance"></span>
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group" style="border: 1px solid rgba(64, 44, 45, 0.4)">
                                                    <input type="text" class="form-control" name="reference" id="reference"
                                                        placeholder="Reference" />
                                                </div> --}}
                                                <button type="submit" id="create_invoice"
                                                    class="btn btn-sm btn-info float-md-right ml-3">Create Invoice</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="col-md-5 ">
                        <!-- general form elements -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Products</h3>
                                @can('make.daily.sale')
                                    <input type="text" id="barcode" class="form-control" name="barcode"
                                        placeholder="Scan barcode">
                                @endcannot
                            </div>
                            <!-- /.card-header -->
                            @can('view.sale.products')
                                {{-- <div class="card-body table-responsive" id="load">
                                    <table id="example1" class="table table-bordered table-striped text-left"
                                        style="font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th>Store</th>
                                                <th>Code</th>
                                                <th>Item</th>
                                                <th>Unit</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Store</th>
                                                <th>Code</th>
                                                <th>Name</th>
                                                <th>Unit</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach ($stores as $key => $store)
                                                <tr>
                                                    <form action="{{ route('ajax.cart.add') }}" method="POST"
                                                        class="addCartItemForm">
                                                        @csrf
                                                        <input type="hidden" name="customer" class="customer"
                                                            value="@if (session()->has('customer')) {{ session('customer')->id }} @endif">
                                                        <input type="hidden" name="id" value="{{ $store->id }}">
                                                        <input type="hidden" name="name" value="{{ $store->name }}">
                                                        <input type="hidden" name="code" value="{{ $store->code }}">
                                                        <input type="hidden" name="store" value="{{ $store->store }}">
                                                        <input type="hidden" name="unit" value="{{ $store->unit }}">
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
                                                       
                                                        <td align="center">
                                                            <button type="submit" class="btn btn-sm btn-success px-2">
                                                                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                        
                                                    </form>
                                                </tr>
                                            @endforeach

                                        </tbody>

                                    </table>

                                </div> --}}
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <i class="ion-android-cart"></i> Supplier Cart: <small>Purchased
                                                    Products</small>
                                                <div class="float-right">
                                                    <a href="javascript:void(0)" data-toggle="modal"
                                                        data-target="#add_product_form"
                                                        class="btn btn-sm btn-secondary float-md-right"
                                                        style="margin-left: 2px;"><i class="fa fa-plus"></i> Add Product </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="add_product_form" style="display: none;" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Add product to cart</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('ajax.cart.add') }}" method="POST"
                                                    class="addCartItemForm">
                                                    <input type="hidden" name="customer" class="customer"
                                                            value="@if (session()->has('customer')) {{ session('customer')->id }} @endif">
                                                    <input type="hidden" name="type" value="{{ 'proforma' }}" />
                                                    @csrf
                                                    
                                                    <div class="form-group">
                                                        <label for="product_id">Product Name</label>
                                                        <select
                                                            class="form-control select2-single ajax-products {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                                                            name="product_id" id="product_id" required="required">
                                                            <option value="">Select...</option>
                                                            @if (isset($products))
                                                              
                                                                @foreach ($products as $data)
                                                                    <option value="{{ $data->id }}"
                                                                        {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                                                        {{ $data->code }}-{{ $data->name }}</option>
                                                                @endforeach
                                                               
                                                            @endif
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="qty">Quantity</label>
                                                        <input type="number"
                                                            class="form-control {{ $errors->has('qty') ? ' is-invalid' : '' }}"
                                                            name="qty" id="qty" placeholder=""
                                                            required="required">
                                                        @if ($errors->has('qty'))
                                                            <div class="invalid-feedback">
                                                                <strong>{{ $errors->first('qty') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    {{-- <div class="form-group">
                                                        <label for="cost_price">Cost Price</label>
                                                        <input type="text"
                                                            class="form-control {{ $errors->has('cost_price') ? ' is-invalid' : '' }}"
                                                            name="cost_price" id="cost_price" placeholder=""
                                                            required="required">
                                                        @if ($errors->has('cost_price'))
                                                            <div class="invalid-feedback">
                                                                <strong>{{ $errors->first('cost_price') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div> --}}
                                                    <div class="form-group">
                                                        <label for="store">Store</label>
                                                        <select
                                                            class="form-control select2-single {{ $errors->has('store') ? ' is-invalid' : '' }}"
                                                            name="store" id="store" required="required">
                                                            @if (isset($stores))
                                                                <option value="">Select...</option>
                                                                @foreach ($stores as $data)
                                                                    <option value="{{ $data->code }}"
                                                                        {{ $data->id == $model->source_store_id ? 'selected' : '' }}>
                                                                        {{ $data->code }}-{{ $data->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="form-group text-right ">
                                                        <button type="submit" class="btn btn-primary"><span
                                                                class="ion-android-cart"> </span>Add to
                                                            Cart</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-7">

                        <div class="card card-default">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-shopping-cart"></i>
                                    Cart
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body cart-container">

                            </div>
                            <span class="text text-danger error_price"></span>
                            <!-- /.card-body -->
                        </div>

                    </div>

                    <!--/.col (left) -->

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div> <!-- Content Wrapper end -->

    <!--  modal create customer -->
    <div class="modal fade" id="customermodal" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add new Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('customers.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ old('name') }}" placeholder="Enter Name">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" class="form-control" name="phone"
                                        value="{{ old('phone') }}" placeholder="Enter Phone">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="address"
                                        value="{{ old('address') }}" placeholder="Enter Address">
                                </div>

                                <div class="form-group">
                                    <label>Credit Limit</label>
                                    <input type="text" class="form-control" name="credit_limit"
                                        value="{{ old('credit_limit') }}" placeholder="Credit Limit">
                                </div>
                            </div>
                            <input type="hidden" name="modal" value="modal" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                                Close
                            </button>
                            <button type="submit" class="btn btn-info px-3"><i class="icon-trash"></i> Save
                            </button>
                        </div>
                        @method('post')
                    </form>
                </div>
            </div>
        </div>
    </div><!-- End modal delete -->
    <!--  modal create customer -->
    <div class="modal fade" id="credit_limitform" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Increase Customer Credit Limit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('customers.update.credit_limit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Existing Amount</label>
                                    <input type="text" class="form-control" name="amount" value=""
                                        id="existing_amount">
                                </div>
                                <div class="form-group">
                                    <label>New Amount:</label>
                                    <input type="text" class="form-control" name="new_amount"
                                        value="{{ old('new_amount') }}" placeholder="Enter the amount" required>
                                </div>
                                <input type="hidden" class="form-control" name="customer_id" id="credit_limit_customer"
                                    value="">
                            </div>
                        </div>
                        <input type="hidden" name="modal" value="modal" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                        Close
                    </button>
                    <button type="submit" class="btn btn-info px-3"><i class="icon-trash"></i> Save
                    </button>
                </div>
                @method('post')
                </form>
            </div>
        </div>
    </div>
    </div><!-- End modal delete -->
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
                                @php
                                    $customers = clone $customers;
                                @endphp
                                <option value="">Select...</option>
                                @foreach ($customers as $data)
                                    <option value="{{ $data->id }}">{{ $data->code }}-{{ $data->name }}
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
@endsection




@push('js')
    <!-- Sweet Alert Js -->
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script>
        /*$('.cart-container').addClass('d-none')
                                                            $('select[name=account_type]').change( () => {
                                                                $('.cart-container').removeClass('d-none')
                                                            })*/
        $(function() {
            $("#example1").DataTable();

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


        });
    </script>
    <script type="text/javascript">
        function validate(selling_price, cost_price, tagg) {
            tagg_id = "valid_" + tagg;
            $("#" + tagg_id).html("");
            if (parseFloat(selling_price.replace(/£/g, "")) <= 0) {
                $("#" + tagg_id).html("Price cannot be less or equal to than zero");
            }
        }

        function validateQTY(sale_qty, avail_qty, tagg) {
            tagg_id = "valid_" + tagg;
            $("#" + tagg_id).html("");
            if (sale_qty < avail_qty) {
                $("#" + tagg_id).html("Selling QTY is more than the available quantity");
            }
        }
        $('#customer_record').on("change", function() {
            customer_id = $(this).val();

            $.ajax({
                type: "GET",
                url: "{{ route('ajax.load.customer.credit_limit') }}",
                data: {
                    customer_id: customer_id
                }
            }).done(function(data) {
                balance = formatMoney(data);
                $("#credit_balance").html("Credit limit: &#8358;" + balance);
                $('#existing_amount').val(balance);
                $('#credit_limit_customer').val(customer_id);
            });
        });

        function formatMoney(n, c, d, t) {
            var c = isNaN(c = Math.abs(c)) ? 2 : c,
                d = d == undefined ? "." : d,
                t = t == undefined ? "," : t,
                s = n < 0 ? "-" : "",
                i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ?
                d + Math.abs(n - i).toFixed(c).slice(2) : "");
        }

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

        $(function() {

            $('.price').on('input', function() {
                // Get the value of the text field
                var priceValue = parseFloat($(this).val());

                // Hide or show the button based on the condition
                if (priceValue <= 0 || $(this).val() == "") {
                    $('#create_invoice').hide();
                    $('.error_price').html("Price cannot be less or equal to than zero");
                } else {
                    $('#create_invoice').show();
                    $('.error_price').html("");
                }
            });

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

        });

        var delay = (function() {
            var timer = 0;
            return function(callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();


        $('.quantity,.price').keyup(function() {
            id = $(this).attr('data-value');
            $("#valid_qty" + id.substr(1)).html("");
            // if (parseFloat($('#quantity' + id.substr(1)).val()) > parseFloat($('#quantity' + id.substr(1)).attr(
            //         'max-qty'))) {
            //     $("#valid_qty" + id.substr(1)).html("Selling QTY is more than the available QTY(" + $('#quantity' +
            //         id.substr(1)).attr('max-qty') + ")");
            //     $('#quantity' + id.substr(1)).val($('#quantity' + id.substr(1)).attr('max-qty'));
            //     return false;
            // }
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
        let code = "";
        let reading = false;

        document.addEventListener('keypress', e => {
            //usually scanners throw an 'Enter' key at the end of read
            if (e.keyCode === 13) {
                if (code.length >= 5) {
                    //code = code.substr(2, code.length - 3);
                    alert(code);
                    $.ajax({
                        url: "{{ route('barcode.search.product') }}",
                        type: 'GET',
                        data: {
                            barcode: code
                        },
                        dataType: 'html',
                        success: function(response) {
                            // update the cart items container with the new cart data
                            $('#load_cart').html(response);
                            $('#barcode').val("")
                        },
                        error: function(xhr, status, error) {
                            // display an error message
                            alert('An error occurred while adding the product to cart.');
                        }
                    });
                }
            } else {
                code += e.key; //while this is not an 'enter' it stores the every key
            }

            //run a timeout of 200ms at the first read and clear everything
            if (!reading) {
                reading = true;
                setTimeout(() => {
                    code = "";
                    reading = false;
                }, 200); //200 works fine for me but you can adjust it
            }
        });
    </script>
@endpush
