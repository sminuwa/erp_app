@extends('layouts.backend.app')

@section('title', 'Create Daily Manufacturing Schedule')

@push('css')
<style>
    #items-table .form-control-sm { min-width: 100px; }
</style>
@endpush

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
    <form action="{{ route('manufacturing.schedules.store') }}" method="POST" id="schedule-form">
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
                                        <option value="{{ $order->id }}">
                                            {{ $order->reference }}
                                        </option>
                                        @endforeach
                                    </select>
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
                        <h5>Schedule Items</h5>
                        <div id="items-loading" class="text-center py-3" style="display: none;">
                            <i class="fa fa-spinner fa-spin"></i> Loading order items...
                        </div>
                        <table class="table table-bordered table-sm" id="items-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>BOM</th>
                                    <th>Finish Product</th>
                                    <th>Type</th>
                                    <th class="text-right" width="100">Order Qty</th>
                                    <th class="text-right" width="100">Scheduled</th>
                                    <th class="text-right" width="100">Remaining</th>
                                    <th width="130">Schedule Qty</th>
                                    <th width="60">Action</th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                <tr class="empty-row">
                                    <td colspan="8" class="text-center text-muted">Please select a Production Order to load items</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            <i class="fa fa-save"></i> Save Schedule
                        </button>
                        <a href="{{ route('manufacturing.schedules.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="materials-panel" style="display:none;" class="mt-3">
            <div class="card">
                <div class="card-header"><h5 class="card-title">Required Raw Materials</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="materials-table">
                        <thead><tr><th>#</th><th>Product</th><th>Store</th><th class="text-right">Required Qty</th></tr></thead>
                        <tbody></tbody>
                    </table>
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

    var orderItems = [];

    function formatNum(num) {
        return parseFloat(num || 0).toFixed(4);
    }

    function recalculateMaterials() {
        var aggregated = {};
        $('.item-row').each(function() {
            var itemId = $(this).data('item-id');
            var qty = parseFloat($(this).find('.item-qty').val()) || 0;
            if (qty <= 0) return;

            // Find matching orderItem
            var item = orderItems.find(function(oi) { return oi.id == itemId; });
            if (!item || !item.bom_materials) return;

            $.each(item.bom_materials, function(_, mat) {
                var key = mat.product_id + '_' + mat.store_id;
                var materialQty = mat.bom_qty * qty;
                if (aggregated[key]) {
                    aggregated[key].quantity += materialQty;
                } else {
                    aggregated[key] = {
                        product_name: mat.product_name,
                        store_name: mat.store_name,
                        quantity: materialQty
                    };
                }
            });
        });

        var materialsBody = $('#materials-table tbody');
        materialsBody.empty();
        var values = Object.values(aggregated);
        if (values.length > 0) {
            $.each(values, function(i, mat) {
                materialsBody.append(
                    '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + mat.product_name + '</td>' +
                    '<td>' + mat.store_name + '</td>' +
                    '<td class="text-right">' + formatNum(mat.quantity) + '</td>' +
                    '</tr>'
                );
            });
            $('#materials-panel').show();
        } else {
            $('#materials-panel').hide();
        }
    }

    // Load order items via AJAX when order is selected
    $('#order_id').on('change', function() {
        var orderId = $(this).val();
        $('#items-body').empty();
        orderItems = [];

        if (!orderId) {
            $('#items-body').append('<tr class="empty-row"><td colspan="8" class="text-center text-muted">Please select a Production Order to load items</td></tr>');
            $('#materials-panel').hide();
            $('#materials-table tbody').empty();
            return;
        }

        $('#items-loading').show();
        $('#items-table').hide();

        $.ajax({
            url: '{{ route("manufacturing.schedules.get_order_items") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order_id: orderId
            },
            success: function(response) {
                $('#items-loading').hide();
                $('#items-table').show();

                if (response.status && response.data) {
                    orderItems = response.data;
                    loadAllItems();
                    recalculateMaterials();
                }
            },
            error: function() {
                $('#items-loading').hide();
                $('#items-table').show();
                $('#items-body').append('<tr class="empty-row"><td colspan="8" class="text-center text-danger">Error loading order items</td></tr>');
            }
        });
    });

    // Auto-load ALL order items that have remaining qty
    function loadAllItems() {
        $('#items-body').empty();
        var hasItems = false;

        orderItems.forEach(function(item, index) {
            if (item.remaining > 0) {
                addItemRow(item, index);
                hasItems = true;
            }
        });

        if (!hasItems) {
            $('#items-body').append('<tr class="empty-row"><td colspan="8" class="text-center text-warning">All items in this order are fully scheduled</td></tr>');
        }
    }

    function addItemRow(item, index) {
        var typeClass = item.bom_type === 'batch' ? 'info' : 'primary';
        var newRow = `
            <tr class="item-row" data-item-id="${item.id}">
                <td>
                    <input type="hidden" name="items[${index}][order_item_id]" value="${item.id}">
                    ${item.bom_ref}
                </td>
                <td>${item.product}</td>
                <td><span class="badge badge-${typeClass}">${item.bom_type}</span></td>
                <td class="text-right">${formatNum(item.qty_to_produce)}</td>
                <td class="text-right">${formatNum(item.scheduled_qty)}</td>
                <td class="text-right font-weight-bold ${item.remaining > 0 ? 'text-warning' : 'text-success'}">${formatNum(item.remaining)}</td>
                <td>
                    <input type="number" name="items[${index}][quantity]" class="form-control form-control-sm item-qty"
                        step="0.0001" min="0.0001" max="${item.remaining}" value="${formatNum(item.remaining)}" required
                        data-remaining="${item.remaining}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-xs remove-row" title="Remove">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        `;

        if ($('.empty-row').length > 0) {
            $('.empty-row').remove();
        }

        $('#items-body').append(newRow);
    }

    // Remove row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        if ($('.item-row').length === 0) {
            $('#items-body').append('<tr class="empty-row"><td colspan="8" class="text-center text-muted">No items. Select a Production Order to reload.</td></tr>');
        }
        recalculateMaterials();
    });

    // Validate qty on change and recalculate materials
    $(document).on('change', '.item-qty', function() {
        var max = parseFloat($(this).data('remaining')) || 0;
        var val = parseFloat($(this).val()) || 0;
        if (val > max) {
            $(this).val(formatNum(max));
            alert('Quantity cannot exceed remaining: ' + formatNum(max));
        }
        if (val <= 0) {
            $(this).val(formatNum(0.0001));
        }
        recalculateMaterials();
    });
    $(document).on('input', '.item-qty', function() {
        recalculateMaterials();
    });

    // Form submit validation
    $('#schedule-form').on('submit', function(e) {
        if ($('.item-row').length === 0) {
            e.preventDefault();
            alert('Please add at least one schedule item.');
            return false;
        }

        var hasError = false;
        $('.item-qty').each(function() {
            var max = parseFloat($(this).data('remaining')) || 0;
            var val = parseFloat($(this).val()) || 0;
            if (val > max) {
                hasError = true;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('One or more quantities exceed the remaining schedulable amount.');
            return false;
        }
    });
});
</script>
@endpush
