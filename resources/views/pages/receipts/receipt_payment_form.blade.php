<form action="{{ isset($route) ? $route : route('receipt.payment.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="type">Payer Category</label>
                <select class="form-control select2-single {{ $errors->has('type') ? ' is-invalid' : '' }}"
                    name="type" id="type" required="required">
                    <option value="">Select...</option>
                    <option value="Customer" {{ 'Customer' == $model->model_name ? 'selected' : '' }}>Customer</option>
                    <option value="Supplier" {{ 'Suppplier' == $model->model_name ? 'selected' : '' }}>Suppplier
                    </option>
                </select>
                @if ($errors->has('type'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('type') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="payer_id">Customer/Supplier</label>
                <select class="form-control select2-single {{ $errors->has('payer_id') ? ' is-invalid' : '' }}"
                    name="payer_id" id="payer_id" required="required">

                </select>
                @if ($errors->has('payer_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('payer_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="payment_date">Payment Date</label>
                <input type="text"
                    class="form-control datepicker {{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
                    name="payment_date" id="payment_date"
                    value="{{ old('payment_date', $model->payment_date) == '' ? date('Y-m-d') : old('payment_date', $model->payment_mode) }}"
                    required="required">
                @if ($errors->has('payment_date'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('payment_date') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="account_id">Account</label>
                <select class="form-control select2-single {{ $errors->has('account_id') ? ' is-invalid' : '' }}"
                    name="account_id" id="account_id" required="required">
                    <option value="">Select...</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ $account->id = old('account_id', $model->branch_id) }}>
                            {{ $account->number }} - {{ $account->description }}</option>
                    @endforeach
                </select>
                @if ($errors->has('payer_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('payer_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="amount_paid">Amount</label>
                <input type="number" class="form-control {{ $errors->has('amount_paid') ? ' is-invalid' : '' }}"
                    name="amount_paid" id="amount_paid" value="{{ old('amount_paid', $model->amount_paid) }}" required>
                @if ($errors->has('amount_paid'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('amount_paid') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="payment_ref">Description</label>
                <textarea type="text" class="form-control" name="payment_ref" id="payment_ref"></textarea>
                @if ($errors->has('payment_ref'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('payment_ref') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="">
        <div class="col-md-8">
            <div class="form-group text-right ">
                <input type="submit" class="btn btn-primary" value="Save" />
            </div>
        </div>
    </div>
</form>
