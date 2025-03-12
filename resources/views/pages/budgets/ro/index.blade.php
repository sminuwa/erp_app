@extends('layouts.backend.app')

@section('title', 'Manage RO Budgets')

@push('css')
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
            <a href="{{ route('ro_budgets.create') }}" class="btn btn-primary btn-sm mb-3"><span class="fa fa-plus-circle"></span> Add New Budget</a>
            <a class="btn btn-secondary btn-sm mb-3" href="{{ route('ro_budgets.import') }}">
                <span class="fa fa-upload"></span> Import Budget
            </a>
            {{-- <form action="{{ route('ro_budgets.import.store') }}" method="POST" enctype="multipart/form-data" class="mb-3">
                @csrf
                <input type="file" name="file" required>
                <button type="submit" class="btn btn-success">Import</button>
            </form> --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">RO Budget List</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Staff</th>
                                <th>Branch</th>
                                <th>Category</th>
                                <th>Year</th>
                                <th>Quarter</th>
                                <th>Month 1</th>
                                <th>Month 2</th>
                                <th>Month 3</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($budgets as $budget)
                            <tr>
                                <td>{{ $budget->id }}</td>
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
                                    <a href="{{ route('ro_budgets.edit', $budget->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('ro_budgets.destroy', $budget->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $budgets->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
