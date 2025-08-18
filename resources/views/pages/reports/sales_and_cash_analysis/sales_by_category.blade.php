@extends('layouts.backend.app')

@section('title', 'Sales Report By Category')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
    <style>
        caption {
            caption-side: top;
        }
    </style>
@endpush

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Sales Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h4>Sales Report - Group by Product or Category</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="salesReportForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                            name="from_date" id="from_date" value="{{ old('from_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                            name="to_date" id="to_date" value="{{ old('to_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_id">Company</label>
                                        <select class="form-control select2-single ajax-companies" name="company_id"
                                            id="company_id">
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="branch_id">Branch</label>
                                        <select class="form-control select2-multiple ajax-branches" name="branch_id[]"
                                            id="branch_id" multiple>
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

                                <!-- Grouping Checkboxes -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Group By</label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="group_by_category" name="group_by_category">
                                            <label class="form-check-label" for="group_by_category">Category</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="group_by_product" name="group_by_product">
                                            <label class="form-check-label" for="group_by_product">Product</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group text-right">
                                        <button type="button" class="btn btn-primary" id="generate">Generate</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Data Section -->
                <div class="row">
                    <div class="col-sm-12 table-responsive" id="load">
                        <img src="{{ asset('assets/backend/img/loader.png') }}"
                            style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
                    </div>
                </div>

            </div>
        </section>
    </div>

@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#generate').on("click", function() {
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let company_id = $('#company_id').val();
                let branch_id = $('#branch_id').val();
                let category_id1 = $('#category_id1').val();

                let group_by_category = $('#group_by_category').is(":checked") ? 1 : 0;
                let group_by_product = $('#group_by_product').is(":checked") ? 1 : 0;

                $('#img-loader').show();

                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.category.sales.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        company_id: company_id,
                        branch_id: branch_id,
                        category_id1: category_id1,
                        group_by_category: group_by_category,
                        group_by_product: group_by_product
                    },
                    success: function(data) {
                        $('#img-loader').hide();
                        $("#load").html(data);
                        loadDataTable2();
                    }
                });
            });
        });
    </script>
@endpush

