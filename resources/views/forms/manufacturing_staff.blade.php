<form action="{{ isset($route) ? $route : route('manufacturing.staff.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />

    <div class="form-group">
        <label for="employment_id">Employment ID</label>
        <input type="text" class="form-control {{ $errors->has('employment_id') ? ' is-invalid' : '' }}"
            name="employment_id" id="employment_id" value="{{ old('employment_id', $model->employment_id) }}"
            placeholder="Employment ID" maxlength="50" required="required">
        @if ($errors->has('employment_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('employment_id') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
            name="name" id="name" value="{{ old('name', $model->name) }}"
            placeholder="Staff Name" maxlength="191" required="required">
        @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="text" class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}"
            name="phone" id="phone" value="{{ old('phone', $model->phone) }}"
            placeholder="Phone Number" maxlength="50">
        @if ($errors->has('phone'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('phone') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="address">Address</label>
        <textarea class="form-control {{ $errors->has('address') ? ' is-invalid' : '' }}"
            name="address" id="address" placeholder="Address" rows="2" maxlength="500">{{ old('address', $model->address) }}</textarea>
        @if ($errors->has('address'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('address') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="bvn">BVN</label>
        <input type="text" class="form-control {{ $errors->has('bvn') ? ' is-invalid' : '' }}"
            name="bvn" id="bvn" value="{{ old('bvn', $model->bvn) }}"
            placeholder="Bank Verification Number" maxlength="20">
        @if ($errors->has('bvn'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('bvn') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="nin">NIN</label>
        <input type="text" class="form-control {{ $errors->has('nin') ? ' is-invalid' : '' }}"
            name="nin" id="nin" value="{{ old('nin', $model->nin) }}"
            placeholder="National Identification Number" maxlength="20">
        @if ($errors->has('nin'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('nin') }}</strong>
            </div>
        @endif
    </div>

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
