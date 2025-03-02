<form action="{{ isset($route) ? $route : route('product_unit_measures.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="product_id">Product</label>
        <select class="form-control select2-single {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
            name="product_id" id="product_id">
            <option value="">Select...</option>
            @if (isset($products))
                @foreach ($products as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->product_id ? 'selected' : '' }}>
                        {{ $data->code }}-{{ $data->name }}</option>
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
        <label for="code">Unit Code</label>
        <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code"
            id="code" value="{{ old('code', $model->code) }}" placeholder="" maxlength="50" required="required">
        @if ($errors->has('code'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('code') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="type">Type</label>
        <select class="form-control {{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" id="type">
            <option value="">Select...</option>
            <option value="division" {{ old('type', $model->type) == 'division' ? 'selected' : '' }}>Division</option>
            <option value="multiple" {{ old('type', $model->type) == 'multiple' ? 'selected' : '' }}>Multiple</option>

        </select>
        @if ($errors->has('type'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('type') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
      <label for="value">Value</label>
      <input type="number" class="form-control {{ $errors->has('value') ? ' is-invalid' : '' }}" name="value" step=".01"
          id="value" value="{{ old('value', $model->value) }}" placeholder="" min="1" required="required">
      @if ($errors->has('value'))
          <div class="invalid-feedback">
              <strong>{{ $errors->first('value') }}</strong>
          </div>
      @endif
  </div>
    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
