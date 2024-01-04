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
                    <div class="col-md-6">
                        <!-- general form elements -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">POS</h3>
                                <form action="{{ route('pos.index') }}" method="get">
                                    @csrf
                                    Product Category
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="all">All categories</option>
                                        @foreach ($categories as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == $category_id ? 'selected' : '' }}>
                                                {{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                    Store
                                    <select name="store_id" id="store_id" class="form-control select2-single">
                                        <option value="all">All stores</option>
                                        @foreach ($store as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == $store_id ? 'selected' : '' }}>{{ $data->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="submit" name="btnLoad" id="btnLoad" value="Load"
                                        class="btn btn-sm btn-secondary" />
                                </form>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive" id="load">
                                <table id="example1" class="table table-bordered table-striped text-left">
                                    <thead>
                                        <tr style="font-size: 14px;">
                                            <th>Store</th>
                                            <th>Item Description</th>
                                            <th>QTY Avail.</th>
                                            <th>Price</th>
                                            <th>Add To Cart</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="font-size: 14px;">
                                            <th>Store</th>
                                            <th>Name</th>
                                            <th>QTY Avail.</th>
                                            <th>Price</th>
                                            <th>Add To Cart</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($stores as $key => $store)
                                            <tr>
                                                <form action="{{ route('cart.store') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $store->id }}">
                                                    <input type="hidden" name="name" value="{{ $store->name }}">
                                                    <input type="hidden" name="qty" value="1">
                                                    <input type="hidden" name="selling_price"
                                                        value="{{ $store->selling_price }}">
                                                    <input type="hidden" name="sold_price"
                                                        value="{{ $store->selling_price }}">
                                                    <input type="hidden" name="cost_price"
                                                        value="{{ $store->cost_price }}">

                                                    <td>{{ $store->store }}</td>
                                                    <td>{{ $store->name }}</td>
                                                    <td align="center">{{ $store->qty_available }}</td>
                                                    <td align="right">
                                                        {{ number_format($store->selling_price, 2) }}
                                                    </td>
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
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-6">
                        <div class="card card-default">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-info"></i>
                                    Shopping Lists

                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                @if (Cart::getTotal() < 1)
                                    <div class="alert alert-danger">
                                        No Product Added
                                    </div>
                                @else
                                    <table class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr style="font-size: 14px;">
                                                <th>S.N</th>
                                                <th style="width:30%">Item Description</th>
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
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="text-left">{{ $product->name }}</td>

                                                    <form action="{{ route('cart.update') }}" method="post"
                                                        id="p{{ $product->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <td>
                                                            <input type="number" step=".01" name="sold_price"
                                                                id="price{{ $product->id }}" class="form-control price"
                                                                style="min-width:65px;"
                                                                onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                                                value="{{ $product->price }}"
                                                                data-val="{{ $product->attributes['cost_price'] }}"
                                                                data-value="p{{ $product->id }}">
                                                            <span style="color: red;"
                                                                id="valid_price{{ $product->id }}"></span>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="quantity" step=".01"
                                                                id="quantity{{ $product->id }}"
                                                                class="form-control quantity"
                                                                data-value="p{{ $product->id }}" style="min-width:58px;"
                                                                value="{{ $product->quantity }}" min="1" required>
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

                                <div class="alert alert-info">
                                    {{-- <p>Quantity : {{ Cart::getTotalQuantity() }}</p> --}}
                                    <p>Sub Total : &#8358; <span
                                            id="subtotal">{{ number_format(Cart::getSubTotal(), 2) }}</span></p>
                                </div>
                                <div class="alert alert-success">
                                    Total : &#8358; <span id="total">{{ number_format(Cart::getTotal()) }}</span>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <div class="card">
                            <form action="{{ route('invoice.create') }}" method="post">
                                @csrf
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Customer
                                        <span>
                                            &nbsp;
                                            @can('make.debtor.payment')
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                    data-target="#debtor_payment_form"
                                                    class="btn btn-sm btn-secondary float-md-right"
                                                    style="margin-left: 2px;">Debtor Payment </a>
                                            @endcan
                                            @can('view.customer.ledger')
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                    data-target="#customer_ledgerform"
                                                    class="btn btn-sm btn-secondary float-md-right"
                                                    style="margin-left: 2px;">Customer Ledger </a>
                                            @endcan
                                            @can('increase.customer.credit.limit')
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                    data-target="#credit_limitform"
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
                                    @hasanyrole('Super-admin|Admin')
                                        <div class="form-group">
                                            <label for="order_date">Sale Date</label>
                                            <input type="text" name="order_date" class="form-control datepicker"
                                                value="{{ date('Y-m-d') }}" />
                                        </div>
                                    @else
                                        <input type="hidden" name="order_date" class="form-control datepicker"
                                            value="{{ date('Y-m-d') }}" />
                                    @endhasanyrole
                                    <div class="form-group">
                                        <label>Sales Mode</label>
                                        <select name="sale_mode" id="sale_mode" class="form-control" required>
                                            <option value="" disabled selected>Select...</option>
                                            <option value="Cash">Cash Sales</option>
                                            <option value="Credit">Credit Sales</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Customer</label>
                                        <div class="form-group" id="walk_customer"
                                            style="border: 1px solid rgba(64, 44, 45, 0.4)">
                                            <input type="text" class="form-control" name="customer" id="customer"
                                                placeholder="Customer name" />
                                            <input type="text" class="form-control" name="phone" id="phone"
                                                placeholder="Phone number" />
                                            <input type="text" class="form-control" name="address" id="address"
                                                placeholder="Address" />
                                        </div>
                                        <div class="form-group" id="credit_customer_div">
                                            <select name="customer_id" id="credit_customer"
                                                class="form-control ct select2-single">
                                                <option value="">Select...</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ old('customer_id', $customer->id) }}"
                                                        {{ old('customer_id', $customer->id) == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="form-group" id="display_due_date"
                                                style="border: 1px solid rgba(64, 44, 45, 0.4)">

                                                <input type="text" class="form-control datepicker" name="due_date"
                                                    id="due_date" placeholder="Due Date" value="{{ old('due_date') }}"
                                                    autocomplete="off" />
                                            </div>
                                            <div class="form-group">
                                                <span class="text text-danger ion-android-alert"
                                                    id="credit_balance"></span>
                                            </div>

                                        </div>
                                        <button type="submit" class="btn btn-sm btn-info float-md-right ml-3">Create
                                            Invoice</button>
                                    </div>
                                </div>
                            </form>

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
                                @foreach (App\Models\Customer::where('type', 'credit')->where('branch_id', 'LIKE', App\Models\User::userBranchAction())->get() as $data)
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
    <div class="modal fade" id="debtor_payment_form" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Debtor Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ isset($route) ? $route : route('debtors.payment.store') }}" method="POST"
                        id="debtor_payment">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_id2">Customer</label>
                                    <select
                                        class="form-control select2-single {{ $errors->has('customer_id2') ? ' is-invalid' : '' }}"
                                        name="customer_id2" id="customer_id2" required="required">
                                        <option value="">Select...</option>
                                        @if (isset($customers))
                                            @foreach ($customers as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('customer_id2'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('customer_id2') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_date">Payment Date</label>
                                    <input type="text"
                                        class="form-control datepicker {{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
                                        name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}"
                                        required="required">
                                    @if ($errors->has('payment_date'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('payment_date') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="receipt_no">Receipt No</label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('receipt_no') ? ' is-invalid' : '' }}"
                                        readonly='readonly' name="receipt_no" id="receipt_no"
                                        value="{{ old('receipt_no', isset($receipt_no) ? $receipt_no : '') }}">
                                    @if ($errors->has('receipt_no'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('receipt_no') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="amount_paid">Amount Paid</label>
                                    <input type="number" step=".01"
                                        class="form-control {{ $errors->has('amount_paid') ? ' is-invalid' : '' }}"
                                        name="amount_paid" id="amount_paid" required>
                                    @if ($errors->has('amount_paid'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('amount_paid') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_mode">Payment Mode</label>
                                    <select class="form-control {{ $errors->has('payment_mode') ? ' is-invalid' : '' }}"
                                        name="payment_mode" id="payment_mode" required="required">
                                        <option value="">Select...</option>
                                        <option value="Cash">Cash
                                        </option>
                                        <option value="Cheque">
                                            Cheque</option>
                                    </select>
                                    @if ($errors->has('payment_mode'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('payment_mode') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4" id="account_number">
                                <div class="form-group">
                                    <label for="bank_account_id">Account Number</label>
                                    <select
                                        class="form-control select2-single {{ $errors->has('bank_account_id') ? ' is-invalid' : '' }}"
                                        name="bank_account_id" id="bank_account_id" required="required">
                                        <option value="">Select...</option>
                                    </select>
                                    @if ($errors->has('bank_account_id'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('bank_account_id') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4" id="account_number">
                                <div class="form-group">
                                    <label for="account_name">Account Name</label>
                                    <input type="text" class="form-control" disabled name="account_name"
                                        id="account_name">
                                    @if ($errors->has('account_name'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('account_name') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_ref">Payment Ref</label>
                                    <textarea type="text" class="form-control" name="payment_ref" id="payment_ref"></textarea>
                                    @if ($errors->has('payment_ref'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('payment_ref') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="balance">Total Balance</label>
                                    <input type="text" class="form-control col-4" name="balance" id="balance"
                                        placeholder="Total Balance" value="" readonly>
                                </div>
                                <span class="text text-danger fa fa-mobile">Send SMS: </span> <input type="checkbox"
                                    name="sms" id="debt_sms" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 text-danger">
                                <strong>Total Record is of 1:
                                    {{ number_format(App\Models\CustomerLedger::where('cr', '>', 0)->count('*'), 0, ',', '') }}</strong>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                                Close
                            </button>
                            <a href="" class="btn btn-dark" id="print_reciept" target="_BLANK"
                                style="display: none;"><i class="fa fa-print"></i>
                                Print
                            </a>
                            <button type="button" id="debtor" class="btn btn-info px-3"><i class="fa fa-save"></i>
                                Save
                            </button>
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
        $(function() {
            $("#example1").DataTable({
                    'iDisplayLength':100
                });
            $('#credit_customer_div').hide();
            $('#walk_customer').hide();
            $('#sale_mode').on("change", function() {
                if ($(this).val() == "Credit" || $(this).val() == "Cash/Credit") {
                    $('#walk_customer').hide();
                    $('#display_due_date').show();
                    $('#credit_customer_div').show();
                }
                if ($(this).val() == "Cash") {
                    $('#walk_customer').show();
                    $('#display_due_date').val("");
                    $('#display_due_date').hide();
                    $('#credit_customer_div').hide();
                }
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

            $('#customer_id2').on("change", function() {
                customer_id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customer.balance') }}",
                    data: {
                        customer_id: customer_id
                    }
                }).done(function(data) {
                    balance = 0;
                    if (data < 0)
                        balance = "(" + formatMoney(Math.abs(data)) + ")";
                    else
                        balance = formatMoney(data);
                    $("#balance").val(balance);
                });
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
            $('#debtor').on('click', function() {

                $.ajax({
                    type: "POST",
                    url: "{{ route('debtors.payment.store') }}",
                    data: $('#debtor_payment').serialize()
                }).done(function(data) {
                    if (data > 0) {
                        $('#print_reciept').show();
                        $('#print_reciept').attr('href', '');
                        alert("Payment successfully added");
                        $('#print_reciept').attr('href', "{{ url('/customers/print/receipt') }}/" +
                            data)
                    } else {
                        alert("This is already captured or something wrong!")
                    }

                    $('#debtor_payment_form').modal('hide');
                });

            });
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
