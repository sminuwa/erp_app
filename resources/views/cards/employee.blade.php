<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{route('employees.show',$record->id)}}"> {{$record->id}}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary" href="{{route('employees.edit',$record->id)}}">
    <span class="fa fa-pencil"></span>
</a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
      action="{{route('employees.destroy',$record->id)}}"
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
			<th>Name</th>
			<td>{{$record->name}}</td>
		</tr>
		<tr>
			<th>Email</th>
			<td>{{$record->email}}</td>
		</tr>
		<tr>
			<th>Phone</th>
			<td>{{$record->phone}}</td>
		</tr>
		<tr>
			<th>Address</th>
			<td>{{$record->address}}</td>
		</tr>
		<tr>
			<th>Experience</th>
			<td>{{$record->experience}}</td>
		</tr>
		<tr>
			<th>Photo</th>
			<td>{{$record->photo}}</td>
		</tr>
		<tr>
			<th>Salary</th>
			<td>{{$record->salary}}</td>
		</tr>
		<tr>
			<th>Vacation</th>
			<td>{{$record->vacation}}</td>
		</tr>
		<tr>
			<th>City</th>
			<td>{{$record->city}}</td>
		</tr>

            </tbody>
        </table>
    </div>
</div>
