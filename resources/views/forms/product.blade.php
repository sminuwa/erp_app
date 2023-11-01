<form action="{{ isset($route) ? $route : route('products.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="company_id">Company</label>
        <select type="number" class="form-control ajax-companies select2-single {{ $errors->has('company_id') ? ' is-invalid' : '' }}"
                name="company_id" id="company_id" selected_item="{{ $model->company_id }}" required="required">
        </select>
        @if ($errors->has('company_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('company_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="category_id">Category</label>
        <select type="number" class="form-control ajax-categories select2-single {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
            name="category_id" id="category_id" selected_item="{{ $model->category_id }}" required="required">
        </select>
        @if ($errors->has('category_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('category_id') }}</strong>
            </div>
        @endif
    </div>
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
        <label for="barcode">Barcode</label>
        <input type="text" class="form-control {{ $errors->has('barcode') ? ' is-invalid' : '' }}"
            name="barcode" id="barcode" value="{{ old('barcode', $model->barcode) }}"
            placeholder="">
        @if ($errors->has('barcode'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('barcode') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="barcode">This product can expire</label>
        <div class="form-check">
            <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" value="1"
                   name="expiry_status" id="status_yes" {{ $model->expiry_status == null || $model->expiry_status == 1 ? 'checked' : '' }}>
            Yes
            &nbsp;&nbsp;
            &nbsp;&nbsp;
            <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" value="0"
                   checked name="expiry_status" id="status_no" {{ $model->expiry_status != null && $model->expiry_status == 0 ? 'checked' : '' }}> No

            @if ($errors->has('status'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('status') }}</strong>
                </div>
            @endif
        </div>
    </div>
    <div class="form-group">
        <label for="barcode">Status</label>
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
    </div>

    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
