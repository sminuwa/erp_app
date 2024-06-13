@extends('layouts.backend.app')
@section('title', 'Manage Prices')

@push('css')
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
                        <h4>SMS Notification</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Product Price</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <form action="{{ route('notification.send') }}" method="POST">
                            @csrf
                            <div class="form-check">
                                <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}"
                                    type="radio" value="1" class="option" name="option" id="general1"
                                    checked="checked">
                                General
                                &nbsp;&nbsp;
                                &nbsp;&nbsp;
                                <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}"
                                    type="radio" value="0" class="option" name="option" id="general2">Price
                                @if ($errors->has('status'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div id="price_div" style="display: none;">
                                <div class="form-group">
                                    <label for="store_id">Branch</label>
                                    <select type="number"
                                        class="form-control {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                        name="branch_id" id="branch_id">
                                        <option value="">Select...</option>
                                        @if (isset($branches))
                                            @foreach ($branches as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('branch_id'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('branch_id') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="store_id">Store</label>
                                    <select type="number"
                                        class="form-control {{ $errors->has('store_id') ? ' is-invalid' : '' }}"
                                        name="store_id" id="store_id">
                                    </select>
                                    @if ($errors->has('store_id'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('store_id') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="category_id">Category</label>
                                    <select class="form-control {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
                                        name="category_id" id="category_id">
                                        <option value="">Select...</option>
                                        @if (isset($categories))
                                            @foreach ($categories as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('category_id'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('category_id') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="product_id">Product</label>
                                    <select class="form-control {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                                        name="product_id" id="product_id">
                                        <option value="">Select...</option>
                                        @if (isset($products))
                                            @foreach ($products as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('product_id'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('product_id') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="text">Notification Message</label>
                                <textarea rows="5" cols="50" class="form-control text" style="text-align: left !important;" name="text"
                                    id="text" onkeyup="countChar(this)">Kindly note the price of product has been changed from &#8358;xx to &#8358;yy. Thank You.</textarea>

                                <div id="charNum" class="text text-danger">

                                </div>

                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="1" class="send_option"
                                    name="send_option" checked="checked">
                                Customer
                                &nbsp;&nbsp;
                                &nbsp;&nbsp;
                                <input class="form-check-input" type="radio" value="0" class="send_option"
                                    name="send_option">Other
                            </div>
                            <div class="form-group" id="customer_pick">
                                <label for="customer_id">Customer</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('customer_id') ? ' is-invalid' : '' }}"
                                    name="customer_id" id="customer_id">
                                    <option value="">Select...</option>
                                    <option value="all">Send to All</option>
                                    @if (isset($customers))
                                        @foreach ($customers as $data)
                                            <option value="{{ $data->id }}">
                                                {{ $data->name }}-{{ $data->phone }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group text-right" id="customer_type" style="display: none">
                                <input type="text" class="form-control" name="phone" placeholder="Phone number" />
                            </div>
                            <div class="form-group text-right ">
                                <input type="submit" class="btn btn-primary" value="Save" />
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection
@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
{{--    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>--}}
    <script type="text/javascript">
        $(function() {
            $(document).on("change", "#category_id", function(event) {
                $("#product_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.loadproducts') }}",
                    type: 'GET',
                    data: {
                        category_id: $("#category_id").val()
                    }
                }).done(function(msg) {
                    $("#product_id").html("<option value=''>--select--</option>" + msg);
                });
            });
            $(document).on("change", "#store_id,#product_id", function(event) {
                store_id = $('#store_id').val();
                product_id = $('#product_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.product.price') }}",
                    data: {
                        store_id: store_id,
                        product_id: product_id
                    }
                }).done(function(data) {
                    $("#cost_price").val(data);
                });
            });
            $(document).on("change", "#branch_id", function(event) {
                branch_id = $('#branch_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.loadStores') }}",
                    data: {
                        branch_id: branch_id
                    }
                }).done(function(data) {
                    $("#store_id").html("<option value='all'>All stores</option>" + data);
                });
            });
            $('input[type=radio][name=option]').change(function() {
                if (this.value == 0) {
                    $('#price_div').css("display", "block");
                } else if (this.value == 1) {
                    $('#price_div').css("display", "none");
                }
            });
            $('input[type=radio][name=send_option]').change(function() {
                if (this.value == 0) {
                    $('#customer_type').css("display", "block");
                    $('#customer_pick').css("display", "none");

                } else if (this.value == 1) {
                    $('#customer_type').css("display", "none");
                    $('#customer_pick').css("display", "block");

                }
            });
            product_name = "";
            old_price = "";
            new_price = "";
            $(document).on("change", "#product_id", function(event) {
                text = $("#product_id option:selected").text();
                if (product_name.length == 0)
                    sms = $('#text').text().replace("product", text);
                else
                    sms = $('#text').text().replace(product_name, text);
                $('#text').text(sms);
                product_name = text;
            });
            $(document).on("change", "#store_id,#product_id", function(event) {
                store_id = $('#store_id').val();
                product_id = $('#product_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.selling.price') }}",
                    data: {
                        store_id: store_id,
                        product_id: product_id
                    }
                }).done(function(data) {
                    prices = data.split(',');
                    if (old_price.length == 0) {
                        sms = $('#text').text().replace('xx', prices[0] == 0 ? 'xx' : prices[0]);
                        $('#text').text(sms);
                        sms = $('#text').text().replace('yy', prices[1] == 0 ? 'yy' : prices[1]);
                        $('#text').text(sms);

                    } else {
                        sms = $('#text').text().replace(old_price, prices[0] == 0 ? 'xx' : prices[
                            0]);
                        $('#text').text(sms);
                        sms = $('#text').text().replace(new_price, prices[1] == 0 ? 'yy' : prices[
                            1]);
                        $('#text').text(sms);
                    }
                    $('#text').text(sms);
                    old_price = prices[0] == 0 ? 'xx' : prices[0];
                    new_price = prices[1] == 0 ? 'yy' : prices[1];
                });
            });
        });

        function countChar(val) {
            var len = val.value.length;
            $('#charNum').text(len);
            //}
        };
    </script>
@endpush
