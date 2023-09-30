@extends('layouts.backend.app')
@section('title', 'Manage Prices')

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
                        <h4>Manage Purchase Prices</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Product Price</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('branch_product_prices.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('branch_product_prices.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('price.import.form') }}">
                <span class="fa fa-upload"></span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>

                        @include('forms.purchase_price')
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection
@push('js')

    <script type="text/javascript">

    </script>
@endpush
