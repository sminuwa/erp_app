@extends('layouts.backend.app')

@section('title', 'Journal')

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
                        <h4>Edit Journal </h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('journal.index') }}">Journals List</a></li>
                            <li class="breadcrumb-item active">New Journal</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a href="{{ route('journal.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;">
                <span class="fa fa-plus-circle"> </span> New Journal
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('journal.index') }}">
                <span class="fa fa-list"></span> Journals List
            </a>
            <div class="container-fluid py-4">
                <livewire:edit-journal :journal="$journal" />
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection



@push('css')


    @livewireStyles
@endpush
@push('js')
    @livewireScripts
    <script>

    </script>
@endpush
