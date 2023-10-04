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
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 ">
                        @include('pages.interbanks.form')
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
