<form action="{{ isset($route) ? $route : route('users.store') }}" method="POST">
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
        <label for="user_code">User Code</label>
        <input type="text" class="form-control {{ $errors->has('user_code') ? ' is-invalid' : '' }}" name="user_code"
            id="user_code" value="{{ old('user_code', $model->user_code) }}" placeholder="" maxlength="15"
            required="required">
        @if ($errors->has('user_code'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('user_code') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
            id="email" value="{{ old('email', $model->email) }}" placeholder="" maxlength="100" required="required">
        @if ($errors->has('email'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('email') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone"
            id="phone" value="{{ old('phone', $model->phone) }}" placeholder="" maxlength="15" required="required">
        @if ($errors->has('phone'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('phone') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="gender">Gender</label>
        <select class="form-control {{ $errors->has('gender') ? ' is-invalid' : '' }}" name="gender" id="gender">
            <option value="Male" {{ old('gender', $model->gender) == 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ old('gender', $model->gender) == 'Female' ? 'selected' : '' }}>Female</option>

        </select>
        @if ($errors->has('gender'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('gender') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="branch_id">Branch</label>
        <select class="form-control {{ $errors->has('branch_id') ? ' is-invalid' : '' }}" name="branch_id"
            id="branch_id">
            <option value="">Select</option>
            @if (isset($branches))
                @foreach ($branches as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->branch_id ? 'selected' : '' }}>
                        {{ $data->name }}</option>
                @endforeach
            @endif

        </select>
        @if ($errors->has('branch_id'))
            <div class="pristine-error text-danger mt-2">
                <strong>{{ $errors->first('branch_id') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="status">Account Status</label>
        <select type="text" class="form-control {{ $errors->has('status') ? ' is-invalid' : '' }}" name="status"
            required="required">
            <option value="1" {{ old('status', $model->status) == 1 || $model->status == null ? 'selected' : '' }}>
                Active</option>
            <option value="0" {{ old('status', $model->status) == 0 && $model->status != null ? 'selected' : '' }}>
                Block
            </option>
        </select>
        @if ($errors->has('status'))
            <div class="pristine-error text-danger mt-2">
                <strong>{{ $errors->first('status') }}</strong>
            </div>
        @endif
    </div>

    <p class="text text-danger"><span class="ion-alert-circled"></span>The account will be created with default Password
        of 123456. Please advice the user to change afterward.</p>
    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary"><span class="ion-ios-locked"> </span> Create</button>
    </div>
</form>
