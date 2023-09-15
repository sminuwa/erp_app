@extends('layouts.backend.app')
@section('title', 'Bank Deposit')

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
                        <h4>Bank Deposit</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Bank Deposit</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('cash_movements.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <a href="{{ route('deposits.create') }}" class="btn btn-sm btn-secondary"
            style="margin-left: 2px;"><span class="ion-jet"> </span> New Deposit</a>
            <a href="{{ route('withdraw.create') }}" class="btn btn-sm btn-secondary"
            style="margin-left: 2px;"><span class="ion-jet"> </span> New Withdraw</a>
            <a href="{{ route('cash_movements.create') }}" class="btn btn-sm btn-secondary"
            style="margin-left: 2px;"><span class="ion-model-s"> </span> New Withdraw & Deposit</a>
            <a href="{{ route('bank.ledger') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                class="ion-model-s"> </span> Bank Ledger</a>
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-12'>
                        @include('forms.deposit')
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
        $("#amount").on("keyup",function(event) {
            var stt = $(this).val();
            $("#to_amount").val(stt);
        });
        $("#slip_no").on("keyup",function(event) {
            var stt = $(this).val();
            $("#deposit_slip_no").val(stt);
        });
        $('#from_account').on("change", function() {
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
        
        $('#from_account').on("change", function() {
            bank_account_id = $(this).val();
            $.ajax({
                type: "GET",
                url: "{{ route('ajax.load.account.balance') }}",
                data: {
                    bank_account_id: bank_account_id
                }
            }).done(function(data) {
                $("#balance").val(formatMoney(data));
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
    </script>
@endpush
