@extends('layouts.backend.app')

@section('title', 'Create Single Product Manufacturing')

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
    #materials-table tbody tr td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<section class="content-wrapper">
    <form action="{{ route('manufacturing.single_manufacturing.store') }}" method="POST">
        @csrf
        <input type="hidden" name="batch_number" value="{{ $model->batch_number }}">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Single Product Manufacturing</h3>
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
                                    <label>Batch Number</label>
                                    <input type="text" class="form-control" value="{{ $model->batch_number }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Manufacturing Date <span class="text-danger">*</span></label>
                                    <input type="date" name="manufacturing_date" class="form-control" value="{{ $model->manufacturing_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Requisition <span class="text-danger">*</span></label>
                                    <select name="requisition_id" class="form-control select2-single" required>
                                        <option value="">Select Requisition</option>
                                        @foreach($requisitions as $requisition)
                                        <option value="{{ $requisition->id }}" data-bom-id="{{ $requisition->bom_id }}">
                                            {{ $requisition->reference }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>BOM <span class="text-danger">*</span></label>
                                    <select name="bom_id" id="bom_id" class="form-control select2-single" required>
                                        <option value="">Select BOM</option>
                                        @foreach($boms as $bom)
                                        <option value="{{ $bom->id }}">
                                            {{ $bom->reference }} - {{ $bom->finishProduct->name ?? '' }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Quantity to Manufacture <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" step="0.0001" min="0.0001" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Team <span class="text-danger">*</span></label>
                                    <select name="team_id" class="form-control select2-single" required>
                                        <option value="">Select Team</option>
                                        @foreach($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Machine</label>
                                    <select name="machine_id" class="form-control select2-single">
                                        <option value="">Select Machine (Optional)</option>
                                        @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->code }} - {{ $machine->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Raw Materials (from BOM)</h5>
                                <div id="materials-loading" class="text-center py-3" style="display: none;">
                                    <i class="fa fa-spinner fa-spin"></i> Loading materials...
                                </div>
                                <div id="materials-empty" class="alert alert-info">
                                    Select a BOM and enter quantity to see required materials.
                                </div>
                                <table class="table table-bordered table-sm" id="materials-table" style="display: none;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Store</th>
                                            <th class="text-right" width="100">BOM Qty</th>
                                            <th class="text-right" width="100">Required Qty</th>
                                            <th class="text-right" width="100">Unit Cost</th>
                                            <th class="text-right" width="120">Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody id="materials-body">
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary">
                                            <td colspan="5" class="text-right"><strong>Total Material Cost:</strong></td>
                                            <td class="text-right"><strong id="total-material-cost">0.00</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h5>Cost Summary</h5>
                                <div class="cost-summary">
                                    <div class="row">
                                        <div class="col-6"><label>Material Cost:</label></div>
                                        <div class="col-6 text-right" id="summary-material-cost">0.00</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6"><label>Labor Cost:</label></div>
                                        <div class="col-6 text-right" id="summary-labor-cost">0.00</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6"><label>Power Cost:</label></div>
                                        <div class="col-6 text-right" id="summary-power-cost">0.00</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6"><label>Other Cost:</label></div>
                                        <div class="col-6 text-right" id="summary-other-cost">0.00</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row">
                                        <div class="col-6"><label>Total Other Cost:</label></div>
                                        <div class="col-6 text-right" id="summary-total-other-cost">0.00</div>
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
                                    <div class="row">
                                        <div class="col-6"><label>Expected Output:</label></div>
                                        <div class="col-6 text-right" id="summary-expected-output">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            <i class="fa fa-save"></i> Save Manufacturing
                        </button>
                        <a href="{{ route('manufacturing.single_manufacturing.index') }}" class="btn btn-secondary">Cancel</a>
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
    $('.select2-single').select2();

    var calculateTimeout = null;

    function formatNumber(num) {
        return parseFloat(num || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function calculateCosts() {
        var bomId = $('#bom_id').val();
        var quantity = parseFloat($('#quantity').val()) || 0;

        if (!bomId || quantity <= 0) {
            $('#materials-table').hide();
            $('#materials-empty').show();
            resetCostSummary();
            return;
        }

        $('#materials-loading').show();
        $('#materials-table').hide();
        $('#materials-empty').hide();

        $.ajax({
            url: '{{ route("manufacturing.single_manufacturing.calculate_costs") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                bom_id: bomId,
                quantity: quantity
            },
            success: function(response) {
                $('#materials-loading').hide();

                if (response.status && response.data) {
                    var data = response.data;

                    // Populate materials table
                    var tbody = $('#materials-body');
                    tbody.empty();

                    if (data.materials && data.materials.length > 0) {
                        data.materials.forEach(function(m) {
                            tbody.append(`
                                <tr>
                                    <td>${m.product_name || ''}</td>
                                    <td>${m.store_name || '-'}</td>
                                    <td class="text-right">${formatNumber(m.bom_qty)}</td>
                                    <td class="text-right">${formatNumber(m.quantity)}</td>
                                    <td class="text-right">${formatNumber(m.unit_cost)}</td>
                                    <td class="text-right">${formatNumber(m.total_cost)}</td>
                                </tr>
                            `);
                        });
                        $('#total-material-cost').text(formatNumber(data.material_cost));
                        $('#materials-table').show();
                    } else {
                        $('#materials-empty').html('<div class="alert alert-warning">No materials defined in this BOM.</div>').show();
                    }

                    // Update cost summary
                    $('#summary-material-cost').text(formatNumber(data.material_cost));
                    $('#summary-labor-cost').text(formatNumber(data.labor_cost));
                    $('#summary-power-cost').text(formatNumber(data.power_cost));
                    $('#summary-other-cost').text(formatNumber(data.other_cost));
                    $('#summary-total-other-cost').text(formatNumber(data.total_other_cost));
                    $('#summary-total-cost').text(formatNumber(data.total_cost));
                    $('#summary-unit-cost').text(formatNumber(data.unit_cost));
                    $('#summary-expected-output').text(formatNumber(data.expected_output));
                }
            },
            error: function(xhr) {
                $('#materials-loading').hide();
                $('#materials-empty').html('<div class="alert alert-danger">Error calculating costs. Please try again.</div>').show();
                resetCostSummary();
            }
        });
    }

    function resetCostSummary() {
        $('#summary-material-cost, #summary-labor-cost, #summary-power-cost, #summary-other-cost').text('0.00');
        $('#summary-total-other-cost, #summary-total-cost, #summary-unit-cost').text('0.00');
        $('#summary-expected-output').text('0');
        $('#total-material-cost').text('0.00');
        $('#materials-body').empty();
    }

    // Trigger calculation when BOM or quantity changes
    $('#bom_id, #quantity').on('change input', function() {
        clearTimeout(calculateTimeout);
        calculateTimeout = setTimeout(calculateCosts, 500);
    });

    // Auto-select BOM when requisition is selected (if requisition has a bom_id)
    $('select[name="requisition_id"]').on('change', function() {
        var bomId = $(this).find(':selected').data('bom-id');
        if (bomId) {
            $('#bom_id').val(bomId).trigger('change');
        }
    });
});
</script>
@endpush
