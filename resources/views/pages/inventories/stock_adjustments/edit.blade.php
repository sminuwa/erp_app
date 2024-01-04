@extends('layouts.backend.app')

@section('title', 'Stock Transfer')

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
                        <h4>Stock Adjustment</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Stock Adjustment</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('stock_adjustments.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.create') }}">
                    <span class="fa fa-plus-circle"></span>
                </a>
            @endcan
            @can('stock_adjustments.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.index') }}">
                    <span class="fa fa-list"></span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <div class='card'>

                            <div class="card-body">
                                @include('forms.stock_adjustment', [
                                    'route' => route('stock_adjustments.update', $model->id),
                                    'method' => 'PUT',
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="ion-android-cart"></i> Stock Adjustment Cart
                            </div>
                            <div class="card-body table-responsive">
                                @if (count($adjusment_products) < 1)
                                    <div class="alert alert-danger">
                                        No Product Added
                                    </div>
                                @else
                                    <table class="table table-bordered table-striped text-center mb-3">
                                        <thead>
                                            <tr>
                                                <th>S.N</th>
                                                <th>Name</th>
                                                <th>Qty</th>
                                                <th>Store</th>
                                                <th><span class="ion-refresh"></span></th>
                                                <th><span class="ion-ios-trash"></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @can('stock_adjustments.cart.update')
                                                @foreach ($adjusment_products as $product)
                                                    <tr>
                                                        <form action="{{ route('stock_adjustments.cart.update') }}"
                                                            method="post">
                                                            @csrf
                                                            @method('PUT')
                                                            @php $attr = $product->attributes @endphp
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td class="text-left">{{ $product->name }}</td>
                                                            <input type="hidden" name="id" class="form-control input-sm"
                                                                value="{{ $product->id }}" />
                                                            <input type="hidden" name="store_id" class="form-control input-sm"
                                                                value="{{ $attr['store_id'] }}" />
                                                            <input type="hidden" name="product_id"
                                                                class="form-control input-sm"
                                                                value="{{ $attr['product_id'] }}" />
                                                            <input type="hidden" name="available_qty"
                                                                class="form-control input-sm"
                                                                value="{{ $attr['available_qty'] }}" />

                                                            <td>

                                                                @if ($attr['sign'] == '-')
                                                                    <input type="number" name="quantity" id="quantity" step=".01"
                                                                        class="form-control input-sm"
                                                                        value="{{ 0 - $product->quantity }}" />
                                                                @else
                                                                    <input type="number" name="quantity" id="quantity" step=".01"
                                                                        class="form-control input-sm"
                                                                        value="{{ $product->quantity }}" />
                                                                @endif

                                                            </td>
                                                            <td>{{ \App\Models\Store::find($attr['store_id'])->name }}</td>
                                                            </td>
                                                            <td>
                                                                <button type="submit" class="btn btn-sm btn-success">
                                                                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                                </button>
                                                            </td>
                                                        </form>
                                                        <td>
                                                            <button class="btn btn-danger btn-sm" type="button"
                                                                onclick="deleteItem({{ $product->id }})">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </button>
                                                            <form id="delete-form-{{ $product->id }}"
                                                                action="{{ route('stock_adjustments.cart.remove', $product->id) }}"
                                                                method="post" style="display:none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endcan
                                        </tbody>
                                    </table>
                                @endif
                                <form action="{{ isset($route) ? $route : route('stock_adjustments.update', $model->id) }}"
                                    method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'PUT' }}" />
                                    <div class="form-group">
                                        <label for="refno">Stock Adjustement ID</label>
                                        <input type="text"
                                            class="form-control {{ $errors->has('refno') ? ' is-invalid' : '' }}"
                                            name="refno" id="refno"
                                            value="{{ old('refno', $model->refno) == null ? $refno : old('refno', $model->refno) }}"
                                            placeholder="" maxlength="15" required="required">
                                        @if ($errors->has('refno'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('refno') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <div class="input-group">
                                            <input type="text" autocomplete="off"
                                                class="form-control datepicker {{ $errors->has('date') ? ' is-invalid' : '' }}"
                                                name="date" id="date"
                                                value="{{ old('date', $model->date) != null ? old('date', $model->date) : date('Y-m-d') }}"
                                                placeholder="" required="required">
                                            <div class="input-group-addon">
                                                <label for="date" class="fa fa-calendar">
                                                </label>
                                            </div>
                                        </div>
                                        @if ($errors->has('date'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('date') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="adjusted_by" id="adjusted_by"
                                            value="{{ Auth::id() }}" required="required">
                                    </div>
                                    <div class="form-group text-right ">
                                        <button type="submit" class="btn btn-success"><span class="ui-icon-clock">
                                                Adjust</span></button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection

@push('js')
    <script type="text/javascript">
        <!-- DataTables 
        -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/backend/plugins/fastclick/fastclick.js') }}"></script>

    <!-- Sweet Alert Js -->
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $(document).on("change", "#category_id", function(event) {
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
