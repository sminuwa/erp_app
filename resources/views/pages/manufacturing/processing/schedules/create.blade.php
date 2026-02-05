@extends('layouts.backend.app')

@section('title', 'Create Daily Manufacturing Schedule')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Create Daily Manufacturing Schedule</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Create Schedule</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
    <form action="{{ route('manufacturing.schedules.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Daily Manufacturing Schedule</h3>
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
                                    <label>Schedule Date <span class="text-danger">*</span></label>
                                    <input type="date" name="schedule_date" class="form-control" value="{{ $model->schedule_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Production Order <span class="text-danger">*</span></label>
                                    <select name="order_id" id="order_id" class="form-control select2-single" required>
                                        <option value="">Select Production Order</option>
                                        @foreach($productionOrders as $order)
                                        @php
                                            $itemsData = $order->items->map(function($item) {
                                                return [
                                                    'id' => $item->id,
                                                    'bom' => $item->bom->reference ?? '',
                                                    'product' => $item->bom->finishProduct->name ?? '',
                                                    'qty' => $item->quantity,
                                                    'output' => $item->expected_output
                                                ];
                                            });
                                        @endphp
                                        <option value="{{ $order->id }}" data-items='{{ $itemsData->toJson() }}'>
                                            {{ $order->reference }}
                                        </option>
                                        @endforeach
                                    </select>
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
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Machine</label>
                                    <select name="machine_id" class="form-control select2-single">
                                        <option value="">Select Machine</option>
                                        @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Schedule Items <small class="text-muted">(Select Production Order first)</small></h5>
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>BOM</th>
                                    <th>Finish Product</th>
                                    <th width="120">Order Qty</th>
                                    <th width="120">Expected Output</th>
                                    <th width="120">Schedule Qty</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                <tr class="empty-row">
                                    <td colspan="6" class="text-center text-muted">Please select a Production Order to load items</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <button type="button" class="btn btn-success btn-sm" id="add-item" disabled>
                                            <i class="fa fa-plus"></i> Add Item
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Schedule</button>
                        <a href="{{ route('manufacturing.schedules.index') }}" class="btn btn-secondary">Cancel</a>
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

    var rowIndex = 0;
    var orderItems = [];

    $('#order_id').change(function() {
        var selected = $(this).find(':selected');
        var items = selected.data('items');
        orderItems = items || [];
        
        $('#items-body').empty();
        rowIndex = 0;

        if (orderItems.length === 0) {
            $('#items-body').append('<tr class="empty-row"><td colspan="6" class="text-center text-muted">Please select a Production Order to load items</td></tr>');
            $('#add-item').prop('disabled', true);
        } else {
            // Pre-populate with first item from order
            addItemRow(orderItems[0]);
            $('#add-item').prop('disabled', false);
        }
    });

    $('#add-item').click(function() {
        if (orderItems.length > 0) {
            addItemRow(orderItems[0]);
        }
    });

    function addItemRow(defaultItem) {
        var options = '<option value="">Select Order Item</option>';
        orderItems.forEach(function(item) {
            var selected = (item.id == defaultItem.id) ? 'selected' : '';
            options += '<option value="' + item.id + '" data-output="' + item.output + '" data-qty="' + item.qty + '" ' + selected + '>' + 
                item.bom + ' - ' + item.product + 
                '</option>';
        });

        var newRow = `
            <tr class="item-row" data-row="${rowIndex}">
                <td>
                    <select name="items[${rowIndex}][order_item_id]" class="form-control select2-single item-select" required>
                        ${options}
                    </select>
                </td>
                <td class="item-product">${defaultItem.product}</td>
                <td class="item-order-qty">${parseFloat(defaultItem.qty).toFixed(4)}</td>
                <td class="item-expected">${parseFloat(defaultItem.output).toFixed(4)}</td>
                <td>
                    <input type="number" name="items[${rowIndex}][quantity]" class="form-control item-qty" step="0.0001" min="0.0001" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        if ($('.empty-row').length > 0) {
            $('.empty-row').remove();
        }

        $('#items-body').append(newRow);
        $('#items-body tr:last .select2-single').select2({ width: '100%' });
        rowIndex++;
    }

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        if ($('.item-row').length === 0) {
            $('#items-body').append('<tr class="empty-row"><td colspan="6" class="text-center text-muted">Please select a Production Order to load items</td></tr>');
        }
    });

    $(document).on('change', '.item-select', function() {
        var row = $(this).closest('tr');
        var selected = $(this).find(':selected');
        var product = selected.text().split(' - ')[1] || '';
        var qty = selected.data('qty') || 0;
        var output = selected.data('output') || 0;

        row.find('.item-product').text(product);
        row.find('.item-order-qty').text(parseFloat(qty).toFixed(4));
        row.find('.item-expected').text(parseFloat(output).toFixed(4));
    });
});
</script>
@endpush
