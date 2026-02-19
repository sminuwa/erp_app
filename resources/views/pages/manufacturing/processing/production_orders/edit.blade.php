@extends('layouts.backend.app')

@section('title', 'Edit Production Order')

@push('css')
<style>
    .item-row td { padding: 5px; vertical-align: middle; }
</style>
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Edit Production Order: {{ $model->reference }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.production_orders.index') }}">Production Orders</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.production_orders.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <a class="btn btn-info btn-sm" href="{{ route('manufacturing.production_orders.show', $model->id) }}">
                <span class="fa fa-eye"></span> View
            </a>
            <div class="container-fluid mt-3">
                <form action="{{ route('manufacturing.production_orders.update', $model->id) }}" method="POST" id="order-form">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Order Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="reference">Reference</label>
                                        <input type="text" class="form-control" value="{{ $model->reference }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="order_date">Order Date</label>
                                        <input type="date" class="form-control" name="order_date" id="order_date"
                                            value="{{ old('order_date', $model->order_date) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" id="start_date"
                                            value="{{ old('start_date', $model->start_date) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" name="end_date" id="end_date"
                                            value="{{ old('end_date', $model->end_date) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control" name="notes" id="notes" rows="2">{{ old('notes', $model->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Order Items (BOM Selection)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="items-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="45%">BOM</th>
                                            <th width="15%">Finish Product</th>
                                            <th width="15%">Quantity to Produce</th>
                                            <th width="15%">Expected Output</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-body">
                                        @foreach($model->items as $index => $item)
                                        <tr class="item-row" data-row="{{ $index }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <select class="form-control select2-single item-bom" name="items[{{ $index }}][bom_id]" required>
                                                    <option value="">Select BOM...</option>
                                                    @foreach($boms as $bom)
                                                        <option value="{{ $bom->id }}" data-product="{{ $bom->finishProduct->name ?? '' }}" data-output="{{ $bom->actual_output }}"
                                                            {{ $item->bom_id == $bom->id ? 'selected' : '' }}>
                                                            {{ $bom->reference }} - {{ $bom->description }} ({{ ucfirst($bom->bom_type) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="item-product">{{ $item->bom->finishProduct->name ?? '-' }}</td>
                                            <td>
                                                <input type="number" step="0.0001" class="form-control item-qty" name="items[{{ $index }}][quantity]" min="0.0001" value="{{ $item->quantity_to_produce }}" required>
                                            </td>
                                            <td class="item-output">{{ number_format($item->quantity_to_produce * ($item->bom->actual_output ?? 1), 4) }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-item">
                                <i class="fa fa-plus"></i> Add Item
                            </button>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body text-right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> Update Order
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('js')
<script type="text/javascript">
$(function() {
    var rowIndex = {{ $model->items->count() }};
    var boms = @json($boms);

    $('.select2-single').select2({ width: '100%' });

    $('#add-item').on('click', function() {
        var bomOptions = '<option value="">Select BOM...</option>';
        boms.forEach(function(b) {
            var productName = b.finish_product ? b.finish_product.name : '';
            bomOptions += '<option value="' + b.id + '" data-product="' + productName + '" data-output="' + b.actual_output + '">' +
                b.reference + ' - ' + b.description + ' (' + b.bom_type.charAt(0).toUpperCase() + b.bom_type.slice(1) + ')' +
                '</option>';
        });

        var newRow = `
            <tr class="item-row" data-row="${rowIndex}">
                <td>${rowIndex + 1}</td>
                <td>
                    <select class="form-control select2-single item-bom" name="items[${rowIndex}][bom_id]" required>
                        ${bomOptions}
                    </select>
                </td>
                <td class="item-product">-</td>
                <td>
                    <input type="number" step="0.0001" class="form-control item-qty" name="items[${rowIndex}][quantity]" min="0.0001" required>
                </td>
                <td class="item-output">-</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;

        $('#items-body').append(newRow);
        rowIndex++;
        $('#items-body tr:last .select2-single').select2({ width: '100%' });
        renumberRows();
    });

    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
            renumberRows();
        } else {
            alert('At least one item is required.');
        }
    });

    function renumberRows() {
        $('.item-row').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    $(document).on('change', '.item-bom', function() {
        var row = $(this).closest('tr');
        var selected = $(this).find(':selected');
        var productName = selected.data('product') || '-';
        var output = selected.data('output') || 1;

        row.find('.item-product').text(productName);
        updateExpectedOutput(row, output);
    });

    $(document).on('input', '.item-qty', function() {
        var row = $(this).closest('tr');
        var selected = row.find('.item-bom option:selected');
        var output = selected.data('output') || 1;
        updateExpectedOutput(row, output);
    });

    function updateExpectedOutput(row, bomOutput) {
        var qty = parseFloat(row.find('.item-qty').val()) || 0;
        var expectedOutput = qty * bomOutput;
        row.find('.item-output').text(expectedOutput.toFixed(4));
    }
});
</script>
@endpush
