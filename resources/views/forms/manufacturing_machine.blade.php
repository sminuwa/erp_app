<form action="{{ isset($route) ? $route : route('manufacturing.machines.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />

    <div class="form-group">
        <label for="code">Machine Code</label>
        <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}"
            name="code" id="code" value="{{ old('code', $model->code) }}"
            placeholder="Machine Code" maxlength="20" required="required" @if(isset($model->id)) readonly @endif>
        @if ($errors->has('code'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('code') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <input type="text" class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}"
            name="description" id="description" value="{{ old('description', $model->description) }}"
            placeholder="Machine/Pot Description" maxlength="255" required="required">
        @if ($errors->has('description'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('description') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="capacity">Capacity</label>
        <input type="number" step="0.01" class="form-control {{ $errors->has('capacity') ? ' is-invalid' : '' }}"
            name="capacity" id="capacity" value="{{ old('capacity', $model->capacity) }}"
            placeholder="Capacity (optional)">
        @if ($errors->has('capacity'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('capacity') }}</strong>
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
