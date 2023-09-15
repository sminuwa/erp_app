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
                        <h4>Debtor's Payment</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('debtor.payments') }}">Payments</a></li>
                            <li class="breadcrumb-item active">New Payment</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a href="{{ route('debtors.payment.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="fa fa-plus-circle"> </span> New
                Payment</a>
            <a class="btn btn-secondary btn-sm" href="{{ route('debtor.payments') }}">
                <span class="fa fa-list"> Payments</span>
            </a>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#customer_ledgerform"
                class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span class="fa fa-money"> Customer Ledger</span>
            </a>
            @if (Session::get('prev_id') != null)
                <a href="{{ route('debtor.payment.print', Session::get('prev_id')) }}" target="_BLANK"
                    class="btn btn-sm btn-primary" style="margin-left: 2px;"><span class="fa fa-print"> Print</span> </a>
                <a href="{{ route('debtor.payment.print.pos', Session::get('prev_id')) }}" target="_BLANK"
                    class="btn btn-secondary btn-sm">
                    <i class="fa fa-print" aria-hidden="true">PoS</i>
                </a>
            @endif
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 ">
                        @include('forms.debtor_payment')
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
        $(function() {
            $('#account_number,#account_name').hide();
            $('#payment_mode').on("change", function() {
                if ($(this).val() != "Cash") {
                    $('#bank_account_id,#account_name').removeAttr('disabled');
                    $('#account_number,#account_name').show();
                    $("#bank_account_id").html(" < option value = '' > Loading... < /option>");
                    $.ajax({
                        url: "{{ route('ajax.loadBankAccounts') }}",
                        type: 'GET',
                        data: {
                            payment_mode: $("#payment_mode").val()
                        }
                    }).done(function(msg) {
                        $("#bank_account_id").html("<option value=''>--select--</option>" + msg);
                    });
                } else {
                    $('#bank_account_id,#account_name').attr('disabled', 'disabled');
                    $('#account_number,#account_name').hide();
                }

            });

            $('#customer_id').on("change", function() {
                customer_id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customer.balance') }}",
                    data: {
                        customer_id: customer_id
                    }
                }).done(function(data) {
                    balance = 0;
                    if (data < 0)
                        balance = "(" + formatMoney(Math.abs(data)) + ")";
                    else
                        balance = formatMoney(data);
                    $("#balance").val(balance);
                });
            });
            $('#customer_id').on("change", function() {
                customer_id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.loadCustomerInvoices') }}",
                    data: {
                        customer_id: customer_id
                    }
                }).done(function(data) {
                    $('#invoice').html(data);
                });
            });

            $('#bank_account_id').on("change", function() {
                bank_account_id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.account.name') }}",
                    data: {
                        bank_account_id: bank_account_id
                    }
                }).done(function(data) {
                    $("#account_name").val(data);
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
