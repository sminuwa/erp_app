@extends('layouts.backend.app')

@section('title', 'Balance Sheet')

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
                        <h4>Cash Flow</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Cash Flow</li>
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
                        <form id="trialForm" method="POST">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        &nbsp;&nbsp;
                                        <label for="company_id">Company</label>
                                        <select
                                            class="form-control select2-single ajax-companies {{ $errors->has('company_id') ? ' is-invalid' : '' }}"
                                            name="company_id" id="company_id">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        &nbsp;&nbsp;
                                        <label for="branch_id">Branch</label>
                                        <select
                                            class="form-control select2-single ajax-branches {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                            name="branch_id" id="branch_id">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="text" autocomplete="off" name="from_date" id="from_date" placeholder=""
                                               class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                               value="{{ old('from_date') }}" required/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="text" autocomplete="off" name="to_date" id="to_date" placeholder=""
                                               class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                               value="{{ old('to_date') }}" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" id="generate" name="generate"
                                       value="Generate"/>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 table-responsive" id="load"></div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@push('js')
    <script type="text/javascript">
        $(function () {
            $('#trialForm').on('submit', function (e) {
                e.preventDefault()
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                branch_id = $('#branch_id').val();
                company_id = $('#company_id').val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.cash.flow.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date:from_date,
                        to_date: to_date,
                        branch_id: branch_id,
                        company_id: company_id,
                    }
                }).done(function (data) {
                    // console.log(data)
                    $("#load").html(data);
                    loadDataTable2()
                });
            })
        });
    </script>
@endpush
