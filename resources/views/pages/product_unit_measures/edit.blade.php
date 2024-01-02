@extends('layouts.backend.app')

@section('title', 'Products')

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
                        <h1>Edit Product Unit of Measure<small>{{ $model->product->code }}</small></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            @can('product_unit_measures.index')
                                <li class="breadcrumb-item"><a href="{{ route('product_unit_measures.index') }}">UTM</a></li>
                            @endcan
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('product_unit_measures.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('product_unit_measures.create') }}">
                    <span class="fa fa-plus-circle"> New UTM</span>
                </a>
            @endcan
            @can('categories.import.form')
                <a class="btn btn-secondary btn-sm" href="{{ route('categories.import.form') }}">
                    <span class="fa fa-upload"> Upload UTM</span>
                </a>
            @endcan
            @can('product_unit_measures.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('product_unit_measures.index') }}">
                    <span class="fa fa-list"> View UTM</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <div class='card'>
                            <div class="card-body">
                                @include('forms.product_unit_measure', [
                                    'route' => route('product_unit_measures.update', $model->id),
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
