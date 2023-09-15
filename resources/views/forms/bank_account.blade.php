<form action="{{ isset($route) ? $route : route('bank_accounts.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="account_name">Account Name</label>
        <input type="text" class="form-control {{ $errors->has('account_name') ? ' is-invalid' : '' }}"
            name="account_name" id="account_name" value="{{ old('account_name', $model->account_name) }}"
            placeholder="" maxlength="100" required="required">
        @if ($errors->has('account_name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('account_name') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="account_no">Account No</label>
        <input type="text" class="form-control {{ $errors->has('account_no') ? ' is-invalid' : '' }}" name="account_no"
            id="account_no" value="{{ old('account_no', $model->account_no) }}" placeholder="" maxlength="191">
        @if ($errors->has('account_no'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('account_no') }}</strong>
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
                    <option value="{{ $data->id }}"
                        {{ $data->id == optional($model->branch)->bank_id ? 'selected' : '' }}>
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
        <label for="branch_id">Branch</label>
        <select class="form-control select2-single {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
            name="branch_id" id="branch_id" required="required">
            <option value="">Select...</option>
            @if (isset($branches))
                @foreach ($branches as $data)
                    <option value="{{ $data->id }}" {{ $data->id == $model->branch_id ? 'selected' : '' }}>
                        {{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('branch_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('branch_id') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="account_balance">Account Balance</label>
        <input type="text" class="form-control {{ $errors->has('account_balance') ? ' is-invalid' : '' }}"
            name="account_balance" id="account_balance" value="{{ old('account_balance', $model->account_balance) }}"
            placeholder="" required="required">
        @if ($errors->has('account_balance'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('account_balance') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="account_type">Account Type</label>
        <select class="form-control select2-single {{ $errors->has('account_type') ? ' is-invalid' : '' }}"
            name="account_type" id="account_type">
            <option value="">Select...</option>
            <option value="Current" {{ old('account_type', $model->account_type) == 'Current' ? 'selected' : '' }}>
                Current
            </option>
            <option value="Savings" {{ old('account_type', $model->account_type) == 'Savings' ? 'selected' : '' }}>
                Savings
            </option>
            <option value="Credit" {{ old('account_type', $model->account_type) == 'Credit' ? 'selected' : '' }}>
                Credit
            </option>
            <option value="Domiciliary"
                {{ old('account_type', $model->account_type) == 'Domiciliary' ? 'selected' : '' }}>
                Domiciliary</option>
            <option value="Cash" {{ old('account_type', $model->account_type) == 'Cash' ? 'selected' : '' }}>Cash
            </option>

        </select>
        @if ($errors->has('account_type'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('account_type') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-check">
        <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" value="1"
            name="status" id="status_yes" {{ $model->status == null || $model->status == 1 ? 'checked' : '' }}>
        Active
        &nbsp;&nbsp;
        &nbsp;&nbsp;
        <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" value="0"
            name="status" id="status_no" {{ $model->status != null && $model->status == 0 ? 'checked' : '' }}> Not
        Active
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
