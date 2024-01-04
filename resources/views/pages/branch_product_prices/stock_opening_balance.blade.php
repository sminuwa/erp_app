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
                        <h4>Stock Opening Balance</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Stock Opening Balance</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('products.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('products.create') }}">
                    <span class="fa fa-plus-circle"> Add Product</span>
                </a>
            @endcan
            @can('products.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('products.index') }}">
                    <span class="fa fa-list"> View Products</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <form action="{{ route('stock_opening_balance.store') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="store_id">Store</label>
                                <select type="number"
                                    class="form-control {{ $errors->has('store_id') ? ' is-invalid' : '' }}" name="store_id"
                                    id="store_id" required="required">
                                    <option value="">Select...</option>
                                    @if (isset($stores))
                                        @foreach ($stores as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == $model->store_id ? 'selected' : '' }}>
                                                {{ $data->name }}</option>
                                        @endforeach
                                    @endif
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
                                    name="category_id" id="category_id" required="required">
                                    <option value="">Select...</option>
                                    @if (isset($categories))
                                        @foreach ($categories as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == optional($model->product)->category_id ? 'selected' : '' }}>
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
                                    name="product_id" id="product_id" required="required">
                                    <option value="">Select...</option>
                                    @if (isset($products))
                                        @foreach ($products as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == $model->product_id ? 'selected' : '' }}>
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
                            <div id="available" class="alert text-danger">

                            </div>
                            <div class="form-group">
                                <label for="qty">QTY</label>
                                <input type="number" step=".01" class="form-control {{ $errors->has('qty') ? ' is-invalid' : '' }}"
                                    name="qty" id="qty" value="{{ old('qty', $model->qty) }}" min="0"
                                    placeholder="" required="required">
                                @if ($errors->has('qty'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('qty') }}</strong>
                                    </div>
                                @endif
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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
            $(document).on("change", "#product_id,#store_id", function(event) {
                $.ajax({
                    url: "{{ route('ajax.load.quantity.available') }}",
                    type: 'GET',
                    data: {
                        product_id: $("#product_id").val(),
                        store_id: $("#store_id").val()
                    }
                }).done(function(msg) {
                    if (msg == null || msg == 0)
                        msg = 0;
                    $("#available").html(
                        "<span class='ion-alert-circled'></span>Note that the current balance is <strong>" +
                        msg +
                        "</strong>. Whatever quantity you entered will overwrite the current balance as opening balance"
                    );

                });
            });
        });
    </script>
@endpush
