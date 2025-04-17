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
                        <h4>New/Edit Payment</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            @can('payments.list')
                                <li class="breadcrumb-item"><a href="{{ route('payments.list') }}">Payments</a></li>
                            @endcan
                            <li class="breadcrumb-item active">New Payment</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('payments.list')
                <a class="btn btn-secondary btn-sm" href="{{ route('payments.list') }}">
                    <span class="fa fa-list"> List</span>
                </a>
            @endcan
            @can('create.payment')
                <a href="{{ route('create.payment') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                        class="fa fa-plus-circle"> </span> New
                    Payment</a>
            @endcan
            @if (Session::get('prev_id') != null)
                @can('payments.print')
                    <a href="{{ route('payment.print', Session::get('prev_id')) }}" target="_BLANK"
                        class="btn btn-sm btn-primary" style="margin-left: 2px;"><span class="fa fa-print"> Print</span> </a>
                @endcan
                @can('payments.print.pos')
                    <a href="{{ route('payment.print.pos', Session::get('prev_id')) }}" target="_BLANK"
                        class="btn btn-secondary btn-sm">
                        <i class="fa fa-print" aria-hidden="true">PoS</i>
                    </a>
                @endcan
            @endif
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 ">
                        <form action="{{ isset($route) ? $route : route('payment.store') }}" method="POST" id="payment_form">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
                            <input type="hidden" name="payment_id" value="{{ isset($model) ? $model->id : '' }}" />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type">Category</label>
                                        <select
                                            class="form-control select2-single {{ $errors->has('type') ? ' is-invalid' : '' }}"
                                            name="type" id="type" required="required">
                                            <option value="">Select...</option>
                                            <option value="Customer"
                                                {{ 'Customer' == $model->model_name ? 'selected' : '' }}>Customer</option>
                                            <option value="Supplier"
                                                {{ 'Supplier' == $model->model_name ? 'selected' : '' }}>Supplier
                                            <option value="GeneralAccount"
                                                {{ 'GeneralAccount' == $model->model_name ? 'selected' : '' }}>General Ledger
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        @if (isset($model) && $model->model_name == 'Customer')
                                            <?php $payers = \App\Models\Customer::active()->orderBy('code', 'asc')->get(); ?>
                                        @elseif(isset($model) && $model->model_name == 'Supplier')
                                            <?php $payers = \App\Models\Supplier::active()->orderBy('code', 'asc')->get(); ?>
                                        @else
                                            <?php $payers = \App\Models\GeneralAccount::active()->whereNot('number', 'LIKE', 'R%')->orderBy('number', 'asc')->get(); ?>
                                        @endif
                                        <label for="payer_id">Payable</label>
                                        <select
                                            class="form-control select2-single {{ $errors->has('payer_id') ? ' is-invalid' : '' }}"
                                            name="payer_id" id="payer_id" required="required">
                                            <option value="">Select...</option>
                                            @if (isset($payers))
                                                @foreach ($payers as $payer)
                                                    <option value="{{ $payer->id }}"
                                                        {{ $payer->id == $model->model_id ? 'selected' : '' }}>
                                                        {{ $payer->code ?? $payer->number }} -
                                                        {{ $payer->name ?? $payer->description }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('payer_id'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('payer_id') }}</strong>
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
                                            class="form-control datepicker-entry {{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
                                            name="payment_date" id="payment_date"
                                            value="{{ old('payment_date', $model->payment_date) == '' ? date('Y-m-d') : old('payment_date', $model->payment_mode) }}"
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
                                        <label for="account_id">Bank/Cashbook</label>
                                        <select
                                            class="form-control select2-single {{ $errors->has('account_id') ? ' is-invalid' : '' }}"
                                            name="account_id" id="account_id" required="required">
                                            <option value="">Select...</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}"
                                                    {{ $account->id == $model->charged_account_id ? 'selected' : '' }}
                                                    {{ $account->id = old('account_id', $model->branch_id) }}>
                                                    {{ $account->number }} - {{ $account->description }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('payer_id'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('payer_id') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="amount_paid">Amount</label>
                                        <input type="text" oninput="formatNumber(this)"
                                            class="form-control {{ $errors->has('amount_paid') ? ' is-invalid' : '' }}"
                                            name="amount_paid" id="amount_paid"
                                            value="{{ old('amount_paid', number_format($model->amount, 2)) }}" required>
                                        @if ($errors->has('amount_paid'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('amount_paid') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="payment_ref">Description</label>
                                        <textarea type="text" class="form-control" name="payment_ref" id="payment_ref">
@if (isset($model))
{{ $model->description }}
@endif
</textarea>
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
                                        <input type="submit" class="btn btn-primary" value="Save" id="btn_save" />
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- @include('pages.payments.payment_form') --}}
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
         $('#btn_save').click(function() {
            $('#payment_form').submi();
            $(this).attr('disabled', 'disabled');
        });
        function formatNumber(input) {
            // Remove non-numeric and non-decimal characters
            let value = input.value.replace(/[^\d.]/g, '');

            // Split the value into integer and decimal parts
            const parts = value.split('.');
            let integerPart = parts[0] ? parseFloat(parts[0]) : 0;
            let decimalPart = parts[1] !== undefined ? '.' + parts[1] : '';

            // Check if the integer part is not NaN
            if (!isNaN(integerPart)) {
                // Format the integer part with commas and dot as decimal separator
                integerPart = integerPart.toLocaleString('en-US', {
                    maximumFractionDigits: 2,
                    useGrouping: true
                });

                // Set the formatted value back to the input
                input.value = integerPart + decimalPart;
            }
        }


        $(function() {
            $('#type').on("change", function() {
                $("#payer_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.load.payers') }}",
                    type: 'GET',
                    data: {
                        type: $(this).val(),
                        exclude: 'exclude' // Exclude revenue in payment
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
