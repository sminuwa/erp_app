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
                                            @can('receipt.payment.store')
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#receipt"
                                                    class="btn btn-sm btn-secondary float-md-right"
                                                    onclick="checkCustomerSelection()" style="margin-left: 2px;">Receipt
                                                </a>
                                            @endcan
                                            &nbsp;
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
                                                        value="{{ isset($order) ? Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : date('Y-m-d') }}" />
                                                </div>
                                            @else
                                                <input type="hidden" name="order_date" class="form-control datepicker"
                                                    value="{{ date('Y-m-d') }}" />
                                            @endhasanyrole
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Customer Type</label>
                                                <select name="account_type" id="account_type" class="form-control"
                                                    {{ !empty(old('account_type')) ? 'disabled' : '' }} required>
                                                    <option value="" disabled selected>Select...</option>
                                                    <option value="Retail"
                                                        {{ (isset($order) && $order->customer->type) || old('account_type') == 'Retail' || (session()->has('customer') && session('customer')->type == 'Retail') ? 'selected' : '' }}>
                                                        Retail</option>
                                                    <option value="Wholesale"
                                                        {{ (isset($order) && $order->customer->type) || old('account_type') == 'Wholesale' || (session()->has('customer') && session('customer')->type == 'Wholesale') ? 'selected' : '' }}>
                                                        WholeSale</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <div class="form-group">
                                                    <input type="hidden" class="form-control" name="customer_id"
                                                        id="customer_val_id" value="">
                                                    <select onchange="$('.customer').val($(this).val())" name="customer_id"
                                                        {{ old('customer_id') > 0 ? 'disabled' : '' }} id="customer_record"
                                                        class="form-control select2-single">
                                                        @if (session()->has('customer'))
                                                            <option value="{{ session('customer')->id }}">
                                                                {{ session('customer')->code }} -
                                                                {{ session('customer')->name }}</option>
                                                        @endif
                                                        @if (isset($order))
                                                            <option value="{{ $order->customer->id }}"
                                                                @if ((session()->has('customer') && session('customer')->id) == $order->customer?->id) selected @endif>
                                                                {{ $order->customer->code }} -
                                                                {{ $order->customer->name }}</option>
                                                        @endif
                                                        @if (old('customer_id') > 0)
                                                            <option value="{{ old('customer_id') }}"
                                                                @if ((session()->has('customer') && session('customer')->id) == old('customer_id')) selected @endif>
                                                                {{ App\Models\Customer::find(old('customer_id'))->code }} -
                                                                {{ App\Models\Customer::find(old('customer_id'))->name }}
                                                            </option>
                                                        @endif
                                                    </select>

                                                    <div class="form-group">
                                                        <span class="text text-danger ion-android-alert"
                                                            id="credit_balance"></span>
                                                    </div>
                                                </div>
                                                @isset($order)
                                                    <input type="hidden" name="order_invoice_id"
                                                        value="{{ $order->id }}" />
                                                @endisset

                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <textarea class="form-control" name="description" placeholder="Description" id="description"></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" name="discount" step=".01"
                                                placeholder="Discount" id="discount" />
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="number" step=".01" class="form-control" placeholder="Refund"
                                                    name="refund" id="refund" />

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
                                <h3 class="card-title">POS</h3>

{{--                                @can('make.daily.sale')--}}
                                    <input type="text" id="barcode" class="form-control" name="barcode"
                                        placeholder="Scan barcode">
{{--                                @endcannot--}}
                            </div>
                            <!-- /.card-header -->
{{--                            @can('view.sale.products')--}}
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
{{--                            @endcan--}}
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
                            <div class="card-body cart-container table-responsive">

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
    <div class="modal fade" id="receipt" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ isset($route) ? $route : route('receipt.payment.store') }}" method="POST">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <input type="hidden" name="payer_id" id="payer_id" value="" required />
                        <input type="hidden" name="type" value="Customer" required />
                        <div class="row">
                            <div class="col-md-12">
                                <label for="payment_date">Payment Date</label>
                                <input type="text"
                                    class="form-control datepicker {{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
                                    name="payment_date" id="payment_date"
                                    value="{{ old('payment_date') == '' ? date('Y-m-d') : old('payment_date') }}"
                                    required="required">
                                @if ($errors->has('payment_date'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('payment_date') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="account_id">Account</label>
                                <select
                                    class="form-control select2-single ajax-general-accounts {{ $errors->has('account_id') ? ' is-invalid' : '' }}"
                                    name="account_id" id="account_id" required="required">
                                    <option value="">Select...</option>

                                </select>
                                @if ($errors->has('payer_id'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('payer_id') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="amount_paid">Amount</label>
                                <input type="number" step=".01"
                                    class="form-control {{ $errors->has('amount_paid') ? ' is-invalid' : '' }}"
                                    name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" required>
                                @if ($errors->has('amount_paid'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('amount_paid') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="payment_ref">Description</label>
                                <textarea type="text" class="form-control" name="payment_ref" id="payment_ref"></textarea>
                                @if ($errors->has('payment_ref'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('payment_ref') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal" id="close"><i
                                class="fa fa-times"></i>
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
        //To update available qty based on unit of measure
        $(document).on('change', '.unit_measure', function() {
            var formid = $(this).attr('data-value').substr(1);
            unit = $(this).val();
            $.ajax({
                url: "{{ route('update.available.quantity', ['storeproduct' => ':formid']) }}".replace(
                    ':formid',
                    formid),
                type: 'GET',
                data: {
                    unit: unit
                }
            }).done(function(value) {
                //console.log(value)
                $('#quantity' + formid).attr(
                    'max-qty', value)
            })

        });

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
            $('.customer').val(customer_id);
            $('#payer_id').val(customer_id);
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

        let code = "";
        let reading = false;

        document.addEventListener('keypress', e => {
            //usually scanners throw an 'Enter' key at the end of read
            if (e.keyCode === 13) {
                if (code.length >= 5) {
                    //code = code.substr(2, code.length - 3);

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

        function checkCustomerSelection() {
            var selectedCustomerId = $('#customer_record').val();

            if (!selectedCustomerId) {
                alert('Please select a customer below.');

                return false;
            }
        }
    </script>
@endpush
