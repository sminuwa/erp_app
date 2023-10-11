<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{ route('interstore.show', $record->id) }}"> {{ $record->id }}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary" href="{{ route('interstore.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('interstore.destroy', $record->id) }}" method="post" style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
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
                    <th>Source Store Id</th>
                    <td>{{ $record->source_store_id }}</td>
                </tr>
                <tr>
                    <th>Product Id</th>
                    <td>{{ $record->product_id }}</td>
                </tr>
                <tr>
                    <th>Destination Store Id</th>
                    <td>{{ $record->destination_store_id }}</td>
                </tr>
                <tr>
                    <th>Qty Transfered</th>
                    <td>{{ $record->qty_transfered }}</td>
                </tr>
                <tr>
                    <th>Qty Available</th>
                    <td>{{ $record->qty_available }}</td>
                </tr>
                <tr>
                    <th>Transfered By</th>
                    <td>{{ $record->transfered_by }}</td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
