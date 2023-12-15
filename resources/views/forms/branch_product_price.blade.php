<form action="{{ isset($route) ? $route : route('branch_product_prices.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="branch_id">Branch</label>
        <select class="form-control select2-single ajax-branches {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                name="branch_id"
                id="branch_id"
                selected_item="{{ $model->branch_id }}"
                required>
        </select>
        @if ($errors->has('branch_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('branch_id') }}</strong>
            </div>
        @endif
    </div>
    {{-- <div class="form-group">
        <label for="category_id">Category</label>
        <select class="form-control ajax-categories select2-single {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
                name="category_id"
                id="category_id"

                required="required">
        </select>
        @if ($errors->has('category_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('category_id') }}</strong>
            </div>
        @endif
    </div> --}}
    <div class="form-group">
        <label for="product_id">Product</label>
        <select class="form-control ajax-products select2-single {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                name="product_id"
                id="product_id"
                selected_item="{{ $model->product_id }}"
                required="required">
        </select>
        @if ($errors->has('product_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('product_id') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="retail_selling_price">Retail Price</label>
        <input type="number" class="form-control {{ $errors->has('retail_selling_price') ? ' is-invalid' : '' }}"
            name="retail_selling_price" id="retail_selling_price" value="{{ old('retail_selling_price', $model->retail_selling_price) }}"
            placeholder="" required="required">
        @if ($errors->has('retail_selling_price'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('retail_selling_price') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="whole_selling_price">Wholesale Price</label>
        <input type="number" class="form-control {{ $errors->has('whole_selling_price') ? ' is-invalid' : '' }}"
            name="whole_selling_price" id="whole_selling_price" value="{{ old('whole_selling_price', $model->whole_selling_price) }}"
            placeholder="" required="required">
        @if ($errors->has('whole_selling_price'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('whole_selling_price') }}</strong>
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

    <div class="form-group">
        <input type="hidden" class="form-control {{ $errors->has('updated_by') ? ' is-invalid' : '' }}"
            name="updated_by" id="updated_by" value="{{ Auth::id() }}" placeholder="" required="required">
    </div>
    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />
    </div>
</form>
