@extends('layouts.backend.app')

@section('title', 'Import Budget Expenditures')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Import Budget Expenditures</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Import Budget Expenditures</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="mb-3">
                    <a href="{{ route('budget_expenditures.index') }}" class="btn btn-secondary btn-sm mb-3"><span
                            class="fa fa-plus-circle"></span>Budget List</a>
                    <a href="{{ route('budget_expenditures.create') }}" class="btn btn-secondary btn-sm mb-3"><span
                            class="fa fa-cloud-download"></span> New Entry</a>
                    <a href="{{ route('budget_expenditures.downloadTemplate') }}"
                        class="btn btn-secondary btn-sm mb-3"><span class="fa fa-download"></span> Download Template</a>
                </div>
                <div class="card">
                    <div class="card-header">Upload Excel File</div>
                    <div class="card-body">
                        <a href="{{ route('budget_expenditures.downloadTemplate') }}" class="btn btn-sm btn-info mb-3">
                            <i class="fa fa-download"></i> Download Template
                        </a>
                        <form action="{{ route('budget_expenditures.import.store') }}" method="POST"
                            enctype="multipart/form-data" id="importForm">
                            @csrf
                            <div class="form-group">
                                <label for="file">Select Excel File</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                            <button type="submit" id="importBtn" class="btn btn-primary">
                                <span id="btnText">Import</span>
                                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
                            </button>
                        </form>

                        @if (session('import_errors'))
                            <div class="alert alert-warning mt-4">
                                <strong>Some rows were skipped:</strong>
                                <ul>
                                    @foreach (session('import_errors') as $error)
                                        <li>{{ json_encode($error) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script>
        function disableButton() {
            let button = document.getElementById('importForm');
            button.disabled = true;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Importing...';
        }
    </script>
@endpush
