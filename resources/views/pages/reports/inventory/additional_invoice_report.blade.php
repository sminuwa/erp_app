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
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Additional Invoices Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Additional Invoices Report</li>
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
                            <select
                                class="form-control select2-single ajax-companies {{ $errors->has('company_id') ? ' is-invalid' : '' }}"
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
                            <label for="supplier_id">Suppliers/Transporter</label>
                            <select
                                class="form-control select2-single ajax-suppliers {{ $errors->has('supplier_id') ? ' is-invalid' : '' }}"
                                name="supplier_id" id="supplier_id">

                            </select>
                        </div>
                        <div class="form-group">
                            &nbsp;&nbsp;
                            <label for="status">Status</label>
                            <select class="form-control {{ $errors->has('status') ? ' is-invalid' : '' }}" name="status"
                                id="status">
                                <option value="">Select...</option>
                                <option value="0">Pending</option>
                                <option value="1">Completed</option>
                            </select>
                        </div>
                        <div class="form-group text-right">
                            <input type="button" class="btn btn-primary" id="generate" name="generate" value="Generate" />
                        </div>
                    </div>

                </form>
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
        $(document).ready(function() {
            $('.datepicker3').datepicker({
                minDate: null, // Allow any past date
                dateFormat: 'yy-mm-dd' // Set your preferred date format
            });
        });
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

            $('#generate').on("click", function() {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                supplier_id = $('#supplier_id').val();
                company_id = $('#company_id').val();
                branch_id = $('#branch_id').val();
                status = $('#status').val();
                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.additional.invoice.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        supplier_id: supplier_id,
                        company_id: company_id,
                        branch_id: branch_id,
                        status: status
                    }
                }).done(function(data) {
                    $('#img-loader').hide();
                    $("#load").html(data);
                    loadDataTable2()
                });
            });
        });
    </script>
@endpush
