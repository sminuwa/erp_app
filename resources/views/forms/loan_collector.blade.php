<form action="{{ isset($route) ? $route : route('loan_collectors.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
            id="name" value="{{ old('name', $model->name) }}" placeholder="" maxlength="50" required="required">
        @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="address">Address</label>
        <input type="text" class="form-control {{ $errors->has('address') ? ' is-invalid' : '' }}" name="address"
            id="address" value="{{ old('address', $model->address) }}" placeholder="" maxlength="50">
        @if ($errors->has('address'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('address') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
            id="email" value="{{ old('email', $model->email) }}" placeholder="" maxlength="50">
        @if ($errors->has('email'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('email') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone"
            id="phone" value="{{ old('phone', $model->phone) }}" placeholder="" maxlength="15"
            required="required">
        @if ($errors->has('phone'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('phone') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="reg_code">Reg Code</label>
        <input type="text" class="form-control {{ $errors->has('reg_code') ? ' is-invalid' : '' }}" readonly
            name="reg_code" id="reg_code" value="{{ $model->reg_code != null ? $model->reg_code : $reg_code }}"
            placeholder="" maxlength="10" required="required">
        @if ($errors->has('reg_code'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('reg_code') }}</strong>
            </div>
        @endif
    </div>
    <input type="hidden" class="form-control {{ $errors->has('registered_by') ? ' is-invalid' : '' }}"
        name="registered_by" id="registered_by" value="{{ Auth::id() }}" placeholder="" required="required">
    <div class="form-group">
        <div class="col-sm-6 text-danger">
            <strong>Total Records: {{ number_format(App\Models\LoanCollector::count('*'), 0, ',', '') }}</strong>
        </div>
    </div>
    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />
    </div>
</form>
