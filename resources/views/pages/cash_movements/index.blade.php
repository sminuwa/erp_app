@extends('layouts.backend.app')

@section('title', 'Bank Doposit')

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
                        <h4>Bank Transactions (Withdraw & Deposit)</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Bank Withdraw & Deposit</li>
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
            <a href="{{ route('cash_movements.create') }}" class="btn btn-sm btn-secondary"
                style="margin-left: 2px;"><span class="ion-jet"> </span> New Deposit & Withdraw</a>
            <a href="{{ route('deposits.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="ion-jet"> </span> New Deposit</a>
            <a href="{{ route('withdraw.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="ion-model-s"> </span> New Withdraw</a>
            <a href="{{ route('bank.ledger') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="ion-model-s"> </span> Bank Ledger</a>
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('cash_movements.search') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="search" class="form-control rounded" required placeholder="Search by Slip number"
                                name="refno" aria-label="Search" aria-describedby="search-addon" />
                            <button type="submit" class="btn btn-outline-primary">search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        @include('tables.cash_movement')
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 text-danger">
                        <strong>Total Record is of
                            {{ number_format(App\Models\CashMovement::count('*'), 0, ',', '') }}</strong>
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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable({
                pageLength: 10
            });
            $('#record2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                'pageLength': 1
            });
        });
    </script>
@endpush
