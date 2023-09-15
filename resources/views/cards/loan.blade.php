<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{route('loans.show',$record->id)}}"> {{$record->id}}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary" href="{{route('loans.edit',$record->id)}}">
    <span class="fa fa-pencil"></span>
</a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
      action="{{route('loans.destroy',$record->id)}}"
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
			<th>Loan Collector Id</th>
			<td>{{$record->loan_collector_id}}</td>
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
			<th>Date</th>
			<td>{{$record->date}}</td>
		</tr>
		<tr>
			<th>Granted By</th>
			<td>{{$record->granted_by}}</td>
		</tr>
		<tr>
			<th>Receipt No</th>
			<td>{{$record->receipt_no}}</td>
		</tr>
		<tr>
			<th>Due Date</th>
			<td>{{$record->due_date}}</td>
		</tr>

            </tbody>
        </table>
    </div>
</div>
