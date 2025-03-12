@extends('layouts.backend.app')

@section('title', 'Import RO Budgets')

@push('css')
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Import RO Budgets</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Import RO Budgets</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">Upload Budget File</div>
                <div class="card-body">
                    <a href="{{ route('ro_budgets.index') }}" class="btn btn-sm btn-secondary mb-3">
                        <i class="fa fa-list"></i> List
                    </a>
                    <a href="{{ route('ro_budgets.download_template') }}" class="btn btn-sm btn-secondary mb-3">
                        <i class="fa fa-download"></i> Download Budget Template
                    </a>
                    <form action="{{ route('ro_budgets.import.store') }}" method="POST" enctype="multipart/form-data" onsubmit="disableButton()">
                        @csrf
                        <div class="form-group">
                            <label for="file">Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <button type="submit" id="import-button" class="btn btn-sm btn-primary">
                            Import
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
<script>
    function disableButton() {
        let button = document.getElementById('import-button');
        button.disabled = true;
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Importing...';
    }
</script>
@endpush
