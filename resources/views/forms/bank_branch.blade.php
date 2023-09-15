<form action="{{ isset($route) ? $route : route('bank_branches.store') }}" method="POST">
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
        <label for="bank_id">Bank</label>
        <select class="form-control {{ $errors->has('bank_id') ? ' is-invalid' : '' }}" name="bank_id" id="bank_id"
            required="required">
            <option value="">Select...</option>
            @if (isset($banks))
                @foreach ($banks as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->bank_id ? 'selected' : '' }}>
                        {{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('bank_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('bank_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="sortcode">Sort code</label>
        <input type="text" class="form-control {{ $errors->has('sortcode') ? ' is-invalid' : '' }}" name="sortcode"
            id="sortcode" value="{{ old('sortcode', $model->sortcode) }}" placeholder="" maxlength="191">
        @if ($errors->has('sortcode'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('sortcode') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
