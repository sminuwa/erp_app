<form action="{{ isset($route) ? $route : route('products.purchase_prices') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />

    <div class="form-group">
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
    </div>
    <div class="form-group">
        <label for="product_id">Product</label>
        <select class="form-control ajax-products select2-single"
                name="product_id"
                id="product_id"
                category_id=""
                required>
        </select>
        @if ($errors->has('product_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('product_id') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="purchase_price">Unit Price</label>
        <input type="number" class="form-control" name="purchase_price" id="purchase_price" placeholder="0.0" required>
        @if ($errors->has('purchase_price'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('purchase_price') }}</strong>
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
