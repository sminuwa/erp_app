@extends('layouts.backend.app')

@section('title', 'Budget Expenditures')

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
                        <h4>Manage Budget Expenditures</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Budget Expenditures</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="mb-3">
                    <a href="{{ route('budget_expenditures.create') }}" class="btn btn-secondary btn-sm mb-3"><span class="fa fa-plus-circle"></span> New Entry</a>
                    <a href="{{ route('budget_expenditures.import') }}" class="btn btn-secondary btn-sm mb-3"><span class="fa fa fa-upload"></span> Import</a>
                    <a href="{{ route('budget_expenditures.downloadTemplate') }}" class="btn btn-secondary btn-sm mb-3"><span class="fa fa-download"></span> Download Template</a>
                </div>

                <div class="card">
                    <div class="card-header">Budget Entries</div>
                    <div class="card-body">
                        <table class="table table-bordered" id="record2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Branch</th>
                                    <th>Account</th>
                                    <th>Budget Year</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($budgets as $index => $budget)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $budget->branch->name ?? '' }} ({{ $budget->branch->code ?? '' }})</td>
                                        <td>{{ $budget->account->description ?? '' }} ({{ $budget->account->number ?? '' }})
                                        </td>
                                        <td>{{ $budget->budget_year }}</td>
                                        <td style="text-align: right;">{{ number_format($budget->proposed_budget, 2) }}</td>
                                        <td>
                                            <a href="{{ route('budget_expenditures.edit', $budget->id) }}"
                                                class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                            <form action="{{ route('budget_expenditures.destroy', $budget->id) }}"
                                                method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable({
                'iDisplayLength': 100
            });
            $('#record2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
@endpush
