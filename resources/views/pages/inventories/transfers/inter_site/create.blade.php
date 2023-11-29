@extends('layouts.backend.app')
@section('title', 'Intersite Tranfer')

@push('css')
@endpush

@section('content')
    <input name="cart_page_type" type="hidden" value="intersite">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Intersite Transfer</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Stock Transfer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a class="btn btn-secondary btn-sm" href="{{ route('intersite.index') }}">
                    <span class="fa fa-list"></span> Intersite List
                </a>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="ion-android-cart"></i> Transfer Product Cart
                                <div class="float-right">
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#add_product_form"
                                        class="btn btn-sm btn-secondary float-md-right" style="margin-left: 2px;"><i
                                            class="fa fa-plus"></i> Add Product </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('intersite.store') }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="intersite_id"
                                        value="{{ isset($instersite) ? $intersite->id : '' }}" />
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_date">Date</label>
                                                <input type="text" name="date" class="form-control datepicker"
                                                    value="{{ isset($intersite->date) ? $intersite->date : old('date', date('Y-m-d')) }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_branch_id">Destination Branch</label>
                                                <select
                                                    class="form-control select2-single {{ $errors->has('transfer_branch_id') ? ' is-invalid' : '' }}"
                                                    name="transfer_branch_id" id="transfer_branch_id" required="required">
                                                    <option value="">Select...</option>
                                                    @if (isset($branches))
                                                        @foreach ($branches as $data)
                                                            <option value="{{ $data->id }}"
                                                                {{ $data->id == old('transfer_branch_id', $model->destination_branch_id) ? 'selected' : '' }}>
                                                                {{ $data->code }} - {{ $data->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if ($errors->has('transfer_branch_id'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('transfer_branch_id') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="vehicle_no">Vehicle No</label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('vehicle_no') ? ' is-invalid' : '' }}"
                                                    name="vehicle_no" id="vehicle_no"
                                                    value="{{ old('vehicle_no', $model->vehicle_no) }}"
                                                    placeholder="Vehicle No">
                                                @if ($errors->has('vehicle_no'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('vehicle_no') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group text-right ">
                                        <button type="submit" class="btn btn-success">
                                            <span class="ion-forward">Transfer</span>
                                        </button>
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
                            <select class="form-control select2-single {{ $errors->has('store_id') ? ' is-invalid' : '' }}"
                                name="store_id" id="store_id" required="required">
                                <option value="">Select...</option>
                                @if (isset($stores))
                                    @foreach ($stores as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $data->id == old('store_id', $model->store_id) ? 'selected' : '' }}>
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

                                @if (old('category_id', $model->category_id))
                                    @foreach (\App\Models\Product::where('category_id', old('category_id'))->get() as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                            {{ $data->code }}-{{ $data->name }}</option>
                                    @endforeach
                                @else
                                    @if (isset($products))
                                        @foreach ($products as $data)
                                            <option value="{{ $data->id }}" data-value="{{ $data->qty_available }}"
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
                            <label for="quantity">Qty</label>
                            <input type="number"
                                class="form-control {{ $errors->has('quantity') ? ' is-invalid' : '' }}" name="quantity"
                                id="quantity" value="{{ $model->quantity }}" placeholder="" required="required">
                            @if ($errors->has('quantity'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('quantity') }}</strong>
                                </div>
                            @endif
                        </div>
                        <input type="hidden" value="0" name="cost_price" id="cost_price" />
                        <div class="form-group text-right ">
                            <button type="submit" class="btn btn-primary" onclick="validateQuantity()"><span class="ion-ios-cart-outline"></span> Add
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
        function validateQuantity() {
            var selectedOption = $('#product_id').find(':selected');
            var qty_available = selectedOption.attr('data-value');
            var enteredQuantity = $('#quantity').val();

            if (enteredQuantity > qty_available) {
                // Display an error message
                $('#available').text('Quantity exceeds available quantity.');
                return false;
            } else {
                // Reset the error message and submit the form
                $('#available').text('');
                $('#addCartItemForm').submit();
            }
        }
        $(document).ready(function() {
            // Listen for change event on the select element
            $('#product_id').change(function() {
                // Get the selected option
                var selectedOption = $(this).find(':selected');

                // Get the value of the data-value attribute
                var qty_available = selectedOption.attr('data-value');

                // Log or use the dataValue as needed
                $('#quantity').attr('max', qty_available);
                $('#available').html(qty_available);
            });
        });

        function validate() {

        }
        $(function() {
            // $(document).on("change", "#category_id,#source_store_id", function(event) {
            //     $("#product_id").html(" < option value = '' > Loading... < /option>");
            //     $.ajax({
            //         url: "{{ route('ajax.load.available.products') }}",
            //         type: 'GET',
            //         data: {
            //             category_id: $("#category_id").val(),
            //             store_id: $("#source_store_id").val()
            //         }
            //     }).done(function(msg) {

            //         $("#product_id").html("<option value=''>--select--</option>" + msg);
            //     });
            // });

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
            $(document).on("change", "#destination_branch_id", function(event) {
                branch_id = $('#destination_branch_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.loadStores') }}",
                    data: {
                        branch_id: branch_id
                    }
                }).done(function(data) {
                    $("#destination_store_id").html("<option value=''>Select...</option>" + data);
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
