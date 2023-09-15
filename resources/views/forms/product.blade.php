<form action="{{ isset($route) ? $route : route('products.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
            id="name" value="{{ old('name', $model->name) }}" placeholder="" maxlength="191" required="required">
        @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="strength">Strength</label>
        <input type="text" class="form-control {{ $errors->has('strength') ? ' is-invalid' : '' }}"
            name="strength" id="strength" value="{{ old('strength', $model->strength) }}"
            placeholder="" required="required">
        @if ($errors->has('strength'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('strength') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="generic_name">Generic Name</label>
        <input type="text" class="form-control {{ $errors->has('generic_name') ? ' is-invalid' : '' }}"
            name="generic_name" id="generic_name" value="{{ old('generic_name', $model->generic_name) }}"
            placeholder="" required="required">
        @if ($errors->has('generic_name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('generic_name') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="category_id">Category</label>
        <select type="number" class="form-control {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
            name="category_id" id="category_id" required="required">
            <option value="">Select...</option>
            @if (isset($categories))
                @foreach ($categories as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->category_id ? 'selected' : '' }}>
                        {{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('category_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('category_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="category_id">Company</label>
        <select type="number" class="form-control {{ $errors->has('company_id') ? ' is-invalid' : '' }}"
            name="company_id" id="company_id" required="required">
            <option value="">Select...</option>
            @if (isset($companies))
                @foreach ($companies as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->company_id ? 'selected' : '' }}>
                        {{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('company_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('company_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="dosage_form_id">Dosage Form</label>
        <select type="number" class="form-control {{ $errors->has('dosage_form_id') ? ' is-invalid' : '' }}"
            name="dosage_form_id" id="dosage_form_id" required="required">
            <option value="">Select...</option>
            @if (isset($dosages))
                @foreach ($dosages as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->dosage_form_id ? 'selected' : '' }}>
                        {{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('dosage_form_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('dosage_form_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="barcode">Barcode</label>
        <input type="text" class="form-control {{ $errors->has('barcode') ? ' is-invalid' : '' }}"
            name="barcode" id="barcode" value="{{ old('barcode', $model->barcode) }}"
            placeholder="" required="required">
        @if ($errors->has('barcode'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('barcode') }}</strong>
            </div>
        @endif
    </div>
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
