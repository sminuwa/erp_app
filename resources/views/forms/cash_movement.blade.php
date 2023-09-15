<form action="{{ isset($route) ? $route : route('cash_movements.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <i class="ion-android-send"></i> Withdrawal Panel
                </div>
                <div class="card-body table-responsive">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="from_account">Account Number</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('from_account') ? ' is-invalid' : '' }}"
                                    name="from_account" id="from_account" required="required">
                                    <option value="">Select...</option>
                                    @foreach ($bank_accounts as $bank_account)
                                        <option value="{{ $bank_account->id }}"
                                            {{ old('from_account', $model->source_account_id) == $bank_account->id ? 'selected' : '' }}>
                                            {{ $bank_account->account_no }}-{{ $bank_account->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('from_account'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('from_account') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_withdraw">Date Withdraw</label>
                                <input type="text"
                                    class="form-control datepicker {{ $errors->has('date_withdraw') ? ' is-invalid' : '' }}"
                                    name="date_withdraw" id="date_withdraw"
                                    value="{{ old('date_withdraw', $model->date_withdraw) == '' ? date('Y-m-d') : old('date_withdraw', $model->date_withdraw) }}"
                                    required="required">
                                @if ($errors->has('date_withdraw'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('date_withdraw') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_name">Account Name</label>
                                <input type="text" class="form-control" readonly name="account_name"
                                    id="account_name"
                                    value="{{ old('account_name', optional($model->fromAccount)->account_name) }}">
                                @if ($errors->has('account_name'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('account_name') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6"">
                            <div class="form-group">
                                <label for="from_balance">Balance</label>
                                <input type="text" class="form-control" readonly name="from_balance"
                                    id="from_balance"
                                    value="{{ old('from_balance', number_format(optional($model->fromAccount)->account_balance, 2, '.', ',')) }}">
                                @if ($errors->has('from_balance'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('from_balance') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="amount">Amount Withdraw</label>
                                <input type="number"
                                    class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}"
                                    name="amount" id="amount" value="{{ old('amount', $model->amount) }}"
                                    placeholder="" required="required">
                                @if ($errors->has('amount'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('amount') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="withdraw_by">Withdrawn By</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('withdraw_by') ? ' is-invalid' : '' }}"
                                    name="withdraw_by" id="withdraw_by" required="required">
                                    <option value="">Select...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('withdraw_by', $model->withdraw_by) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}-{{ $user->user_code }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('withdraw_by'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('withdraw_by') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div> --}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="slip_no">Withdrawal Slip No</label>
                                <input type="text" autocomplete="off"
                                    class="form-control {{ $errors->has('slip_no') ? ' is-invalid' : '' }}"
                                    name="slip_no" id="slip_no" value="{{ old('slip_no', $model->slip_no) }}"
                                    placeholder="" required="required">
                                @if ($errors->has('slip_no'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('slip_no') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 text-danger">
                            <strong>Total Record is of
                                {{ number_format(App\Models\CashMovement::count('*'), 0, ',', '') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <i class="ion-load-d"></i> Deposit Panel
                </div>
                <div class="card-body table-responsive">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_account">Account Number</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('to_account') ? ' is-invalid' : '' }}"
                                    name="to_account" id="to_account" required="required">
                                    <option value="">Select...</option>
                                    @foreach ($bank_accounts as $bank_account)
                                        <option value="{{ $bank_account->id }}"
                                            {{ old('to_account', $model->destination_account_id) == $bank_account->id ? 'selected' : '' }}>
                                            {{ $bank_account->account_no }}-{{ $bank_account->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('to_account'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('to_account') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_deposit">Date Deposit</label>
                                <input type="text"
                                    class="form-control datepicker {{ $errors->has('date_deposit') ? ' is-invalid' : '' }}"
                                    name="date_deposit" id="date_deposit"
                                    value="{{ old('date_deposit', $model->date_deposit) == '' ? date('Y-m-d') : old('date_deposit', $model->date_deposit) }}"
                                    required="required">
                                @if ($errors->has('date_deposit'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('date_deposit') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="account_number">
                            <div class="form-group">
                                <label for="to_account_name">Account Name</label>
                                <input type="text" class="form-control" readonly name="to_account_name"
                                    id="to_account_name"
                                    value="{{ old('to_account_name', optional($model->toAccount)->account_name) }}">
                                @if ($errors->has('to_account_name'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('to_account_name') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6"">
                            <div class="form-group">
                                <label for="to_balance">Balance</label>
                                <input type="text" class="form-control" readonly name="to_balance"
                                    id="to_balance"
                                    value="{{ old('to_balance', number_format(optional($model->toAccount)->account_balance), 2, '.', ',') }}">
                                @if ($errors->has('to_balance'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('to_balance') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="to_amount">Amount Deposit</label>
                                <input type="number" readonly
                                    class="form-control {{ $errors->has('to_amount') ? ' is-invalid' : '' }}"
                                    name="to_amount" id="to_amount" value="{{ old('to_amount', $model->amount) }}"
                                    placeholder="" required="required">
                                @if ($errors->has('to_amount'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('to_amount') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="deposit_slip_no">Deposit Slip No</label>
                                <input type="text" autocomplete="off" readonly
                                    class="form-control {{ $errors->has('deposit_slip_no') ? ' is-invalid' : '' }}"
                                    name="deposit_slip_no" id="deposit_slip_no"
                                    value="{{ old('deposit_slip_no', $model->slip_no) }}" placeholder=""
                                    required="required">
                                @if ($errors->has('deposit_slip_no'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('deposit_slip_no') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="deposited_by">Depositor</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('deposited_by') ? ' is-invalid' : '' }}"
                                    name="deposited_by" id="deposited_by" required="required">
                                    <option value="">Select...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('deposited_by', $model->deposited_by) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}-{{ $user->user_code }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('deposited_by'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('deposited_by') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sent_by">User Sent Report To</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('sent_by') ? ' is-invalid' : '' }}"
                                    name="sent_by" id="sent_by" required="required">
                                    <option value="">Select...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('sent_by', $model->sent_by) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}-{{ $user->user_code }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('sent_by'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('sent_by') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group text-right ">
                                <input type="submit" class="btn btn-primary" value="Save" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
