<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.stock.transfer.reports', [$from_date, $to_date,$store_id,$category_id,$product_id,$from_to])}}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Stock
            From
            {{ $from_date }} to
            {{ $to_date }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Date</th>
            <th>Item Name</th>
            <th>From Store</th>
            <th>To Store</th>
            <th>QTY Before Transfer</th>
            <th>QTY Transfer</th>
            <th>Transfer No</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Date</th>
            <th>Item Name</th>
            <th>From Store</th>
            <th>To Store</th>
            <th>QTY Before Transfer</th>
            <th>QTY Transfer</th>
            <th>Transfer No</th>
        </tr>
    </tfoot>
    @foreach ($transfers as $transfer)
        <tr>
            <td>{{ $transfer->updated_at->toFormattedDateString() }}</td>
            <td>{{ $transfer->name }}</td>
            <td>{{ \App\Models\Store::find($transfer->source_store_id)->name }}</td>
            <td>{{ \App\Models\Store::find($transfer->destination_store_id)->name }}</td>
            <td>{{ $transfer->qty_available }}</td>
            <td>{{ $transfer->qty_transfered }}</td>
            <td>{{ $transfer->refno }}</td>
        </tr>
    @endforeach
</table>
