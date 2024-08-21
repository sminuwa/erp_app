@extends('layouts.backend.app')

@section('title', 'Sales Report')

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
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Sales Transactions</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Sales Report</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <form method="POST" class="form-inline">

                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input type="text" autocomplete="off"
                                    class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                    name="from_date" id="from_date" value="{{ old('from_date') }}" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="to_date">To Date</label>
                                <input type="text" autocomplete="off"
                                    class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                    name="to_date" id="to_date" value="{{ old('to_date') }}" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="branch_id">Branch</label>
                                <select class="form-control select2-single ajax-branches" name="branch_id" id="branch_id">
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="store_id">Store</label>
                                <select class="form-control select2-single ajax-stores" name="store_id" id="store_id">
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control select2-single ajax-categories" name="category_id" id="category_id">
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="product_id">Product</label>
                                <select class="form-control select2-single ajax-products" name="product_id" id="product_id">
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="type">Customer Type</label>
                                <select class="form-control type" id="type" name="type">
                                    <option value="all">Select...</option>
                                    <option value="Retail">Retail</option>
                                    <option value="Wholesale">Wholesale</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="customer_id">Customers</label>
                                <select class="form-control select2-single ajax-customers" name="customer_id" id="customer_id">
                                </select>
                            </div>
                            <div class="form-group text-right ">
                                <input type="button" class="btn btn-primary" id="generate" name="generate"
                                    value="Generate" />
                            </div>

                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 table-responsive" id="load">
                        <img src="{{ asset('assets/backend/img/loader.png') }}"
                            style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection

@push('js')
    <!-- Sweet Alert Js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.1/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
        $(function() {
            function formatMoney(n, c, d, t) {
                var c = isNaN(c = Math.abs(c)) ? 0 : c,
                    d = d == undefined ? "." : d,
                    t = t == undefined ? "," : t,
                    s = n < 0 ? "-" : "",
                    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ?
                    d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };



            $('.type').on("change", function() {
                type = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customers') }}",
                    data: {
                        type: type
                    }
                }).done(function(data) {
                    $("#customer_id").html(data);
                });
            });

            $('#generate').on("click", function() {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                branch_id = $('#branch_id').val();
                product_id = $('#product_id').val();
                store_id = $('#store_id').val();
                category_id = $('#category_id').val();
                customer_id = $('#customer_id').val();
                type = $('#type').val();
                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.general.sales.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        branch_id: branch_id,
                        product_id: product_id,
                        store_id: store_id,
                        category_id: category_id,
                        customer_id: customer_id,
                        type: type
                    }
                }).done(function(data) {
                    $('#img-loader').hide();
                    $("#load").html(data);
                    loadDataTable()
                });
            });
        });
    </script>
@endpush
