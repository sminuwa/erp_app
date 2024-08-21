@extends('layouts.backend.app')

@section('title', 'Ageing Report')

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
                        <h4>Ageing Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Ageing Report</li>
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
                        {{-- <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="text" autocomplete="off"
                                class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                name="from_date" id="from_date" value="{{ old('from_date') }}" placeholder="">
                        </div> --}}
                        {{-- <div class="form-group">
                            <label for="to_date">End Date</label>
                            <input type="text" autocomplete="off"
                                class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                name="to_date" id="to_date" value="{{ old('to_date') }}" placeholder="">
                        </div> --}}
                        <div class="form-group">
                            &nbsp;&nbsp;
                            <label for="to_date">Age Time</label>
                            <select class="form-control select2-single {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                name="to_date" id="to_date">
                                <option value="0">Select...</option>
                                <option value="1">0-7 days</option>
                                <option value="2">8 - 14 days</option>
                                <option value="3">15 - 30 days</option>
                                <option value="4">31 - 60 days</option>
                                <option value="5">61 - 90 days</option>
                                <option value="6">91 - 120 days</option>
                                <option value="7">121 - 180 days</option>
                                <option value="8">Above 180 days</option>
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
    <script type="text/javascript">
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

                from_date = 'all'; //$('#from_date').val();
                to_date = $('#to_date').val();
                branch_id = $('#branch_id').val();
                customer_id = $('#customer_id').val();
                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customer.ageing.reports') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        branch_id: branch_id,
                        customer_id: customer_id
                    }
                }).done(function(data) {
                    $("#load").html(data);
                    loadDataTable()
                });
            });
        });
    </script>
@endpush
