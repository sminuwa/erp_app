<form action="{{ isset($route) ? $route : route('customers.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="name"
            value="{{ old('name', $model->name) }}" placeholder="" maxlength="50" required="required">
        @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="account_type">Account Type</label>
        <select class="form-control {{ $errors->has('account_type') ? ' is-invalid' : '' }}" name="account_type" id="account_type" required>
            <option value="">Select...</option>
            <option value="R" {{old('account_type', $model->account_type)=="Retail"?'selected':''}}>Retail</option>
            <option value="W" {{old('account_type', $model->account_type)=="Wholesale"?'selected':''}}>Whole Sale</option>
        </select>
        @if ($errors->has('account_type'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('account_type') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="code">Code</label>
        <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" readonly
            id="code" value="{{ isset($code)?$code:old('code', $model->code) }}" placeholder="" maxlength="20" minlength="6">
        @if ($errors->has(' code'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('code') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
            id="email" value="{{ old('email', $model->email) }}" placeholder="" maxlength="191">
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
        <label for="credit_limit">Credit Limit</label>
        <input type="text" class="form-control {{ $errors->has('credit_limit') ? ' is-invalid' : '' }}"
            name="credit_limit" id="credit_limit" value="{{ old('credit_limit', $model->credit_limit) }}"
            placeholder="" required="required">
        @if ($errors->has('credit_limit'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('credit_limit') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group text-right ">

        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
