<form action="{{ isset($route) ? $route : route('suppliers.payment.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="supplier_id">Supplier</label>
                <select class="form-control select2-single {{ $errors->has('supplier_id') ? ' is-invalid' : '' }}"
                    name="supplier_id" id="supplier_id" required="required">
                    <option value="">Select...</option>
                    @if (isset($suppliers))
                        @foreach ($suppliers as $data)
                            <option value="{{ $data->id }}"
                                {{ $data->id == $model->supplier_id ? 'selected' : '' }}>
                                {{ $data->name }}</option>
                        @endforeach
                    @endif
                </select>
                @if ($errors->has('supplier_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('supplier_id') }}</strong>
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
                    value="{{ old('payment_date', $model->date) == '' ? date('Y-m-d') : old('payment_date', $model->date) }}"
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
                <label for="amount_paid">Amount Paid</label>
                <input type="text" class="form-control {{ $errors->has('amount_paid') ? ' is-invalid' : '' }}"
                    name="amount_paid" id="amount_paid" value="{{ old('amount_paid', $model->amount_paid) }}">
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
                <label for="teller_no">Teller No</label>
                <input type="text" class="form-control" name="teller_no" id="teller_no"
                    value="{{ old('teller_no', $model->teller_no) }}">
                @if ($errors->has('teller_no'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('teller_no') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="balance">Total Balance</label>
                <input type="text" class="form-control col-4" name="balance" id="balance"
                    placeholder="Total Balance" value="">
            </div>
        </div>
        <input type="hidden" name="payment_ref" id="payment_ref" />
    </div>
    <div class="row">
        {{-- <div class="col-md-4">
            <div class="form-group">
                <label for="purchase_id">Payment Ref/Invoice</label>
                <select class="form-control select2-single {{ $errors->has('purchase_id') ? ' is-invalid' : '' }}"
                    name="purchase_id" id="purchase_id" required="required">
                    <option value="">Select...</option>
                    
                </select>
                @if ($errors->has('purchase_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('purchase_id') }}</strong>
                    </div>
                @endif
            </div>
        </div> --}}
        <div class="form-group text-right ">
            <input type="submit" class="btn btn-primary" value="Save" />

        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 text-danger">
            <strong>Total Record is of 1:
                {{ number_format(App\Models\SupplierLedger::where('dr','>',0)->count('*'), 0, ',', '') }}</strong>
        </div>
    </div>
</form>
