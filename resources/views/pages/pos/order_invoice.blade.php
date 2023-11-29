@extends('layouts.backend.app')

@section('title', 'Pos')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')
    <input name="cart_page_type" type="hidden" value="order">
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

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a href="{{ route('order.invoice.list') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                        class="fa fa-list"> </span> Order List</a>
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <div class="card">
                            <form
                                action="{{ isset($order) ? route('order.invoice.update', $order->id) : route('order.invoice.create') }}"
                                method="post">
                                @csrf
                                @isset($order)
                                    @method('PUT')
                                @endisset
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Order Details {{ isset($order) ? ": Edit Mode $order->reference" : '' }}
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
                                                    <option value="Retail" @if(session()->has('customer') && session('customer')->type == 'Retail') selected @endif
                                                        {{ ((isset($order) && $order->customer->type=='Retail') || session()->has('customer') && session('customer')->type == 'Retail') ? 'selected' : '' }}>
                                                        Retail</option>
                                                    <option value="Wholesale"
                                                        {{ ((isset($order) && $order->customer->type=='Wholesale') || session()->has('customer') && session('customer')->type == 'Wholesale') ? 'selected' : '' }}>
                                                        WholeSale</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <div class="form-group">
                                                    <select onchange="$('.customer').val($(this).val())"
                                                        name="customer_id" id="customer_record"
                                                        class="form-control select2-single">
                                                        @if(session()->has('customer'))
                                                            <option value="{{ session('customer')->id }}">{{ session('customer')->code }} - {{ session('customer')->name }}</option>
                                                        @endif
                                                        @if (isset($order))
                                                            <option value="{{ $order->customer->id }}" @if(session()->has('customer') && session('customer')->id == $order->customer?->id) selected @endif>
                                                                {{ $order->customer->code }} - {{ $order->customer->name }}</option>
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
                                                <button type="submit" class="btn btn-sm btn-info float-md-right ml-3">Create Invoice</button>
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
                                <div class="card-body" id="load">
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
                                                    <td>{{ ucwords($store->store) }}</td>
                                                    <td>{{ $store->code }}</td>
                                                    <td>{{ $store->name }}</td>
                                                    <td>{{ $store->unit }}</td>
                                                    <td align="center">
                                                        <form action="{{ route('ajax.cart.add') }}" method="POST" class="addCartItemForm">
                                                            @csrf
                                                            <input type="hidden" name="customer" class="customer" value="">
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

                                                            <button type="submit" class="btn btn-sm btn-success px-2">
                                                                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                                                            </button>
                                                        </form>

                                                    </td>
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
                    <div class="col-md-7 ">

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

       

     

        

        // $('.quantity,.price').keyup(function() {
        //     id = $(this).attr('data-value');
            
        //     $("#valid_qty" + id.substr(1)).html("");
        //     delay(function() {

        //         $.ajax({
        //             url: $('#' + id).attr('action'),
        //             type: $('#' + id).attr('method'),
        //             //dataType: 'json',
        //             data: $('#' + id).serialize(),
        //             success: function(data) {
        //                 id = id.substr(1);

        //                 subtotal = $('#price' + id).val() * $('#quantity' + id).val();
        //                 $('.subtotal' + id).text(formatMoney(subtotal));
        //                 $('#total').text(formatMoney(data));
        //                 $('#subtotal').text(formatMoney(data));
        //             },
        //             error: function(xhr, err) {
        //                 //$('#total').text(formatMoney(data));
        //                 //$('#subtotal').text(formatMoney(data));
        //             }
        //         });

        //     }, 500);
        // });
    </script>
@endpush
