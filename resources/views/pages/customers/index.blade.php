@extends('layouts.backend.app')

@section('title', 'Customer')

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
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Manage Customer</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Customer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        @can('customers.create')
                            <a class="btn btn-secondary btn-sm" href="{{ route('customers.create') }}">
                                <span class="fa fa-plus-circle"> New Customer</span>
                            </a>
                        @endcan
                        @can('customers.import.form')
                            <a class="btn btn-secondary btn-sm" href="{{ route('customers.import.form') }}">
                                <span class="fa fa-upload"> Upload Customers</span>
                            </a>
                        @endcan
                        @can('customer.ledger')
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#customer_ledgerform"
                               class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span class="fa fa-money"> Customer Ledger</span>
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Customers</h3>
                    </div>
                    <div class="card-body">
                        <form class="filter-customer-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select class="form-control ajax-branches select2-single" name="branch_id" id="branch_id"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select class="form-control select2-single" name="type" id="type">
                                            <option value="%">Select type...</option>
                                            <option value="Retail">Retail</option>
                                            <option value="Wholesale">Wholesale</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input class="form-control form-control-sm" name="keyword" id="keyword" placeholder="Keyword" />
                                    </div>
                                </div>
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-success btn-sm" type="submit">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="row mt-3">
                            <div class="col-sm-12 col-md-12 table-responsive load-customers">

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
{{--    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>--}}


    <script type="text/javascript">

        // $(document).ready(function(){
            let customers = { branch_id: '{{ request()->get('branch_id') }}', type: '{{ request()->get('type') }}', keyword: '{{ request()->get('keyword') }}' }
            ajax_loading('{{ route('ajax.customer.list') }}', 'get', 'load-customers', customers)
        // })

    </script>


@endpush
