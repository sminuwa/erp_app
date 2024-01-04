@extends('layouts.backend.app')
@section('title', 'Stock Adjustment')

@push('css')
@endpush

@section('content')
    <input name="cart_page_type" type="hidden" value="adjustment">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Stock Adjustment</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Stock Adjustment</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.index') }}">
                    <span class="fa fa-list"></span> List
                </a>
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="ion-android-cart"></i> Stock Adjustment Cart
                                <div class="float-right">
                                    @can('ajax.cart.add')
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#add_product_form"
                                            class="btn btn-sm btn-secondary float-md-right" style="margin-left: 2px;"><i
                                                class="fa fa-plus"></i> Add Product </a>
                                    @endcan

                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ isset($route) ? $route : route('stock_adjustments.store') }}"
                                    method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
                                    <input type="hidden" name="stock_adjustment_id"
                                        value="{{ isset($model->id) ? $model->id : '' }}" />
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date">Date</label>
                                                <div class="input-group">
                                                    <input type="text" autocomplete="off"
                                                        class="form-control datepicker {{ $errors->has('date') ? ' is-invalid' : '' }}"
                                                        name="date" id="date"
                                                        value="{{ old('date', $model->date) != null ? old('date', $model->date) : date('Y-m-d') }}"
                                                        placeholder="" required="required">
                                                </div>
                                                @if ($errors->has('date'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('date') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date">Operation</label>
                                                <div class="input-group">
                                                    <select type="text" onchange="$('.operation').val($(this).val())"
                                                        class="form-control {{ $errors->has('operation') ? ' is-invalid' : '' }}"
                                                        name="operation" id="operation" required>
                                                        <option value="" diabled="disabled">select...</option>
                                                        <option value="in"
                                                            {{ isset($model->id) ? ($model->operation == 'in' ? 'selected' : '') : '' }}>
                                                            In</option>
                                                        <option value="out"
                                                            {{ isset($model->id) ? ($model->operation == 'out' ? 'selected' : '') : '' }}>
                                                            Out</option>

                                                    </select>
                                                </div>
                                                @if ($errors->has('operation'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('operation') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Description</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}"
                                                        name="description" id="description"
                                                        value="{{ old('description', $model->description) != null ? old('description', $model->description) : '' }}"
                                                        placeholder="Descriptionn" required="required">
                                                </div>
                                                @if ($errors->has('description'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('description') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group text-right ">
                                        <button type="submit" class="btn btn-success"><span
                                                class="ui-icon-clock">Adjust</span></button>
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
                        <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
                        <input type="hidden" name="operation" class="operation" required="required">
                        <div class="form-group">
                            <label for="store_id">Store</label>
                            <select
                                class="form-control select2-single {{ $errors->has('store_id') ? ' is-invalid' : '' }}"
                                name="store_id" id="store_id" required="required">
                                <option value="">Select...</option>
                                @if (isset($stores))
                                    @foreach ($stores as $data)
                                        <option value="{{ $data->id }}"
                                            {{ old('store_id') == $data->id ? 'selected' : '' }}>
                                            {{ $data->code }}-{{ $data->name }}</option>
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
                            <label for="product_id">Product</label>
                            <select
                                class="form-control select2-single {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                                name="product_id" id="product_id" required="required">
                                <option value="">Select...</option>
                                @foreach ($products as $data)
                                    <option value="{{ $data->id }}">
                                        {{ $data->code }} - {{ $data->name }}</option>
                                @endforeach
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
                            <label for="quantity">Quantity</label>
                            <input type="number" step=".01"
                                class="form-control {{ $errors->has('quantity') ? ' is-invalid' : '' }}" name="quantity"
                                id="quantity" value="" placeholder="Quantity" required="required"
                                min="1">
                            @if ($errors->has('quantity'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('quantity') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group" id="cost_price" style="display: none;">
                            <label for="cost_price">Cost Price</label>
                            <input type="text" class="form-control" name="cost_price" id="cost_price">
                        </div>
                        <div class="form-group">
                            <label for="quantity">Expiry Date</label>
                            <input type="text" autocomplete="off"
                                class="form-control datepicker {{ $errors->has('expiry_date') ? ' is-invalid' : '' }}"
                                name="expiry_date" id="expiry_date" placeholder="Expiry Date (Optional)">
                            @if ($errors->has('expiry_date'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('expiry_date') }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="form-group text-right ">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-cart-plus"> Add to
                                    Cart</i></button>
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
        function toggleTextField() {
            var selectedOption = $('#operation').val();

            // Toggle the visibility of the text field based on the selected option
            if (selectedOption === 'in') {
                $('#cost_price').show();
            } else {
                $('#cost_price').hide();
            }
        }
        $(function() {
            $('#operation').change(function() {

                toggleTextField();
            });
            toggleTextField();


            $(document).on("change", "#product_id,#store_id", function(event) {
                $("#available_qty").val("");

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
                    if (msg != 0)
                        $("#available_qty").val(msg);
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
