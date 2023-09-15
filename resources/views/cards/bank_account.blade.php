<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{route('bank_accounts.show',$record->id)}}"> {{$record->id}}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary" href="{{route('bank_accounts.edit',$record->id)}}">
    <span class="fa fa-pencil"></span>
</a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
      action="{{route('bank_accounts.destroy',$record->id)}}"
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
			<th>Account Name</th>
			<td>{{$record->account_name}}</td>
		</tr>
		<tr>
			<th>Account No</th>
			<td>{{$record->account_no}}</td>
		</tr>
		<tr>
			<th>Bank Branch Id</th>
			<td>{{$record->bank_branch_id}}</td>
		</tr>
		<tr>
			<th>Account Balance</th>
			<td>{{$record->account_balance}}</td>
		</tr>
		<tr>
			<th>Account Type</th>
			<td>{{$record->account_type}}</td>
		</tr>
		<tr>
			<th>Status</th>
			<td>{{$record->status}}</td>
		</tr>

            </tbody>
        </table>
    </div>
</div>
