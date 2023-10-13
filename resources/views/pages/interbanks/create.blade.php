@extends('layouts.backend.app')

@section('title', 'Customer')

@push('css')
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
                        <h4>Inter Bank Transfer </h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('interbank.list') }}">Inter Banks Transfer</a></li>
                            <li class="breadcrumb-item active">New Transfer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a href="{{ route('create.interbank') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="fa fa-plus-circle"> </span> New
                Transfer</a>
            <a class="btn btn-secondary btn-sm" href="{{ route('interbank.list') }}">
                <span class="fa fa-list"> Transfers</span>
            </a>
            @if (Session::get('prev_id') != null)
                <a href="{{ route('interbank.print', Session::get('prev_id')) }}" target="_BLANK"
                    class="btn btn-sm btn-primary" style="margin-left: 2px;"><span class="fa fa-print"> Print</span> </a>
                <a href="{{ route('interbank.print.pos', Session::get('prev_id')) }}" target="_BLANK"
                    class="btn btn-secondary btn-sm">
                    <i class="fa fa-print" aria-hidden="true">PoS</i>
                </a>
            @endif
            <div class="container-fluid py-4">
                <div class="card" >
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 ">
                                <form action="{{ isset($route) ? $route : route('interbank.store') }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
                                    <input type="hidden" name="interbank_id" value="{{ isset($model) ? $model->id : '' }}" />
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="source_account_id">Source Account</label>
                                                <select class="form-control select2-single {{ $errors->has('source_account_id') ? ' is-invalid' : '' }}"
                                                        name="source_account_id" id="source_account_id" required="required">
                                                    <option value="">Select...</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}" {{ $account->id == $model->source_account_id ? 'selected' : '' }} {{ $account->id == old('source_account_id', $model->source_account_id) }}>
                                                            {{ $account->number }} - {{ $account->description }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('source_account_id'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('source_account_id') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="destination_account_id">Destination Account</label>
                                                <select class="form-control select2-single {{ $errors->has('destination_account_id') ? ' is-invalid' : '' }}"
                                                        name="destination_account_id" id="destination_account_id" required="required">
                                                    <option value="">Select...</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}" {{ $account->id == $model->destination_account_id ? 'selected' : '' }} {{ $account->id == old('destination_account_id', $model->source_account_id) }}>
                                                            {{ $account->number }} - {{ $account->description }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('destination_account_id'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('destination_account_id') }}</strong>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="payment_date">Payment Date</label>
                                                <input type="text"
                                                       class="form-control datepicker {{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
                                                       name="payment_date" id="payment_date"
                                                       value="@if($model) {{ $model->payment_date }} @endif {{ old('payment_date', $model->payment_date) == '' ? date('Y-m-d') : old('payment_date', $model->payment_mode) }}"
                                                       required="required">
                                                @if ($errors->has('payment_date'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('payment_date') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="amount">Amount</label>
                                                <input type="number" class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}"
                                                       name="amount" id="amount" value="{{ old('amount', $model->amount) }}" required>
                                                @if ($errors->has('amount'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('amount') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="payment_ref">Description</label>
                                                <textarea type="text" class="form-control" name="payment_ref" id="payment_ref">{{ $model->description }}</textarea>
                                                @if ($errors->has('payment_ref'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('payment_ref') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="col-md-8">
                                            <div class="form-group text-right ">
                                                <input type="submit" class="btn btn-primary" value="Save & Post" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@push('js')
    <script>
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
        });
    </script>
@endpush
