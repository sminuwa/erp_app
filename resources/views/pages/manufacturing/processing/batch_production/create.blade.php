@extends('layouts.backend.app')

@section('title', 'Create Batch Production')

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
    .wip-highlight {
        background-color: #fff3cd;
        border: 1px solid #ffc107;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Create Batch Production</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Create Batch Production</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="{{ route('manufacturing.batch_production.store') }}" method="POST">
            @csrf
            <input type="hidden" name="reference" value="{{ $model->reference }}">
            <input type="hidden" name="batch_number" value="{{ $model->batch_number }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create Batch Production</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Reference</label>
                                        <input type="text" class="form-control" value="{{ $model->reference }}" readonly>
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
                                        <label>Production Date <span class="text-danger">*</span></label>
                                        <input type="date" name="production_date" class="form-control" value="{{ $model->production_date }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Requisition <span class="text-danger">*</span></label>
                                        <select name="requisition_id" id="requisition_id" class="form-control select2-single" required>
                                            <option value="">Select Requisition</option>
                                            @foreach($requisitions as $requisition)
                                            @php
                                                $reqQty = $requisition->quantity ?? 0;
                                                $manufactured = $manufacturedQtyByRequisition[$requisition->id] ?? 0;
                                                $remaining = $reqQty - $manufactured;
                                                // Get BOM from direct link or from schedule items
                                                $bomId = $requisition->bom_id;
                                                $bomLabel = $requisition->bom->finishProduct->name ?? null;
                                                $bomIds = [];
                                                $bomQtys = [];
                                                if ($bomId) {
                                                    $bomIds[] = $bomId;
                                                    $bomQtys[$bomId] = $reqQty;
                                                } elseif ($requisition->workOrder) {
                                                    foreach ($requisition->workOrder->items as $woItem) {
                                                        $itemBom = $woItem->scheduleItem->productionOrderItem->bom ?? null;
                                                        if ($itemBom && $itemBom->bom_type === 'batch') {
                                                            $bomIds[] = $itemBom->id;
                                                            $bomQtys[$itemBom->id] = ($bomQtys[$itemBom->id] ?? 0) + $woItem->planned_qty;
                                                            if (!$bomLabel) $bomLabel = $itemBom->finishProduct->name ?? 'N/A';
                                                        }
                                                    }
                                                    $bomIds = array_unique($bomIds);
                                                    if (count($bomIds) === 1) $bomId = $bomIds[0];
                                                } elseif ($requisition->schedule) {
                                                    foreach ($requisition->schedule->items as $schedItem) {
                                                        $itemBom = $schedItem->productionOrderItem->bom ?? null;
                                                        if ($itemBom && $itemBom->bom_type === 'batch') {
                                                            $bomIds[] = $itemBom->id;
                                                            $bomQtys[$itemBom->id] = ($bomQtys[$itemBom->id] ?? 0) + $schedItem->scheduled_qty;
                                                            if (!$bomLabel) $bomLabel = $itemBom->finishProduct->name ?? 'N/A';
                                                        }
                                                    }
                                                    $bomIds = array_unique($bomIds);
                                                    if (count($bomIds) === 1) $bomId = $bomIds[0];
                                                }
                                                $bomManufactured = $manufacturedQtyByReqAndBom[$requisition->id] ?? [];

                                                // For schedule-based requisitions, derive qty from per-BOM scheduled quantities
                                                if (!$requisition->bom_id && count($bomQtys) > 0) {
                                                    $reqQty = array_sum($bomQtys);
                                                    $manufactured = array_sum($bomManufactured);
                                                    $remaining = $reqQty - $manufactured;
                                                }
                                            @endphp
                                            <option value="{{ $requisition->id }}"
                                                data-bom-id="{{ $bomId }}"
                                                data-bom-ids="{{ json_encode(array_values($bomIds)) }}"
                                                data-bom-qtys="{{ json_encode($bomQtys) }}"
                                                data-bom-manufactured="{{ json_encode($bomManufactured) }}"
                                                data-team-id="{{ $requisition->workOrder->team_id ?? '' }}"
                                                data-machine-id="{{ $requisition->workOrder->machine_id ?? '' }}"
                                                data-req-qty="{{ $reqQty }}"
                                                data-manufactured="{{ $manufactured }}"
                                                data-remaining="{{ $remaining }}">
                                                {{ $requisition->reference }} - {{ $bomLabel ?? 'N/A' }} (Remaining: {{ number_format($remaining, 0) }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>BOM (Batch Type) <span class="text-danger">*</span></label>
                                        <select name="bom_id" id="bom_id" class="form-control select2-single" required disabled>
                                            <option value="">Select a requisition first</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Number of Batches <span class="text-danger">*</span></label>
                                        <input type="number" name="quantity" id="quantity" class="form-control" step="0.0001" min="0.0001" value="1" required>
                                        <small class="text-muted">
                                            Req. Qty: <span id="req-qty-display">-</span> |
                                            Manufactured: <span id="manufactured-display">-</span> |
                                            Max: <span id="remaining-display" class="font-weight-bold">-</span>
                                        </small>
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
                                        Select a requisition and BOM to see required materials.
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
                                        <div class="row wip-highlight p-2 rounded">
                                            <div class="col-6"><label class="text-warning mb-0">WIP Value:</label></div>
                                            <div class="col-6 text-right text-warning"><strong id="summary-wip-value">0.00</strong></div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-6"><label>Expected Output:</label></div>
                                            <div class="col-6 text-right" id="summary-expected-output">0</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><label>Unit Cost:</label></div>
                                            <div class="col-6 text-right" id="summary-unit-cost">0.00</div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-3" style="font-size: 12px;">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Note:</strong> WIP (Work in Progress) value = Material Cost + Other Costs.
                                        This will be credited to WIP account upon posting.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="btn-submit">
                                <i class="fa fa-save"></i> Save Batch Production
                            </button>
                            <a href="{{ route('manufacturing.batch_production.index') }}" class="btn btn-secondary">Cancel</a>
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
    $('.select2-single').select2({ width: '100%' });

    var calculateTimeout = null;

    // BOM data keyed by ID — used to dynamically rebuild the BOM dropdown per requisition
    var bomData = @php
        $bomMap = [];
        foreach ($boms as $b) {
            $bomMap[$b->id] = [
                'reference' => $b->reference,
                'name' => $b->finishProduct->name ?? '',
                'output' => $b->actual_output ?? 1,
            ];
        }
        echo json_encode($bomMap);
    @endphp;

    function formatNumber(num) {
        return parseFloat(num || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function calculateCosts() {
        var bomId = $('#bom_id').val();
        var quantity = parseInt($('#quantity').val()) || 0;

        if (!bomId || quantity < 1) {
            $('#materials-table').hide();
            $('#materials-empty').show();
            resetCostSummary();
            return;
        }

        $('#materials-loading').show();
        $('#materials-table').hide();
        $('#materials-empty').hide();

        $.ajax({
            url: '{{ route("manufacturing.batch_production.calculate_costs") }}',
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
                    $('#summary-wip-value').text(formatNumber(data.wip_value));
                    $('#summary-expected-output').text(formatNumber(data.expected_output));
                    $('#summary-unit-cost').text(formatNumber(data.unit_cost));
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
        $('#summary-total-other-cost, #summary-wip-value, #summary-unit-cost').text('0.00');
        $('#summary-expected-output').text('0');
        $('#total-material-cost').text('0.00');
        $('#materials-body').empty();
    }

    // Trigger calculation when BOM or quantity changes
    $('#bom_id, #quantity').on('change input', function() {
        clearTimeout(calculateTimeout);
        calculateTimeout = setTimeout(calculateCosts, 500);
    });

    // Update quantity display based on selected BOM within the requisition
    function updateQtyForBom() {
        var reqSelected = $('#requisition_id').find(':selected');
        var bomQtys = reqSelected.data('bom-qtys') || {};
        var bomManufactured = reqSelected.data('bom-manufactured') || {};
        var selectedBom = $('#bom_id').val();

        if (selectedBom && bomQtys[selectedBom] !== undefined) {
            var qty = parseFloat(bomQtys[selectedBom]);
            var mfg = parseFloat(bomManufactured[selectedBom] || 0);
            var remaining = qty - mfg;

            $('#req-qty-display').text(qty);
            $('#manufactured-display').text(mfg);
            $('#remaining-display').text(remaining);
            $('#quantity').attr('max', remaining);

            if (parseInt($('#quantity').val()) > remaining) {
                $('#quantity').val(Math.min(1, remaining));
            }
        } else {
            // Fallback to overall requisition qty
            var reqQty = parseFloat(reqSelected.data('req-qty')) || 0;
            var manufactured = parseFloat(reqSelected.data('manufactured')) || 0;
            var remaining = parseFloat(reqSelected.data('remaining')) || 0;

            if (reqQty > 0) {
                $('#req-qty-display').text(reqQty);
                $('#manufactured-display').text(manufactured);
                $('#remaining-display').text(remaining);
                $('#quantity').attr('max', remaining);
            } else {
                $('#req-qty-display').text('-');
                $('#manufactured-display').text('-');
                $('#remaining-display').text('-');
                $('#quantity').removeAttr('max');
            }
        }
    }

    // When requisition is selected: filter BOMs, auto-load team/machine, update qty constraints
    $('#requisition_id').on('change', function() {
        var reqVal = $(this).val();

        // No requisition selected — disable BOM and reset state
        if (!reqVal) {
            $('#bom_id').empty().append('<option value="">Select a requisition first</option>');
            $('#bom_id').val('').prop('disabled', true);
            $('#bom_id').select2({ width: '100%' });
            $('#req-qty-display, #manufactured-display, #remaining-display').text('-');
            $('#quantity').removeAttr('max');
            $('#materials-table').hide();
            $('#materials-empty')
                .show()
                .html('Select a requisition and BOM to see required materials.');
            resetCostSummary();
            return;
        }

        var selected = $(this).find(':selected');
        var bomId = selected.data('bom-id');
        var bomIds = selected.data('bom-ids') || [];
        var teamId = selected.data('team-id');
        var machineId = selected.data('machine-id');

        // Rebuild BOM dropdown with only the BOMs linked to this requisition
        $('#bom_id').empty().append('<option value="">Select BOM</option>');
        if (bomIds.length > 0) {
            bomIds.forEach(function(id) {
                if (bomData[id]) {
                    var b = bomData[id];
                    var opt = new Option(b.reference + ' - ' + b.name, id, false, false);
                    $(opt).attr('data-output', b.output);
                    $('#bom_id').append(opt);
                }
            });
        }
        $('#bom_id').prop('disabled', false);
        $('#bom_id').select2({ width: '100%' });

        // Auto-select BOM from requisition (or reset if not in filtered list)
        if (bomId) {
            $('#bom_id').val(bomId).trigger('change');
        } else if (bomIds.length === 1) {
            $('#bom_id').val(bomIds[0]).trigger('change');
        } else {
            var currentBom = parseInt($('#bom_id').val());
            if (bomIds.length > 0 && bomIds.indexOf(currentBom) === -1) {
                $('#bom_id').val('').trigger('change');
            }
        }

        // Auto-select team from requisition's schedule
        if (teamId) {
            $('select[name="team_id"]').val(teamId).trigger('change');
        }

        // Auto-select machine from requisition's schedule
        if (machineId) {
            $('select[name="machine_id"]').val(machineId).trigger('change');
        }

        // Update qty display for the selected BOM
        updateQtyForBom();
    });

    // When BOM changes, update per-BOM quantity display
    $('#bom_id').on('change', function() {
        if ($('#requisition_id').val()) {
            updateQtyForBom();
        }
    });

    // Validate quantity on form submit (use per-BOM remaining if available)
    $('form').on('submit', function(e) {
        var reqSelected = $('#requisition_id').find(':selected');
        var selectedBom = $('#bom_id').val();
        var bomQtys = reqSelected.data('bom-qtys') || {};
        var bomManufactured = reqSelected.data('bom-manufactured') || {};
        var remaining, manufactured;

        if (selectedBom && bomQtys[selectedBom] !== undefined) {
            var bomQty = parseFloat(bomQtys[selectedBom]);
            manufactured = parseFloat(bomManufactured[selectedBom] || 0);
            remaining = bomQty - manufactured;
        } else {
            remaining = parseFloat(reqSelected.data('remaining')) || 0;
            manufactured = parseFloat(reqSelected.data('manufactured')) || 0;
        }

        var qty = parseInt($('#quantity').val()) || 0;
        if (remaining > 0 && qty > remaining) {
            e.preventDefault();
            alert('Quantity (' + qty + ') exceeds the remaining quantity (' + remaining + ').\nAlready manufactured: ' + manufactured);
            return false;
        }
    });

    // Trigger initial calculation if BOM is already selected
    if ($('#bom_id').val()) {
        calculateCosts();
    }
});
</script>
@endpush
