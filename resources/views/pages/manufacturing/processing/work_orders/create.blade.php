@extends('layouts.backend.app')

@section('title', 'Create Work Order')

@push('css')
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Create Work Order</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Create Work Order</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
    <form action="{{ route('manufacturing.work_orders.store') }}" method="POST" id="work-order-form">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Work Order</h3>
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
                                    <input type="date" name="work_order_date" class="form-control" value="{{ $model->work_order_date ?? date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Daily Schedule <span class="text-danger">*</span></label>
                                    <select name="daily_schedule_id" id="daily_schedule_id" class="form-control select2-single" required>
                                        <option value="">Select Schedule</option>
                                        @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->id }}">{{ $schedule->reference }} - {{ date('d M Y', strtotime($schedule->schedule_date)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Machine <span class="text-danger">*</span></label>
                                    <select name="machine_id" id="machine_id" class="form-control select2-single" required>
                                        <option value="">Select Machine</option>
                                        @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->code }} - {{ $machine->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Team <span class="text-danger">*</span></label>
                                    <select name="team_id" id="team_id" class="form-control select2-single" required>
                                        <option value="">Select Team</option>
                                        @foreach($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
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

                        {{-- Schedule Items --}}
                        <div id="schedule-items-section" style="display:none;">
                            <hr>
                            <h5>Schedule Items</h5>
                            <table class="table table-bordered table-striped" id="items-table">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="select-all"></th>
                                        <th>BOM Reference</th>
                                        <th>Finish Product</th>
                                        <th>Scheduled Qty</th>
                                        <th>Remaining Qty</th>
                                        <th width="150">Planned Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="items-tbody">
                                </tbody>
                            </table>
                        </div>

                        {{-- Required Materials --}}
                        <div id="materials-section" style="display:none;">
                            <hr>
                            <h5>Required Materials</h5>
                            <table class="table table-bordered table-striped" id="materials-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Store</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody id="materials-tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Work Order</button>
                        <a href="{{ route('manufacturing.work_orders.index') }}" class="btn btn-secondary">Cancel</a>
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

    // Select all checkbox
    $('#select-all').change(function() {
        var checked = $(this).is(':checked');
        $('#items-tbody .item-checkbox').prop('checked', checked);
    });

    // Load schedule items on schedule change
    $('#daily_schedule_id').change(function() {
        var scheduleId = $(this).val();

        // Clear existing items
        $('#items-tbody').empty();
        $('#materials-tbody').empty();
        $('#schedule-items-section').hide();
        $('#materials-section').hide();

        if (!scheduleId) return;

        $.ajax({
            url: "{{ route('manufacturing.work_orders.get_schedule_items') }}",
            type: 'GET',
            data: { schedule_id: scheduleId },
            success: function(response) {
                // Populate items table
                if (response.items && response.items.length > 0) {
                    $.each(response.items, function(index, item) {
                        var row = '<tr>' +
                            '<td><input type="checkbox" class="item-checkbox" name="items[' + index + '][selected]" value="1"></td>' +
                            '<td>' + item.bom_reference + '<input type="hidden" name="items[' + index + '][schedule_item_id]" value="' + item.schedule_item_id + '"></td>' +
                            '<td>' + item.finish_product + '</td>' +
                            '<td>' + item.scheduled_qty + '</td>' +
                            '<td>' + item.remaining_qty + '</td>' +
                            '<td><input type="number" name="items[' + index + '][planned_qty]" class="form-control form-control-sm" step="0.0001" min="0.0001" max="' + item.remaining_qty + '" value="' + item.remaining_qty + '"></td>' +
                            '</tr>';
                        $('#items-tbody').append(row);
                    });
                    $('#schedule-items-section').show();
                }

                // Populate materials table
                if (response.materials && response.materials.length > 0) {
                    $.each(response.materials, function(index, material) {
                        var row = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + material.product_name + '</td>' +
                            '<td>' + material.store_name + '</td>' +
                            '<td>' + material.quantity + '</td>' +
                            '</tr>';
                        $('#materials-tbody').append(row);
                    });
                    $('#materials-section').show();
                }
            },
            error: function() {
                alert('Failed to load schedule items.');
            }
        });
    });
});
</script>
@endpush
