@extends('layouts.backend.app')

@section('title', 'Edit Daily Manufacturing Schedule')

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
                    <h4>Edit Daily Manufacturing Schedule</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Edit Schedule</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
    <form action="{{ route('manufacturing.schedules.update', $model->id) }}" method="POST" id="schedule-form">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Schedule: {{ $model->reference }}</h3>
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
                                    <label>Schedule Date <span class="text-danger">*</span></label>
                                    <input type="date" name="schedule_date" class="form-control" value="{{ $model->schedule_date instanceof \Carbon\Carbon ? $model->schedule_date->format('Y-m-d') : $model->schedule_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Production Order <span class="text-danger">*</span></label>
                                    <select name="order_id" id="order_id" class="form-control select2-single" required>
                                        <option value="">Select Production Order</option>
                                        @foreach($productionOrders as $order)
                                        <option value="{{ $order->id }}" {{ $model->production_order_id == $order->id ? 'selected' : '' }}>
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
                                        <option value="{{ $team->id }}" {{ $model->team_id == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
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
                                        <option value="{{ $machine->id }}" {{ $model->machine_id == $machine->id ? 'selected' : '' }}>{{ $machine->code }} - {{ $machine->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="1">{{ $model->notes }}</textarea>
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
                                    <td colspan="8" class="text-center text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            <i class="fa fa-save"></i> Update Schedule
                        </button>
                        <a href="{{ route('manufacturing.schedules.show', $model->id) }}" class="btn btn-secondary">Cancel</a>
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

    var scheduleId = {{ $model->id }};
    var orderItems = [];

    // Existing schedule items (to pre-populate qty)
    var existingItems = {};
    @foreach($model->items as $item)
    existingItems[{{ $item->production_order_item_id }}] = {{ $item->scheduled_qty }};
    @endforeach

    function formatNum(num) {
        return parseFloat(num || 0).toFixed(4);
    }

    // Load order items via AJAX
    function loadOrderItems(orderId, preserveExisting) {
        if (!orderId) {
            $('#items-body').empty();
            $('#items-body').append('<tr class="empty-row"><td colspan="8" class="text-center text-muted">Please select a Production Order</td></tr>');
            return;
        }

        $('#items-loading').show();
        $('#items-table').hide();

        $.ajax({
            url: '{{ route("manufacturing.schedules.get_order_items") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order_id: orderId,
                schedule_id: scheduleId
            },
            success: function(response) {
                $('#items-loading').hide();
                $('#items-table').show();

                if (response.status && response.data) {
                    orderItems = response.data;
                    loadAllItems(preserveExisting);
                }
            },
            error: function() {
                $('#items-loading').hide();
                $('#items-table').show();
                $('#items-body').empty();
                $('#items-body').append('<tr class="empty-row"><td colspan="8" class="text-center text-danger">Error loading order items</td></tr>');
            }
        });
    }

    function loadAllItems(preserveExisting) {
        $('#items-body').empty();
        var hasItems = false;

        orderItems.forEach(function(item, index) {
            // Show items that have remaining qty OR are in the current schedule
            var existingQty = preserveExisting ? (existingItems[item.id] || 0) : 0;
            if (item.remaining > 0 || existingQty > 0) {
                addItemRow(item, index, existingQty);
                hasItems = true;
            }
        });

        if (!hasItems) {
            $('#items-body').append('<tr class="empty-row"><td colspan="8" class="text-center text-warning">All items in this order are fully scheduled</td></tr>');
        }
    }

    function addItemRow(item, index, existingQty) {
        var typeClass = item.bom_type === 'batch' ? 'info' : 'primary';
        var scheduleQty = existingQty > 0 ? existingQty : item.remaining;
        var maxQty = item.remaining;

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
                        step="0.0001" min="0.0001" max="${maxQty}" value="${formatNum(scheduleQty)}" required
                        data-remaining="${maxQty}">
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

    // On order change, reload items (without preserving existing)
    $('#order_id').on('change', function() {
        existingItems = {};
        loadOrderItems($(this).val(), false);
    });

    // Initial load with existing items preserved
    var initialOrderId = $('#order_id').val();
    if (initialOrderId) {
        loadOrderItems(initialOrderId, true);
    }

    // Remove row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        if ($('.item-row').length === 0) {
            $('#items-body').append('<tr class="empty-row"><td colspan="8" class="text-center text-muted">No items. Change order to reload.</td></tr>');
        }
    });

    // Validate qty on change
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
