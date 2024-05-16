<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.interstore.transfer.reports', [$from_date, $to_date, $branch_id, $source_store_id, $destination_store_id, $category_id, $product_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Interstore Stock Transfer Report
            From
            {{ Carbon\Carbon::parse($from_date)->toFormattedDateString() }} to
            {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Date</th>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Branch</th>
            <th>From Store</th>
            <th>To Store</th>
            <th>Rerefence No</th>
            <th>QTY</th>
        </tr>
    </thead>
    @php $total =0; @endphp
    @foreach ($transfers as $transfer)
        <tr>
            <td>{{ Carbon\Carbon::parse($transfer->date)->toFormattedDateString() }}</td>
            <td>{{ $transfer->product_code }}</td>
            <td>{{ $transfer->product_name }}</td>
            <td>{{ \App\Models\Branch::find($transfer->branch_id)->code }}</td>
            <td>{{ \App\Models\Store::find($transfer->source_store_id)->code }}</td>
            <td>{{ \App\Models\Store::find($transfer->destination_store_id)->code }}</td>
            <td>{{ $transfer->reference }}</td>
            <td style="text-align: right">{{ $transfer->quantity }}</td>
        </tr>
    @endforeach
</table>
