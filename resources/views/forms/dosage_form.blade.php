<form action="{{ isset($route) ? $route : route('dosage_forms.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
            id="name" value="{{ old('name', $model->name) }}" placeholder="" maxlength="100" required="required">
        @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>


    <div class="form-group text-right ">
        <input type="reset" class="btn btn-default" value="Clear" />
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
