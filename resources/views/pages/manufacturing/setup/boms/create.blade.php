@extends('layouts.backend.app')

@section('title', 'Create Bill of Materials')

@push('css')
<style>
    .nav-tabs .nav-link.active {
        font-weight: bold;
    }
    .material-row td {
        padding: 5px;
        vertical-align: middle;
    }
    .cost-display {
        font-weight: bold;
        color: #28a745;
    }
</style>
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Create Bill of Materials</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.boms.index') }}">BOM</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.boms.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.boms.create') }}">
                    <span class="fa fa-plus-circle"></span>
                </a>
            @endcan
            <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.boms.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <div class="container-fluid mt-3">
                @include('forms.manufacturing_bom')
            </div>
        </section>
    </div>
@endsection

@push('js')
    @include('pages.manufacturing.setup.boms._scripts')
@endpush
