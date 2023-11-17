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
                        <h4>Interstore Stock Transfer</h4>
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

            <div class="container">
                <a class="btn btn-secondary btn-sm" href="{{ route('interstore.create') }}">
                    <span class="fa fa-plus-circle"></span> New
                </a>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Interstore Transfer</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form action="{{route('interstore.search')}}" method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <input type="search" class="form-control rounded" required placeholder="Search" name="refno" aria-label="Search" aria-describedby="search-addon" />
                                        <button type="submit" class="btn btn-outline-primary">search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                @isset($records)
                                    <table class="table table-striped" id="record1">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Product</th>
                                            <th>Source Store</th>
                                            <th>Destination Store</th>
                                            <th>Qty Transfered </th>
                                            {{-- <th>Stock In/Out </th> --}}
                                            <th>Transfered By </th>
                                            <th>Status</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($records as $record)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td> {{ $record->transfer_date }} </td>
                                                <td> {{ $record->reference }} </td>
                                                <td>{{ $record->product->code }} - {{ $record->product->name }} </td>
                                                <td> {{ $record->source->code }} - {{ $record->source->name }} </td>
                                                <td> {{ $record->destination->code }} - {{ $record->destination->name }} </td>
                                                <td> {{ $record->qty_transfered }} </td>

                                                {{-- <td> {{ $record->stock_in_out }} </td> --}}
                                                <td> {{ optional($record->user)->name }} </td>
                                                <td> {{ $record->status == 1 ? 'Completed' : 'Cancelled' }} </td>
                                                <td>
                                                    <a class="btn btn-secondary btn-sm" title="Stock Tranfer Report" target="_BLANK"
                                                       href="{{ route('interstore.print', $record->transfer_id) }}">
                                                        <span class="fa fa-print"></span> Print
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endisset
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
