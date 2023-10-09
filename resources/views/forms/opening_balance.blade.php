<form action="{{ isset($route) ? $route : route('opening_balance.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="customer_id">Customer Name</label>
        <select class="form-control select2-single {{ $errors->has('customer_id') ? ' is-invalid' : '' }}"
            name="customer_id" id="customer_id"
            required="required">
            <option value="">Select...</option>
            @if (isset($customers))
                @foreach ($customers as $data)
                    <option value="{{ $data->id }}" {{ $data->id == optional($model)->customer_id ? 'selected' : '' }}>
                        {{ $data->code }}-{{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('customer_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('customer_id') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="text" class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}" name="amount"
            id="amount" value="{{ old('amount',  optional($model)->amount) }}" placeholder="" required="required">
        @if ($errors->has('amount'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('amount') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">

        <input type="hidden" class="form-control " name="updated_by" id="updated_by" value="{{ Auth::id() }}"
            placeholder="" required="required">

    </div>


    <div class="form-group text-right ">

        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
