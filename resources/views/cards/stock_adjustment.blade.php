<div class="card card-default">
    <div class="card-header">
       <div class="card-title">
        <h3>Stock Adjustment Details</h3>
       </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card-block">
                <table class="table table-bordered table-striped">
                    <tbody>
                        
                        <tr>
                            <th>Date</th>
                            <td>{{ \Carbon\Carbon::parse($record->date)->toFormattedDateString() }}</td>
                        </tr>
                        <tr>
                            <th>Ref No</th>
                            <td>{{ $record->refno }}</td>
                        </tr>
                        <tr>
                            <th>Adjusted By</th>
                            <td>{{ $record->user->name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
       <div class="col-md-8">
        <table class="table table-bordered table-striped" id="record1">
            <thead>
                <tr>
                    <th>Store</th>
                    <th>Group</th>
                    <th>Product</th>
                    <th>Qty B/F Adjust </th>
                    <th>Adjusted Qty </th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach ($record->AdjustedProducts()->get() as $record)
                    <tr>
                        <td> {{ $record->store->name }} </td>
                        <td> {{ $record->product->category->name }} </td>
                        <td> {{ $record->product->name }} </td>
                        <td> {{ $record->available_qty }} </td>
                        <td> {{ $record->adjusted_qty }} </td>
                       
                        <td><a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.edit', $record->id) }}">
                                <span class="fa fa-pencil"></span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                
            </tbody>
        </table>
       </div>
        
            <div class="text text-danger">
                Total : 
                {{ $record->count() }}
            </div>
        </div>
    </div>
</div>
