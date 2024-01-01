@extends('layouts.backend.app')

@section('title', 'Prices')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
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
                        <h4>Import Prices</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('branch_product_prices.index') }}">Pricse</a></li>
                            <li class="breadcrumb-item active">Import</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('branch_product_prices.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('branch_product_prices.create') }}">
                    <span class="fa fa-plus-circle"> New Price</span>
                </a>
            @endcan
            <a class="btn btn-secondary btn-sm" href="{{ url('upload_templates/price_template.xlsx') }}">
                <span class="fa fa-download"> Template</span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <form action="{{ route('price.import.form') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" class="form-control">
                            <button type="submit" class="btn btn-primary">Import</button>
                        </form>
                        @if (isset($faileds))
                            <h4 class="text text-danger">Prices of {{ $count }} products were successfully updated,
                                however, there are {{ count($faileds) }} invalid product codes that are failed to update as
                                shown below:</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Product Code</th>
                                        <th>Cost Price</th>
                                        <th>Selling Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 0; $i < count($faileds); $i++)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $faileds[$i][0] }}</td>
                                            <td>{{ $faileds[$i][1] }}</td>
                                            <td>{{ $faileds[$i][2] }}</td>

                                        </tr>
                                    @endfor
                                </tbody>

                            </table>
                        @endif
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
            $("#record1").DataTable({
                'iDisplayLength': 100
            });
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
