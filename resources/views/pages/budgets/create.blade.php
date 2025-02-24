@extends('layouts.backend.app')

@section('title', 'Manage Budget')

@push('css')
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>{{ isset($budget) ? 'Edit Budget' : 'Create Budget' }}</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ isset($budget) ? 'Edit Budget' : 'Create Budget' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">{{ isset($budget) ? 'Edit Budget' : 'Add New Budget' }}</div>
                        <div class="card-body">
                            <form action="{{ isset($budget) ? route('budgets.update', $budget->id) : route('budgets.store') }}" method="POST">
                                @csrf
                                @if(isset($budget))
                                    @method('PUT')
                                @endif
                                
                                <div class="form-group">
                                    <label for="branch_id">Branch</label>
                                    <select name="branch_id" class="form-control" required>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ isset($budget) && $budget->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="category_id">Category</label>
                                    <select name="category_id" class="form-control" required>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ isset($budget) && $budget->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="budget_year">Budget Year</label>
                                    <select name="budget_year" class="form-control" required>
                                        @for($i = date('Y'); $i <= date('Y') + 5; $i++)
                                            <option value="{{ $i }}" {{ isset($budget) && $budget->budget_year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="quarter">Quarter</label>
                                    <select name="quarter" class="form-control" required>
                                        <option value="Q1" {{ isset($budget) && $budget->quarter == 'Q1' ? 'selected' : '' }}>Q1</option>
                                        <option value="Q2" {{ isset($budget) && $budget->quarter == 'Q2' ? 'selected' : '' }}>Q2</option>
                                        <option value="Q3" {{ isset($budget) && $budget->quarter == 'Q3' ? 'selected' : '' }}>Q3</option>
                                        <option value="Q4" {{ isset($budget) && $budget->quarter == 'Q4' ? 'selected' : '' }}>Q4</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="month1">Month 1</label>
                                    <input type="number" name="month1" id="month1" class="form-control" value="{{ isset($budget) ? $budget->month1 : old('month1') }}" required oninput="calculateTotal()">
                                </div>
                                
                                <div class="form-group">
                                    <label for="month2">Month 2</label>
                                    <input type="number" name="month2" id="month2" step="0.2" class="form-control" value="{{ isset($budget) ? $budget->month2 : old('month2') }}" required oninput="calculateTotal()">
                                </div>
                                
                                <div class="form-group">
                                    <label for="month3">Month 3</label>
                                    <input type="number" name="month3" id="month3" step="0.2" class="form-control" value="{{ isset($budget) ? $budget->month3 : old('month3') }}" required oninput="calculateTotal()">
                                </div>
                                
                                <div class="form-group">
                                    <label for="total">Total</label>
                                    <input type="number" name="total" id="total" step="0.2" class="form-control" value="{{ isset($budget) ? $budget->total : old('total') }}" readonly>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">{{ isset($budget) ? 'Update' : 'Create' }}</button>
                            </form>
                        </div>
                    </div>
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
        
        document.getElementById('total').value = total.toFixed(2); // Keep two decimal places
    }
</script>
@endpush
