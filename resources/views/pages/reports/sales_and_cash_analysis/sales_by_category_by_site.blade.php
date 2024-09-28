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
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">

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
            <div class="container">
                <div class="card">
                    <div class="card-header">
                        <h4>Sales by Category by Site</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="from_date">From Date</label>
                                                <input type="text" autocomplete="off"
                                                    class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                                    name="from_date" id="from_date" value="{{ old('from_date') }}"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="to_date">To Date</label>
                                                <input type="text" autocomplete="off"
                                                    class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                                    name="to_date" id="to_date" value="{{ old('to_date') }}"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                &nbsp;&nbsp;
                                                <label for="company_id">Company</label>
                                                <select
                                                    class="form-control select2-single ajax-companies {{ $errors->has('company_id') ? ' is-invalid' : '' }}"
                                                    name="company_id" id="company_id" required>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="branch_id">Branch</label>
                                                <select class="form-control select2-multiple ajax-branches" name="branch_id"
                                                    id="branch_id" name="branch_id[]" multiple>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="category_id1">Category</label>
                                                <select class="form-control select2-multiple ajax-categories"
                                                    name="category_id1[]" id="category_id1" multiple>
                                                </select>

                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="category_id2">To Category</label>
                                                <select class="form-control select2-multiple ajax-categories"
                                                    name="category_id2[]" id="category_id2" multiple>
                                                </select>

                                            </div>
                                        </div> --}}
                                        <div class="col-md-12">
                                            <div class="form-group text-right ">
                                                <input type="button" class="btn btn-primary" id="generate" name="generate"
                                                    value="Generate" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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
            /*
                        $('#generate').on("click", function() {
                            from_date = $('#from_date').val();
                            to_date = $('#to_date').val();
                            branch_id = $('#branch_id').val();
                            category_id1 = $('#category_id1').val();
                            category_id2 = $('#category_id2').val();

                            $('#img-loader').show();
                            $.ajax({
                                type: "GET",
                                url: "{{ route('ajax.category.sales.report') }}",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    from_date: from_date,
                                    to_date: to_date,
                                    branch_id: branch_id,
                                    category_id1: category_id1,
                                    category_id2: category_id2
                                }
                            }).done(function(data) {
                                $('#img-loader').hide();
                                $("#load").html(data);
                                loadDataTable()
                            });
                        });
            */
            $('#generate').on("click", function() {
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let company_id = $('#company_id').val();
                let branch_id = $('#branch_id').val();
                let category_id1 = $('#category_id1').val(); // Now multiple
                //let category_id2 = $('#category_id2').val(); // Now multiple

                $('#img-loader').show(); // Show loader

                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.category.site.sales.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        company_id: company_id,
                        branch_id: branch_id,
                        category_id1: category_id1, // Send multiple categories
                        //category_id2: category_id2 // Send multiple categories
                    },
                    success: function(data) {
                        $('#img-loader').hide(); // Hide loader
                        $("#load").html(data); // Load data into the report section
                        loadDataTable(); // Initialize DataTable
                    }
                });
            });

        });
    </script>
@endpush
