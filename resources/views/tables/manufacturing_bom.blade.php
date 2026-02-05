<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Reference</th>
            <th>Type</th>
            <th>Description</th>
            <th>Finish Product</th>
            <th>Output Store</th>
            <th class="text-right">Cost/Unit</th>
            <th>Status</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->reference }}</td>
                <td>
                    <span class="badge badge-{{ $record->bom_type == 'batch' ? 'primary' : 'success' }}">
                        {{ ucfirst($record->bom_type) }}
                    </span>
                </td>
                <td>{{ Str::limit($record->description, 40) }}</td>
                <td>{{ $record->finishProduct->code ?? '' }} - {{ $record->finishProduct->name ?? '-' }}</td>
                <td>{{ $record->outputStore->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($record->total_cost_per_unit, 2) }}</td>
                <td>{{ $record->status == 1 ? 'Active' : 'Inactive' }}</td>
                <td>
                    @can('manufacturing.boms.show')
                        <a class="btn btn-info btn-sm" href="{{ route('manufacturing.boms.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    @can('manufacturing.boms.edit')
                        <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.boms.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('manufacturing.boms.delete')
                        <form onsubmit="return confirm('Are you sure you want to delete this BOM?')"
                            action="{{ route('manufacturing.boms.destroy', $record->id) }}" method="post" style="display: inline">
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
