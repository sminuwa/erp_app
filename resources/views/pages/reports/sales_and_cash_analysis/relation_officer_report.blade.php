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

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Relation Officer Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h4>Relation Officer Sales Report</h4>
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
                                                <label for="category_id1">Category</label>
                                                <select class="form-control select2-multiple ajax-categories"
                                                        name="category_id1[]" id="category_id1" multiple>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            &nbsp;&nbsp;
                                            <label for="user_id">Relation Officer</label>
                                            <select class="form-control select2-multiple ajax-relation-officers"
                                                    name="user_id[]" id="user_id" multiple>
                                            </select>
                                        </div>

                                        

                                        <div class="form-group col-md-2">
                                            <label for="budget_year">Budget Year</label>
                                            <input type="number" class="form-control" id="budget_year" name="budget_year" value="{{ date('Y') }}" required>
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label for="quarter">Quarter</label>
                                            <select class="form-control" id="quarter" name="quarter" required>
                                                <option value="Q1">Q1</option>
                                                <option value="Q2">Q2</option>
                                                <option value="Q3">Q3</option>
                                                <option value="Q4">Q4</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            &nbsp;&nbsp;
                                            <label for="summary">Summary Report</label>
                                            <input type="checkbox" checked class="form-control" name="is_summary"
                                                   id="summary">
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group text-right">
                                                <input type="button" class="btn btn-primary" id="generate" name="generate" value="Generate"/>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div id="img-loader" style="display:none;">
                        <img src="{{ asset('assets/backend/img/loader.png') }}" style="width:80px;height:80px;text-align:center">
                    </div>
                    <div class="col-sm-12 table-responsive mt-2" id="load"></div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.1/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#generate').on("click", function () {
                const from_date = $('#from_date').val();
                const to_date = $('#to_date').val();
                const company_id = $('#company_id').val();
                const category_id = $('#category_id1').val();
                const user_id = $('#user_id').val();
                const summary = $('#summary').prop('checked');
                const budget_year = $('#budget_year').val();
                const quarter = $('#quarter').val();

                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.relation_officer.sales.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        company_id: company_id,
                        category_id: category_id,
                        user_id: user_id,
                        is_summary: summary ? 1 : 0,
                        budget_year: budget_year,
                        quarter: quarter
                    }
                }).done(function (data) {
                    $('#img-loader').hide();
                    $("#load").html(data);
                    loadDataTable();
                });
            });
        });
    </script>
@endpush
