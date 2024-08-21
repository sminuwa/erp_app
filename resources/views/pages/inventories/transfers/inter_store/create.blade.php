@extends('layouts.backend.app')
@section('title', 'Stock Tranfer')

@push('css')
@endpush

@section('content')
    <input name="cart_page_type" type="hidden" value="interstore">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Interstore Stock Transfer</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Stock Transfer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a class="btn btn-secondary btn-sm" href="{{ route('interstore.index') }}">
                    <span class="fa fa-list"></span> Interstore List
                </a>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="ion-android-cart"></i> Transfer Product Cart
                                <div class="float-right">
{{--                                    @can('ajax.cart.add')--}}
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#add_product_form"
                                            class="btn btn-sm btn-secondary float-md-right" style="margin-left: 2px;"><i
                                                class="fa fa-plus"></i> Add Product </a>
{{--                                    @endcan--}}
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ isset($route) ? $route : route('interstore.store') }}" method="POST">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="text" name="date" class="form-control datepicker" required
                                            value="{{ isset($model->date) ? $model->date : old('date', date('Y-m-d')) }}" />
                                    </div>
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />

                                    @if (isset($model) && $model->status != null)
                                        <div class="form-check">
                                            <input
                                                class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}"
                                                type="radio" value="Completed" name="status" id="status_yes"
                                                {{ isset($model) && $model->status == 'Completed' ? 'checked' : '' }}>
                                            Completed
                                            &nbsp;&nbsp;
                                            &nbsp;&nbsp;
                                            <input
                                                class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}"
                                                type="radio" value="Cancelled" name="status" id="status_no"
                                                {{ isset($model) && $model->status == 'Cancelled' ? 'checked' : '' }}>Cancelled
                                            @if ($errors->has('status'))
                                                <div class="invalid-feedback">
                                                    <strong>{{ $errors->first('status') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <input type="hidden" name="transfered_by" id="transfered_by"
                                            value="{{ Auth::id() }}" required="required">
                                    </div>
                                    <div class="form-group text-right ">
{{--                                        @can('interstore.store')--}}
                                            <button type="submit" class="btn btn-success"><span
                                                    class="ion-forward">Transfer</span></button>
{{--                                        @endcan--}}
                                    </div>
                                </form>
                                <div class="cart-container"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

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
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="source_store_id">Source Store</label>
                            <select
                                class="form-control select2-single {{ $errors->has('source_store_id') ? ' is-invalid' : '' }}"
                                name="source_store_id" id="source_store_id" required="required">
                                <option value="">Select...</option>
                                @if (isset($stores))
                                    @foreach ($stores as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $data->id == old('source_store_id', $model->source_store_id) ? 'selected' : '' }}>
                                            {{ $data->code }}-{{ $data->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($errors->has('source_store_id'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('source_store_id') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select
                                class="form-control select2-single  ajax-store-products  {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                                name="product_id" id="product_id" required="required">
                                <option value="">Select...</option>

                                @if (old('category_id', $model->category_id))
                                    @foreach (\App\Models\Product::where('category_id', old('category_id'))->get() as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                            {{ $data->code }}-{{ $data->name }}</option>
                                    @endforeach
                                @else
                                    @if (isset($products))
                                        @foreach ($products as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                                {{ $data->code }}-{{ $data->name }}</option>
                                        @endforeach
                                    @endif
                                @endif

                            </select>
                            <div class="input-group-prepend">
                                <p class="text text-danger" id="available"></p>
                            </div>
                            @if ($errors->has('product_id'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('product_id') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="destination_store_id">Destination Store</label>
                            <select
                                class="form-control select2-single {{ $errors->has('destination_store_id') ? ' is-invalid' : '' }}"
                                name="destination_store_id" id="destination_store_id" required="required">
                                <option value="">Select...</option>
                                @if (isset($stores))
                                    @foreach ($stores as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $data->id == old('destination_store_id', $model->destination_store_id) ? 'selected' : '' }}>
                                            {{ $data->code }}-{{ $data->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($errors->has('destination_store_id'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('destination_store_id') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="qty_transfered">Qty</label>
                            <input type="number" step=".000001"
                                class="form-control {{ $errors->has('qty_transfered') ? ' is-invalid' : '' }}"
                                name="qty_transfered" id="qty_transfered" value="{{ $model->qty_transfered }}"
                                placeholder="" required="required">
                            @if ($errors->has('qty_transfered'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('qty_transfered') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group text-right ">
                            <button type="submit" class="btn btn-primary"><span class="ion-ios-cart-outline"></span> Add
                                to Cart</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $(document).on("change", "#category_id,#source_store_id", function(event) {
                $("#product_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.load.available.products') }}",
                    type: 'GET',
                    data: {
                        category_id: $("#category_id").val(),
                        store_id: $("#source_store_id").val()
                    }
                }).done(function(msg) {

                    $("#product_id").html("<option value=''>--select--</option>" + msg);
                });
            });

            $(document).on("change", "#product_id,#source_store_id", function(event) {
                $.ajax({
                    url: "{{ route('ajax.load.quantity.available') }}",
                    type: 'GET',
                    data: {
                        product_id: $("#product_id").val(),
                        store_id: $("#source_store_id").val()
                    }
                }).done(function(msg) {
                    if (msg == null || msg == 0)
                        msg = 0;
                    if (msg != 0)
                        $("#available").html(
                            "<span class='ion-alert-circled'></span> Available Quantity: " + msg);
                    $('#qty_transfered').attr('max', msg);
                });
            });
        });

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
    </script>
@endpush
