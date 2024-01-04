@extends('layouts.backend.app')

@section('title', 'Pos')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')
    <input name="cart_page_type" type="hidden" value="invoice">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 offset-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pos</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <div class="row">
            <div class="col-sm-2">
                <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                        class="fa fa-list"> </span> Sales </a>
            </div>
        </div>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <div class="card">
                            <form action="{{ route('invoice.create') }}" method="post">
                                @csrf
                                <input type="hidden" name="invoice_id" value="{{ $order->id }}">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Customer
                                        <span>
                                            &nbsp
                                            @can('customer.ledger')
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                    data-target="#customer_ledgerform"
                                                    class="btn btn-sm btn-secondary float-md-right"
                                                    style="margin-left: 2px;">Customer Ledger </a>
                                            @endcan
                                            @can('credit_limits.create')
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#credit_limitform"
                                                    class="btn btn-sm btn-success float-md-right"
                                                    style="margin-left: 2px;">Increase Limit </a>
                                            @endcan
                                            @can('customers.create')
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#customermodal"
                                                    class="btn btn-sm btn-primary float-md-right">Add New</a>
                                            @endcan
                                            <span class="text text-danger fa fa-mobile">Send SMS: </span> <input
                                                type="checkbox" name="sms" id="sms" />
                                        </span>
                                    </h3>

                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-4">
                                            @hasanyrole('Super-admin|Admin')
                                                <div class="form-group">
                                                    <label for="order_date">Sale Date</label>
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
                                                    <option value="Retail"
                                                        {{ $order->customer->type == 'Retail' ? 'selected' : '' }}>Retail
                                                    </option>
                                                    <option value="Wholesale"
                                                        {{ $order->customer->type == 'Wholesale' ? 'selected' : '' }}>
                                                        WholeSale</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="hidden" class="form-control" name="customer_id"
                                                    id="customer_val_id" value="">
                                                <label>Customer</label>
                                                <div class="form-group">
                                                    <select name="customer_id" id="customer_record"
                                                        class="form-control select2-single">
                                                        <option value="{{ $order->customer->id }}">
                                                            {{ $order->customer->code }}-{{ $order->customer->name }}
                                                        </option>
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
                                                {{-- <button type="submit"
                                                    class="btn btn-sm btn-info float-md-right ml-3">Create Invoice</button> --}}
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="number" step=".01" class="form-control" name="discount"
                                                placeholder="Discount" id="discount" value="{{ $order->discount }}" />
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="number" step=".01" class="form-control" placeholder="Refund"
                                                    name="refund" id="refund" value="{{ $order->refund }}" />

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-sm btn-info float-md-right ml-3">Create
                                                Invoice</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="col-md-5">
                        <!-- general form elements -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title text text-danger" style="font-weight: 900;"><span
                                        class="ion-alert-circled"> </span>PoS Edit Mode: <br><small>Invoice:
                                        {{ $order->invoice_no }} <br />Customer: {{ $order->customer->name }}</small></h3>

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive" id="load">
                                <table id="example1" class="table table-bordered table-striped text-left"
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
                                                    <td align="center">{{ $store->qty_available }}</td>
                                                    {{-- <td align="right">
                                                        {{ number_format($store->selling_price, 2) }}
                                                    </td> --}}
                                                    @if ($store->qty_available > 0 && str_replace(',', '', $store->retail_selling_price) > 0)
                                                        <td align="center">
                                                            <button type="submit"
                                                                class="btn btn-sm btn-success px-2 add">
                                                                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                    @else
                                                        <td align="center">
                                                            <span class="fa fa-crosshairs text text-danger"
                                                                title="Selling price not set!"></span>
                                                        </td>
                                                    @endif
                                                </form>
                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-7">
                        <div class="card card-default">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-info"></i>
                                    Shopping Lists

                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body cart-container">

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

@endsection





@push('js')
    <!-- Sweet Alert Js -->
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(function() {
            $("#example1").DataTable({
                'iDisplayLength': 100,

            });


            $('body').on('click', '.add', function() {
                var customer_id = $('#customer_record').val();
                $('.customer').val(customer_id);
            })

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
        $('#credit_customer').on("change", function() {
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
    </script>
@endpush
