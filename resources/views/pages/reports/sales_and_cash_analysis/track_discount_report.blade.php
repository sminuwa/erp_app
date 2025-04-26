@extends('layouts.backend.app')

@section('title', 'Track Discount Granted Report')

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
                        <h4>Track Discount Granted Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Discount Granted Report</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form method="POST">
                    <div class="row">
                        <div class="form-group  col-sm-2">
                            <label for="from_date">From Date</label>
                            <input type="text" autocomplete="off"
                                class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                name="from_date" id="from_date" value="{{ old('from_date') }}" placeholder="">
                        </div>
                        <div class="form-group  col-sm-2">
                            <label for="to_date">To Date</label>
                            <input type="text" autocomplete="off"
                                class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                name="to_date" id="to_date" value="{{ old('to_date') }}" placeholder="">
                        </div>
                        <div class="form-group  col-sm-2">
                            &nbsp;&nbsp;
                            <label for="store_id">Store</label>
                            <select class="form-control {{ $errors->has('store_id') ? ' is-invalid' : '' }}"
                                name="store_id" id="store_id">
                                <option value="all">All</option>
                                @foreach ($stores as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group  col-sm-2">
                            &nbsp;&nbsp;
                            <label for="category_id">Category</label>
                            <select class="form-control {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
                                name="category_id" id="category_id">
                                <option value="all">All</option>
                                @foreach ($categories as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group  col-sm-2">
                            &nbsp;&nbsp;
                            <label for="product_id">Product</label>
                            <select
                                class="form-control select2-single {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                                name="product_id" id="product_id">
                                <option value="all">All</option>
                                @foreach ($products as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group  col-sm-4">
                            &nbsp;&nbsp; &nbsp;&nbsp;
                            <input type="radio" name="credit_walkedin" value="all" class="type" checked />
                            &nbsp;&nbsp;All Customers &nbsp;&nbsp; &nbsp;&nbsp;
                            <input type="radio" name="credit_walkedin" value="Credit" class="type" />
                            &nbsp;&nbsp;Credit Customer &nbsp;&nbsp; &nbsp;&nbsp;
                            <input type="radio" name="credit_walkedin" value="Walked In" class="type" />
                            &nbsp;&nbsp;Walked In
                        </div>
                        <div class="form-group  col-sm-2">
                            &nbsp;&nbsp;
                            <label for="customer_id">Customer</label>
                            <select
                                class="form-control select2-single {{ $errors->has('customer_id') ? ' is-invalid' : '' }}"
                                name="customer_id" id="customer_id">
                                <option value="all">All</option>
                            </select>
                        </div>
                        <div class="form-group  col-sm-1">
                            &nbsp;&nbsp;
                            <label for="lower">Range</label>
                            <input type="number" step=".01" name="lower" id="lower" value="1" class="form-control" min="1" placeholder="Min value"/>
                            <input type="number" step=".01" name="upper" id="upper" value="100" class="form-control" min="1" placeholder="Max value"/>
                        </div>
                        <div class="form-group text-right  col-sm-2">
                            <input type="button" class="btn btn-primary" id="generate" name="generate" value="Generate" />
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-10 table-responsive" id="load">
                        <img src="{{ asset('assets/backend/img/loader.png') }}" style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
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

            $('#category_id,#store_id').on("change", function() {
                category_id = $('#category_id').val();
                store_id = $('#store_id').val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.store.products') }}",
                    data: {
                        category_id: category_id,
                        store_id: store_id
                    }
                }).done(function(data) {
                    $("#product_id").html("<option value='all'>All</option>" + data);
                });
            });

            $('.type').on("change", function() {
                type = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customers') }}",
                    data: {
                        type: type
                    }
                }).done(function(data) {
                    $("#customer_id").html("<option value='all'>All</option>" + data);
                });
            });

            $('#generate').on("click", function() {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                product_id = $('#product_id').val();
                store_id = $('#store_id').val();
                category_id = $('#category_id').val();
                lower = $('#lower').val();
                upper = $('#upper').val();
                customer_id = $('#customer_id').val();
                credit_walkedin = $('input[name="credit_walkedin"]:checked').val();
                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.discount.granted.reports') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        product_id: product_id,
                        store_id: store_id,
                        credit_walkedin: credit_walkedin,
                        customer_id: customer_id,
                        category_id: category_id,
                        lower: lower,
                        upper: upper
                    }
                }).done(function(data) {
                    $("#load").html(data);
                    loadDataTable2()
                });
            });
        });
    </script>
@endpush
