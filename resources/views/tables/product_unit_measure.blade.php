<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Product Code</th>
            <th>Description</th>
            <th>Code </th>
            <th>Type </th>
            <th>Value </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->product->code ?? '' }} </td>
                <td> {{ $record->product->name ?? '' }} </td>
                <td> {{ $record->code }} </td>
                <td> {{ ucfirst($record->type) }} </td>
                <td> {{ $record->value }} </td>
                <td>
                    @can('product_unit_measures.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('product_unit_measures.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('product_unit_measures.destroy')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('product_unit_measures.destroy', $record->id) }}" method="post"
                            style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-secondary cursor-pointer btn-sm">
                                <i class="text-danger fa fa-remove"></i>
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
