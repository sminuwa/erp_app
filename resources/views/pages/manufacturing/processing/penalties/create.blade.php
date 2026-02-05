@extends('layouts.app')

@section('title', 'Create Penalty')

@section('content')
<section class="content">
    <form action="{{ route('manufacturing.penalties.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Manufacturing Penalty</h3>
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
                                    <label>Penalty Date <span class="text-danger">*</span></label>
                                    <input type="date" name="penalty_date" class="form-control" value="{{ $model->penalty_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Penalty Type <span class="text-danger">*</span></label>
                                    <select name="penalty_type" id="penalty_type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="team">Team</option>
                                        <option value="staff">Staff</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4" id="team-select" style="display:none;">
                                <div class="form-group">
                                    <label>Team</label>
                                    <select name="team_id" class="form-control select2-single">
                                        <option value="">Select Team</option>
                                        @foreach($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="staff-select" style="display:none;">
                                <div class="form-group">
                                    <label>Staff</label>
                                    <select name="staff_id" class="form-control select2-single">
                                        <option value="">Select Staff</option>
                                        @foreach($staff as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
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
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Penalty</button>
                        <a href="{{ route('manufacturing.penalties.index') }}" class="btn btn-secondary">Cancel</a>
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

    $('#penalty_type').change(function() {
        var type = $(this).val();
        if (type == 'team') {
            $('#team-select').show();
            $('#staff-select').hide();
        } else if (type == 'staff') {
            $('#team-select').hide();
            $('#staff-select').show();
        } else {
            $('#team-select').hide();
            $('#staff-select').hide();
        }
    });
});
</script>
@endsection
