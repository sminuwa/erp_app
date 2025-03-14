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
                        <h4>{{ isset($roBudget) ? 'Edit RO Budget' : 'Create RO Budget' }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">{{ isset($roBudget) ? 'Edit RO Budget' : 'Create RO Budget' }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">{{ isset($roBudget) ? 'Edit RO Budget' : 'Add New RO Budget' }}</div>
                    <div class="card-body">
                        <form
                            action="{{ isset($roBudget) ? route('ro_budgets.update', $roBudget->id) : route('ro_budgets.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($roBudget))
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label for="staff_id">Staff</label>
                                <select name="staff_id" class="form-control" required>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}"
                                            {{ isset($roBudget) && $roBudget->staff_id == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="branch_id">Branch</label>
                                <select name="branch_id" class="form-control">
                                    <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ isset($roBudget) && $roBudget->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select name="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ isset($roBudget) && $roBudget->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="budget_year">Budget Year</label>
                                <select name="budget_year" class="form-control" required>
                                    @for ($i = date('Y'); $i <= date('Y') + 5; $i++)
                                        <option value="{{ $i }}"
                                            {{ isset($roBudget) && $roBudget->budget_year == $i ? 'selected' : '' }}>
                                            {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="quarter">Quarter</label>
                                <select name="quarter" class="form-control" required>
                                    <option value="Q1"
                                        {{ isset($roBudget) && $roBudget->quarter == 'Q1' ? 'selected' : '' }}>Q1</option>
                                    <option value="Q2"
                                        {{ isset($roBudget) && $roBudget->quarter == 'Q2' ? 'selected' : '' }}>Q2</option>
                                    <option value="Q3"
                                        {{ isset($roBudget) && $roBudget->quarter == 'Q3' ? 'selected' : '' }}>Q3</option>
                                    <option value="Q4"
                                        {{ isset($roBudget) && $roBudget->quarter == 'Q4' ? 'selected' : '' }}>Q4</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="month1">Month 1</label>
                                <input type="number" name="month1" id="month1" class="form-control"
                                    value="{{ isset($roBudget) ? $roBudget->month1 : old('month1') }}" required
                                    oninput="calculateTotal()">
                            </div>

                            <div class="form-group">
                                <label for="month2">Month 2</label>
                                <input type="number" name="month2" id="month2" class="form-control"
                                    value="{{ isset($roBudget) ? $roBudget->month2 : old('month2') }}" required
                                    oninput="calculateTotal()">
                            </div>

                            <div class="form-group">
                                <label for="month3">Month 3</label>
                                <input type="number" name="month3" id="month3" class="form-control"
                                    value="{{ isset($roBudget) ? $roBudget->month3 : old('month3') }}" required
                                    oninput="calculateTotal()">
                            </div>

                            <div class="form-group">
                                <label for="total">Total</label>
                                <input type="number" name="total" id="total" class="form-control"
                                    value="{{ isset($roBudget) ? $roBudget->total : old('total') }}" readonly>
                            </div>

                            <button type="submit"
                                class="btn btn-primary">{{ isset($roBudget) ? 'Update' : 'Create' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script>
        function calculateTotal() {
            let month1 = parseFloat(document.getElementById('month1').value) || 0;
            let month2 = parseFloat(document.getElementById('month2').value) || 0;
            let month3 = parseFloat(document.getElementById('month3').value) || 0;

            let total = month1 + month2 + month3;
            if (total > 100) {
                alert('Total budget allocation cannot exceed 100.');
                total = 100;
            }

            document.getElementById('total').value = total.toFixed(2);
        }
    </script>
@endpush
