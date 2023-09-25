@extends('layouts.backend.app')

@section('title', 'Import Chart of Account')

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
                        <h4>Import Chart of Accounts</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('chart_of_accounts.index') }}">Chart of Acconuts</a></li>
                            <li class="breadcrumb-item active">Import</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('chart_of_accounts.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('chart_of_accounts.index') }}">
                <span class="fa fa-list"></span>
            </a><br/>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <form action="{{ route('price.import.form') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" class="form-control">
                            <button type="submit" class="btn btn-primary">Import</button>
                        </form>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection

@push('js')

@endpush
