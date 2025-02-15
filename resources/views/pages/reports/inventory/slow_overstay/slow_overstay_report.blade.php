@extends('layouts.backend.app')

@section('title', 'Slow Moving & Overstayed Report')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Slow Moving & Overstayed Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Slow Moving & Overstayed Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <form method="POST">
                    <div class="row">
                        <!-- Company Selection -->
                        <div class="form-group col-md-3">
                            <label for="company_id">Company</label>
                            <select class="form-control select2-single ajax-companies" name="company_id" id="company_id"
                                required></select>
                        </div>

                        <!-- Branch Selection -->
                        <div class="form-group col-md-3">
                            <label for="branch_id">Branch</label>
                            <select class="form-control select2-single ajax-branches" name="branch_id" id="branch_id" required></select>
                        </div>

                        <!-- Report Type Selection -->
                        <div class="form-group col-md-3">
                            <label for="report_type">Report Type</label>
                            <select class="form-control" name="report_type" id="report_type" required>
                                <option value="overstayed">Overstayed Inventory</option>
                                <option value="slow_moving">Slow Moving Inventory</option>
                            </select>
                        </div>

                        <!-- Generate Report Button -->
                        <div class="form-group col-md-3 text-right">
                            <button type="button" class="btn btn-primary" id="generate">Generate</button>
                        </div>
                    </div>
                </form>

                <!-- Report Table Loader -->
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
        $(function() {
            $('#generate').on("click", function() {
                let company_id = $('#company_id').val();
                let branch_id = $('#branch_id').val();
                let report_type = $('#report_type').val();
                
                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.slow.overstayed.report') }}",
                    data: {
                        company_id: company_id,
                        branch_id: branch_id,
                        report_type: report_type
                    }
                }).done(function(data) {
                    $("#load").html(data);
                    $('#img-loader').hide();
                    loadDataTable();
                });
            });

        });
    </script>
@endpush
