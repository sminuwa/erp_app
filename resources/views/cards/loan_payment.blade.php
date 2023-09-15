<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{route('loan_payments.show',$record->id)}}"> {{$record->id}}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary" href="{{route('loan_payments.edit',$record->id)}}">
    <span class="fa fa-pencil"></span>
</a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
      action="{{route('loan_payments.destroy',$record->id)}}"
      method="post"
      style="display: inline">
    {{csrf_field()}}
    {{method_field('DELETE')}}
    <button type="submit" class="btn btn-secondary cursor-pointer">
        <i class="text-danger fa fa-remove"></i>
    </button>
</form>
                </div>
            </div>
        </div>
    </div>
    <div class="card-block">
        <table class="table table-bordered table-striped">
            <tbody>
            		<tr>
			<th>Loan Id</th>
			<td>{{$record->loan_id}}</td>
		</tr>
		<tr>
			<th>Amount</th>
			<td>{{$record->amount}}</td>
		</tr>
		<tr>
			<th>Payment Mode</th>
			<td>{{$record->payment_mode}}</td>
		</tr>
		<tr>
			<th>Bank Account Id</th>
			<td>{{$record->bank_account_id}}</td>
		</tr>
		<tr>
			<th>Cheque No</th>
			<td>{{$record->cheque_no}}</td>
		</tr>
		<tr>
			<th>Receipt No</th>
			<td>{{$record->receipt_no}}</td>
		</tr>
		<tr>
			<th>Received By</th>
			<td>{{$record->received_by}}</td>
		</tr>

            </tbody>
        </table>
    </div>
</div>
