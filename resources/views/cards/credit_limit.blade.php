<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{route('credit_limits.show',$record->id)}}"> {{$record->id}}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary" href="{{route('credit_limits.edit',$record->id)}}">
    <span class="fa fa-pencil"></span>
</a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
      action="{{route('credit_limits.destroy',$record->id)}}"
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
			<th>Customer Name</th>
			<td>{{$record->name}}</td>
		</tr>
		<tr>
			<th>Amount</th>
			<td>{{$record->amount}}</td>
		</tr>
		<tr>
			<th>Status</th>
			<td>{{$record->status}}</td>
		</tr>
		<tr>
			<th>Updated By</th>
			<td>{{$record->updated_by}}</td>
		</tr>

            </tbody>
        </table>
    </div>
</div>
