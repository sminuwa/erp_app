<form action="{{ isset($route) ? $route : route('manufacturing.teams.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Team Name</label>
                <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                    name="name" id="name" value="{{ old('name', $model->name) }}"
                    placeholder="Team Name" maxlength="191" required="required">
                @if ($errors->has('name'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('name') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="branch_id">Branch</label>
                <select class="form-control select2-single {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                    name="branch_id" id="branch_id" required="required">
                    <option value="">Select Branch...</option>
                    @if (isset($branches))
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $model->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @if ($errors->has('branch_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('branch_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="supervisors">Supervisors (System Users)</label>
                <select class="form-control select2-multiple {{ $errors->has('supervisors') ? ' is-invalid' : '' }}"
                    name="supervisors[]" id="supervisors" multiple="multiple">
                    @php
                        $selectedSupervisors = old('supervisors', isset($model->id) ? $model->supervisors->pluck('user_id')->toArray() : []);
                    @endphp
                    @if (isset($users))
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, $selectedSupervisors) ? 'selected' : '' }}>
                                {{ $user->surname }} {{ $user->firstname }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @if ($errors->has('supervisors'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('supervisors') }}</strong>
                    </div>
                @endif
                <small class="text-muted">Select one or more supervisors from system users.</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="members">Team Members (Manufacturing Staff)</label>
                <select class="form-control select2-multiple {{ $errors->has('members') ? ' is-invalid' : '' }}"
                    name="members[]" id="members" multiple="multiple">
                    @php
                        $selectedMembers = old('members', isset($model->id) ? $model->members->pluck('staff_id')->toArray() : []);
                    @endphp
                    @if (isset($staff))
                        @foreach ($staff as $staffMember)
                            <option value="{{ $staffMember->id }}" {{ in_array($staffMember->id, $selectedMembers) ? 'selected' : '' }}>
                                {{ $staffMember->name }} ({{ $staffMember->employment_id }})
                            </option>
                        @endforeach
                    @endif
                </select>
                @if ($errors->has('members'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('members') }}</strong>
                    </div>
                @endif
                <small class="text-muted">Select one or more manufacturing staff members.</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Status</label>
        <div class="form-check">
            <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" value="1"
                name="status" id="status_yes" {{ old('status', $model->status) == 1 ? 'checked' : '' }}>
            <label class="form-check-label" for="status_yes">Active</label>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" value="0"
                name="status" id="status_no" {{ old('status', $model->status) === 0 || old('status', $model->status) === '0' ? 'checked' : '' }}>
            <label class="form-check-label" for="status_no">Inactive</label>
            @if ($errors->has('status'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('status') }}</strong>
                </div>
            @endif
        </div>
    </div>

    <div class="form-group text-right">
        <input type="submit" class="btn btn-primary" value="Save" />
    </div>
</form>
