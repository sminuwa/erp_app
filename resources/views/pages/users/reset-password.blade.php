@extends('layouts.backend.app')

@section('title', 'Manage User')

@push('css')
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container">
                <div class="row mb-2 mt-3">
                    <div class="col-sm-6">
                        <h4>Reset password :: User Account of <small>{{ $model->name }}</small></h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            @can('users.index')
                                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                            @endcan
                            <li class="breadcrumb-item active">Reset Password</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container">
                <div class="row">
                    <div class='col-md-4'>
                        <div class='card'>

                            <div class="card-body">
                                @if(session()->has('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                    @if(session()->has('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif
                                <form action="" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="user_id" value="{{ isset($model) ? $model->id : 0 }}" />

                                    <div class="form-group">
                                        <label for="user_code">Password</label>
                                        <input type="text" class="form-control" name="password" required="required" placeholder="Enter password">
                                    </div>
                                    <div class="form-group">
                                        <label for="user_code">Confirm Password</label>
                                        <input type="text" class="form-control" name="confirm_password" required="required" placeholder="Confirm password">
                                    </div>

                                    <p class="text text-danger"><span class="ion-alert-circled"></span></p>
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary"><span class="ion-ios-locked"> </span> Create</button>
                                    </div>
                                </form>

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
