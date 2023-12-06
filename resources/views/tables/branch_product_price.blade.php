<table class="table table-bordered table-striped" id="record2">
    <thead>
        <tr>
            <th>Code</th>
            <th>Branch</th>
            <th>Product</th>
            <th>C Price </th>
            <th>R Price </th>
            <th>W Price </th>
            <th>Status </th>
            <th>Modified</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->branch_code }} </td>
                <td> {{ $record->branch_name }} </td>
                <td> {{ $record->product_name }} </td>
                <td style="text-align: right"> {{ number_format(str_replace(',', '', $record->cost_price), 2) }} </td>
                <td style="text-align: right">
                    &#8358;{{ number_format(str_replace(',', '', $record->retail_selling_price), 2) }}
                </td>
                <td style="text-align: right">
                    &#8358;{{ number_format(str_replace(',', '', $record->whole_selling_price), 2) }}
                </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Inactive' }} </td>
                <td> {{ $record->user->name ?? '' }} </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @can('set.product.price')
                                <a class="dropdown-item"
                                    href="{{ route('branch_product_prices.edit', $record->id) }}">
                                    <span class="fa fa-pencil"> Edit</span>
                                </a>
                            @endcan
                            @can('delete.product.price')
                                <form onsubmit="return confirm('Are you sure you want to delete?')"
                                    action="{{ route('branch_product_prices.destroy', $record->id) }}" method="post"
                                    style="display: inline">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="dropdown-item">
                                        <i class="text-danger fa fa-remove"> Delete</i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
