@extends('layouts.backend.app')

@section('title', 'Roles')

@push('css')
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Stock Adjustment</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Stock Adjustment</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.create') }}">
                    <span class="fa fa-plus-circle"></span> New
                </a>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-striped" id="record1">
                                    <thead>
                                    <tr>
                                        <th>Date </th>
                                        <th>Reference </th>
                                        <th>Operation </th>
                                        <th>Description </th>
                                        <th>Created By </th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($records as $record)
                                        <tr>
                                            <td> {{ \Carbon\Carbon::parse($record->date)->toFormattedDateString() }} </td>
                                            <td> {{ $record->reference }} </td>
                                            <td> {{ $record->operation }} </td>
                                            <td> {{ $record->description ?? null }} </td>
                                            <td> {{ $record->createdBy->name ?? null }} </td>
                                            <td style="text-align: right">
                                                @can('make.stock.adjustment')
                                                    <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.show', $record->id) }}">
                                                        <span class="fa fa-eye"></span>
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
