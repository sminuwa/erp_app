<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Product</th>
            <th>No Of Days </th>
            <th>Updated By</th>
            <th>Last Date Updated</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->product->name}} </td>
                <td> {{ $record->no_of_days }} </td>
                <td> {{ $record->user->name}} </td>
                <td> {{ Carbon\Carbon::parse($record->updated_at)->toFormattedDateString()}} </td>
                <td><a class="btn btn-info btn-sm" href="{{ route('product_expire_settings.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('product_expire_settings.destroy', $record->id) }}" method="post"
                        style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-danger btn-sm cursor-pointer">
                            <i class="text-white fa fa-remove"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
