@extends('layouts.backend.app')

@section('title', isset($budgetExpenditure) ? 'Edit Budget Expenditure' : 'Create Budget Expenditure')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>{{ isset($budgetExpenditure) ? 'Edit Budget Expenditure' : 'Create Budget Expenditure' }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">
                                {{ isset($budgetExpenditure) ? 'Edit Budget Expenditure' : 'Create Budget Expenditure' }}
                            </li>
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
                    <a href="{{ route('budget_expenditures.import') }}" class="btn btn-secondary btn-sm mb-3"><span
                            class="fa fa fa-upload"></span> Import</a>
                    <a href="{{ route('budget_expenditures.downloadTemplate') }}"
                        class="btn btn-secondary btn-sm mb-3"><span class="fa fa-download"></span> Download Template</a>
                </div>
                <div class="card">
                    <div class="card-header">{{ isset($budgetExpenditure) ? 'Update' : 'New' }} Budget Form</div>
                    <div class="card-body">
                        <form
                            action="{{ isset($budgetExpenditure) ? route('budget_expenditures.update', $budgetExpenditure->id) : route('budget_expenditures.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($budgetExpenditure))
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label for="branch_id">Branch</label>
                                <select name="branch_id" class="form-control select2-single" required>
                                    <option>Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ isset($budgetExpenditure) && $budgetExpenditure->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }} ({{ $branch->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="general_account_id">Account</label>
                                <select name="general_account_id" class="form-control select2-single" required>
                                    <option>Select Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ isset($budgetExpenditure) && $budgetExpenditure->general_account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->description }} ({{ $account->number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="budget_year">Budget Year</label>
                                <select name="budget_year" class="form-control" required>
                                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}"
                                            {{ isset($budgetExpenditure) && $budgetExpenditure->budget_year == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="proposed_budget">Proposed Budget Amount</label>
                                <input type="number" name="proposed_budget" step="0.01" class="form-control"
                                    value="{{ isset($budgetExpenditure) ? $budgetExpenditure->proposed_budget : old('proposed_budget') }}"
                                    required>
                            </div>

                            <button type="submit"
                                class="btn btn-primary">{{ isset($budgetExpenditure) ? 'Update' : 'Submit' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
