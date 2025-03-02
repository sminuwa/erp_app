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
                        <h4>Manage Product Prices</h4>
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
            @can('branch_product_prices.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('branch_product_prices.create') }}">
                    <span class="fa fa-plus-circle"> New Price</span>
                </a>
            @endcan
            @can('branch_product_prices.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('branch_product_prices.index') }}">
                    <span class="fa fa-list"> View Prices</span>
                </a>
            @endcan
            @can('price.import.form')
                <a class="btn btn-secondary btn-sm" href="{{ route('price.import.form') }}">
                    <span class="fa fa-upload"> Upload Prices</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        @include('forms.branch_product_price')
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
    {{--    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script> --}}
    {{--    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> --}}
    <script type="text/javascript">
        /* $(function() {
                                $(document).on("change", "#branch_id,#category_id", function(event) {
                                    $("#product_id").html(" < option value = '' > Loading... < /option>");
                                    $.ajax({
                                        url: "{{ route('ajax.load.available.products') }}",
                                        type: 'GET',
                                        data: {
                                            category_id: $("#category_id").val(),
                                            branch_id: $("#branch_id").val()
                                        }
                                    }).done(function(msg) {
                                        $("#product_id").html("<option value=''>--select--</option>" + msg);
                                    });
                                });

                                $(document).on("change", "#branch_id,#product_id", function(event) {
                                    branch_id = $('#branch_id').val();
                                    product_id = $('#product_id').val();
                                    $.ajax({
                                        type: "GET",
                                        url: "{{ route('ajax.load.product.selling_price') }}",
                                        data: {
                                            branch_id: branch_id,
                                            product_id: product_id
                                        }
                                    }).done(function(data) {
                                        $("#selling_price").val(data);
                                    });
                                });
                            });*/
    </script>
@endpush
