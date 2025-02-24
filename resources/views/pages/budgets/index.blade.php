@extends('layouts.backend.app')

@section('title', 'Budget List')

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
                        <h4>Manage Budgets</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Budgets</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        @can('budget.create')
                            <a class="btn btn-secondary btn-sm mb-3" href="{{ route('budgets.create') }}">
                                <span class="fa fa-plus-circle"></span> Add New Budget
                            </a>
                        @endcan
                        @can('budget.import')
                            <a class="btn btn-secondary btn-sm mb-3" href="{{ route('budgets.import.form') }}">
                                <span class="fa fa-upload"></span> Import Budget
                            </a>
                        @endcan
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Budget List</h3>
                            </div>
                            <div class="card-body">
                                <table class="display table table-bordered" id="record1">
                                    <thead>
                                        <tr>
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
                                        @foreach ($budgets as $budget)
                                            <tr>
                                                <td>{{ $budget->branch->name }}</td>
                                                <td>{{ $budget->category->name }}</td>
                                                <td>{{ $budget->budget_year }}</td>
                                                <td>{{ $budget->quarter }}</td>
                                                <td>{{ $budget->month1 }}</td>
                                                <td>{{ $budget->month2 }}</td>
                                                <td>{{ $budget->month3 }}</td>
                                                <td>{{ $budget->total }}</td>
                                                <td>
                                                    @can('budget.edit')
                                                        <a href="{{ route('budgets.edit', $budget->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('budget.delete')
                                                        <form action="{{ route('budgets.destroy', $budget->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Are you sure?')">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                {{ $budgets->links() }} <!-- Pagination -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    {{--    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> --}}
    <script type="text/javascript">
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
