@extends('layouts.backend.app')

@section('title', 'Current Stock Report')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
    <style>
        caption {
            caption-side: top;
        }
    </style>
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Current Stock Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Current Stock Report</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="branch_id">Branch</label>
                                <select class="form-control select2-single ajax-branches"
                                        name="branch_id"
                                        id="branch_id">
                                </select>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="store_id">Store</label>
                                <select class="form-control select2-single ajax-stores"
                                        name="store_id"
                                        id="store_id">
                                </select>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control select2-multiple ajax-categories"
                                        name="category_id[]"
                                        id="category_id" multiple>
                                </select>

                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="form-group">
                                <label for="product_id">Product</label>
                                <select class="form-control select2-single ajax-products"
                                        name="product_id"
                                        id="product_id">
                                </select>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group text-right">
                                <label for=""></label>
                                <input type="button" class="btn btn-primary" id="generate" name="generate" value="Generate" />
                            </div>
                        </div>
                    </div>

                </form>


                <div class="row">
                    <div class="col-sm-12" id="load">
                        <img src="{{ asset('assets/backend/img/loader.png') }}" style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
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
        $(function() {

            function formatMoney(n, c, d, t) {
                var c = isNaN(c = Math.abs(c)) ? 2 : c,
                    d = d == undefined ? "." : d,
                    t = t == undefined ? "," : t,
                    s = n < 0 ? "-" : "",
                    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ?
                    d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };

            $('#generate').on("click", function() {

                branch_id = $('#branch_id').val();
                store_id = $('#store_id').val();
                category_id = $('#category_id').val();
                product_id = $('#product_id').val();
                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.current.stock.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        branch_id: branch_id,
                        store_id: store_id,
                        category_id: category_id,
                        product_id: product_id
                    }
                }).done(function(data) {
                    console.log(data)
                    $('#img-loader').hide();
                    $("#load").html(data);
                    loadDataTable()
                });
            });
        });
    </script>
@endpush
