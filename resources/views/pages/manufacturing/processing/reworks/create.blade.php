@extends('layouts.app')

@section('title', 'Create Manufacturing Rework')

@section('content')
<section class="content">
    <form action="{{ route('manufacturing.reworks.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Manufacturing Rework</h3>
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
                                    <label>Rework Date <span class="text-danger">*</span></label>
                                    <input type="date" name="rework_date" class="form-control" value="{{ $model->rework_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Single Manufacturing</label>
                                    <select name="single_manufacturing_id" class="form-control select2-single">
                                        <option value="">Select (if applicable)</option>
                                        @foreach($singleManufacturing as $sm)
                                        <option value="{{ $sm->id }}">{{ $sm->reference }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Batch Production</label>
                                    <select name="batch_production_id" class="form-control select2-single">
                                        <option value="">Select (if applicable)</option>
                                        @foreach($batchProductions as $bp)
                                        <option value="{{ $bp->id }}">{{ $bp->batch_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Reason <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="1" required maxlength="500">{{ old('reason') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Additional Costs</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Additional Labor Cost</label>
                                    <input type="number" name="additional_labor_cost" class="form-control cost-input" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Additional Power Cost</label>
                                    <input type="number" name="additional_power_cost" class="form-control cost-input" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Additional Other Cost</label>
                                    <input type="number" name="additional_other_cost" class="form-control cost-input" step="0.01" min="0" value="0">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Additional Materials</h5>
                        <table class="table table-bordered" id="materials-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Store</th>
                                    <th width="120">Quantity</th>
                                    <th width="120">Unit Cost</th>
                                    <th width="120">Total Cost</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="material-row">
                                    <td>
                                        <select name="materials[0][product_id]" class="form-control select2-single">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="materials[0][store_id]" class="form-control select2-single">
                                            <option value="">Select Store</option>
                                            @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="materials[0][quantity]" class="form-control mat-qty" step="0.0001" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="materials[0][unit_cost]" class="form-control mat-cost" step="0.01" min="0">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control mat-total" readonly>
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
                                    <td><input type="text" id="total-material-cost" class="form-control" readonly></td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm" id="add-material">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Rework</button>
                        <a href="{{ route('manufacturing.reworks.index') }}" class="btn btn-secondary">Cancel</a>
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

    var rowIndex = 1;

    function calculateRowTotal(row) {
        var qty = parseFloat(row.find('.mat-qty').val()) || 0;
        var cost = parseFloat(row.find('.mat-cost').val()) || 0;
        row.find('.mat-total').val((qty * cost).toFixed(2));
        calculateTotalMaterialCost();
    }

    function calculateTotalMaterialCost() {
        var total = 0;
        $('.mat-total').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total-material-cost').val(total.toFixed(2));
    }

    $(document).on('change', '.mat-qty, .mat-cost', function() {
        calculateRowTotal($(this).closest('tr'));
    });

    $('#add-material').click(function() {
        var newRow = `
            <tr class="material-row">
                <td>
                    <select name="materials[${rowIndex}][product_id]" class="form-control select2-single">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="materials[${rowIndex}][store_id]" class="form-control select2-single">
                        <option value="">Select Store</option>
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="materials[${rowIndex}][quantity]" class="form-control mat-qty" step="0.0001" min="0">
                </td>
                <td>
                    <input type="number" name="materials[${rowIndex}][unit_cost]" class="form-control mat-cost" step="0.01" min="0">
                </td>
                <td>
                    <input type="number" class="form-control mat-total" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#materials-table tbody').append(newRow);
        $('#materials-table tbody tr:last .select2-single').select2();
        rowIndex++;
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateTotalMaterialCost();
    });
});
</script>
@endsection
