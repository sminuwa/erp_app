@extends('layouts.backend.app')

@section('title', 'Report')

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
                        <h4>Interstore Stock Transfer Reports</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Stock Transfer</li>
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
                                &nbsp;&nbsp;
                                <label for="company_id">Company</label>
                                <select class="form-control select2-single ajax-companies {{ $errors->has('company_id') ? ' is-invalid' : '' }}"
                                    name="company_id" id="company_id" required>

                                </select>
                            </div>
                            <div class="form-group">
                                &nbsp;&nbsp;
                                <label for="branch_id">Branch</label>
                                <select
                                    class="form-control select2-single ajax-branches {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                    name="branch_id" id="branch_id">

                                </select>
                            </div>

                            <div class="form-group">
                                &nbsp;&nbsp;
                                <label for="source_store_id">From Store</label>
                                <select
                                    class="form-control select2-single ajax-stores {{ $errors->has('source_store_id') ? ' is-invalid' : '' }}"
                                    name="source_store_id" id="source_store_id">

                                </select>
                            </div>
                            <div class="form-group">
                                &nbsp;&nbsp;
                                <label for="destination_store_id">To Store</label>
                                <select
                                    class="form-control select2-single ajax-stores {{ $errors->has('destination_store_id') ? ' is-invalid' : '' }}"
                                    name="destination_store_id" id="destination_store_id">

                                </select>
                            </div>
                            <div class="form-group">
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
                            <div class="form-group">
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
                            <div class="form-group text-right ">
                                <input type="button" class="btn btn-primary" id="generate" name="generate"
                                    value="Generate" />
                            </div>

                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 table-responsive" id="load">

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

            $('#generate').on("click", function() {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                product_id = $('#product_id').val();
                company_id = $('#company_id').val();
                branch_id = $('#branch_id').val();
                source_store_id = $('#source_store_id').val();
                destination_store_id = $('#destination_store_id').val();
                category_id = $('#category_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.interstore.transfer.reports') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        product_id: product_id,
                        company_id: company_id,
                        branch_id: branch_id,
                        source_store_id: source_store_id,
                        destination_store_id: destination_store_id,
                        category_id: category_id
                    }
                }).done(function(data) {
                    $("#load").html(data);
                    loadDataTable2()
                });
            });
        });
    </script>
@endpush
