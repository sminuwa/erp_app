@extends('layouts.backend.app')

@section('title', 'Manage RO Budgets')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>RO Budgets</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">RO Budgets</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <a href="{{ route('ro_budgets.create') }}" class="btn btn-primary btn-sm mb-3"><span
                        class="fa fa-plus-circle"></span> Add New Budget</a>
                <a class="btn btn-secondary btn-sm mb-3" href="{{ route('ro_budgets.import') }}">
                    <span class="fa fa-upload"></span> Import Budget
                </a>
                {{-- <form action="{{ route('ro_budgets.import.store') }}" method="POST" enctype="multipart/form-data"
                    class="mb-3" onsubmit="disableButton()">
                    @csrf
                    <input type="file" name="file" required>
                    <button type="submit" id="import-button" class="btn btn-sm btn-primary">
                        Import
                    </button>
                </form> --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">RO Budget List</h3>
                    </div>
                    <div class="card-body">
                        <table class="display table table-bordered" id="record1">
                            <thead>
                                <tr>
                                    <th>RO Code</th>
                                    <th>RO Name</th>
                                    <th>Branch</th>
                                    <th>Category</th>
                                    <th>Year</th>
                                    <th>Quarter</th>
                                    <th>Month 1 (%)</th>
                                    <th>Month 2(%)</th>
                                    <th>Month 3(%)</th>
                                    <th>Total (%)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($budgets as $budget)
                                    <tr>
                                        <td>{{ $budget->staff->user_code }}</td>
                                        <td>{{ $budget->staff->name }}</td>
                                        <td>{{ $budget->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $budget->category->name ?? 'N/A' }}</td>
                                        <td>{{ $budget->budget_year }}</td>
                                        <td>{{ $budget->quarter }}</td>
                                        <td>{{ $budget->month1 }}</td>
                                        <td>{{ $budget->month2 }}</td>
                                        <td>{{ $budget->month3 }}</td>
                                        <td>{{ $budget->total }}</td>
                                        <td>

                                            <a class="btn btn-sm btn-primary"
                                                href="{{ route('ro_budgets.edit', $budget->id) }}">
                                                <span class="fa fa-pencil"></span>
                                            </a>
                                            <form action="{{ route('ro_budgets.destroy', $budget->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><span
                                                        class="fa fa-trash"></span></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <script>
        function disableButton() {
            let button = document.getElementById('import-button');
            button.disabled = true;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Importing...';
        }
        $(function() {
            $("#record1").DataTable({
                'iDisplayLength': 100
            });
            $('#record2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
@endpush
