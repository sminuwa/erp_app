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
                        <h4>Product Details</h4>
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
            @can('products.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('products.create') }}">
                    <span class="fa fa-plus"></span>
                </a>
            @endcan
            @can('products.edit')
                <a class="btn btn-secondary btn-sm" href="{{ route('products.edit', $record->id) }}">
                    <span class="fa fa-pencil"></span>
                </a>
            @endcan
            @can('products.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('products.index') }}">
                    <span class="fa fa-list"></span>
                </a>
            @endcan
            @can('products.destroy')
                <form onsubmit="return confirm('Are you sure you want to delete?')"
                    action="{{ route('products.destroy', $record->id) }}" method="post" style="display: inline">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                        <i class="text-danger fa fa-remove"></i>
                    </button>
                </form>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4">
                        @include('cards.product')
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
