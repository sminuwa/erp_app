<form action="{{ isset($route) ? $route : route('loans.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="loan_collector_id">Loan Collector</label>
                <select class="form-control select2-single {{ $errors->has('loan_collector_id') ? ' is-invalid' : '' }}"
                  name="loan_collector_id" id="loan_collector_id" required="required">
                  <option value="">Select...</option>
                  @if (isset($collectors))
                      @foreach ($collectors as $data)
                          <option value="{{ $data->id }}"
                              {{ $data->id == $model->loan_collector_id ? 'selected' : '' }}>
                              {{ $data->name }}</option>
                      @endforeach
                  @endif
              </select>
                @if ($errors->has('loan_collector_id'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('loan_collector_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="text" class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}"
                    name="amount" id="amount" value="{{ old('amount', $model->amount) }}" placeholder=""
                    required="required">
                @if ($errors->has('amount'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('amount') }}</strong>
                    </div>
                @endif
            </div>
        </div>
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
                <select
                    class="form-control select2-single {{ $errors->has('bank_account_id') ? ' is-invalid' : '' }}"
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
                <label for="date">Date</label>
                <div class="input-group">
                    <input type="text" class="form-control datepicker {{ $errors->has('date') ? ' is-invalid' : '' }}"
                        name="date" id="date" value="{{ old('date', $model->date) == '' ? date('Y-m-d') : old('date', $model->date) }}" placeholder="" autocomplete="off"
                        required="required">
                    <div class="input-group-addon">
                        <label for="date" class="fa fa-calendar">
                        </label>
                    </div>
                </div>
                @if ($errors->has('date'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('date') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="receipt_no">Receipt No</label>
                <input type="text" class="form-control {{ $errors->has('receipt_no') ? ' is-invalid' : '' }}"
                    readonly name="receipt_no" id="receipt_no"
                    value="{{ $model->receipt_no != null ? $model->receipt_no : $receipt_no }}" placeholder=""
                    maxlength="20" required="required">
                @if ($errors->has('receipt_no'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('receipt_no') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <div class="input-group">
                    <input type="text"
                        class="form-control datepicker {{ $errors->has('due_date') ? ' is-invalid' : '' }}" autocomplete="off"
                        name="due_date" id="due_date" value="{{ old('due_date', $model->due_date) }}" placeholder=""
                        required="required">
                    <div class="input-group-addon">
                        <label for="due_date" class="fa fa-calendar">
                        </label>
                    </div>
                </div>
                @if ($errors->has('due_date'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('due_date') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <input type="hidden" class="form-control {{ $errors->has('granted_by') ? ' is-invalid' : '' }}"
        name="granted_by" id="granted_by" value="{{ Auth::id() }}" placeholder="" required="required">
    <div class="form-group">
        <div class="col-sm-6 text-danger">
            <strong>Total Records: {{ number_format(App\Models\Loan::count('*'), 0, ',', '') }}</strong>
        </div>
    </div>
    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />
    </div>
</form>
