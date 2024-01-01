@extends('layouts.backend.app')
@section('title', 'Manage Products')

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
                        <h5>Create New product expiration date</h5>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Products</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('product_expire_settings.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('product_expire_settings.create') }}">
                    <span class="fa fa-plus-circle"></span>
                </a>
            @endcan
            @can('product_expire_settings.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('product_expire_settings.index') }}">
                    <span class="fa fa-list"></span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        @include('forms.product_expire_setting')
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
