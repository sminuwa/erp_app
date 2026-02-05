@extends('layouts.backend.app')

@section('title', 'Create Batch Production')

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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Batch Production</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Batch Number <span class="text-danger">*</span></label>
                                    <input type="text" name="batch_number" class="form-control" value="{{ $model->batch_number }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Production Date <span class="text-danger">*</span></label>
                                    <input type="date" name="production_date" class="form-control" value="{{ $model->production_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>BOM (Batch Type) <span class="text-danger">*</span></label>
                                    <select name="bom_id" id="bom_id" class="form-control select2-single" required>
                                        <option value="">Select BOM</option>
                                        @foreach($boms as $bom)
                                        <option value="{{ $bom->id }}" data-output="{{ $bom->actual_output }}">
                                            {{ $bom->reference }} - {{ $bom->finishProduct->name ?? '' }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Requisition</label>
                                    <select name="requisition_id" class="form-control select2-single">
                                        <option value="">Select Requisition (Optional)</option>
                                        @foreach($requisitions as $requisition)
                                        <option value="{{ $requisition->id }}">{{ $requisition->reference }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Quantity (Number of Batches) <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" step="1" min="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Labor Cost</label>
                                    <input type="number" name="labor_cost" class="form-control" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Power Cost</label>
                                    <input type="number" name="power_cost" class="form-control" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Other Cost</label>
                                    <input type="number" name="other_cost" class="form-control" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Raw Materials</h5>
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
                                        <select name="materials[0][product_id]" class="form-control select2-single product-select">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
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
                        <button type="submit" class="btn btn-primary">Save Batch Production</button>
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
                    <select name="materials[${rowIndex}][product_id]" class="form-control select2-single product-select">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="materials[${rowIndex}][store_id]" class="form-control select2-single store-select">
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
        if ($('.material-row').length > 1) {
            $(this).closest('tr').remove();
            calculateTotalMaterialCost();
        }
    });
});
</script>
@endpush
