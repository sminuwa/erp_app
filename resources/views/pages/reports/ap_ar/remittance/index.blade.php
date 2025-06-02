@extends('layouts.backend.app')

@section('title', 'Ledger')

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
                        <h4>Daily Remittance</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Daily Remittance</li>
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
                        <form id="remitForm" method="POST">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        &nbsp;&nbsp;
                                        <label for="user_id">User</label>
                                        <select
                                            class="form-control select2-single ajax-users {{ $errors->has('user_id') ? ' is-invalid' : '' }}"
                                            name="user_id" id="user_id">
                                            <option value="">Select...</option>
                                            {{--                                            <option value="all">All</option> --}}
                                            @foreach ($users as $data)
                                                <option value="{{ $data->id }}">{{ $data->user_code }}
                                                    -{{ $data->name }}

                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="type">Account Type</label>
                                        <select class="form-control {{ $errors->has('type') ? ' is-invalid' : '' }}"
                                            name="type" id="type" required="required">
                                            <option value="all">All</option>
                                            <option value="Customer">
                                                Customer
                                            </option>
                                            <option value="Supplier">
                                                Supplier
                                            <option value="GeneralAccount">General Accounts
                                            </option>
                                        </select>
                                        @if ($errors->has('type'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('type') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="payer_id">Account</label>
                                        <select
                                            class="form-control select2-single ajax-payee {{ $errors->has('payer_id') ? ' is-invalid' : '' }}"
                                            name="payer_id" id="payer_id">
                                            <option value="all">All</option>
                                        </select>
                                        @if ($errors->has('payer_id'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('payer_id') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="text" autocomplete="off" name="from_date" id="from_date"
                                            class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                            value="{{ old('from_date') }}" placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="text" autocomplete="off" name="to_date" id="to_date"
                                            placeholder=""
                                            class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                            value="{{ old('to_date') }}" required />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" id="generate" name="generate"
                                    value="Generate" />
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 table-responsive" id="load"></div>
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

            $('#remitForm').on('submit', function(e) {
                e.preventDefault()
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let company_id = $('#company_id').val();
                let branch_id = $('#branch_id').val();
                let user_id = $('#user_id').val();
                let payee_id = $('#payer_id').val();
                let type = $('#type').val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.remittance.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date,
                        to_date,
                        company_id,
                        branch_id,
                        user_id,
                        payee_id,
                        type
                    }
                }).done(function(data) {
                    console.log(data)
                    $("#load").html(data);
                    loadDataTable2()
                });
            })
        });
    </script>
@endpush
