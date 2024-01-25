
<form action="{{ isset($route) ? $route : route('customers.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="code">Branch</label>
        <select class="form-control ajax-branches  {{ $errors->has('account_type') ? ' is-invalid' : '' }}"
                name="branch_id" id="branch_id"
                selected_item="{{ $model->branch_id }}"
                required>
            <
        </select>
        @if ($errors->has('account_type'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('branch_id') }}</strong>
            </div>
        @endif
    </div>
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
            <option value="R" {{old('account_type', $model->account_type)}} {{ $model->type =="Retail" ? 'selected':'' }}>Retail</option>
            <option value="W" {{old('account_type', $model->account_type)}} {{ $model->type =="Wholesale" ? 'selected':'' }}>Whole Sale</option>
        </select>
        @if ($errors->has('account_type'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('account_type') }}</strong>
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
            placeholder="" value="0">
        @if ($errors->has('credit_limit'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('credit_limit') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <?php $officers = App\Models\User::orderBy('user_code', 'asc')->get(); ?>
        <label for="code">Relation Officer</label>
        <select class="form-control ajax-users{{ $errors->has('relation_officer') ? ' is-invalid' : '' }}"
                name="relation_officer" id="relation_officer"
                required>
            @foreach($officers as $officer)
                <option value="{{$officer->id}}" {{ $model->relation_officer == $officer->id ? 'selected' :'' }}>{{ $officer->user_code }} - {{ $officer->name }}</option>
            @endforeach
        </select>
        @if ($errors->has('account_type'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('branch_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group text-right ">

        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
