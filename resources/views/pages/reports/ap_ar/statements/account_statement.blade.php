@extends('layouts.backend.app')

@section('title', 'Account Statement')

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
                        <h4>Account Statements</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Account Statements</li>
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
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="type">Payee Category</label>
                                        <select class="form-control {{ $errors->has('type') ? ' is-invalid' : '' }}"
                                            name="type" id="type" required="required">
                                            <option value="all">All</option>
                                            <option value="Customer">
                                                Customer
                                            </option>
                                            <option value="Supplier">
                                                Supplier
                                            <option value="GeneralAccount">General Ledger Accounts
                                            </option>
                                        </select>
                                        @if ($errors->has('type'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('type') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    &nbsp;&nbsp;
                                    <label for="company_id">Company</label>
                                    <select class="form-control select2-single ajax-companies {{ $errors->has('company_id') ? ' is-invalid' : '' }}"
                                        name="company_id" id="company_id" required>
    
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="branch_id">Branch</label>
                                    <select class="form-control select2-single ajax-branches" name="branch_id"
                                        id="branch_id">
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="payer_id">Payee</label>
                                        <select
                                            class="form-control select2-single ajax-payee {{ $errors->has('payer_id') ? ' is-invalid' : '' }}"
                                            name="payer_id" id="payer_id" required="required">
                                            <option value="all">All</option>
                                        </select>
                                        @if ($errors->has('payer_id'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('payer_id') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="text" autocomplete="off" name="from_date" id="from_date"
                                            class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                            value="{{ old('from_date') }}" placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="text" autocomplete="off" name="to_date" id="to_date"
                                            placeholder=""
                                            class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                            value="{{ old('to_date') }}" required />
                                    </div>

                                </div>
                            </div>

                            <div class="text-right form-group col-sm-12">
                                <input type="button" class="btn btn-primary" id="generate" name="generate"
                                    value="Generate" />
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 table-responsive" id="load">
                        <img src="{{ asset('assets/backend/img/loader.png') }}" style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
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
                var c = isNaN(c = Math.abs(c)) ? 2 : c,
                    d = d == undefined ? "." : d,
                    t = t == undefined ? "," : t,
                    s = n < 0 ? "-" : "",
                    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ?
                    d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };


            $('#type').on("change", function() {
                $("#payer_id").html(" < option value = '' > Loading... < /option>");

                $.ajax({
                    url: "{{ route('ajax.load.payers') }}",
                    type: 'GET',
                    data: {
                        type: $(this).val()
                    }
                }).done(function(msg) {

                    $("#payer_id").html(msg);

                });
            });

            $('#generate').on("click", function() {
                // from_date = $('#from_date').val();
                // to_date = $('#to_date').val();
                // payer_id = $('#payer_id').val();
                // company_id = $('#company_id').val();
                // branch_id = $('#branch_id').val();
                // type = $('#type').val();

                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let payer_id = $('#payer_id').val();
                let company_id = $('#company_id').val();
                let branch_id = $('#branch_id').val();
                let type = $('#type').val();


                $('#img-loader').show();

                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.account.statement.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        payer_id: payer_id,
                        company_id: company_id,
                        branch_id: branch_id,
                        type: type
                    }
                }).done(function(data) {
                    // $('#img-loader').hide();
                    console.log(data)
                    $("#load").html(data);
                    loadDataTable();
                });
            });
        });
    </script>
@endpush
