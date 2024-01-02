@extends('layouts.backend.app')
@section('title', 'Manage General Accounts')

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
                        <h4>Add General Accounts</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('general_accounts.index') }}">General Accounts</a>
                            </li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('general_accounts.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('general_accounts.create') }}">
                    <span class="fa fa-plus-circle"> New G Account</span>
                </a>
            @endcan
            @can('general_accounts.import.form')
                <a class="btn btn-secondary btn-sm" href="{{ route('general_accounts.import.form') }}">
                    <span class="fa fa-upload"> Upload G Accounts</span>
                </a>
            @endcan

            @can('general_accounts.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('general_accounts.index') }}">
                    <span class="fa fa-list"> View Accounts</span>
                </a>
            @endcan
            <br />
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        @include('forms.general_account')
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection
@push('js')
    <script type="text/javascript"></script>
@endpush
