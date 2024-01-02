@extends('layouts.backend.app')
@section('title', 'Manage Branch')

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
                        <h4>Add Branch</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Manage Branches</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('branches.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('branches.create') }}">
                    <span class="fa fa-plus-circle"> New Branch</span>
                </a>
            @endcan
            @can('branches.import.form')
                <a class="btn btn-secondary btn-sm" href="{{ route('branches.import.form') }}">
                    <span class="fa fa-upload"> Upload Prices</span>
                </a>
            @endcan
            @can('branches.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('branches.index') }}">
                    <span class="fa fa-list"> View Branch</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        @include('forms.branch')
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
