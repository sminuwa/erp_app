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
                        <h4>New Receipt </h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            @can('receipt.payments')
                                <li class="breadcrumb-item"><a href="{{ route('receipt.payments') }}">Receipts</a></li>
                            @endcan
                            <li class="breadcrumb-item active">New Receipt</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('receipt.payments')
                <a class="btn btn-secondary btn-sm" href="{{ route('receipt.payments') }}">
                    <span class="fa fa-list"> </span> List
                </a>
            @endcan

            @if (Session::get('prev_id') != null)
                @can('receipt.payment.print')
                    <a href="{{ route('receipt.payment.print', Session::get('prev_id')) }}" target="_BLANK"
                        class="btn btn-sm btn-primary" style="margin-left: 2px;"><span class="fa fa-print"> Print</span> </a>
                @endcan
                @can('receipt.payment.print.pos')
                    <a href="{{ route('receipt.payment.print.pos', Session::get('prev_id')) }}" target="_BLANK"
                        class="btn btn-secondary btn-sm">
                        <i class="fa fa-print" aria-hidden="true">PoS</i>
                    </a>
                @endcan
            @endif
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 mt-4">
                        {{--                        @include('pages.receipts.receipt_payment_form') --}}
                        <form action="{{ isset($route) ? $route : route('receipt.payment.store') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
                            <input type="hidden" name="receipt_id" value="{{ isset($model) ? $model->id : '' }}" />
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
                                                {{ 'Suppplier' == $model->model_name ? 'selected' : '' }}>Suppplier
                                            <option value="GeneralAccount"
                                                {{ 'GeneralAccount' == $model->model_name ? 'selected' : '' }}>General
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
                                        <label for="payer_id">Receiver</label>
                                        @if (isset($model) && $model->model_name == 'Customer')
                                            <?php $payers = \App\Models\Customer::orderBy('code', 'asc')->get(); ?>
                                        @elseif(isset($model) && $model->model_name == 'Supplier')
                                            <?php $payers = \App\Models\Supplier::orderBy('code', 'asc')->get(); ?>
                                        @else
                                            <?php $payers = \App\Models\GeneralAccount::orderBy('number', 'asc')->get(); ?>
                                        @endif
                                        <select
                                            class="form-control select2-single {{ $errors->has('payer_id') ? ' is-invalid' : '' }}"
                                            name="payer_id" id="payer_id" selected_item="{{ $model->model_id }}" required>
                                            <option value="">Select...</option>
                                            @if (isset($payers))
                                                @foreach ($payers as $payer)
                                                    <option value="{{ $payer->id }}"
                                                        {{ $payer->id == $model->model_id ? 'selected' : '' }}>
                                                        {{ $payer->code ?? $payer->number }} -
                                                        {{ $payer->name ?? $payer->description }}</option>
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
                                        <label for="payment_date">Receipt Date</label>
                                        <input type="text"
                                            class="form-control datepicker2 {{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
                                            name="payment_date" id="payment_date"
                                            value="{{ old('payment_date', \Carbon\Carbon::parse($model->date)->format('Y-m-d')) == '' ? date('Y-m-d') : old('payment_date', \Carbon\Carbon::parse($model->date)->format('Y-m-d')) }}"
                                            required>
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
                                                    {{ $account->id == $model->charged_account_id ? 'selected' : null }}>
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
                                        <input type="submit" class="btn btn-primary" value="Save" />
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <div class="modal fade" id="customer_ledgerform" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Ledger</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="get" action="{{ route('ajax.general.customer.ledger') }}" id="ledger_form"
                        target="_BLANK">
                        @csrf
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="text" class="form-control datepicker" name="from_date" id="from_date"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="text" class="form-control datepicker" name="to_date" id="to_date"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            &nbsp;&nbsp;
                            <label for="customer_id">Customer</label>
                            <select class="form-control select2-single" name="customer_id" id="customer_id" required>
                                {{-- <option value="all">All</option> --}}
                                <option value="">Select...</option>
                                @foreach (App\Models\Customer::where('type', 'credit')->where('branch_id', 'LIKE', App\Models\User::userBranchAction())->get() as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}-{{ $data->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="print" value="print" />
                        <input type="hidden" name="modal" value="modal" />
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                                Close
                            </button>
                            <button type="submit" class="btn btn-info px-3"><i class="icon-trash"></i> Generate
                            </button>
                        </div>
                        @method('post')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
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
                /*if($(this).val() === 'Customer'){
                    $("#payer_id").addClass('form-control ajax-customers select2-single');
                    ajerks('GET','/misc/ajax/customers','ajax-customers')
                }
                if($(this).val() === 'Supplier'){
                    $("#payer_id").addClass('form-control ajax-suppliers select2-single');
                    ajerks('GET','/misc/ajax/suppliers','ajax-suppliers')
                }
                if($(this).val() === 'GeneralAccount'){
                    $("#payer_id").addClass('form-control ajax-general-accounts select2-single');
                    ajerks('GET','/misc/ajax/general-accounts','ajax-general-accounts')
                }*/
                // ajerks('GET','/misc/ajax/customers','ajax-customers')
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
