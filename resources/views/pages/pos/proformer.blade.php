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
                                        <input type="hidden" class="form-control" name="customer_id"
                                        id="customer_val_id" value="">
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
                                                <div class="form-group">
                                                    <textarea class="form-control" name="description" placeholder="Description" id="description"></textarea>
                                                </div>
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
                            <div class="float-right">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#add_product_form"
                                    class="btn btn-sm btn-secondary float-md-right" style="margin-left: 2px;"><i
                                        class="fa fa-plus"></i> Add Product </a>
                            </div>
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
                            <div>
                                <span class="text text-danger error_price"></span>
                            </div>
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
                    <form action="{{ route('ajax.cart.add') }}" method="POST" class="addCartItemForm">
                        <input type="hidden" name="customer" class="customer"
                            value="@if (session()->has('customer')) {{ session('customer')->id }} @endif">
                        <input type="hidden" name="type" value="{{ 'order' }}" />
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
                            <input type="number" class="form-control {{ $errors->has('qty') ? ' is-invalid' : '' }}"
                                name="qty" id="qty" placeholder="" required="required">
                            @if ($errors->has('qty'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('qty') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="cost_price">Cost Price</label>
                            <input type="text"
                                class="form-control {{ $errors->has('cost_price') ? ' is-invalid' : '' }}"
                                name="cost_price" id="cost_price" placeholder="" required="required">
                            @if ($errors->has('cost_price'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('cost_price') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="store">Store</label>
                            <select class="form-control select2-single {{ $errors->has('store') ? ' is-invalid' : '' }}"
                                name="store" id="store" required="required">
                                @if (isset($stores))
                                    <option value="">Select...</option>
                                    @foreach ($stores as $data)
                                        <option value="{{ $data->code }}">
                                            {{ $data->code }}-{{ $data->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group text-right ">
                            <button type="submit" class="btn btn-primary"><span class="ion-android-cart"> </span>Add to
                                Cart</button>
                        </div>
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
            $("#example1").DataTable({
                'iDisplayLength': 100
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

            $(document).on('input', '.price', function() {
                // Get the value of the text field
                var priceValue = parseFloat($(this).val());
                //alert(priceValue)
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
