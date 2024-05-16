<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.intersite.transfer.reports', [$from_date, $to_date, $source_branch_id, $destination_branch_id, $category_id, $product_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Intersite Stock Transfer Report
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
            <th>From Branch</th>
            <th>To Branch</th>
            <th>Rerefence No</th>
            <th>QTY</th>
            <th>Cost Price</th>
            <th>Total</th>
        </tr>
    </thead>
    @php $total =0; @endphp
    @foreach ($transfers as $transfer)
        <tr>
            <td>{{ Carbon\Carbon::parse($transfer->date)->toFormattedDateString() }}</td>
            <td>{{ $transfer->product_code }}</td>
            <td>{{ $transfer->product_name }}</td>
            <td>{{ \App\Models\Branch::find($transfer->source_branch_id)->code }}</td>
            <td>{{ \App\Models\Branch::find($transfer->destination_branch_id)->code }}</td>
            <td>{{ $transfer->reference }}</td>
            <td style="text-align: right">{{ $transfer->quantity }}</td>
            <td style="text-align: right">{{ number_format($transfer->cost_price, 2) }}</td>
            @php $total += $transfer->cost_price * $transfer->quantity; @endphp
            <td style="text-align: right">{{ number_format($transfer->cost_price * $transfer->quantity, 2) }}</td>

        </tr>
    @endforeach
    <tfoot>
        <tr>

            <th colspan="8"> Total</th>
            <th style="text-align: right">{{ number_format($total, 2) }}</th>
        </tr>
    </tfoot>
</table>
