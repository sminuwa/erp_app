<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-6">
                <h6>Details Purchase of <small>products by {{ $record->supplier->name }}</small>
                </h6>
            </div>
            <div class="col-sm-6 text-right">
                <div class="btn-group">
                    @if ($record->status == 0)
                        <form onsubmit="return confirm('Are you sure you want to link to GRN?')"
                            action="{{ route('purchase.request.link', $record->id) }}" method="post"
                            style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('POST') }}
                            <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                <i class="text-white fa fa-link"> Link</i>
                            </button>
                        </form>
                        <a class="btn btn-secondary btn-sm" href="{{ route('purchases.request.edit', $record->id) }}">
                            <span class="fa fa-pencil"> Edit</span>
                        </a>
                    @endif
                    <a class="btn btn-secondary btn-sm" href="{{ route('purchases.request.index', $record->id) }}">
                        <span class="fa fa-list"> List</span>
                    </a>

                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('purchases.request.destroy', $record->id) }}" method="post"
                        style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                            <i class="text-danger fa fa-remove"> Delete</i>
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
                    <th>Branch</th>
                    <td> {{ $record->branch->name ?? '' }} </td>
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
