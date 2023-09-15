<form action="{{route('expenses.cart.create') }}" method="POST">
    {{ csrf_field() }}
    <div class="form-group">
        <label for="expense_item_id">Expense Item</label>
        <select type="number" class="form-control {{ $errors->has('expense_item_id') ? ' is-invalid' : '' }}"
            name="expense_item_id" id="expense_item_id" required="required">
            <option value="">Select...</option>
            @if (isset($expense_items))
                @foreach ($expense_items as $data)
                    <option value="{{ $data->id }}">
                        {{ $data->name }}</option>
                @endforeach
            @endif
        </select>
        @if ($errors->has('expense_item_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('expense_item_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="number" class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}" name="amount"
            id="amount" value="" placeholder="" required="required"
            min="0">
        @if ($errors->has('amount'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('amount') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="reason">Reason for Expense</label>
        <input type="text" class="form-control {{ $errors->has('reason') ? ' is-invalid' : '' }}" name="reason"
            id="reason" value="" placeholder="" maxlength="191">
        @if ($errors->has('reason'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('reason') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group text-right ">
        <button type="submit" class="btn btn-primary"><span class="fa fa-cart-plus"> </span> Add to Cart</button>
    </div>
</form>
