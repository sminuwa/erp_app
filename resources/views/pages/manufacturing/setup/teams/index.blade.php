@extends('layouts.backend.app')

@section('title', 'Manufacturing Teams')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Production Teams</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item active">Teams</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.teams.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.teams.create') }}">
                    <span class="fa fa-plus-circle"></span> Add New
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive mt-3">
                        @include('tables.manufacturing_team')
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
