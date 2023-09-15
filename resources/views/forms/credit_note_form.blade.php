<form action="{{ isset($route) ? $route : route('suppliers.credit.note.store') }}" method="POST" target="_BLANK">
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
                <label for="payment_date">Date</label>
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
                <label for="amount_paid">Amount</label>
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
                <label for="teller_no">Receipt No</label>
                <input type="text" class="form-control" name="teller_no" id="teller_no"
                    value="{{  old('teller_no', $model->teller_no) }}">
                @if ($errors->has('teller_no'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('teller_no') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="cheque">Cheque No</label>
                <input type="text" class="form-control" name="cheque" id="cheque" readonly
                    value="{{ isset($receipt_no) ? $receipt_no :old('cheque', $model->Ref) }}">
                @if ($errors->has('cheque'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('cheque') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="group_id">Group Name</label>
                <select class="form-control select2-single {{ $errors->has('group_id') ? ' is-invalid' : '' }}"
                    name="group_id" id="group_id" required="required">
                    <option value="">Select...</option>
                    @if (isset($categories))
                        @foreach ($categories as $data)
                            <option value="{{ $data->id }}"
                                {{ $data->id == $model->bank_account_id ? 'selected' : '' }}>
                                {{ $data->name }}</option>
                        @endforeach
                    @endif
                </select>
                @if ($errors->has('group_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('group_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
