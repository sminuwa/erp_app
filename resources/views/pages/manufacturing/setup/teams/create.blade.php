@extends('layouts.backend.app')

@section('title', 'Add Production Team')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Add Production Team</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.teams.index') }}">Teams</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.teams.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.teams.create') }}">
                    <span class="fa fa-plus-circle"></span>
                </a>
            @endcan
            <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.teams.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-8'>
                        @include('forms.manufacturing_team')
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(function() {
            $('.select2-multiple').select2({
                placeholder: 'Select...',
                allowClear: true
            });
        });
    </script>
@endpush
