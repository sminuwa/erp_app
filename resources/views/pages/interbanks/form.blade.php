<form action="{{ isset($route) ? $route : route('interbank.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="row">
        @json($model)
        <div class="col-md-4">
            <div class="form-group">
                <label for="source_account_id">Source Account</label>
                <select class="form-control select2-single {{ $errors->has('source_account_id') ? ' is-invalid' : '' }}"
                    name="source_account_id" id="source_account_id" required="required">
                    <option value="">Select...</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ $account->id == old('source_account_id', $model->source_account_id) }}>
                            {{ $account->number }} - {{ $account->description }}</option>
                    @endforeach
                </select>
                @if ($errors->has('source_account_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('source_account_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="destination_account_id">Destination Account</label>
                <select class="form-control select2-single {{ $errors->has('destination_account_id') ? ' is-invalid' : '' }}"
                        name="destination_account_id" id="destination_account_id" required="required">
                    <option value="">Select...</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ $account->id == old('destination_account_id', $model->source_account_id) }}>
                            {{ $account->number }} - {{ $account->description }}</option>
                    @endforeach
                </select>
                @if ($errors->has('destination_account_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('destination_account_id') }}</strong>
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
                    class="form-control datepicker-entry {{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
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
                <label for="amount_paid">Amount</label>
                <input type="number" step=".01" class="form-control {{ $errors->has('amount_paid') ? ' is-invalid' : '' }}"
                    name="amount_paid" id="amount_paid" value="{{ old('amount_paid', $model->amount_paid) }}" required>
                @if ($errors->has('amount_paid'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('amount_paid') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">

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
