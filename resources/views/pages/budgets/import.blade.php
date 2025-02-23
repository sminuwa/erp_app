@extends('layouts.backend.app')

@section('title', 'Import Budget')

@push('css')
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Import Budget</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Import Budget</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-secondary btn-sm mb-3" href="{{ route('budgets.create') }}">
                        <span class="fa fa-plus-circle"></span> Add New Budget
                    </a>
                    <a class="btn btn-secondary btn-sm mb-3" href="{{asset('upload_templates/budget.xlsx')}}">
                        <span class="fa fa-download"></span> Download Budget Template
                    </a>
                    <div class="card">
                        <div class="card-header">Upload Budget File</div>
                        <div class="card-body">
                            <form action="{{ route('budgets.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="file">Excel File</label>
                                    <input type="file" name="file" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Import</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
