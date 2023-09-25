<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{route('branch_product_prices.show',$record->id)}}"> {{$record->id}}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary" href="{{route('branch_product_prices.edit',$record->id)}}">
    <span class="fa fa-pencil"></span>
</a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
      action="{{route('branch_product_prices.destroy',$record->id)}}"
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
			<th>Store Id</th>
			<td>{{$record->store_id}}</td>
		</tr>
		<tr>
			<th>Product Id</th>
			<td>{{$record->product_id}}</td>
		</tr>
		<tr>
			<th>Selling Price</th>
			<td>{{$record->selling_price}}</td>
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
