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
                        <h4>Customer Last Transaction Date Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Customer Last Transaction Date</li>
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
                            <label for="customer_id">Customer</label>
                            <select
                                class="form-control select2-single ajax-customers {{ $errors->has('customer_id') ? ' is-invalid' : '' }}"
                                name="customer_id" id="customer_id">
                            </select>
                        </div>
                        <div class="form-group text-right  col-sm-2">
                            <input type="button" class="btn btn-primary" id="generate" name="generate"
                                   value="Generate"/>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <img src="{{ asset('assets/backend/img/loader.png') }}"
                             style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
                        <div id="load"></div>
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
        $(function () {
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

            $('#generate').on("click", function () {
                company_id = $('#company_id').val();
                branch_id = $('#branch_id').val();
                customer_id = $('#customer_id').val();

                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customer.last.transaction.reports') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        company_id: company_id,
                        branch_id: branch_id,
                        customer_id: customer_id
                    }
                }).done(function (data) {
                    $('#img-loader').hide()
                    $("#load").html(data);
                    loadDataTable2()
                });
            });
        });
    </script>
@endpush
