<form action="{{ isset($route) ? $route : route('employees.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="name"
            value="{{ old('name', $model->name) }}" placeholder="" maxlength="191" required="required">
        @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
            id="email" value="{{ old('email', $model->email) }}" placeholder="" maxlength="191" required="required">
        @if ($errors->has('email'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('email') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone"
            id="phone" value="{{ old('phone', $model->phone) }}" placeholder="" maxlength="191" required="required">
        @if ($errors->has('phone'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('phone') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="address">Address</label>
        <input type="text" class="form-control {{ $errors->has('address') ? ' is-invalid' : '' }}" name="address"
            id="address" value="{{ old('address', $model->address) }}" placeholder="" maxlength="191"
            required="required">
        @if ($errors->has('address'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('address') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="experience">Experience</label>
        <input type="text" class="form-control {{ $errors->has('experience') ? ' is-invalid' : '' }}"
            name="experience" id="experience" value="{{ old('experience', $model->experience) }}" placeholder=""
            maxlength="191" required="required">
        @if ($errors->has('experience'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('experience') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="salary">Salary</label>
        <input type="text" class="form-control {{ $errors->has('salary') ? ' is-invalid' : '' }}" name="salary"
            id="salary" value="{{ old('salary', $model->salary) }}" placeholder="" maxlength="191"
            required="required">
        @if ($errors->has('salary'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('salary') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="city">City</label>
        <input type="text" class="form-control {{ $errors->has('city') ? ' is-invalid' : '' }}" name="city" id="city"
            value="{{ old('city', $model->city) }}" placeholder="" maxlength="191" required="required">
        @if ($errors->has('city'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('city') }}</strong>
            </div>
        @endif
    </div>


    <div class="form-group text-right ">

        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
