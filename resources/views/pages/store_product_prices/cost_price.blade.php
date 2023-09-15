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
                        <h4>Manage Product Cost Prices</h4>
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
            <a class="btn btn-secondary btn-sm" href="{{ route('store_product_prices.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('store_product_prices.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <form action="{{ isset($route) ? $route : route('store_product_cost_prices.store') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
                            <div class="form-group">
                                <label for="store_id">Branch</label>
                                <select type="number"
                                    class="form-control {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                    name="branch_id" id="branch_id" required="required">
                                    <option value="">Select...</option>
                                    @if ($model->store_id != null)
                                        <option value="{{ $model->store->branch_id }}" selected>
                                            {{ $model->store->branch->name }}</option>
                                    @endif
                                    @if (isset($branches))
                                        @foreach ($branches as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == $model->branch_id ? 'selected' : '' }}>
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
                                    name="store_id" id="store_id" required="required">
                                    @if ($model->store_id != null)
                                        <option value="all">All stores</option>
                                        <option value="{{ $model->store_id }}" selected>
                                            {{ $model->store->name }}</option>
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
                            <div class="form-group">
                                <label for="cost_price">Cost Price</label>
                                <input type="text"
                                    class="form-control {{ $errors->has('cost_price') ? ' is-invalid' : '' }}"
                                    name="cost_price" id="cost_price" value="{{ old('cost_price', $model->cost_price) }}"
                                    placeholder="" required="required">
                                @if ($errors->has('cost_price'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('cost_price') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <input type="hidden"
                                    class="form-control {{ $errors->has('updated_by') ? ' is-invalid' : '' }}"
                                    name="updated_by" id="updated_by" value="{{ Auth::id() }}" placeholder=""
                                    required="required">
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
        });
    </script>
@endpush
