<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{ route('products.show', $record->id) }}"> {{ $record->name }}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary btn-sm" href="{{ route('products.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('products.destroy', $record->id) }}" method="post" style="display: inline">
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
                    <th>Item Code</th>
                    <td>{{ $record->code }}</td>
                </tr>
                <tr>
                    <th>Item Name</th>
                    <td>{{ $record->name }}</td>
                </tr>
                <tr>
                    <th>Company</th>
                    <td>{{ $record->company?->name }}</td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td>{{ $record->category->name ?? '' }}</td>
                </tr>
                <tr>
                    <th>Base Unit of Measure</th>
                    <td>{{ $record->unit }}</td>
                </tr>
                <tr>
                    <th>Barcode</th>
                    <td>{{ $record->barcode }}<br />
                        @if ($record->barcode != null)
                            {!! DNS1D::getBarcodeHTML($record->barcode, 'CODABAR') !!}
                        @endif
                    </td>

                </tr>
                <tr>
                    <th>Last Date Updated</th>
                    <td>{{ Carbon\Carbon::parse($record->updated_at)->toFormattedDateString() }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $record->status == 1 ? 'Active' : 'Inactive' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
