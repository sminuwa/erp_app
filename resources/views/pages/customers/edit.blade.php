@extends('layouts.backend.app')

@section('title', 'Customter')

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
                        <h4>Customer</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Customer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('customers.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('customers.create') }}">
                    <span class="fa fa-plus-circle"> New Customer</span>
                </a>
            @endcan
            @can('customers.import.form')
                <a class="btn btn-secondary btn-sm" href="{{ route('customers.import.form') }}">
                    <span class="fa fa-upload"> Upload Customers</span>
                </a>
            @endcan
            @can('customers.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('customers.index') }}">
                    <span class="fa fa-list"> View Customers</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <div class='card'>

                            <div class="card-body">
                                @include('forms.customer', [
                                    'route' => route('customers.update', $model->id),
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
