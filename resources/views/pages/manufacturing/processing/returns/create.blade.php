@extends('layouts.app')

@section('title', 'Create Manufacturing Return')

@section('content')
<section class="content">
    <form action="{{ route('manufacturing.returns.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Manufacturing Return</h3>
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
                                    <label>Return Date <span class="text-danger">*</span></label>
                                    <input type="date" name="return_date" class="form-control" value="{{ $model->return_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Return Type <span class="text-danger">*</span></label>
                                    <select name="return_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="full">Full Return</option>
                                        <option value="partial">Partial Return</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Return Quantity</label>
                                    <input type="number" name="return_qty" class="form-control" step="0.0001" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Reason <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="3" required maxlength="500">{{ old('reason') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Materials to Return (Optional)</h5>
                        <table class="table table-bordered" id="materials-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Store</th>
                                    <th width="120">Quantity</th>
                                    <th width="120">Unit Cost</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="material-row">
                                    <td>
                                        <select name="materials[0][product_id]" class="form-control select2-single">
                                            <option value="">Select Product</option>
                                            @foreach($products ?? [] as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="materials[0][store_id]" class="form-control select2-single">
                                            <option value="">Select Store</option>
                                            @foreach($stores ?? [] as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="materials[0][quantity]" class="form-control" step="0.0001" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="materials[0][unit_cost]" class="form-control" step="0.01" min="0">
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
                                    <td colspan="5">
                                        <button type="button" class="btn btn-success btn-sm" id="add-material">
                                            <i class="fa fa-plus"></i> Add Row
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Return</button>
                        <a href="{{ route('manufacturing.returns.index') }}" class="btn btn-secondary">Cancel</a>
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

    $('#add-material').click(function() {
        var newRow = `
            <tr class="material-row">
                <td>
                    <select name="materials[${rowIndex}][product_id]" class="form-control select2-single">
                        <option value="">Select Product</option>
                        @foreach($products ?? [] as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="materials[${rowIndex}][store_id]" class="form-control select2-single">
                        <option value="">Select Store</option>
                        @foreach($stores ?? [] as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="materials[${rowIndex}][quantity]" class="form-control" step="0.0001" min="0">
                </td>
                <td>
                    <input type="number" name="materials[${rowIndex}][unit_cost]" class="form-control" step="0.01" min="0">
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
    });
});
</script>
@endsection
