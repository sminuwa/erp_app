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
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                   data-target="#credit_limitform"
                                                   class="btn btn-sm btn-success float-md-right"
                                                   style="margin-left: 2px;">Increase Limit </a>
                                            @endcan
                                            @can('customers.create')
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                   data-target="#customermodal"
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
                                                type="checkbox" name="sms" id="sms"/>
                                        </span>
                                    </h3>

                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-4">
                                            {{--                                            @hasanyrole('Super-admin|Admin') --}}
                                            <div class="form-group">
                                                <label for="order_date">Sale Date</label>
                                                <input type="text" name="order_date"
                                                       class="form-control datepicker-entry"
                                                       value="{{ isset($order) ? Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : date('Y-m-d') }}"/>
                                            </div>
                                            {{--                                            @else --}}
                                            {{--                                                <input type="hidden" name="order_date" class="form-control datepicker-entry" --}}
                                            {{--                                                    value="{{ date('Y-m-d') }}" /> --}}
                                            {{--                                            @endhasanyrole --}}
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Customer Type</label>
                                                <select name="account_type" id="account_type" class="form-control"
                                                        {{ !empty(old('account_type')) ? 'disabled' : '' }} required>
                                                    <option value="" disabled selected>Select...</option>
                                                    <option value="Retail"
                                                        {{ (isset($order) && $order->customer->type == 'Retail') || old('account_type') == 'Retail' || (session()->has('customer') && session('customer')->type == 'Retail') ? 'selected' : '' }}>
                                                        Retail
                                                    </option>
                                                    <option value="Wholesale"
                                                        {{ (isset($order) && $order->customer->type == 'Wholesale') || old('account_type') == 'Wholesale' || (session()->has('customer') && session('customer')->type == 'Wholesale') ? 'selected' : '' }}>
                                                        WholeSale
                                                    </option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <div class="form-group">
                                                    <input type="hidden" class="form-control" name="customer_id"
                                                           id="customer_val_id" value="">
                                                    <select onchange="$('.customer').val($(this).val())"
                                                            name="customer_id"
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
                                                                {{ App\Models\Customer::find(old('customer_id'))->code }}
                                                                -
                                                                {{ App\Models\Customer::find(old('customer_id'))->name }}
                                                            </option>
                                                        @endif
                                                    </select>

                                                    <div class="form-group">
                                                        <span class="text  ion-android-alert"
                                                              id="credit_balance"></span>: <span
                                                            class="text ion-android-alert"
                                                            id="customer_balance"></span>
                                                    </div>

                                                    <!-- Receipt Selection Section -->
                                                    <div class="form-group" id="receipt_selection_section" style="display: none;">
                                                        <label for="customer_receipts"><strong>Available Receipts</strong></label>
                                                        <div id="receipt_summary" style="margin-bottom: 10px; padding: 5px; background: #f8f9fa; border-radius: 4px; font-size: 12px;">
                                                            <!-- Summary will be shown here -->
                                                        </div>
                                                        <div id="customer_receipts_container" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                                            <!-- Dynamic receipts will be loaded here -->
                                                        </div>
                                                        <input type="hidden" name="selected_receipts" id="selected_receipts" value="">
                                                        <small class="text-muted">System auto-selects optimal receipts. You can manually adjust amounts.</small>
                                                    </div>
                                                </div>
                                                @isset($order)
                                                    <input type="hidden" name="order_invoice_id"
                                                           value="{{ $order->id }}"/>
                                                @endisset

                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <textarea class="form-control" name="description" placeholder="Description"
                                                      id="description" required></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="discount" id="discount"
                                                   oninput="formatNumber(this)" placeholder="Discount"/>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="text" class="form-control" oninput="formatNumber(this)"
                                                       placeholder="Refund" name="refund" id="refund"/>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="checkbox" id="show_vat" name="show_vat" data-switch="bool">
                                            <label for="show_vat" data-on-label="On" data-off-label="Off">Show
                                                VAT</label>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-sm btn-info float-md-right ml-3">Create
                                                Invoice
                                            </button>
                                        </div>
                                        @if (old('customer_id'))
                                            <div class="col-md-6">
                                                <span style="color:red;">If the credit limit is exceeded, click here to
                                                    refresh <i class="fa fa-refresh"
                                                               onclick="window.location.reload()"></i></span>
                                            </div>
                                        @endif
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

                                {{--                                @can('make.daily.sale') --}}
                                <input type="text" id="barcode" class="form-control" name="barcode"
                                       placeholder="Scan barcode">
                                {{--                                @endcannot --}}
                            </div>
                            <!-- /.card-header -->
                            {{--                            @can('view.sale.products') --}}
                            <div class="card-body table-responsive" id="load">
                                <table id="example1" class="table table-bordered table-striped text-left"
                                       style="font-size: 12px;">
                                    <thead>
                                    <tr>
                                        <th>Store</th>
                                        <th>Code</th>
                                        <th>Name</th>
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
                                                <td title="{{$store->information}}">{{ $store->code }}</td>
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
                            {{--                            @endcan --}}
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
                                    <input type="number" class="form-control" name="phone"
                                           value="{{ old('phone') }}" placeholder="Enter Phone">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="email" class="form-control" name="address"
                                           value="{{ old('address') }}" placeholder="Enter Address">
                                </div>

                                <div class="form-group">
                                    <label>Credit Limit</label>
                                    <input type="text" class="form-control" name="credit_limit"
                                           oninput="formatNumber(this)" value="{{ old('credit_limit') }}"
                                           placeholder="Credit Limit">
                                </div>
                            </div>
                            <input type="hidden" name="modal" value="modal"/>
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
                        <input type="hidden" name="payer_id" id="payer_id" value="" required/>
                        <input type="hidden" name="type" value="Customer" required/>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="payment_date">Payment Date</label>
                                <input type="text"
                                       class="form-control datepicker-entry {{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
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
                                <input type="text" oninput="formatNumber(this)"
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
                                <textarea type="text" class="form-control" name="payment_ref"
                                          id="payment_ref"></textarea>
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
                                           oninput="formatNumber(this)" value="{{ old('new_amount') }}"
                                           placeholder="Enter the amount" required>
                                </div>
                                <input type="hidden" class="form-control" name="customer_id" id="credit_limit_customer"
                                       value="">
                            </div>
                        </div>
                        <input type="hidden" name="modal" value="modal"/>
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
                            <input type="text" class="form-control datepicker-entry" name="from_date" id="from_date"
                                   placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="text" class="form-control datepicker-entry" name="to_date" id="to_date"
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
                        <input type="hidden" name="print" value="print"/>
                        <input type="hidden" name="modal" value="modal"/>
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
        $(function () {
            $("#example1").DataTable({
                'iDisplayLength': 100,
            });

            $('body').on('click', '.add', function () {
                var customer_id = $('#customer_record').val();
                $('.customer').val(customer_id);
            })

            $('#account_type').on("change", function () {
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customers') }}",
                    data: {
                        type: $(this).val()
                    }
                }).done(function (data) {
                    $("#customer_record").html(data);
                });
            });
        });
    </script>
    <script type="text/javascript">
        //To update available qty based on unit of measure
        $(document).on('change', '.unit_measure', function () {
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
            }).done(function (value) {
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

        $('#customer_record').on("change", function () {
            customer_id = $(this).val();
            $('.customer').val(customer_id);
            $('#payer_id').val(customer_id);
            $.ajax({
                type: "GET",
                url: "{{ route('ajax.load.customer.credit_limit') }}",
                data: {
                    customer_id: customer_id
                }
            }).done(function (data) {
                balance = formatMoney(data);
                $("#credit_balance").html("<b>Credit limit: &#8358;</b>" + balance);
                $('#existing_amount').val(balance);
                $('#credit_limit_customer').val(customer_id);
            });
        });

        $('#customer_record').on("change", function () {
            customer_id = $(this).val();
            $('.customer').val(customer_id);
            $('#payer_id').val(customer_id);
            $.ajax({
                type: "GET",
                url: "{{ route('ajax.load.customer.balance') }}",
                data: {
                    customer_id: customer_id
                }
            }).done(function (data) {
                let $new_data = formatMoney(Math.abs(data));
                if (data < 0) {
                    balance = `<span class="text-danger"><b>Balance: &#8358;</b>` + $new_data + `</span>`;
                } else
                    balance = `<span class="text-success"><b>Balance: &#8358;</b>` + $new_data + `</span>`;
                // balance = formatMoney(Math.abs(data));

                $("#customer_balance").html(balance);

                // Load customer receipts
                console.log('About to load customer receipts for customer_id:', customer_id);
                loadCustomerReceipts(customer_id);
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

        $(function () {
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

        var delay = (function () {
            var timer = 0;
            return function (callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

        $('.quantity,.price').keyup(function () {
            id = $(this).attr('data-value');
            $("#valid_qty" + id.substr(1)).html("");
            if (parseFloat($('#quantity' + id.substr(1)).val()) > parseFloat($('#quantity' + id.substr(1)).attr(
                'max-qty'))) {
                $("#valid_qty" + id.substr(1)).html("Selling QTY is more than the available QTY(" + $('#quantity' +
                    id.substr(1)).attr('max-qty') + ")");
                $('#quantity' + id.substr(1)).val($('#quantity' + id.substr(1)).attr('max-qty'));
                return false;
            }
            delay(function () {

                $.ajax({
                    url: $('#' + id).attr('action'),
                    type: $('#' + id).attr('method'),
                    //dataType: 'json',
                    data: $('#' + id).serialize(),
                    success: function (data) {
                        id = id.substr(1);

                        subtotal = $('#price' + id).val() * $('#quantity' + id).val();
                        $('.subtotal' + id).text(formatMoney(subtotal));
                        $('#total').text(formatMoney(data));
                        $('#subtotal').text(formatMoney(data));
                    },
                    error: function (xhr, err) {
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
                        success: function (response) {
                            // update the cart items container with the new cart data
                            $('#load_cart').html(response);
                            $('#barcode').val("")
                        },
                        error: function (xhr, status, error) {
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

        function formatNumber(input) {
            // Remove non-numeric and non-decimal characters
            let value = input.value.replace(/[^\d.]/g, '');

            // Split the value into integer and decimal parts
            const parts = value.split('.');
            let integerPart = parts[0] ? parseFloat(parts[0]) : 0;
            let decimalPart = parts[1] !== undefined ? '.' + parts[1] : '';

            // Check if the integer part is not NaN
            if (!isNaN(integerPart)) {
                // Format the integer part with commas and dot as decimal separator
                integerPart = integerPart.toLocaleString('en-US', {
                    maximumFractionDigits: 2,
                    useGrouping: true
                });

                // Set the formatted value back to the input
                input.value = integerPart + decimalPart;
            }
        }

        // Load customer receipts function
        function loadCustomerReceipts(customer_id) {
            console.log('loadCustomerReceipts called with customer_id:', customer_id);

            if (!customer_id) {
                $('#receipt_selection_section').hide();
                console.log('No customer_id provided, hiding receipt section');
                return;
            }

            // Show loading indicator
            $('#customer_receipts_container').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading receipts...</div>');
            $('#receipt_selection_section').show();

            $.ajax({
                type: "GET",
                url: "{{ route('ajax.load.customer.receipts', ':customer_id') }}".replace(':customer_id', customer_id),
                success: function(response) {
                    console.log('AJAX response:', response);
                    if (response.success && response.receipts.length > 0) {
                        console.log('Found', response.receipts.length, 'receipts for customer');
                        displayCustomerReceipts(response.receipts);
                        $('#receipt_selection_section').show();
                    } else {
                        console.log('No receipts found for customer');
                        $('#customer_receipts_container').html('<div class="alert alert-info" style="padding: 8px; font-size: 12px;">No available receipts found for this customer.</div>');
                        $('#receipt_selection_section').show(); // Still show the section with the "no receipts" message
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', xhr.responseText);
                    $('#customer_receipts_container').html('<div class="alert alert-danger" style="padding: 8px; font-size: 12px;">Error loading receipts: ' + error + '</div>');
                    $('#receipt_selection_section').show(); // Show with error message
                }
            });
        }

        // Display customer receipts function
        function displayCustomerReceipts(receipts) {
            // Update summary
            const totalReceipts = receipts.length;
            const totalAvailable = receipts.reduce((sum, r) => sum + parseFloat(r.remaining_balance), 0);

            $('#receipt_summary').html(`
                <strong>📋 Receipt Summary:</strong>
                ${totalReceipts} available receipt${totalReceipts !== 1 ? 's' : ''} |
                Total Available: ₦${formatMoney(totalAvailable)}
            `);

            let html = '';
            receipts.forEach(function(receipt, index) {
                const formattedAmount = formatMoney(receipt.amount);
                const formattedBalance = formatMoney(receipt.remaining_balance);
                const receiptDate = new Date(receipt.date).toLocaleDateString();

                html += `
                    <div class="receipt-item" style="margin-bottom: 8px; padding: 6px; border: 1px solid #eee; border-radius: 4px; background: ${index % 2 === 0 ? '#fafafa' : '#ffffff'};">
                        <label style="cursor: pointer; display: block; margin: 0;">
                            <input type="checkbox" class="receipt-checkbox" data-receipt-id="${receipt.id}"
                                   data-remaining-balance="${receipt.remaining_balance}"
                                   style="margin-right: 6px;">
                            <strong style="font-size: 11px;">${receipt.receipt_no}</strong>
                            <span style="font-size: 10px; color: #666;"> - ${receiptDate}</span>
                            <br>
                            <small class="text-muted" style="font-size: 10px;">${receipt.description || 'No description'}</small>
                            <br>
                            <span class="text-info" style="font-size: 10px;">Total: ₦${formattedAmount}</span> |
                            <span class="text-success" style="font-size: 10px;"><strong>Available: ₦${formattedBalance}</strong></span>
                        </label>
                        <div class="applied-amount-section" style="display: none; margin-top: 4px;">
                            <label style="font-size: 10px; margin-bottom: 2px;">Apply Amount:</label>
                            <input type="number" class="form-control applied-amount-input"
                                   data-receipt-id="${receipt.id}"
                                   style="font-size: 10px; height: 25px; padding: 2px 4px;"
                                   max="${receipt.remaining_balance}"
                                   step="0.01" placeholder="Enter amount">
                        </div>
                    </div>
                `;
            });

            $('#customer_receipts_container').html(html);

            // Auto-select receipts with optimal selection
            autoSelectReceipts();
        }

        // Auto-select receipts function with optimal application
        function autoSelectReceipts() {
            // Get current cart total (invoice total)
            const cartTotal = getCartTotal();

            // Debug logging
            console.log('Cart total detected:', cartTotal);

            if (cartTotal <= 0) {
                // No cart items, don't select any receipts
                console.log('No cart total detected, skipping receipt optimization');
                return;
            }

            let remainingInvoiceAmount = cartTotal;
            const receipts = [];

            // Collect all receipts with their data
            $('.receipt-checkbox').each(function() {
                const checkbox = $(this);
                receipts.push({
                    element: checkbox,
                    id: checkbox.data('receipt-id'),
                    balance: parseFloat(checkbox.data('remaining-balance'))
                });
            });

            // Debug logging
            console.log('Available receipts:', receipts.map(r => ({id: r.id, balance: r.balance})));
            console.log('Invoice amount to cover:', cartTotal);

            // Smart optimization algorithm
            const optimalSelection = findOptimalReceiptCombination(receipts, cartTotal);

            // Apply the optimal selection
            receipts.forEach(function(receipt) {
                const optimalReceipt = optimalSelection.find(r => r.id === receipt.id);

                if (optimalReceipt && optimalReceipt.amount > 0) {
                    // This receipt should be used
                    receipt.element.prop('checked', true);
                    const amountInput = $(`.applied-amount-section input[data-receipt-id="${receipt.id}"]`);
                    amountInput.val(optimalReceipt.amount.toFixed(2));
                    receipt.element.closest('.receipt-item').find('.applied-amount-section').show();
                } else {
                    // This receipt should not be used
                    receipt.element.prop('checked', false);
                    receipt.element.closest('.receipt-item').find('.applied-amount-section').hide();
                }
            });

            remainingInvoiceAmount = cartTotal - optimalSelection.reduce((sum, r) => sum + r.amount, 0);

            updateSelectedReceipts();

            // Show optimization results
            const selectedCount = optimalSelection.length;
            const totalCovered = optimalSelection.reduce((sum, r) => sum + r.amount, 0);

            let resultHtml = '';
            if (remainingInvoiceAmount > 0) {
                const formattedRemaining = formatMoney(remainingInvoiceAmount);
                resultHtml = `
                    <div class="alert alert-warning" style="margin-top: 10px; padding: 6px; font-size: 11px;">
                        <strong>⚠️ Partial Coverage:</strong><br>
                        Invoice: ₦${formatMoney(cartTotal)} |
                        Covered: ₦${formatMoney(totalCovered)} (${selectedCount} receipt${selectedCount !== 1 ? 's' : ''})<br>
                        <span class="text-danger">Still Need: ₦${formattedRemaining}</span>
                    </div>
                `;
            } else if (remainingInvoiceAmount === 0) {
                resultHtml = `
                    <div class="alert alert-success" style="margin-top: 10px; padding: 6px; font-size: 11px;">
                        <strong>✅ Perfect Coverage!</strong><br>
                        Invoice: ₦${formatMoney(cartTotal)} - Covered by ${selectedCount} receipt${selectedCount !== 1 ? 's' : ''}
                    </div>
                `;
            } else {
                // Over coverage (shouldn't happen with good optimization)
                resultHtml = `
                    <div class="alert alert-info" style="margin-top: 10px; padding: 6px; font-size: 11px;">
                        <strong>ℹ️ Over Coverage:</strong><br>
                        Invoice: ₦${formatMoney(cartTotal)} |
                        Applied: ₦${formatMoney(totalCovered)} (${selectedCount} receipt${selectedCount !== 1 ? 's' : ''})
                    </div>
                `;
            }

            $('#customer_receipts_container').append(resultHtml);
        }

        // Get current cart total
        function getCartTotal() {
            // Try to get from cart total display (.totalCart)
            const totalCartElement = $('.totalCart');
            if (totalCartElement.length > 0) {
                const totalText = totalCartElement.text().replace(/[^\d.]/g, '');
                const total = parseFloat(totalText);
                if (!isNaN(total) && total > 0) {
                    return total;
                }
            }

            // Fallback: try other common total selectors
            const otherTotalElements = $('#total, #subtotal, .cart-total, .total, .subtotal');
            if (otherTotalElements.length > 0) {
                const totalText = otherTotalElements.first().text().replace(/[^\d.]/g, '');
                const total = parseFloat(totalText);
                if (!isNaN(total) && total > 0) {
                    return total;
                }
            }

            // Fallback: calculate from cart subtotals
            let total = 0;
            $('[class*="subtotal"]').each(function() {
                const itemTotal = parseFloat($(this).text().replace(/[^\d.]/g, '')) || 0;
                total += itemTotal;
            });

            return total;
        }

        // Handle receipt selection
        $(document).on('change', '.receipt-checkbox', function() {
            const receiptId = $(this).data('receipt-id');
            const remainingBalance = parseFloat($(this).data('remaining-balance'));
            const amountSection = $(this).closest('.receipt-item').find('.applied-amount-section');
            const amountInput = amountSection.find('.applied-amount-input');

            if ($(this).is(':checked')) {
                amountSection.show();
                amountInput.val(remainingBalance); // Auto-fill with full remaining balance
            } else {
                amountSection.hide();
                amountInput.val('');
            }

            updateSelectedReceipts();
        });

        // Handle amount input changes
        $(document).on('input', '.applied-amount-input', function() {
            const max = parseFloat($(this).attr('max'));
            let value = parseFloat($(this).val()) || 0;

            if (value > max) {
                $(this).val(max);
                alert('Amount cannot exceed available balance');
            }

            updateSelectedReceipts();
        });

        // Update selected receipts hidden field
        function updateSelectedReceipts() {
            const selectedReceipts = [];

            $('.receipt-checkbox:checked').each(function() {
                const receiptId = $(this).data('receipt-id');
                const appliedAmount = parseFloat($(`.applied-amount-input[data-receipt-id="${receiptId}"]`).val()) || 0;

                if (appliedAmount > 0) {
                    selectedReceipts.push({
                        receipt_id: receiptId,
                        applied_amount: appliedAmount
                    });
                }
            });

            $('#selected_receipts').val(JSON.stringify(selectedReceipts));
        }

        // Smart receipt optimization algorithm
        function findOptimalReceiptCombination(receipts, targetAmount) {
            if (targetAmount <= 0 || receipts.length === 0) {
                return [];
            }

            // Try different strategies and pick the best one
            const strategies = [
                findExactMatch,
                findMinimalCombination,
                findGreedyLargestFirst,
                findGreedySmallestFirst
            ];

            let bestResult = [];
            let bestScore = -1;

            for (let strategy of strategies) {
                const result = strategy(receipts, targetAmount);
                const score = evaluateResult(result, targetAmount);

                console.log('Strategy result:', {
                    strategy: strategy.name,
                    receiptsUsed: result.length,
                    totalAmount: result.reduce((sum, r) => sum + r.amount, 0),
                    score: score
                });

                if (score > bestScore) {
                    bestResult = result;
                    bestScore = score;
                }
            }

            console.log('Best strategy selected:', bestResult);
            return bestResult;
        }

        // Strategy 1: Look for exact matches (single receipt that covers full amount)
        function findExactMatch(receipts, targetAmount) {
            for (let receipt of receipts) {
                if (receipt.balance >= targetAmount) {
                    return [{id: receipt.id, amount: targetAmount}];
                }
            }
            return [];
        }

        // Strategy 2: Find minimal number of receipts
        function findMinimalCombination(receipts, targetAmount) {
            // Sort by balance descending for minimal combination
            const sortedReceipts = [...receipts].sort((a, b) => b.balance - a.balance);
            let remaining = targetAmount;
            let result = [];

            for (let receipt of sortedReceipts) {
                if (remaining <= 0) break;

                const useAmount = Math.min(remaining, receipt.balance);
                if (useAmount > 0) {
                    result.push({id: receipt.id, amount: useAmount});
                    remaining -= useAmount;
                }
            }

            return result;
        }

        // Strategy 3: Largest receipts first
        function findGreedyLargestFirst(receipts, targetAmount) {
            const sortedReceipts = [...receipts].sort((a, b) => b.balance - a.balance);
            return applyGreedyStrategy(sortedReceipts, targetAmount);
        }

        // Strategy 4: Smallest receipts first (consume older/smaller receipts)
        function findGreedySmallestFirst(receipts, targetAmount) {
            const sortedReceipts = [...receipts].sort((a, b) => a.balance - b.balance);
            return applyGreedyStrategy(sortedReceipts, targetAmount);
        }

        function applyGreedyStrategy(sortedReceipts, targetAmount) {
            let remaining = targetAmount;
            let result = [];

            for (let receipt of sortedReceipts) {
                if (remaining <= 0) break;

                const useAmount = Math.min(remaining, receipt.balance);
                if (useAmount > 0) {
                    result.push({id: receipt.id, amount: useAmount});
                    remaining -= useAmount;
                }
            }

            return result;
        }

        // Evaluate how good a result is
        function evaluateResult(result, targetAmount) {
            if (result.length === 0) return -1;

            const totalAmount = result.reduce((sum, r) => sum + r.amount, 0);
            const coverage = totalAmount / targetAmount;

            // Perfect match gets highest score
            if (totalAmount === targetAmount) {
                return 1000 - result.length; // Prefer fewer receipts for exact matches
            }

            // Partial coverage gets lower score
            if (totalAmount < targetAmount) {
                return coverage * 100 - result.length; // Coverage is important, but fewer receipts is better
            }

            // Over-coverage gets lowest score
            return 50 - result.length;
        }

        // Re-optimize receipts when cart changes
        function reOptimizeReceipts() {
            // Clear any previous alerts and results
            $('#customer_receipts_container .alert').remove();

            // Re-run optimization if receipts are loaded
            if ($('.receipt-checkbox').length > 0) {
                console.log('Re-optimizing receipts due to cart change...');
                autoSelectReceipts();
            }
        }

        // Monitor cart changes using MutationObserver
        $(document).ready(function() {
            const cartContainer = document.querySelector('.cart-container');
            if (cartContainer) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' || mutation.type === 'subtree') {
                            // Debounce the re-optimization
                            clearTimeout(window.receiptOptimizeTimeout);
                            window.receiptOptimizeTimeout = setTimeout(reOptimizeReceipts, 500);
                        }
                    });
                });

                observer.observe(cartContainer, {
                    childList: true,
                    subtree: true,
                    characterData: true
                });
            }
        });

        // Also trigger re-optimization on specific cart events
        $(document).on('DOMSubtreeModified', '.cart-container', function() {
            // Fallback for older browsers
            clearTimeout(window.receiptOptimizeTimeout);
            window.receiptOptimizeTimeout = setTimeout(reOptimizeReceipts, 500);
        });
    </script>
@endpush
