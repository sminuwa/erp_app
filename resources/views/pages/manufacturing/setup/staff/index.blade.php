@extends('layouts.backend.app')

@section('title', 'Manufacturing Staff')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Manufacturing Staff</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item active">Staff</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.staff.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.staff.create') }}">
                    <span class="fa fa-plus-circle"></span> Add New
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive mt-3">
                        @include('tables.manufacturing_staff')
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable({
                'iDisplayLength': 100
            });
        });
    </script>
@endpush
