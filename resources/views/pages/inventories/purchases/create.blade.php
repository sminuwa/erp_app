@extends('layouts.backend.app')
@section('title', 'Purchase Panel')

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
                        <h4>Purchase Panel</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Purchases</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-secondary btn-sm" href="{{ route('inventories.purchases.index') }}">
                            <span class="fa fa-list"></span> List
                        </a>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class='col-md-12'>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="card">
                                    <div class="card-header">
                                        Purchase Details
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"></h5>
                                        <form action="{{ route('inventories.purchases.store') }}" method="POST">
                                            {{ csrf_field() }}

                                            <div class="form-group">
                                                <label for="supplier_id">Supplier Name</label>
                                                <select
                                                    class="form-control ajax-suppliers select2-single {{ $errors->has('supplier_id') ? ' is-invalid' : '' }}"
                                                    name="supplier_id" id="supplier_id" required="required">
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="invoice">Reference</label>
                                                <input type="text" class="form-control" name="reference" id="reference" placeholder="" maxlength="191" required="required">
                                            </div>
                                            <div class="form-group">
                                                <label for="purchase_date">Purchase Date</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                           class="form-control datepicker {{ $errors->has('purchase_date') ? ' is-invalid' : '' }}"
                                                           name="purchase_date" id="purchase_date"
                                                           placeholder="" required="required">
                                                    <?php date_default_timezone_set('Africa/Lagos'); ?>
                                                    <div class="input-group-addon">
                                                        <label for="purchase_date" class="fa fa-calendar">
                                                        </label>
                                                    </div>
                                                </div>
                                                @if ($errors->has('purchase_date'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('purchase_date') }}</strong>
                                                    </div>
                                                @endif
                                            </div>


                                            <div class="form-group">
                                                <label for="vehicle_reg_no">Truck No</label>
                                                <input type="text"
                                                       class="form-control {{ $errors->has('vehicle_reg_no') ? ' is-invalid' : '' }}"
                                                       name="vehicle_reg_no" id="vehicle_reg_no"
                                                       maxlength="191">
                                                @if ($errors->has('vehicle_reg_no'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('vehicle_reg_no') }}</strong>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="source_store_id">Store/Shop</label>
                                                <select
                                                    class="form-control select2-single {{ $errors->has('source_store_id') ? ' is-invalid' : '' }}"
                                                    name="source_store_id" id="source_store_id" required="required">
                                                </select>
                                            </div>
                                            <input type="hidden" name="updated_by" value="{{ Auth::id() }}" />
                                            <div class="form-group text-right ">
                                                <input type="submit" class="btn btn-primary" value="Save" />
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="card">
                                    <div class="card-header">
                                        <i class="ion-android-cart"></i> Supplier Cart: <small>Purchased Products</small>
                                        <div class="div float-right">
                                            <button class="btn btn-primary" data-toggle="modal" id="add-product-btn" data-target="#add-product-modal">
                                                <span class="fa fa-plus-circle"></span>
                                                Add Product
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive">
                                        @if (Cart::getTotal() < 1)
                                            <div class="alert alert-danger">
                                                No Product Added
                                            </div>
                                        @else
                                            <table class="table table-bordered table-striped text-center mb-3">
                                                <thead>
                                                <tr>
                                                    <th>S.N</th>
                                                    <th>Name</th>
                                                    <th>Unit Price</th>
                                                    <th>Qty</th>
                                                    <th>Sub Total</th>
                                                    <th><span class="ion-ios-trash"></span></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($cart_products as $product)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="text-left">{{ $product->name }}</td>


                                                        <form action="{{ route('inventories.purchases.store') }}" method="post"
                                                              id="p{{ $product->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <td>
                                                                <input type="text" name="cost_price" id="price{{ $product->id }}"
                                                                       class="form-control price" style="min-width:65px;"
                                                                       onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                                                       value="{{ $product->price }}" data-val="{{ $product->price }}"
                                                                       data-value="p{{ $product->id }}">
                                                                <input type="hidden" name="selling_price" class="form-control"
                                                                       value="{{ $product->attributes['selling_price'] }}">
                                                                <input type="hidden" name="expire_date" class="form-control"
                                                                       value="{{ $product->attributes['expire_date'] }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="quantity" id="quantity{{ $product->id }}"
                                                                       class="form-control quantity" data-value="p{{ $product->id }}"
                                                                       style="min-width:58px;" value="{{ $product->quantity }}"
                                                                       min="1" required>
                                                            </td>
                                                            <td><span
                                                                    class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
                                                            </td>
                                                            <input type="hidden" name="id" class="form-control"
                                                                   value="{{ $product->id }}">

                                                        </form>

                                                        <td>
                                                            <button class="btn btn-danger btn-sm" type="button"
                                                                    onclick="deleteItem({{ $product->id }})">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </button>
                                                            <form id="delete-form-{{ $product->id }}"
                                                                  action="{{ route('cart.remove', $product->id) }}" method="post"
                                                                  style="display:none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                        <div class="alert alert-success" id="total">
                                            Total : <span id="total">{{ number_format(Cart::getTotal()) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <div class="modal fade" id="add-product-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('purchases.cart.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="product_id">Product Name</label>
                                    <select name="product_id[]" id="product_id" class="form-control select2-single ajax-products" required></select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" class="form-control" name="quantity[]" id="quantity" placeholder="Quantity" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="unit_price">Unit Price</label>
                                    <input type="number" class="form-control" name="unit_price[]" id="unit_price" placeholder="Unit Price" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right ">
                            <button type="submit" class="btn btn-primary"><span class="ion-android-cart"> </span>Add Product</button>
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
