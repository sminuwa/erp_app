@extends('layouts.backend.app')

@section('title', 'Create Manufacturing Rework')

@push('css')
<style>
    .production-info { display: none; }
    .production-info.active { display: block; }
    .material-row .select2-container { width: 100% !important; }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Create Manufacturing Rework</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Create Rework</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="{{ route('manufacturing.reworks.store') }}" method="POST">
            @csrf
            <input type="hidden" name="reference" value="{{ $model->reference }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Manufacturing Rework Details</h3>
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
                                        <label>Rework Date <span class="text-danger">*</span></label>
                                        <input type="date" name="rework_date" class="form-control" value="{{ $model->rework_date }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Production Document <span class="text-danger">*</span></label>
                                        <select name="production_select" id="production_select" class="form-control select2-single" required>
                                            <option value="">Select Production</option>
                                            <optgroup label="Single Product Manufacturing">
                                                @foreach($singleManufacturing as $sm)
                                                <option value="single_{{ $sm->id }}"
                                                    data-type="single_product"
                                                    data-id="{{ $sm->id }}"
                                                    data-product="{{ $sm->bom->finishProduct->name ?? 'N/A' }}"
                                                    data-qty="{{ $sm->quantity }}">
                                                    {{ $sm->reference }} - {{ $sm->bom->finishProduct->name ?? 'N/A' }}
                                                </option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Batch Conversion">
                                                @foreach($batchConversions as $bc)
                                                <option value="batch_{{ $bc->id }}"
                                                    data-type="batch_conversion"
                                                    data-id="{{ $bc->id }}"
                                                    data-product="{{ $bc->finishProduct->name ?? ($bc->batchProduction->bom->finishProduct->name ?? 'N/A') }}"
                                                    data-qty="{{ $bc->produced_qty }}">
                                                    {{ $bc->reference }} - {{ $bc->finishProduct->name ?? ($bc->batchProduction->bom->finishProduct->name ?? 'N/A') }}
                                                </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <input type="hidden" name="single_manufacturing_id" id="single_manufacturing_id" value="">
                                        <input type="hidden" name="batch_conversion_id" id="batch_conversion_id" value="">
                                    </div>
                                </div>
                            </div>

                            <!-- Production Info Panel -->
                            <div class="row production-info" id="production-info-panel">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Finish Product:</strong>
                                                <span id="info-product">-</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Produced Qty:</strong>
                                                <span id="info-qty">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Reason <span class="text-danger">*</span></label>
                                        <textarea name="reason" class="form-control" rows="2" required maxlength="500" placeholder="Enter rework reason">{{ old('reason') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5>Other Costs</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Labor Cost</label>
                                        <input type="number" name="labor_cost" id="labor_cost" class="form-control cost-input" step="0.01" min="0" value="{{ old('labor_cost', 0) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Power Cost</label>
                                        <input type="number" name="power_cost" id="power_cost" class="form-control cost-input" step="0.01" min="0" value="{{ old('power_cost', 0) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Other Cost</label>
                                        <input type="number" name="other_cost" id="other_cost" class="form-control cost-input" step="0.01" min="0" value="{{ old('other_cost', 0) }}">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5>Additional Materials</h5>
                            <table class="table table-bordered" id="materials-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Store</th>
                                        <th width="120">Quantity</th>
                                        <th width="120">Unit Cost</th>
                                        <th width="120">Total Cost</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="materials-tbody">
                                    <tr class="material-row" data-row="0">
                                        <td>
                                            <select name="materials[0][product_id]" class="form-control select2-single product-select">
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-cost="{{ $product->cost_price ?? 0 }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="materials[0][store_id]" class="form-control select2-single store-select">
                                                <option value="">Select Store</option>
                                                @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="materials[0][quantity]" class="form-control mat-qty" step="0.0001" min="0" value="0">
                                        </td>
                                        <td>
                                            <input type="number" name="materials[0][unit_cost]" class="form-control mat-cost" step="0.01" min="0" value="0">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control mat-total" readonly value="0.00">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total Material Cost:</strong></td>
                                        <td><input type="text" id="total-material-cost" class="form-control" readonly value="0.00"></td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm" id="add-material">
                                                <i class="fa fa-plus"></i> Add
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-secondary">
                                        <strong>Total Other Costs:</strong> <span id="total-other-cost">0.00</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-success">
                                        <strong>Grand Total Cost:</strong> <span id="grand-total-cost">0.00</span>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fa fa-info-circle"></i>
                                <strong>Note:</strong> Upon posting, the rework will:
                                <ul class="mb-0 mt-1">
                                    <li>Debit additional raw materials from inventory</li>
                                    <li>Add all costs (materials + labor + power + other) to the finish goods</li>
                                    <li>Recalculate average cost for the finish product</li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Rework
                            </button>
                            <a href="{{ route('manufacturing.reworks.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<!-- Hidden template for product options -->
<script type="text/template" id="product-options-template">
    <option value="">Select Product</option>
    @foreach($products as $product)
    <option value="{{ $product->id }}" data-cost="{{ $product->cost_price ?? 0 }}">{{ $product->name }}</option>
    @endforeach
</script>

<!-- Hidden template for store options -->
<script type="text/template" id="store-options-template">
    <option value="">Select Store</option>
    @foreach($stores as $store)
    <option value="{{ $store->id }}">{{ $store->name }}</option>
    @endforeach
</script>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Initialize all Select2 dropdowns
    $('.select2-single').select2({ width: '100%' });

    var rowIndex = 1;

    // Production selection handling
    $('#production_select').on('change', function() {
        var selected = $(this).find('option:selected');
        var type = selected.data('type');
        var id = selected.data('id');

        // Reset hidden fields
        $('#single_manufacturing_id').val('');
        $('#batch_conversion_id').val('');

        if (type && id) {
            // Set the appropriate hidden field
            if (type === 'single_product') {
                $('#single_manufacturing_id').val(id);
            } else if (type === 'batch_conversion') {
                $('#batch_conversion_id').val(id);
            }

            // Update info panel
            $('#info-product').text(selected.data('product'));
            $('#info-qty').text(parseFloat(selected.data('qty') || 0).toFixed(4));

            // Show info panel
            $('#production-info-panel').addClass('active');
        } else {
            $('#production-info-panel').removeClass('active');
        }
    });

    // Material cost calculations
    function calculateRowTotal(row) {
        var qty = parseFloat(row.find('.mat-qty').val()) || 0;
        var cost = parseFloat(row.find('.mat-cost').val()) || 0;
        var total = qty * cost;
        row.find('.mat-total').val(total.toFixed(2));
        calculateTotals();
    }

    function calculateTotals() {
        // Calculate total material cost
        var totalMaterialCost = 0;
        $('.mat-total').each(function() {
            totalMaterialCost += parseFloat($(this).val()) || 0;
        });
        $('#total-material-cost').val(totalMaterialCost.toFixed(2));

        // Calculate total other costs
        var laborCost = parseFloat($('#labor_cost').val()) || 0;
        var powerCost = parseFloat($('#power_cost').val()) || 0;
        var otherCost = parseFloat($('#other_cost').val()) || 0;
        var totalOtherCost = laborCost + powerCost + otherCost;
        $('#total-other-cost').text(totalOtherCost.toFixed(2));

        // Calculate grand total
        var grandTotal = totalMaterialCost + totalOtherCost;
        $('#grand-total-cost').text(grandTotal.toFixed(2));
    }

    // Event handlers for cost calculations
    $(document).on('change keyup input', '.mat-qty, .mat-cost', function() {
        calculateRowTotal($(this).closest('tr'));
    });

    $(document).on('change keyup input', '.cost-input', function() {
        calculateTotals();
    });

    // Auto-fill unit cost when product is selected
    $(document).on('change', '.product-select', function() {
        var row = $(this).closest('tr');
        var selectedOption = $(this).find('option:selected');
        var costPrice = parseFloat(selectedOption.data('cost')) || 0;
        row.find('.mat-cost').val(costPrice.toFixed(2));
        calculateRowTotal(row);
    });

    // Add material row
    $('#add-material').on('click', function(e) {
        e.preventDefault();

        var productOptions = $('#product-options-template').html();
        var storeOptions = $('#store-options-template').html();

        var newRowHtml = '<tr class="material-row" data-row="' + rowIndex + '">' +
            '<td>' +
                '<select name="materials[' + rowIndex + '][product_id]" class="form-control select2-single product-select">' +
                    productOptions +
                '</select>' +
            '</td>' +
            '<td>' +
                '<select name="materials[' + rowIndex + '][store_id]" class="form-control select2-single store-select">' +
                    storeOptions +
                '</select>' +
            '</td>' +
            '<td>' +
                '<input type="number" name="materials[' + rowIndex + '][quantity]" class="form-control mat-qty" step="0.0001" min="0" value="0">' +
            '</td>' +
            '<td>' +
                '<input type="number" name="materials[' + rowIndex + '][unit_cost]" class="form-control mat-cost" step="0.01" min="0" value="0">' +
            '</td>' +
            '<td>' +
                '<input type="number" class="form-control mat-total" readonly value="0.00">' +
            '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-sm remove-row">' +
                    '<i class="fa fa-trash"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';

        $('#materials-tbody').append(newRowHtml);

        // Initialize Select2 on the new row's dropdowns
        var newRow = $('#materials-tbody tr:last');
        newRow.find('.select2-single').select2({ width: '100%' });

        rowIndex++;
    });

    // Remove material row
    $(document).on('click', '.remove-row', function() {
        var row = $(this).closest('tr');
        // Destroy Select2 before removing to prevent memory leaks
        row.find('.select2-single').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        row.remove();
        calculateTotals();
    });

    // Initial calculation
    calculateTotals();
});
</script>
@endpush
