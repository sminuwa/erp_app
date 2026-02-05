@extends('layouts.backend.app')

@section('title', 'Create Additional Cost')

@push('css')
<style>
    .production-select { display: none; }
    .production-select.active { display: block; }
</style>
@endpush

@section('content')
<section class="content-wrapper">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Reference <span class="text-danger">*</span></label>
                                    <input type="text" name="reference" class="form-control" value="{{ $model->reference }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cost Date <span class="text-danger">*</span></label>
                                    <input type="date" name="cost_date" class="form-control" value="{{ $model->cost_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Production Type <span class="text-danger">*</span></label>
                                    <select name="production_type" id="production_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="single_product">Single Product Manufacturing</option>
                                        <option value="batch_conversion">Batch Conversion</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Account <span class="text-danger">*</span></label>
                                    <select name="account_id" class="form-control select2" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->class }} - {{ $account->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Single Product Manufacturing Select -->
                            <div class="col-md-4 production-select" id="single-select">
                                <div class="form-group">
                                    <label>Single Product Manufacturing <span class="text-danger">*</span></label>
                                    <select name="single_manufacturing_id" class="form-control select2">
                                        <option value="">Select Production</option>
                                        @forelse($singleManufacturing as $sm)
                                        <option value="{{ $sm->id }}">
                                            {{ $sm->reference }} - {{ $sm->finishProduct->name ?? 'N/A' }} (Qty: {{ $sm->output_qty ?? $sm->quantity }})
                                        </option>
                                        @empty
                                        <option value="" disabled>No single product manufacturing found</option>
                                        @endforelse
                                    </select>
                                    @if(count($singleManufacturing) == 0)
                                    <small class="text-danger">Create a Single Product Manufacturing record first</small>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Batch Conversion Select -->
                            <div class="col-md-4 production-select" id="batch-select">
                                <div class="form-group">
                                    <label>Batch Conversion <span class="text-danger">*</span></label>
                                    <select name="batch_production_id" class="form-control select2">
                                        <option value="">Select Production</option>
                                        @forelse($batchProductions as $bp)
                                        <option value="{{ $bp->id }}">
                                            {{ $bp->reference }} - {{ $bp->finishProduct->name ?? 'N/A' }} (Qty: {{ $bp->produced_qty }})
                                        </option>
                                        @empty
                                        <option value="" disabled>No batch conversion found</option>
                                        @endforelse
                                    </select>
                                    @if(count($batchProductions) == 0)
                                    <small class="text-danger">Create a Batch Conversion record first</small>
                                    @endif
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

@push('js')
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    function toggleProductionSelect() {
        var type = $('#production_type').val();
        
        // Hide all first
        $('.production-select').removeClass('active');
        $('select[name="single_manufacturing_id"]').prop('required', false);
        $('select[name="batch_production_id"]').prop('required', false);
        
        if (type === 'single_product') {
            $('#single-select').addClass('active');
            $('select[name="single_manufacturing_id"]').prop('required', true);
        } else if (type === 'batch_conversion') {
            $('#batch-select').addClass('active');
            $('select[name="batch_production_id"]').prop('required', true);
        }
    }

    // Run on page load
    toggleProductionSelect();

    // Run on change
    $('#production_type').on('change', toggleProductionSelect);
});
</script>
@endpush
