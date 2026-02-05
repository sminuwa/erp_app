@extends('layouts.backend.app')

@section('title', 'Create Materials Requisition')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Create Materials Requisition</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Create Materials Requisition</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
    <form action="{{ route('manufacturing.requisitions.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Materials Requisition</h3>
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
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="requisition_date" class="form-control" value="{{ $model->requisition_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Schedule <span class="text-danger">*</span></label>
                                    <select name="schedule_id" id="schedule_id" class="form-control select2-single" required>
                                        <option value="">Select Schedule</option>
                                        @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->id }}">{{ $schedule->reference }} - {{ date('d M Y', strtotime($schedule->schedule_date)) }}</option>
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
                        <h5>Requisition Items</h5>
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Store</th>
                                    <th width="150">Quantity</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][product_id]" class="form-control select2-single product-select">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="items[0][store_id]" class="form-control select2-single store-select">
                                            <option value="">Select Store</option>
                                            @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control" step="0.0001" min="0">
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
                                    <td colspan="4">
                                        <button type="button" class="btn btn-success btn-sm" id="add-row">
                                            <i class="fa fa-plus"></i> Add Row
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Requisition</button>
                        <a href="{{ route('manufacturing.requisitions.index') }}" class="btn btn-secondary">Cancel</a>
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

    $('#add-row').click(function() {
        var newRow = `
            <tr class="item-row">
                <td>
                    <select name="items[${rowIndex}][product_id]" class="form-control select2-single product-select">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="items[${rowIndex}][store_id]" class="form-control select2-single store-select">
                        <option value="">Select Store</option>
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][quantity]" class="form-control" step="0.0001" min="0">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#items-table tbody').append(newRow);
        $('#items-table tbody tr:last .select2-single').select2();
        rowIndex++;
    });

    $(document).on('click', '.remove-row', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
        }
    });
});
</script>
@endpush
