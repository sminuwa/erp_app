@extends('layouts.backend.app')

@section('title', 'Dashboard')

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
                        <h1>Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Categories</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('categories.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('categories.create') }}">
                    <span class="fa fa-plus-circle"> New Categories</span>
                </a>
            @endcan
            @can('categories.import.form')
                <a class="btn btn-secondary btn-sm" href="{{ route('categories.import.form') }}">
                    <span class="fa fa-upload"> Upload Categories</span>
                </a>
            @endcan
            @can('categories.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('categories.index') }}">
                    <span class="fa fa-list"> View Categories</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <div class='card'>

                            <div class="card-body">
                                @include('forms.category', [
                                    'route' => route('categories.update', $model->id),
                                    'method' => 'PUT',
                                ])
                            </div>
                        </div>
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
