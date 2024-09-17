@extends('layouts.backend.app')
@section('title', 'Manage Loan Collector')

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
                        <h4>Grant Loan</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('loan_collectors.index') }}">Collectors</a></li>
                            <li class="breadcrumb-item active">Grant Loan</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('loans.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('loans.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <a href="{{ route('loan_collectors.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                class="ion-model-s"> </span> Loan Collector</a>
            <a href="{{ route('loan_payments.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="ion-jet"> </span> Loan Payment</a>
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-8'>
                        @include('forms.loan')
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

            $('#payment_mode').on("change", function() {

                $("#bank_account_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.loadBankAccounts') }}",
                    type: 'GET',
                    data: {
                        bank_account_id: $("#payment_mode").val()
                    }
                }).done(function(msg) {
                    $("#bank_account_id").html("<option value=''>--select--</option>" + msg);
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
