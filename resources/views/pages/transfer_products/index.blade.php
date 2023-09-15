@extends('layouts.backend.app')

@section('title', 'Stock Tranfer')

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
                        <h4>Stock Transfer</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stores.index') }}">Store</a></li>
                            <li class="breadcrumb-item active">Stock Tranfer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('transfer_products.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{route('transfer_products.search')}}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="search" class="form-control rounded" required placeholder="Search" name="refno" aria-label="Search" aria-describedby="search-addon" />
                                <button type="submit" class="btn btn-outline-primary">search</button>
                              </div>
                        </form>
                    </div>
                </div>
                <div class="row table-responsive-lg">
                    <div class="col-sm-12 table-responsive-lg">
                        @include('tables.transfer_product')
                    </div>

                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection

@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable();
            $('#record2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
@endpush
