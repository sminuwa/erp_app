@extends('layouts.backend.app')
@section('title', 'Purchase Panel')

@push('css')
@endpush

@section('content')
    <input name="cart_page_type" type="hidden" value="request">

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Purchase (Request)</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Purchses</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a class="btn btn-secondary btn-sm" href="{{ route('purchases.request.index') }}">
                    <span class="fa fa-list"> </span> Request List
                </a>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                Purchase Details
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"></h5>
                                <form action="{{ isset($route) ? $route : route('purchases.request.store') }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label for="supplier_id">Supplier Name</label>
                                                <select
                                                    class="form-control select2-single {{ $errors->has('supplier_id') ? ' is-invalid' : '' }}"
                                                    name="supplier_id" id="supplier_id" required="required">
                                                    <option value="">Select...</option>
                                                    @if (isset($suppliers))
                                                        @foreach ($suppliers as $data)
                                                            <option value="{{ $data->id }}"
                                                                {{ $data->id == $model->supplier_id ? 'selected' : '' }}>
                                                                {{ $data->code }}-{{ $data->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="invoice">Reference No</label>
                                                <input type="hidden"
                                                       class="form-control {{ $errors->has('old_invoice') ? ' is-invalid' : '' }}"
                                                       name="old_invoice" id="old_invoice" value="{{ old('old_invoice', $model->invoice) }}">
                                                <input type="text" class="form-control {{ $errors->has('invoice') ? ' is-invalid' : '' }}"
                                                       name="invoice" id="invoice" value="{{ old('invoice', $model->invoice) }}"
                                                       placeholder="" maxlength="191" required="required">
                                                @if ($errors->has('invoice'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('invoice') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                <input type="hidden" name="updated_by" value="{{ Auth::id() }}" />
                                <div class="form-group text-right ">
                                    <input type="submit" class="btn btn-primary" value="Save" />
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="ion-android-cart"></i> Request Cart: <small>Purchased Products</small>
                                <div class="float-right">
                                    <a href="javascript:void(0)" data-toggle="modal"
                                       data-target="#add_product_form"
                                       class="btn btn-sm btn-secondary float-md-right"
                                       style="margin-left: 2px;"><i class="fa fa-plus"></i> Add Product </a>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <?php $total_price = 0; ?>
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
                        <input type="hidden" name="purchase_id" value="{{ $model->id }}" />
                        <input type="hidden" name="type" value="{{ $type }}" />
                        @csrf
                        {{-- <div class="form-group">
                            <label for="category_id">Category</label>
                            <select type="number"
                                class="form-control select2-single ajax-categories  {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
                                name="category_id" id="category_id" required="required"></select>
                            @if ($errors->has('category_id'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('category_id') }}</strong>
                                </div>
                            @endif
                        </div> --}}


                        <div class="form-group">
                            <label for="product_id">Product Name</label>
                            <select
                                class="form-control select2-single ajax-products {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                                name="product_id" id="product_id" required="required">
                                <option value="">Select...</option>
                                @if (isset($products))
                                    @if (old('category_id', $model->category_id))
                                        @foreach (\App\Models\Product::where('category_id', old('category_id'))->get() as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                                {{ $data->name }}</option>
                                        @endforeach
                                    @else
                                        @foreach ($products as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                                {{ $data->name }}</option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="qty_supplied">Quantity</label>
                            <input type="number"
                                   class="form-control {{ $errors->has('qty_supplied') ? ' is-invalid' : '' }}"
                                   name="qty_supplied" id="qty_supplied" placeholder="" required="required">
                            @if ($errors->has('qty_supplied'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('qty_supplied') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="unit_price">Cost Price</label>
                            <input type="text"
                                   class="form-control {{ $errors->has('unit_price') ? ' is-invalid' : '' }}"
                                   name="unit_price" id="unit_price" placeholder="Optional">
                            @if ($errors->has('unit_price'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('unit_price') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group text-right ">
                            <button type="submit" class="btn btn-primary"><span class="ion-android-cart"> </span>Add to
                                Cart</button>
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
            $("#category_link,#product_link,#supplier_link").on('click', function() {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'shortcut',
                    name: 'shortcut'
                }).appendTo('form');
            });
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
