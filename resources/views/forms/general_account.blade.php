<form action="{{ isset($route) ? $route : route('general_accounts.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="class">Class</label>
        <input type="text" class="form-control {{ $errors->has('class') ? ' is-invalid' : '' }}" name="class"
            id="class" value="{{ old('class', $model->class) }}" placeholder="" maxlength="30" required="required">
        @if ($errors->has('class'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('class') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="number">Number</label>
        <input type="text" class="form-control {{ $errors->has('number') ? ' is-invalid' : '' }}" name="number"
            id="number" value="{{ old('number', $model->number) }}" placeholder="" maxlength="30"
            required="required">
        @if ($errors->has('number'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('number') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <input type="text" class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}"
            name="description" id="description" value="{{ old('description', $model->description) }}" placeholder=""
            maxlength="100">
        @if ($errors->has('description'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('description') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="branch_id">Branch</label>
        <select type="number" class="form-control {{ $errors->has('branch_id') ? ' is-invalid' : '' }}" readonly
            name="branch_id" id="branch_id" required="required">
            @if (isset($branches))
                @foreach ($branches as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->branch_id ? 'selected' : '' }}>
                        {{ $data->code }}-{{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('branch_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('branch_id') }}</strong>
            </div>
        @endif
    </div>
    <label for="is_control">Is Control</label>
    <div class="form-check form-group">
        
        <input class="form-check-input {{ $errors->has('is_control') ? ' is-invalid' : '' }}" type="radio"
            value="1" name="is_control" id="status_yes"
            {{ $model->is_control == null || $model->is_control == 1 ? 'checked' : '' }}>
        Yes
        &nbsp;&nbsp;
        &nbsp;&nbsp;
        <input class="form-check-input {{ $errors->has('is_control') ? ' is-invalid' : '' }}" type="radio"
            value="0" name="is_control" id="status_no"
            {{ $model->is_control != null && $model->is_control == 0 ? 'checked' : '' }}> Not
        No
        @if ($errors->has('is_control'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('is_control') }}</strong>
            </div>
        @endif
    </div>
    <label for="status">Status</label>
    <div class="form-check">
        <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" value="1"
            name="status" id="status_yes" {{ $model->status == null || $model->status == 1 ? 'checked' : '' }}>
        Active
        &nbsp;&nbsp;
        &nbsp;&nbsp;
        <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" value="0"
            name="status" id="status_no" {{ $model->status != null && $model->status == 0 ? 'checked' : '' }}> Not
        Active
        @if ($errors->has('status'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('status') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
