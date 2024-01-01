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
                        <h4>Intersite Stock Transfer</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Stock Transfer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('intersite.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('intersite.create') }}">
                    <span class="fa fa-plus-circle"></span>
                </a>
            @endcan

            @can('intersite.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('intersite.index') }}">
                    <span class="fa fa-list"></span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <div class='card'>

                            <div class="card-body">
                                @include('forms.intersite_transfer', [
                                    'route' => route('intersite.update', $model->id),
                                    'method' => 'PUT',
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="ion-android-cart"></i> Transfer Product Cart
                            </div>
                            <div class="card-body table-responsive">
                                @if (count($transfer_products) < 1)
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
                                                <th>Source Store</th>
                                                <th><span class="ion-ios-trash"></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transfer_products as $product)
                                                <tr>
                                                    @php $attr = $product->attributes @endphp
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="text-left">{{ $product->name }}</td>
                                                    <td>{{ number_format($product->quantity, 0, '', ',') }}</td>
                                                    <td>{{ \App\Models\Store::find($attr['store_id'])->name }}</td>


                                                    <td>
                                                        @can('interstore.cart.remove')
                                                            <button class="btn btn-danger btn-sm" type="button"
                                                                onclick="deleteItem({{ $product->id }})">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </button>
                                                        @endcan
                                                        <form id="delete-form-{{ $product->id }}"
                                                            action="{{ route('interstore.cart.remove', $product->id) }}"
                                                            method="post" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                                <form action="{{ isset($route) ? $route : route('intersite.update', $model->id) }}"
                                    method="POST">
                                    {{ csrf_field() }}
                                    {{-- <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" /> --}}
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="transfer_branch_id">Destination Branch</label>
                                        <select
                                            class="form-control select2-single {{ $errors->has('transfer_branch_id') ? ' is-invalid' : '' }}"
                                            name="transfer_branch_id" id="transfer_branch_id" required="required">
                                            <option value="">Select...</option>
                                            @if (isset($branches))
                                                @foreach ($branches as $data)
                                                    <option value="{{ $data->id }}"
                                                        {{ $data->id == old('transfer_branch_id', $model->transfer_branch_id) ? 'selected' : '' }}>
                                                        {{ $data->code }}-{{ $data->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('transfer_branch_id'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('transfer_branch_id') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="vehicle_no">Vehicle No</label>
                                        <input type="text"
                                            class="form-control {{ $errors->has('vehicle_no') ? ' is-invalid' : '' }}"
                                            name="vehicle_no" id="vehicle_no"
                                            value="{{ old('vehicle_no', $model->vehicle_no) }}" placeholder="Vehicle No">
                                        @if ($errors->has('vehicle_no'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('vehicle_no') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="transfer_date">Date</label>
                                        <input type="text" name="transfer_date" class="form-control datepicker"
                                            value="{{ isset($model->transfer_date) ? $model->transfer_date : old('transfer_date', date('Y-m-d')) }}" />
                                    </div>
                                    @if (isset($model))
                                        <div class="form-check">
                                            <input
                                                class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}"
                                                type="radio" value="Completed" name="status" id="status_yes"
                                                {{ isset($model) && $model->status == 'Completed' ? 'checked' : '' }}>
                                            Completed
                                            &nbsp;&nbsp;
                                            &nbsp;&nbsp;
                                            {{-- <input
                                                class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}"
                                                type="radio" value="Cancelled" name="status" id="status_no"
                                                {{ isset($model) && $model->status == 'Cancelled' ? 'checked' : '' }}>Cancelled
                                            @if ($errors->has('status'))
                                                <div class="invalid-feedback">
                                                    <strong>{{ $errors->first('status') }}</strong>
                                                </div>
                                            @endif --}}
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <input type="hidden" name="transfered_by" id="transfered_by"
                                            value="{{ Auth::id() }}" required="required">
                                    </div>
                                    @can('intersite.update')
                                        <div class="form-group text-right ">
                                            <button type="submit" class="btn btn-success"><span class="ion-forward">
                                                    Transfer</span></button>
                                        </div>
                                    @endcan
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
                    $('#quantity_requested').attr('max', msg);
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
