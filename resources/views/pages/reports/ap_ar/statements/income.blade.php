@extends('layouts.backend.app')

@section('title', 'Income Statement')

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
                        <h4>Income Statement Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Income Statement Report</li>
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
                        <div class="form-group">
                            <label for="branch_id">Branch</label>
                            <select class="form-control select2-single ajax-branches" name="branch_id" id="branch_id">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category_id1">From Category</label>
                            <select class="form-control select2-single ajax-categories" name="category_id1"
                                id="category_id1">
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="category_id2">To Category</label>
                            <select class="form-control select2-single ajax-categories" name="category_id2"
                                id="category_id2">
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="income_year">Year</label>
                            <select class="form-control select2-single" name="income_year" id="income_year">
                                @for ($i = date('Y'); $i > 40; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="from_month">From Period</label>
                            <select class="form-control select2-single" name="from_month" id="from_month">
                                <option value="">Select...</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="to_month">To Period</label>
                            <select class="form-control select2-single" name="to_month" id="to_month">
                                <option value="">Select...</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <div class="form-group text-right col-sm-4">
                            <input type="button" class="btn btn-primary" id="generate" name="generate" value="Generate" />
                        </div>
                    </div>

                </form>

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
                income_year = $('#income_year').val();
                from_month = $('#from_month').val();
                to_month = $('#to_month').val();
                category_id1 = $('#category_id1').val();
                category_id2 = $('#category_id2').val();
                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.income.statement.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        branch_id: branch_id,
                        from_month: from_month,
                        to_month: to_month,
                        income_year: income_year,
                        category_id1: category_id1,
                        category_id2: category_id2
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
