@extends('layouts.backend.app')

@section('title', 'Pos')

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
                            <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">PoS</a></li>
                            <li class="breadcrumb-item active">Order Invoice</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <div class="row">
            <div class="col-sm-2">
                <a href="{{ route('orders.approved') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                        class="fa fa-list"> </span> Sales </a>
                <a href="{{ route('order.invoice.list') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                        class="fa fa-list"> </span> Orders </a>

            </div>
        </div>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-6">
                        <!-- general form elements -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Orders</h3>

                                @can('make.daily.sale')
                                    <input type="text" id="barcode" class="form-control" name="barcode"
                                        placeholder="Scan barcode">
                                @endcannot
                            </div>
                            <!-- /.card-header -->
                            @can('view.sale.products')
                                <div class="card-body table-responsive" id="load">
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
                                                    <form action="{{ route('cart.store') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $store->id }}">
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
                            @endcan
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <form action="{{ route('order.invoice.create') }}" method="post">
                                @csrf
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Customer
                                        <span>
                                            &nbsp;
                                            @can('view.customer.ledger')
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                    data-target="#customer_ledgerform"
                                                    class="btn btn-sm btn-secondary float-md-right"
                                                    style="margin-left: 2px;">Customer Ledger </a>
                                            @endcan
                                            @can('increase.customer.credit.limit')
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#credit_limitform"
                                                    class="btn btn-sm btn-success float-md-right"
                                                    style="margin-left: 2px;">Increase Limit </a>
                                            @endcan
                                            @can('add.customer')
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#customermodal"
                                                    class="btn btn-sm btn-primary float-md-right">Add New</a>
                                            @endcan
                                            <span class="text text-danger fa fa-mobile">Send SMS: </span> <input
                                                type="checkbox" name="sms" id="sms" />
                                        </span>
                                    </h3>

                                </div>
                                <div class="card-body">

                                    <input type="hidden" name="order_date" class="form-control datepicker"
                                        value="{{ date('Y-m-d') }}" />

                                    <div class="form-group">
                                        <label>Customer Type</label>
                                        <select name="account_type" id="account_type" class="form-control" required>
                                            <option value="" disabled selected>Select...</option>
                                            <option value="Retail">Retail</option>
                                            <option value="Wholesale">WholeSale</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Customer</label>
                                        <div class="form-group">
                                            <select name="customer_id" id="customer_record"
                                                class="form-control select2-single">

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
                                        <button type="submit" class="btn btn-sm btn-info float-md-right ml-3">Create
                                            Invoice</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="card card-default">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-info"></i>
                                    Shopping Lists

                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive" id="load_cart">
                                @if (Cart::getTotal() < 1)
                                    <div class="alert alert-danger">
                                        No Product Added
                                    </div>
                                @else
                                    <table class="table table-bordered table-striped text-center"
                                        style="font-size: 12px;">
                                        <thead>
                                            <tr>
                                                {{-- <th>S.N</th> --}}
                                                <th>Store</th>
                                                <th style="width:30%">Code</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                {{-- <th><span class="ion-refresh"></span></th> --}}
                                                <th><span class="ion-ios-trash"></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cart_products as $product)
                                                <tr>
                                                    {{-- <td>{{ $loop->iteration }}</td> --}}
                                                    <td class="text-left">{{ $product->attributes['store'] }}</td>
                                                    <td class="text-left">{{ $product->attributes['code'] }}</td>

                                                    <form action="{{ route('cart.update') }}" method="post"
                                                        id="p{{ $product->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <td>
                                                            @can('edit.daily.sale')
                                                                <input type="text" name="sold_price"
                                                                    id="price{{ $product->id }}" class="form-control price"
                                                                    style="min-width:65px;"
                                                                    onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                                                    value="{{ $product->price }}"
                                                                    data-val="{{ $product->attributes['cost_price'] }}"
                                                                    data-value="p{{ $product->id }}">
                                                                <span style="color: red;"
                                                                    id="valid_price{{ $product->id }}"></span>
                                                            @else
                                                                <input type="text" name="sold_price"
                                                                    id="price{{ $product->id }}" class="form-control price"
                                                                    readonly style="min-width:65px;"
                                                                    onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                                                    value="{{ $product->price }}"
                                                                    data-val="{{ $product->attributes['cost_price'] }}"
                                                                    data-value="p{{ $product->id }}">
                                                                <span style="color: red;"
                                                                    id="valid_price{{ $product->id }}"></span>
                                                            @endcan

                                                        </td>
                                                        <td>
                                                            <input type="text" name="quantity"
                                                                id="quantity{{ $product->id }}"
                                                                class="form-control quantity"
                                                                data-value="p{{ $product->id }}" style="min-width:58px;"
                                                                value="{{ $product->quantity }}" min="1"
                                                                max-qty="{{ $product->attributes['qty_available'] }}"
                                                                required>
                                                            <span style="color: red;"
                                                                id="valid_qty{{ $product->id }}"></span>
                                                        </td>
                                                        <td><span
                                                                class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
                                                        </td>
                                                        <input type="hidden" name="id" class="form-control"
                                                            value="{{ $product->id }}">
                                                        <input type="hidden" name="selling_price" class="form-control"
                                                            value="{{ $product->attributes['selling_price'] }}">
                                                        <input type="hidden" name="cost_price" class="form-control"
                                                            value="{{ $product->attributes['cost_price'] }}">
                                                        <input type="hidden" name="qty_available" class="form-control"
                                                            value="{{ $product->attributes['qty_available'] }}">
                                                        {{-- <td>
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                            </button>
                                                        </td> --}}
                                                    </form>

                                                    <td>
                                                        <button class="btn btn-danger btn-sm" type="button"
                                                            onclick="deleteItem({{ $product->id }})">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                        <form id="delete-form-{{ $product->id }}"
                                                            action="{{ route('cart.remove', $product->id) }}"
                                                            method="post" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                {{-- <div class="alert alert-info">
                                    <p>Quantity : {{ Cart::getTotalQuantity() }}</p>
                                    <p>Sub Total : &#8358; <span
                                            id="subtotal">{{ number_format(Cart::getSubTotal(), 2) }}</span></p>
                                </div> --}}
                                <div class="alert alert-success">
                                    Total : &#8358; <span id="total">{{ number_format(Cart::getTotal()) }}</span>
                                </div>
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
                                @foreach ($customers->where('branch_id', 'LIKE', App\Models\User::userBranchAction())->get() as $data)
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
            if (parseFloat(selling_price.replace(/£/g, "")) < parseFloat(cost_price.replace(/£/g, ""))) {
                $("#" + tagg_id).html("Selling price is less than the cost price");
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
            $('#account_number,#account_name').hide();
            $('#payment_mode').on("change", function() {
                if ($(this).val() != "Cash") {
                    $('#bank_account_id,#account_name').removeAttr('disabled');
                    $('#account_number,#account_name').show();
                    $("#bank_account_id").html(" < option value = '' > Loading... < /option>");
                    $.ajax({
                        url: "{{ route('ajax.loadBankAccounts') }}",
                        type: 'GET',
                        data: {
                            payment_mode: $("#payment_mode").val()
                        }
                    }).done(function(msg) {
                        $("#bank_account_id").html("<option value=''>--select--</option>" + msg);
                    });
                } else {
                    $('#bank_account_id,#account_name').attr('disabled', 'disabled');
                    $('#account_number,#account_name').hide();
                }

            });

           
            $('#bank_account_id').on("change", function() {
                bank_account_id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.account.name') }}",
                    data: {
                        bank_account_id: bank_account_id
                    }
                }).done(function(data) {
                    $("#account_name").val(data);
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
        // detect when a barcode is scanned
        // let code = "";
        // let barcode ="";
        // $(document).on('input', '#barcode', function(e) {
        //     // var barcode = $(this).val();
        //     // alert($('#barcode').val());
        //     if (e.keyCode === 13) {
        //         if (code.length >= 10) {
        //             barcode = code;
        //         }
        //     } else {
        //         code = code + e.key;
        //     }

        //     // send the barcode to the server to add the product to the cart
        //     $.ajax({
        //         url: "{{ route('barcode.search.product') }}",
        //         type: 'GET',
        //         data: {
        //             barcode: barcode
        //         },
        //         dataType: 'html',
        //         success: function(response) {
        //             // update the cart items container with the new cart data
        //             $('#load_cart').html(response);

        //         },
        //         error: function(xhr, status, error) {
        //             // display an error message
        //             alert('An error occurred while adding the product to cart.');
        //         }
        //     });
        //     // clear the barcode input field
        //     //$(this).val('');
        // });

        //     let UPC = '';
        // document.addEventListener("keydown", function(e) {
        //     const textInput = e.key || String.fromCharCode(e.keyCode);
        //     const targetName = e.target.localName;
        //     let newUPC = '';
        //     if (textInput && textInput.length === 1 && targetName !== 'input'){
        //         newUPC = UPC+textInput;

        //       if (newUPC.length >= 6) {
        //         console.log('barcode scanned:  ', newUPC);
        //       }
        //    }
        // });
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
