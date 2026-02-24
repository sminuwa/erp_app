@extends('layouts.backend.app')

@section('title', 'Create Batch Conversion')

@push('css')
<style>
    .cost-summary {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }
    .cost-summary .row {
        margin-bottom: 8px;
    }
    .cost-summary label {
        font-weight: 600;
    }
    .batch-info {
        background-color: #e7f3ff;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #b8daff;
    }
    .batch-info .row {
        margin-bottom: 5px;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Create Batch Conversion</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Create Batch Conversion</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

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
                                        <label>Reference</label>
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
                                        <select name="batch_production_id" id="batch_production_id" class="form-control select2-single" required>
                                            <option value="">Select Batch</option>
                                            @foreach($batches as $batch)
                                            <option value="{{ $batch->id }}">
                                                {{ $batch->batch_number }} - {{ $batch->bom->finishProduct->name ?? '' }}
                                                (Remaining: {{ number_format($batch->remaining_qty, 4) }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="batch-details-section" style="display: none;">
                                <div class="col-md-12">
                                    <div class="batch-info mb-3">
                                        <h6><i class="fa fa-info-circle"></i> Batch Information</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted">Batch Number:</small><br>
                                                <strong id="info-batch-number">-</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Finish Product:</small><br>
                                                <strong id="info-finish-product">-</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Output Store:</small><br>
                                                <strong id="info-output-store">-</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">WIP Value:</small><br>
                                                <strong id="info-wip-value">-</strong>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted">Total Expected Output:</small><br>
                                                <strong id="info-total-output">-</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Already Converted:</small><br>
                                                <strong id="info-converted-qty">-</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Remaining Qty:</small><br>
                                                <strong id="info-remaining-qty" class="text-success">-</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">WIP Cost/Unit:</small><br>
                                                <strong id="info-wip-cost-per-unit">-</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Quantity to Produce <span class="text-danger">*</span></label>
                                                <input type="number" name="produced_qty" id="produced_qty" class="form-control" step="0.0001" min="0.0001" required>
                                                <small class="text-muted">
                                                    Allowed range:
                                                    <span id="min-qty" class="text-warning font-weight-bold">-</span>
                                                    &ndash;
                                                    <span id="max-qty" class="text-success font-weight-bold">-</span>
                                                    <span id="tolerance-info" class="text-muted"></span>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6>Cost Calculation</h6>
                                    <div class="cost-summary">
                                        <div class="row">
                                            <div class="col-6"><label>WIP Cost/Unit:</label></div>
                                            <div class="col-6 text-right" id="summary-wip-cost-per-unit">0.00</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><label>Produced Qty:</label></div>
                                            <div class="col-6 text-right" id="summary-produced-qty">0</div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-6"><label class="text-danger">WIP Deducted:</label></div>
                                            <div class="col-6 text-right text-danger"><strong id="summary-wip-deducted">0.00</strong></div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-6"><label class="text-primary">Total Cost:</label></div>
                                            <div class="col-6 text-right text-primary"><strong id="summary-total-cost">0.00</strong></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><label>Unit Cost:</label></div>
                                            <div class="col-6 text-right" id="summary-unit-cost">0.00</div>
                                        </div>
                                    </div>
                                    <div class="alert alert-warning mt-3" style="font-size: 11px;">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Note:</strong> Upon posting, WIP will be deducted and finish goods will be added to inventory.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="btn-submit" disabled>
                                <i class="fa fa-save"></i> Save Conversion
                            </button>
                            <a href="{{ route('manufacturing.batch_conversion.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('.select2-single').select2();

    var batchData = null;
    var calculateTimeout = null;

    function formatNumber(num) {
        return parseFloat(num || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatQty(num) {
        return parseFloat(num || 0).toFixed(4);
    }

    function loadBatchDetails(batchId) {
        if (!batchId) {
            $('#batch-details-section').hide();
            $('#btn-submit').prop('disabled', true);
            resetCostSummary();
            return;
        }

        $.ajax({
            url: '{{ route("manufacturing.batch_conversion.get_batch_details") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                batch_production_id: batchId
            },
            success: function(response) {
                if (response.status && response.data) {
                    batchData = response.data;

                    // Populate batch info
                    $('#info-batch-number').text(batchData.batch_number);
                    $('#info-finish-product').text(batchData.finish_product_name);
                    $('#info-output-store').text(batchData.output_store_name);
                    $('#info-wip-value').text(formatNumber(batchData.wip_value));
                    $('#info-total-output').text(formatQty(batchData.total_expected_output));
                    $('#info-converted-qty').text(formatQty(batchData.converted_qty));
                    $('#info-remaining-qty').text(formatQty(batchData.remaining_qty));
                    $('#info-wip-cost-per-unit').text(formatNumber(batchData.wip_cost_per_unit));

                    $('#min-qty').text(formatQty(batchData.min_qty));
                    $('#max-qty').text(formatQty(batchData.max_qty));
                    if (batchData.accepted_excess > 0 || batchData.accepted_shortage > 0) {
                        $('#tolerance-info').text('(+' + batchData.accepted_excess + '% / -' + batchData.accepted_shortage + '%)');
                    } else {
                        $('#tolerance-info').text('');
                    }
                    $('#produced_qty').attr('max', batchData.max_qty);

                    $('#batch-details-section').show();
                    $('#btn-submit').prop('disabled', false);

                    // If quantity already entered, recalculate
                    if ($('#produced_qty').val()) {
                        calculateCosts();
                    }
                }
            },
            error: function() {
                $('#batch-details-section').hide();
                $('#btn-submit').prop('disabled', true);
            }
        });
    }

    function calculateCosts() {
        var batchId = $('#batch_production_id').val();
        var producedQty = parseFloat($('#produced_qty').val()) || 0;

        if (!batchId || producedQty <= 0 || !batchData) {
            resetCostSummary();
            return;
        }

        // Validate against max (BOM tolerance-aware)
        if (producedQty > parseFloat(batchData.max_qty)) {
            $('#produced_qty').val(batchData.max_qty);
            producedQty = parseFloat(batchData.max_qty);
        }

        $.ajax({
            url: '{{ route("manufacturing.batch_conversion.calculate_costs") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                batch_production_id: batchId,
                produced_qty: producedQty
            },
            success: function(response) {
                if (response.status && response.data) {
                    var data = response.data;
                    $('#summary-wip-cost-per-unit').text(formatNumber(data.wip_cost_per_unit));
                    $('#summary-produced-qty').text(formatQty(data.produced_qty));
                    $('#summary-wip-deducted').text(formatNumber(data.wip_cost_deducted));
                    $('#summary-total-cost').text(formatNumber(data.total_cost));
                    $('#summary-unit-cost').text(formatNumber(data.unit_cost));
                }
            }
        });
    }

    function resetCostSummary() {
        $('#summary-wip-cost-per-unit, #summary-wip-deducted, #summary-total-cost, #summary-unit-cost').text('0.00');
        $('#summary-produced-qty').text('0');
    }

    // Load batch details when batch is selected
    $('#batch_production_id').on('change', function() {
        loadBatchDetails($(this).val());
    });

    // Calculate costs when quantity changes
    $('#produced_qty').on('input change', function() {
        clearTimeout(calculateTimeout);
        calculateTimeout = setTimeout(calculateCosts, 300);
    });
});
</script>
@endpush
