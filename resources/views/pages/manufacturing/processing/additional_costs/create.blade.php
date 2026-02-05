@extends('layouts.app')

@section('title', 'Create Additional Cost')

@section('content')
<section class="content">
    <form action="{{ route('manufacturing.additional_costs.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Manufacturing Additional Cost</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Reference <span class="text-danger">*</span></label>
                                    <input type="text" name="reference" class="form-control" value="{{ $model->reference }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cost Date <span class="text-danger">*</span></label>
                                    <input type="date" name="cost_date" class="form-control" value="{{ $model->cost_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Product <span class="text-danger">*</span></label>
                                    <select name="product_id" class="form-control select2-single" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Expense Account <span class="text-danger">*</span></label>
                                    <select name="expense_account_id" class="form-control select2-single" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="1">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Additional Cost</button>
                        <a href="{{ route('manufacturing.additional_costs.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.select2-single').select2();
});
</script>
@endsection
