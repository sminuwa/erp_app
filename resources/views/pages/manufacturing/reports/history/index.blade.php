@extends('layouts.backend.app')

@section('title', 'Manufacturing History Report')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Manufacturing History Report</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">History</li>
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
                    <h3 class="card-title">Manufacturing History Report</h3>
                </div>
                <div class="card-body">
                    <form id="report-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From <span class="text-danger">*</span></label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ date('Y-m-01') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To <span class="text-danger">*</span></label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Branch</label>
                                    <select name="branch_id" id="branch_id" class="form-control select2-single">
                                        <option value="">All Branches</option>
                                        @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $userBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Report Type</label>
                                    <select name="report_type" id="report_type" class="form-control">
                                        <option value="all">All Manufacturing</option>
                                        <option value="single">Single Product Only</option>
                                        <option value="batch">Batch Production Only</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Finish Product</label>
                                    <select name="product_id" id="product_id" class="form-control select2-single">
                                        <option value="">All Products</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>BOM</label>
                                    <select name="bom_id" id="bom_id" class="form-control select2-single">
                                        <option value="">All BOMs</option>
                                        @foreach($boms as $bom)
                                        <option value="{{ $bom->id }}">{{ $bom->reference }} - {{ $bom->finishProduct->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" id="load-report" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Load Report
                                        </button>
                                        <button type="button" id="print-report" class="btn btn-secondary" disabled>
                                            <i class="fa fa-print"></i> Print
                                        </button>
                                    </div>
                                </div>
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
    $('.select2-single').select2();

    $('#load-report').click(function() {
        var form = $('#report-form');
        var btn = $(this);

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

        $.ajax({
            url: '{{ route("manufacturing.reports.history.load") }}',
            type: 'GET',
            data: form.serialize(),
            success: function(response) {
                $('#report-results').html(response);
                $('#print-report').prop('disabled', false);
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
        var url = '{{ route("manufacturing.reports.history.print") }}?' + form.serialize();
        window.open(url, '_blank');
    });
});
</script>
@endpush
