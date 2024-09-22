<form action="{{ isset($route) ? $route : route('suppliers.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="code">Code</label>
        <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code"
            id="code" value="{{ old('code', $model->code) }}" placeholder="" maxlength="191" required="required">
        @if ($errors->has('code'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('code') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
            id="name" value="{{ old('name', $model->name) }}" placeholder="" maxlength="191" required="required">
        @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
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
    @if (!isset($model->code))
        <div class="form-group">
            <label for="code">Type</label>
            <input type="radio" class="type" name="type" class="type" value="TS" required="required"
                {{ old('type', substr($model->code, 0, 2)) == 'TS' ? 'checked' : '' }} /> Transpoter
            <input type="radio" class="type" name="type" class="type"
                {{ old('type', substr($model->code, 0, 2)) == 'MS' ? 'checked' : '' }} value="MS"
                required="required" /> Supplier
            @if ($errors->has('code'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('code') }}</strong>
                </div>
            @endif
        </div>
    @endif
    @if (!isset($model->code))
        <div class="form-group">
            <label for="code">Supplier Code</label>
            <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code"
                id="code" value="" placeholder="" maxlength="4" required="required" readonly>
            @if ($errors->has('code'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('code') }}</strong>
                </div>
            @endif
        </div>
    @endif

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

    {{-- <div class="form-group">
        <label for="account_holder">Account Name</label>
        <input type="text" class="form-control {{ $errors->has('account_holder') ? ' is-invalid' : '' }}"
            name="account_holder" id="account_holder" value="{{ old('account_holder', $model->account_holder) }}"
            placeholder="" maxlength="191">
        @if ($errors->has('account_holder'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('account_holder') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="account_number">Account Number</label>
        <input type="text" class="form-control {{ $errors->has('account_number') ? ' is-invalid' : '' }}"
            name="account_number" id="account_number" value="{{ old('account_number', $model->account_number) }}"
            placeholder="" maxlength="191">
        @if ($errors->has('account_number'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('account_number') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="account_type">Account Type</label>
        <select class="form-control {{ $errors->has('account_type') ? ' is-invalid' : '' }}" name="account_type"
            id="account_type">
            <option value="Current" {{ old('account_type', $model->account_type) == 'Current' ? 'selected' : '' }}>
                Current
            </option>
            <option value="Savings" {{ old('account_type', $model->account_type) == 'Savings' ? 'selected' : '' }}>
                Savings
            </option>

        </select>
        @if ($errors->has('account_type'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('account_type') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="bank_id">Bank</label>
        <select class="form-control select2-single {{ $errors->has('bank_id') ? ' is-invalid' : '' }}" name="bank_id"
            id="bank_id" value="{{ old('bank_id', $model->bank_id) }}" placeholder="" required="required">
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
    </div> --}}
    <div class="form-group">
        <label for="code">Status</label>
        <select class="form-control {{ $errors->has('status') ? ' is-invalid' : '' }}"
                name="status" id="status"
                required>
            <option value="1" @if($model->status == 1) selected @endif>Active</option>
            <option value="0" @if($model->status == 0) selected @endif>Inactive</option>
        </select>
        @if ($errors->has('status'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('status') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
