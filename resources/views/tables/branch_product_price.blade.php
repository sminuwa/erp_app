<table class="table table-bordered table-striped" id="record2">
    <thead>
        <tr>
            <th>Code</th>
            <th>Branch</th>
            <th>Product</th>
            <th>Cost Price </th>
            <th>Selling Price </th>
            <th>Status </th>
            <th>Updated By </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->branch?->code }} </td>
                <td> {{ optional($record->branch)->name }} </td>
                <td> {{ optional($record->product)->name }} </td>
                <td style="text-align: right"> {{ number_format($record->cost_price, 2) }} </td>
                <td style="text-align: right"> &#8358;{{ number_format($record->selling_price, 2) }} </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Inactive' }} </td>
                <td> {{ $record->user->name }} </td>
                <td>
                    @can('set.product.price')
                        <a class="btn btn-secondary btn-sm" href="{{ route('branch_product_prices.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.product.price')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('branch_product_prices.destroy', $record->id) }}" method="post"
                            style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                <i class="text-danger fa fa-remove"></i>
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
