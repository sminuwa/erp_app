<form action="{{ isset($route) ? $route : route('expense_items.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="name"
            value="{{ old('name', $model->name) }}" placeholder="" maxlength="100" required="required">
        @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="code">Code</label>
        <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" id="code"
            value="{{ old('code', isset($code)?$code:$model->code) }}" placeholder="" maxlength="10" required="required">
        @if ($errors->has('code'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('code') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <input type="hidden" class="form-control {{ $errors->has('created_by') ? ' is-invalid' : '' }}"
            name="created_by" id="created_by" value="{{ Auth::id() }}" placeholder="" required="required">
    </div>


    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
