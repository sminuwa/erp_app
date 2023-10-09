<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <h3>Details Purchase of <small>products by {{ $record->supplier->name }}</small>
                </h3>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    @if ($record->status == 0)
                    <form onsubmit="return confirm('Are you sure you want to approve?')"
                    action="{{ route('purchase.approve', $record->id) }}" method="post" style="display: inline">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                        <i class="text-white fa fa-check"> Approve</i>
                    </button>
                </form>
                    @endif
                    <a class="btn btn-secondary btn-sm" href="{{ route('purchases.index', $record->id) }}">
                        <span class="fa fa-list"></span>
                    </a>
                    <a class="btn btn-secondary btn-sm" href="{{ route('purchases.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('purchases.destroy', $record->id) }}" method="post" style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
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
                    <th>Supplier</th>
                    <td>{{ optional($record->supplier)->name }}</td>
                </tr>
                <tr>
                    <th>Invoice</th>
                    <td>{{ $record->invoice }}</td>
                </tr>
                <tr>
                    <th>Purchase Date</th>
                    <td>{{ optional($record->purchase_date)->toDayDateTimeString() }}</td>
                </tr>
                <tr>
                    <th>Truck No</th>
                    <td>{{ $record->vehicle_reg_no }}</td>
                </tr>
                <tr>
                    <th>Store/Shop</th>
                    <td> {{ $record->sourceStore->name }} </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $record->status === 1 ? 'Completed' : 'Pending' }}</td>
                </tr>
                <tr>
                    <th>Updated By</th>
                    <td>{{ $record->user->name }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
