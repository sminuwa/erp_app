@extends('layouts.backend.app')

@section('title', 'Loan Collector Payment')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
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
                        <h4>Loan Collector Payments</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('loan_collectors.index') }}">Loan Collector</a></li>
                            <li class="breadcrumb-item active">Loan Collector Payments</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('loan_payments.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('loan_payments.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <a href="{{ route('loan_collectors.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                class="ion-model-s"> </span> Loan Collector</a>
            <a href="{{ route('loans.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="ion-jet"> </span> Grant Loan</a>
            <a href="{{ route('bank.ledger') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="ion-model-s"> </span> Bank Ledger</a>
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('loan_payments.search') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="search" class="form-control rounded" required placeholder="Search by Receipt No, or cheque or collector reg code"
                                name="refno" aria-label="Search" aria-describedby="search-addon" />
                            <button type="submit" class="btn btn-outline-primary">search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        @include('tables.loan_payment')
                    </div>

                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
{{--    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>--}}
    <script type="text/javascript">
        $(function() {
             $("#record1").DataTable({
                'iDisplayLength':100
            });
            $('#record2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
@endpush
