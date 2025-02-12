<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.list.report.print', [$company_id, $branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">List of Products</h5>
    </caption>
    <thead>
        <tr>
            @isset($branch_id)
                @if ($branch_id == 'all')
                    <th>Branch</th>
                @endif
            @endisset
            <th>Code </th>
            <th>Brand Name </th>
            <th>Category</th>
            <th>Barcode</th>
            <th>Status </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                @isset($branch_id)
                    @if ($branch_id == 'all')
                        <td>{{ $record->branch_code }}</td>
                    @endif
                @endisset
                <td> {{ $record->code }} </td>
                <td> {{ $record->name }} </td>
                <td> {{ $record->category?->name }} </td>
                <td><span style="font-size: 10pt;">{{ $record->barcode }}</span>
                </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Inactive' }} </td>
            </tr>
        @endforeach
    </tbody>
</table>
