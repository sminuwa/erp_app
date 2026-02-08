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
                                    <label>Link Type <span class="text-danger">*</span></label>
                                    <select id="link_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="schedule">Daily Schedule</option>
                                        <option value="bom">BOM (Direct)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="schedule-section" style="display:none;">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Daily Schedule</label>
                                    <select name="schedule_id" id="schedule_id" class="form-control select2-single">
                                        <option value="">Select Schedule</option>
                                        @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->id }}">{{ $schedule->reference }} - {{ date('d M Y', strtotime($schedule->schedule_date)) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Materials will be auto-calculated from the selected schedule.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="bom-section" style="display:none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bill of Materials</label>
                                    <select name="bom_id" id="bom_id" class="form-control select2-single">
                                        <option value="">Select BOM</option>
                                        @foreach($boms as $bom)
                                        <option value="{{ $bom->id }}">{{ $bom->reference }} - {{ $bom->description }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Materials will be auto-calculated from the selected BOM.</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" step="0.0001" min="0.0001" value="1">
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

    $('#link_type').change(function() {
        var type = $(this).val();
        $('#schedule-section').hide();
        $('#bom-section').hide();
        // Clear hidden fields when switching
        $('#schedule_id').val(null).trigger('change');
        $('#bom_id').val(null).trigger('change');

        if (type === 'schedule') {
            $('#schedule-section').show();
        } else if (type === 'bom') {
            $('#bom-section').show();
        }
    });
});
</script>
@endpush
