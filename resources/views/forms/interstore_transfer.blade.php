<form action="{{ route('interstore.cart') }}" method="POST">
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

    {{-- <div class="form-group">
        <label for="category_id">Category</label>
        <select type="number" class="form-control {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
            name="category_id" id="category_id" required="required">
            <option value="">Select...</option>
            @if (isset($categories))
                @foreach ($categories as $data)
                    <option value="{{ $data->id }}"
                        {{ $data->id == old('category_id', optional($model->product)->category_id) ? 'selected' : '' }}>
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
    <div class="form-group">
        <label for="destination_store_id">Destination Store</label>
        <select class="form-control select2-single {{ $errors->has('destination_store_id') ? ' is-invalid' : '' }}"
            name="destination_store_id" id="destination_store_id" required="required">
            <option value="">Select...</option>
            @if (isset($stores))
                @foreach ($stores as $data)
                    <option value="{{ $data->id }}"
                        {{ $data->id == old('destination_store_id', $model->destination_store_id) ? 'selected' : '' }}>
                        {{ $data->code }}-{{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('destination_store_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('destination_store_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="qty_transfered">Qty</label>
        <input type="number" class="form-control {{ $errors->has('qty_transfered') ? ' is-invalid' : '' }}"
            name="qty_transfered" id="qty_transfered" value="{{ $model->qty_transfered }}" placeholder=""
            required="required">
        @if ($errors->has('qty_transfered'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('qty_transfered') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group text-right ">
        <button type="submit" class="btn btn-primary"><span class="ion-ios-cart-outline"></span> Add to Cart</button>
    </div>
</form>
