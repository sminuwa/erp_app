@extends('layouts.backend.app')

@section('title', 'Manufacturing Teams Report')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manufacturing Teams Report</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Teams</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report Filters</h3>
                </div>
                <div class="card-body">
                    <form id="report-form">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date From <span class="text-danger">*</span></label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ date('Y-m-01') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date To <span class="text-danger">*</span></label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Factory/Branch</label>
                                    <select name="branch_id" id="branch_id" class="form-control select2-single">
                                        <option value="">All Factories</option>
                                        @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $userBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Team</label>
                                    <select name="team_id" id="team_id" class="form-control select2-single">
                                        <option value="">All Teams</option>
                                        @foreach($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category_id" id="category_id" class="form-control select2-single">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Batch Number</label>
                                    <input type="text" name="batch_number" id="batch_number" class="form-control" placeholder="Search batch...">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" id="load-report" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Load Report
                                </button>
                                <button type="button" id="print-report" class="btn btn-secondary" disabled>
                                    <i class="fa fa-print"></i> Print
                                </button>
                                <button type="button" id="export-pdf" class="btn btn-danger" disabled>
                                    <i class="fa fa-file-pdf"></i> Export PDF
                                </button>
                                <button type="button" id="export-excel" class="btn btn-success" disabled>
                                    <i class="fa fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="report-results"></div>
        </div>
    </div>
    </section>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('.select2-single').select2({ width: '100%' });

    $('#load-report').click(function() {
        var form = $('#report-form');
        var btn = $(this);

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

        $.ajax({
            url: '{{ route("manufacturing.reports.teams.load") }}',
            type: 'GET',
            data: form.serialize(),
            success: function(response) {
                $('#report-results').html(response);
                $('#print-report').prop('disabled', false);
                $('#export-pdf').prop('disabled', false);
                $('#export-excel').prop('disabled', false);
            },
            error: function(xhr) {
                var message = xhr.responseJSON?.message || 'Error loading report';
                toastr.error(message);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-search"></i> Load Report');
            }
        });
    });

    $('#print-report').click(function() {
        var form = $('#report-form');
        var url = '{{ route("manufacturing.reports.teams.print") }}?' + form.serialize();
        window.open(url, '_blank');
    });

    $('#export-pdf').click(function() {
        var form = $('#report-form');
        var url = '{{ route("manufacturing.reports.teams.export_pdf") }}?' + form.serialize();
        window.open(url, '_blank');
    });

    $('#export-excel').click(function() {
        var form = $('#report-form');
        var url = '{{ route("manufacturing.reports.teams.export_excel") }}?' + form.serialize();
        window.open(url, '_blank');
    });
});
</script>
@endpush
