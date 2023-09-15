<form action="{{ isset($route) ? $route : route('product_expire_settings.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="product_id">Product</label>
        <select class="form-control select2-single {{ $errors->has('product_id') ? ' is-invalid' : '' }}" name="product_id"
            id="product_id">
            <option>Select...</option>
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

    <div class="form-group">
        <label for="no_of_days">No Of Days</label>
        <input type="number" class="form-control {{ $errors->has('no_of_days') ? ' is-invalid' : '' }}"
            name="no_of_days" id="no_of_days" value="{{ old('no_of_days', $model->no_of_days) }}" placeholder=""
            required="required">
        @if ($errors->has('no_of_days'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('no_of_days') }}</strong>
            </div>
        @endif
    </div>
    <input type="hidden" value="{{ Auth::id() }}" name="user_id" id="user_id" />
    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />
    </div>
</form>
