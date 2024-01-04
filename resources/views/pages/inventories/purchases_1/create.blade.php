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
                                                <label for="source_store_id">Store</label>
                                                <select
                                                    class="form-control ajax-stores select2-single {{ $errors->has('source_store_id') ? ' is-invalid' : '' }}"
                                                    name="store_id" branch_id="{{ auth()->user()->branch->id }}" id="source_store_id" required="required">
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
                                    <div class="card-body product-details table-responsive">
                                        No product added
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
                    <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="create-form" action="{{ route('inventories.purchases.ajax.create') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="product_id">Product Name</label>
                                    <select name="product_id" id="product_id" class="form-control select2-single ajax-products" required></select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" class="form-control" step=".01" name="quantity" id="quantity" placeholder="Quantity" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="unit_price">Unit Price</label>
                                    <input type="number" step=".01" class="form-control" name="unit_price" id="unit_price" placeholder="Unit Price" required>
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

            let body = $('body');

            body.on('keyup', 'input[name=cost_price], input[name=quantity]', function(){
                /*let element = $(this);
                let item_id = element.attr('item_id')
                let cost = 0; let quantity = 0;
                $('[item_id="'+item_id+'"]').each(function() {
                    if($(this).attr('name') === 'cost_price')
                        cost = $(this).val()
                    if($(this).attr('name') === 'quantity')
                        quantity = $(this).val()
                    console.log($(this).val())
                });
                console.log(cost * quantity)
                $('span[item_id="'+item_id+'"]').html(cost * quantity)*/
                let element = $(this);
                let item_id = element.attr('item_id')
                $('body').on('submit','.itemForm'+item_id, function(e){
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),
                        success: function(response){
                            $('.product-details').html(response)
                            console.log(response)
                        }
                    })
                })
            })


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

            $('body').on('submit','.create-form', function(e){
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    beforeSend: function(){
                        $('.close-modal').trigger('click')
                    },
                    success: function(response){
                        $('.product-details').html(response)
                        console.log(response)
                    }
                })
            })
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
