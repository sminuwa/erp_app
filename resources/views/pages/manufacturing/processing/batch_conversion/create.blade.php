@extends('layouts.app')

@section('title', 'Create Batch Conversion')

@section('content')
<section class="content">
    <form action="{{ route('manufacturing.batch_conversion.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Batch Conversion</h3>
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
                                    <label>Conversion Date <span class="text-danger">*</span></label>
                                    <input type="date" name="conversion_date" class="form-control" value="{{ $model->conversion_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Batch Production <span class="text-danger">*</span></label>
                                    <select name="batch_id" id="batch_id" class="form-control select2-single" required>
                                        <option value="">Select Batch</option>
                                        @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}"
                                                data-remaining="{{ ($batch->quantity * $batch->bom->actual_output) - $batch->converted_qty }}"
                                                data-product="{{ $batch->bom->finishProduct->name ?? '' }}"
                                                data-wip="{{ $batch->wip_value }}">
                                            {{ $batch->batch_number }} - {{ $batch->bom->finishProduct->name ?? '' }}
                                            (Remaining: {{ number_format(($batch->quantity * $batch->bom->actual_output) - $batch->converted_qty, 4) }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Output Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="output_qty" id="output_qty" class="form-control" step="0.0001" min="0.0001" required>
                                    <small class="text-muted">Max available: <span id="max-qty">-</span></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Finish Product</label>
                                    <input type="text" id="finish_product" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Conversion</button>
                        <a href="{{ route('manufacturing.batch_conversion.index') }}" class="btn btn-secondary">Cancel</a>
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

    $('#batch_id').change(function() {
        var selected = $(this).find(':selected');
        var remaining = selected.data('remaining') || 0;
        var product = selected.data('product') || '';

        $('#max-qty').text(parseFloat(remaining).toFixed(4));
        $('#finish_product').val(product);
        $('#output_qty').attr('max', remaining);
    });
});
</script>
@endsection
