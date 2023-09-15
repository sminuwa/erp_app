<form action="{{ isset($route) ? $route : route('store_product_prices.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="store_id">Branch</label>
        <select type="number" class="form-control {{ $errors->has('branch_id') ? ' is-invalid' : '' }}" name="branch_id"
            id="branch_id" required="required">
            <option value="">Select...</option>
            @if ($model->store_id != null)
                <option value="{{ $model->store->branch_id }}" selected>
                    {{ $model->store->branch->name }}</option>
            @endif
            @if (isset($branches))
                @foreach ($branches as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->branch_id ? 'selected' : '' }}>
                        {{ $data->name }}</option>
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
        <label for="store_id">Store</label>
        <select type="number" class="form-control {{ $errors->has('store_id') ? ' is-invalid' : '' }}" name="store_id"
            id="store_id" required="required">
            @if ($model->store_id != null)
                <option value="all">All stores</option>
                <option value="{{ $model->store_id }}" selected>
                    {{ $model->store->name }}</option>
            @endif
        </select>
        @if ($errors->has('store_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('store_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="category_id">Category</label>
        <select class="form-control {{ $errors->has('category_id') ? ' is-invalid' : '' }}" name="category_id"
            id="category_id" required="required">
            <option value="">Select...</option>
            @if (isset($categories))
                @foreach ($categories as $data)
                    <option value="{{ $data->id }}"
                        {{ $data->id == optional($model->product)->category_id ? 'selected' : '' }}>
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
        <label for="product_id">Product</label>
        <select class="form-control {{ $errors->has('product_id') ? ' is-invalid' : '' }}" name="product_id"
            id="product_id" required="required">
            <option value="">Select...</option>
            @if (isset($products))
                @foreach ($products as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->product_id ? 'selected' : '' }}>
                        {{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('product_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('product_id') }}</strong>
            </div>
        @endif
    </div>
    {{--<div class="form-group">
        <label for="cost_price">Cost Price</label>
        <input type="text" class="form-control {{ $errors->has('cost_price') ? ' is-invalid' : '' }}"
            name="cost_price" id="cost_price" value="{{ old('cost_price', $model->cost_price) }}" placeholder=""
            required="required">
        @if ($errors->has('cost_price'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('cost_price') }}</strong>
            </div>
        @endif
    </div>--}}
    <div class="form-group">
        <label for="selling_price">Selling Price</label>
        <input type="text" class="form-control {{ $errors->has('selling_price') ? ' is-invalid' : '' }}"
            name="selling_price" id="selling_price" value="{{ old('selling_price', $model->selling_price) }}"
            placeholder="" required="required">
        @if ($errors->has('selling_price'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('selling_price') }}</strong>
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
