@extends('layouts.backend.app')

@section('title', 'Prices')

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
                        <h4>Import General Accounts</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('general_accounts.index') }}">General Accounts</a>
                            </li>
                            <li class="breadcrumb-item active">Import</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('general_accounts.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('general_accounts.create') }}">
                    <span class="fa fa-plus-circle"> New General Account</span>
                </a>
            @endcan
            <a class="btn btn-secondary btn-sm" href="{{ url('upload_templates/general_accounts_template.xlsx') }}">
                <span class="fa fa-download"> Template</span>
            </a>
            <br />
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <form action="{{ route('general_accounts.import') }}" method="POST" enctype="multipart/form-data">
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
