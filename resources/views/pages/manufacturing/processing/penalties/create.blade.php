@extends('layouts.backend.app')

@section('title', 'Create Penalty')

@push('css')
<style>
    .target-select { display: none; }
    .target-select.active { display: block; }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Create Penalty</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Create Penalty</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="{{ route('manufacturing.penalties.store') }}" method="POST">
            @csrf
            <input type="hidden" name="reference" value="{{ $model->reference }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create Manufacturing Penalty</h3>
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
                                        <label>Penalty Date <span class="text-danger">*</span></label>
                                        <input type="date" name="penalty_date" class="form-control" value="{{ $model->penalty_date }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Penalty Type <span class="text-danger">*</span></label>
                                        <select name="penalty_type" id="penalty_type" class="form-control" required>
                                            <option value="">Select Type</option>
                                            <option value="team">Team</option>
                                            <option value="staff">Staff</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Production Document No</label>
                                        <select name="production_reference" class="form-control select2-single">
                                            <option value="">Select Production (Optional)</option>
                                            <optgroup label="Single Product Manufacturing">
                                                @foreach($singleManufacturing as $sm)
                                                <option value="{{ $sm->reference }}">
                                                    {{ $sm->reference }} - {{ $sm->bom->finishProduct->name ?? 'N/A' }}
                                                </option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Batch Conversion">
                                                @foreach($batchConversions as $bc)
                                                <option value="{{ $bc->reference }}">
                                                    {{ $bc->reference }} - {{ $bc->batchProduction->bom->finishProduct->name ?? 'N/A' }}
                                                </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Team Select -->
                                <div class="col-md-4 target-select" id="team-select">
                                    <div class="form-group">
                                        <label>Team <span class="text-danger">*</span></label>
                                        <select name="team_id" id="team_id" class="form-control select2-single">
                                            <option value="">Select Team</option>
                                            @foreach($teams as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Staff Select -->
                                <div class="col-md-4 target-select" id="staff-select">
                                    <div class="form-group">
                                        <label>Staff <span class="text-danger">*</span></label>
                                        <select name="staff_id" id="staff_id" class="form-control select2-single">
                                            <option value="">Select Staff</option>
                                            <optgroup label="Manufacturing Staff">
                                                @foreach($staff as $s)
                                                <option value="{{ $s->id }}">{{ $s->employment_id ?? '' }} - {{ $s->name }}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="ABTC Staff">
                                                @foreach($abtcStaff as $u)
                                                <option value="{{ $u->id }}" data-staff-type="abtc">{{ $u->user_code ?? '' }} - {{ $u->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Amount Charged <span class="text-danger">*</span></label>
                                        <input type="number" name="amount_charged" class="form-control" step="0.01" min="0.01" placeholder="0.00" value="{{ old('amount_charged') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total Loss Incurred</label>
                                        <input type="number" name="total_loss_incurred" class="form-control" step="0.01" min="0" placeholder="0.00" value="{{ old('total_loss_incurred') }}">
                                        <small class="text-muted">Total monetary value of the loss caused</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description <span class="text-danger">*</span></label>
                                        <textarea name="description" class="form-control" rows="3" placeholder="Enter penalty description/reason" required>{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Note:</strong> Upon posting, the penalty amount will be added to the team's ledger balance.
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Penalty
                            </button>
                            <a href="{{ route('manufacturing.penalties.index') }}" class="btn btn-secondary">Cancel</a>
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

    function toggleTargetSelect() {
        var type = $('#penalty_type').val();

        // Hide all and reset required
        $('.target-select').removeClass('active');
        $('#team_id').prop('required', false);
        $('#staff_id').prop('required', false);

        if (type === 'team') {
            $('#team-select').addClass('active');
            $('#team_id').prop('required', true);
        } else if (type === 'staff') {
            $('#staff-select').addClass('active');
            $('#staff_id').prop('required', true);
        }
    }

    // Run on page load
    toggleTargetSelect();

    // Run on change
    $('#penalty_type').on('change', toggleTargetSelect);
});
</script>
@endpush
