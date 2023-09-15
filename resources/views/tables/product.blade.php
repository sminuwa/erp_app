<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Brand Name </th>
            <th>Genetic Name </th>
            <th>Strength </th>
            <th>Company </th>
            <th>Category</th>
            <th>Dosage Form</th>
            <th>Barcode</th>
            <th>Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->name }} </td>
                <td> {{ $record->generic_name }} </td>
                <td> {{ $record->strength }} </td>
                <td> {{ $record->company?->name }} </td>
                <td> {{ $record->category?->name }} </td>
                <td> {{ $record->dosage?->name }} </td>
                <td><span style="font-size: 10pt;">{{$record->barcode}}</span>
                    {{-- @if ($record->barcode != null)
                        {!! DNS1D::getBarcodeHTML($record->barcode, 'CODABAR') !!}
                    @endif --}}
                </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Inactive' }} </td>
                <td>
                    @can('view.product')
                        <a class="btn btn-secondary btn-sm" href="{{ route('products.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('edit.product')
                        <a class="btn btn-secondary btn-sm" href="{{ route('products.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.product')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('products.destroy', $record->id) }}" method="post" style="display: inline">
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
