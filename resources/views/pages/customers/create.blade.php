@extends('layouts.backend.app')
@section('title', 'Customer')

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
                        <h4>Add Customer</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Customers</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
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
            @can('customers.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('customers.index') }}">
                    <span class="fa fa-list"> View Customers</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        @include('forms.customer')
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
            $(document).on("change", "#bank_id", function(event) {
                $("#bank_branch_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.loadbranches') }}",
                    type: 'GET',
                    data: {
                        bank_id: $("#bank_id").val()
                    }
                }).done(function(msg) {
                    $("#bank_branch_id").html("<option value=''>--select--</option>" + msg);
                });
            });

            $(document).on("change", "#account_type", function(event) {
                $("#code").val("Loading...");
                $.ajax({
                    url: "{{ route('generate.customerCode') }}",
                    type: 'GET',
                    data: {
                        account_type: $("#account_type").val()
                    }
                }).done(function(msg) {
                    $("#code").val(msg);
                });
            });
        });
    </script>
@endpush
