<form action="{{ route('intersite.cart') }}" method="POST">
    {{ csrf_field() }}
    <div class="form-group">
        <label for="source_store_id">Source Store</label>
        <select class="form-control select2-single {{ $errors->has('source_store_id') ? ' is-invalid' : '' }}"
            name="source_store_id" id="source_store_id" required="required">
            <option value="">Select...</option>
            @if (isset($stores))
                @foreach ($stores as $data)
                    <option value="{{ $data->id }}"
                        {{ $data->id == old('source_store_id', $model->source_store_id) ? 'selected' : '' }}>
                        {{ $data->code }}-{{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('source_store_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('source_store_id') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="product_id">Product</label>
        <select class="form-control select2-single {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
            name="product_id" id="product_id" required="required">
            <option value="">Select...</option>

            @if (old('category_id', $model->category_id))
                @foreach (\App\Models\Product::where('category_id', old('category_id'))->get() as $data)
                    <option value="{{ $data->id }}"
                        {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                        {{ $data->code }}-{{ $data->name }}</option>
                @endforeach
            @else
                @if (isset($products))
                    @foreach ($products as $data)
                        <option value="{{ $data->id }}"
                            {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
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

    {{-- <div class="form-group">
        <label for="destination_store_id">Destination Store</label>
        <select class="form-control select2-single {{ $errors->has('destination_store_id') ? ' is-invalid' : '' }}"
            name="destination_store_id" id="destination_store_id" required="required">
            <option value="">Select...</option>

        </select>
        @if ($errors->has('destination_store_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('destination_store_id') }}</strong>
            </div>
        @endif
    </div> --}}
    <div class="form-group">
        <label for="quantity_requested">Qty</label>
        <input type="number" class="form-control {{ $errors->has('quantity_requested') ? ' is-invalid' : '' }}"
            name="quantity_requested" id="quantity_requested" value="{{ $model->quantity_requested }}" placeholder=""
            required="required">
        @if ($errors->has('quantity_requested'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('quantity_requested') }}</strong>
            </div>
        @endif
    </div>
    {{-- <div class="form-group">
        <label for="cost_price">Product Code Price</label>
        <input type="text"
            class="form-control {{ $errors->has('cost_price') ? ' is-invalid' : '' }}"
            name="cost_price" id="cost_price" placeholder="Product code price" required="required">
        @if ($errors->has('cost_price'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('cost_price') }}</strong>
            </div>
        @endif
    </div> --}}
    <input type="hidden" value="0" name="cost_price" id="cost_price"/>
    <div class="form-group text-right ">
        <button type="submit" class="btn btn-primary"><span class="ion-ios-cart-outline"></span> Add to Cart</button>
    </div>
</form>
