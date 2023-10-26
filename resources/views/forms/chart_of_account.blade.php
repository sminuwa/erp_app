<form action="{{ isset($route) ? $route : route('chart_of_accounts.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="prefix">Prefix</label>
        <input @if(isset($model->id)) readonly @endif type="text" class="form-control {{ $errors->has('prefix') ? ' is-invalid' : '' }}" name="prefix"
            id="prefix" value="{{ old('prefix', $model->prefix) }}" placeholder="" maxlength="50"
            required="required">
        @if ($errors->has('prefix'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('prefix') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="class">Class</label>
        <input @if(isset($model->id)) readonly @endif type="text" class="form-control {{ $errors->has('class') ? ' is-invalid' : '' }}" name="class"
            id="class" value="{{ old('class', $model->class) }}" placeholder="" maxlength="50" required="required">
        @if ($errors->has('class'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('class') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <input type="text" class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}"
            name="description" id="description" value="{{ old('description', $model->description) }}" placeholder=""
            maxlength="100" required="required">
        @if ($errors->has('description'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('description') }}</strong>
            </div>
        @endif
    </div>


    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
