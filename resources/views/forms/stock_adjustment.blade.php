<form action="{{ isset($route) ? $route : route('stock_adjustments.cart') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="store_id">Store</label>
        <select class="form-control select2-single {{ $errors->has('store_id') ? ' is-invalid' : '' }}" name="store_id"
            id="store_id" required="required">
            <option value="">Select...</option>
            @if (isset($stores))
                @foreach ($stores as $data)
                    <option value="{{ $data->id }}" {{ old('store_id') == $data->id ? 'selected' : '' }}>
                        {{ $data->code }}-{{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('store_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('store_id') }}</strong>
            </div>
        @endif
    </div>
    {{-- <div class="form-group">
        <label for="category_id">Group </label>
        <select class="form-control select2-single {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
            name="category_id" id="category_id" required="required">
            <option value="">Select...</option>
            @if (isset($stores))
                @foreach ($categories as $data)
                    <option value="{{ $data->id }}" {{ $data->id == old('category_id') ? 'selected' : '' }}>
                        {{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('category_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('category_id') }}</strong>
            </div>
        @endif
    </div> --}}

    <div class="form-group">
        <label for="product_id">Product</label>
        <select class="form-control select2-single {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
            name="product_id" id="product_id" required="required">
            <option value="">Select...</option>

            @if (old('category_id', optional(optional($model->product)->category)->id))
                @foreach (\App\Models\Product::where('category_id', old('category_id'))->get() as $data)
                    <option value="{{ $data->id }}">
                        {{ $data->code }}-{{ $data->name }}</option>
                @endforeach
            @else
                @if (isset($products))
                    @foreach ($products as $data)
                        <option value="{{ $data->id }}">
                            {{ $data->code }}-{{ $data->name }}</option>
                    @endforeach
                @endif
            @endif

        </select>
        <div class="input-group-prepend">
            <p class="text text-danger" id="available"></p>
        </div>
        @if ($errors->has('product_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('product_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="available_qty">Available Qty</label>
        <input type="number" class="form-control {{ $errors->has('available_qty') ? ' is-invalid' : '' }}" readonly
            name="available_qty" id="available_qty" value="" placeholder="">
        @if ($errors->has('available_qty'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('available_qty') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <span>Type Number: </span>
        <label for="adjusted_qty">Adjusted Qty</label>
        <input type="number" class="form-control {{ $errors->has('adjusted_qty') ? ' is-invalid' : '' }}"
            name="adjusted_qty" id="adjusted_qty" value="" placeholder="" required="required" min="1">
        @if ($errors->has('adjusted_qty'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('adjusted_qty') }}</strong>
            </div>
        @endif
    </div>
    <input type="radio"  name="operation" value="1" {{ $model->qty > 0  ? 'checked' : '' }}> Add (+)<br>
    <input type="radio" name="operation" value="-1" {{ !$model->qty < 0 ? 'checked' : '' }}> Subtract(-)<br>
    <div class="form-group text-right ">
        <button type="submit" class="btn btn-primary"><i class="fa fa-cart-plus"> Add to Cart</i></button>
    </div>
</form>
