<form action="{{ isset($route) ? $route : route('debtors.payment.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="customer_id">Customer</label>
                <select class="form-control select2-single {{ $errors->has('customer_id') ? ' is-invalid' : '' }}"
                    name="customer_id" id="customer_id" required="required">
                    <option value="">Select...</option>
                    @if (isset($customers))
                        @foreach ($customers as $data)
                            <option value="{{ $data->id }}"
                                {{ $data->id == $model->customer_id ? 'selected' : '' }}>
                                {{ $data->name }}</option>
                        @endforeach
                    @endif
                </select>
                @if ($errors->has('customer_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('customer_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>
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
                <label for="receipt_no">Receipt No</label>
                <input type="text" class="form-control {{ $errors->has('receipt_no') ? ' is-invalid' : '' }}"
                    readonly='readonly' name="receipt_no" id="receipt_no"
                    value="{{ old('receipt_no', isset($receipt_no) ? $receipt_no : $model->receipt_no) }}">
                @if ($errors->has('receipt_no'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('receipt_no') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="amount_paid">Amount Paid</label>
                <input type="number" class="form-control {{ $errors->has('amount_paid') ? ' is-invalid' : '' }}"
                    name="amount_paid" id="amount_paid" value="{{ old('amount_paid', $model->amount_paid) }}" required>
                @if ($errors->has('amount_paid'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('amount_paid') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="form-group">
                <label for="invoice">Invoice</label>
                <select class="form-control select2-single {{ $errors->has('invoice') ? ' is-invalid' : '' }}"
                    name="invoice" id="invoice">
                    <option value="">Select...</option>
                </select>
                @if ($errors->has('invoice'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('invoice') }}</strong>
                    </div>
                @endif
            </div>
        </div> --}}
        <div class="col-md-4">
            <div class="form-group">
                <label for="payment_mode">Payment Mode</label>
                <select class="form-control {{ $errors->has('payment_mode') ? ' is-invalid' : '' }}"
                    name="payment_mode" id="payment_mode" required="required">
                    <option value="">Select...</option>
                    <option value="Cash" {{ 'Cash' == $model->payment_mode ? 'selected' : '' }}>Cash</option>
                    <option value="Cheque" {{ 'Cheque' == $model->payment_mode ? 'selected' : '' }}>Cheque</option>
                </select>
                @if ($errors->has('payment_mode'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('payment_mode') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4" id="account_number">
            <div class="form-group">
                <label for="bank_account_id">Account Number</label>
                <select class="form-control select2-single {{ $errors->has('bank_account_id') ? ' is-invalid' : '' }}"
                    name="bank_account_id" id="bank_account_id" required="required">
                    <option value="">Select...</option>
                </select>
                @if ($errors->has('bank_account_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('bank_account_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4" id="account_number">
            <div class="form-group">
                <label for="account_name">Account Name</label>
                <input type="text" class="form-control" disabled name="account_name" id="account_name"
                    value="{{ old('account_name', $model->account_name) }}">
                @if ($errors->has('account_name'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('account_name') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="payment_ref">Payment Ref</label>
                <textarea type="text" class="form-control" name="payment_ref" id="payment_ref"></textarea>
                @if ($errors->has('payment_ref'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('payment_ref') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        {{-- <div class="col-md-4">
            <div class="form-group">
                <label for="teller_no">Teller No</label>
                <input type="text" class="form-control" name="teller_no" id="teller_no"
                    value="{{ old('teller_no', $model->teller_no) }}">
                @if ($errors->has('payment_ref'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('payment_ref') }}</strong>
                    </div>
                @endif
            </div>
        </div> --}}
        <div class="col-md-4">
            <div class="form-group">
                <label for="balance">Total Balance</label>
                <input type="text" class="form-control col-4" name="balance" id="balance"
                    placeholder="Total Balance" value="">
                <span class="text text-danger fa fa-mobile"> Send SMS: </span> <input type="checkbox" name="sms"
                    id="debt_sms" />
            </div>
        </div>
    </div>



    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
    <div class="row">
        <div class="col-sm-6 text-danger">
            <strong>Total Record is of 1:
                {{ number_format(App\Models\CustomerLedger::where('cr','>',0)->count('*'), 0, ',', '') }}</strong>
        </div>
    </div>
</form>
