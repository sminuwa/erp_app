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
                        <h4>Add Unit of Measures</h4>
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
            <a class="btn btn-secondary btn-sm" href="{{ route('product_unit_measures.create') }}">
                <span class="fa fa-plus-circle">New Product </span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('product_unit_measures.index') }}">
                <span class="fa fa-list"> View UTM</span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        @include('forms.product_unit_measure')
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
        $('#category_id').on('change', function(event) {
            category_id = $(this).val();
            $.ajax({
                type: 'GET',
                url: "{{ route('generate.productCode') }}",
                data: {
                    category_id: category_id
                }
            }).done(function(data) {
                $('#code').val(data);
            });
        });
    </script>
@endpush
