@extends('layouts.backend.app')

@section('title', 'Backdated & Postdated Entries Report')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Backdated & Postdated Entries Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Entries Report</li>
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
                            <select class="form-control select2-single ajax-companies" name="company_id" id="company_id" required></select>
                        </div>

                        <!-- Branch Selection -->
                        <div class="form-group col-md-3">
                            <label for="branch_id">Branch</label>
                            <select class="form-control select2-single ajax-branches" name="branch_id" id="branch_id"></select>
                        </div>

                        <!-- Report Type Selection -->
                        <div class="form-group col-md-3">
                            <label for="type">Report Type</label>
                            <select class="form-control" name="type" id="type" required>
                                <option value="credit_notes">Credit Notes</option>
                                <option value="inter_banks">Inter Banks</option>
                                <option value="intersite_transfers">Intersite Transfers</option>
                                <option value="interstore_transfers">Interstore Transfers</option>
                                <option value="journals">Journals</option>
                                {{-- <option value="order_invoices">Order Invoices</option> --}}
                                <option value="orders">Invoices</option>
                                <option value="payments">Payments</option>
                                {{-- <option value="proformers">Proformers</option> --}}
                                <option value="purchase_expenses">Additional Invoice</option>
                                <option value="purchases">Purchase Invoice/GRN</option>
                                <option value="receipts">Receipts</option>
                                <option value="return_debits">Return Debits</option>
                            </select>
                        </div>

                        <!-- Entry Type Selection -->
                        <div class="form-group col-md-3">
                            <label for="entry_type">Entry Type</label>
                            <select class="form-control" name="entry_type" id="entry_type" required>
                                <option value="backdated">Backdated</option>
                                <option value="postdated">Postdated</option>
                            </select>
                        </div>

                        <!-- Date Range Selection -->
                        <div class="form-group col-md-3">
                            <label for="from_date">From Date</label>
                            <input type="text" class="form-control datepicker" name="from_date" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="to_date">To Date</label>
                            <input type="text" class="form-control datepicker" name="to_date" required>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group col-md-3 text-right">
                            <button type="button" class="btn btn-primary" id="generate">Generate</button>
                        </div>
                    </div>
                </form>
                
                <!-- Report Table Loader -->
                <div class="row">
                    <div class="col-sm-12 table-responsive" id="load">
                        <img src="{{ asset('assets/backend/img/loader.png') }}" style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
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
                let type = $('#type').val();
                let entry_type = $('#entry_type').val();
                let from_date = $('input[name="from_date"]').val();
                let to_date = $('input[name="to_date"]').val();
                
                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.backdated_postdated_report') }}",
                    data: {
                        company_id: company_id,
                        branch_id: branch_id,
                        type: type,
                        entry_type: entry_type,
                        from_date: from_date,
                        to_date: to_date,
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
