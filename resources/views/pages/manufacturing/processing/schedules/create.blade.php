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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Reference <span class="text-danger">*</span></label>
                                    <input type="text" name="reference" class="form-control" value="{{ $model->reference }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Schedule Date <span class="text-danger">*</span></label>
                                    <input type="date" name="schedule_date" class="form-control" value="{{ $model->schedule_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Production Order <span class="text-danger">*</span></label>
                                    <select name="production_order_id" id="production_order_id" class="form-control select2-single" required>
                                        <option value="">Select Production Order</option>
                                        @foreach($productionOrders as $order)
                                        <option value="{{ $order->id }}">{{ $order->reference }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
                                    <label>Machine</label>
                                    <select name="machine_id" class="form-control select2-single">
                                        <option value="">Select Machine</option>
                                        @foreach($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Schedule Items</h5>
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>BOM</th>
                                    <th width="150">Quantity</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][bom_id]" class="form-control select2-single bom-select">
                                            <option value="">Select BOM</option>
                                            @foreach($boms as $bom)
                                            <option value="{{ $bom->id }}">{{ $bom->reference }} - {{ $bom->finishProduct->name ?? '' }}</option>
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
                                    <td colspan="3">
                                        <button type="button" class="btn btn-success btn-sm" id="add-row">
                                            <i class="fa fa-plus"></i> Add Row
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

    var rowIndex = 1;

    $('#add-row').click(function() {
        var newRow = `
            <tr class="item-row">
                <td>
                    <select name="items[${rowIndex}][bom_id]" class="form-control select2-single bom-select">
                        <option value="">Select BOM</option>
                        @foreach($boms as $bom)
                        <option value="{{ $bom->id }}">{{ $bom->reference }} - {{ $bom->finishProduct->name ?? '' }}</option>
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
