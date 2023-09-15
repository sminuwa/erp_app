@extends('layouts.backend.app')

@section('title', 'Ledger')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
    <style>
        caption {
            caption-side: top;
        }

    </style>
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
                        <h4>Purchase Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Purchase/li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        @include('cards.purchase')
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-header">
                                Purchased Products
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered" id="record1">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Product</th>
                                            <th>QTY</th>
                                            <th>Unit Price (&#8358;)</th>
                                            <th>Subtotal (&#8358;)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach ($record->purchasedProducts()->get() as $product)
                                            <tr>
                                                <th>{{ $loop->index + 1 }}</th>
                                                <td>{{ $product->product->name }}</td>
                                                <td>{{ number_format($product->qty_supplied, 0, '', ',') }}</td>
                                                <td style="text-align: right">{{ number_format($product->unit_price, 2) }}
                                                </td>
                                                <td style="text-align: right">
                                                    {{ number_format($product->qty_supplied * $product->unit_price, 2) }}
                                                </td>
                                                <td>{{ $product->status == 1 ? 'Completed' : 'Cancelled' }}</td>
                                                @php $total += $product->unit_price * $product->qty_supplied; @endphp
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right">Total</th>
                                        <th style="text-align: right">&#8358;{{ number_format($total, 2) }}</th>
                                        <th></th>
                                    </tfoot>
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
    <!-- DataTables -->
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/backend/plugins/fastclick/fastclick.js') }}"></script>

    <!-- Sweet Alert Js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.1/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable();
        });
    </script>
@endpush
