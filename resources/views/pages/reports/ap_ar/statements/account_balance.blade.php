@extends('layouts.backend.app')

@section('title', 'Account Balances')

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
                        <h4>Account Balances</h4>
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
                                        <label for="type">Account Type</label>
                                        <select class="form-control {{ $errors->has('type') ? ' is-invalid' : '' }}"
                                                name="type" id="type" required="required">
                                            <option value="">Select...</option>
                                            <option value="Customer">
                                                Customer
                                            </option>
                                            <option value="Supplier">
                                                Supplier
                                            <option value="GeneralAccount">General Ledger
                                                Accounts
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
                                    <label for="company_id">Company</label>
                                    <select class="form-control select2-single ajax-companies" name="company_id"
                                            id="company_id">
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
                                        <label for="date">Date</label>
                                        <input type="text" autocomplete="off" name="date" id="date"
                                               class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                               value="{{ old('date') }}" placeholder="" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    &nbsp;&nbsp;
                                    <label for="zero_balance">Include Zero Balances</label>
                                    <input type="checkbox" class="form-control" name="zero_balance"
                                           id="zero_balance">
                                </div>
                            </div>

                            <div class="text-right form-group col-sm-12">
                                <input type="button" class="btn btn-primary" id="generate" name="generate"
                                       value="Generate"/>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <div class="col-sm-12 table-responsive">
                            <img src="{{ asset('assets/backend/img/loader.png') }}"
                                 style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
                        </div>
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
    <script type="text/javascript">
        $(function () {
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


            $('#generate').on("click", function () {
                date = $('#date').val();
                account_type = $('#type').val();
                company_id = $('#company_id').val();
                branch_id = $('#branch_id').val();
                company_id = $('#branch_id').val();
                zeros = $('#zero_balance').prop('checked')

                if (account_type == "") {
                    alert("Please select account type!");
                    return false;
                }

                if (date === '') {
                    alert('Please select date to continue')
                    return
                }

                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.account.balance.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        date: date,
                        account_type: account_type,
                        company_id: company_id,
                        branch_id: branch_id,
                        zeros: zeros
                    }
                }).done(function (data) {
                    $('#img-loader').hide();
                    $("#load").html(data);
                    loadDataTable2()
                });
            });
        });
    </script>
@endpush
