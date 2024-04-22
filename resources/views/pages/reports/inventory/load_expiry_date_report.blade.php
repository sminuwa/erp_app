<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.expiry.date.report.print', [$from_date, $to_date, $branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">Expiry Date Tracking
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>EXPIRY DATE</th>
            <th>Batch No</th>
            <th>PRODUCT</th>
            <TH>QTY</TH>
        </tr>
    </thead>
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->expiry_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->batch_no }}</td>
            <td>{{ $sale->product->code ?? '' }}</td>
            <td style="text-align: right">{{ $sale->new_quantity }}</td>
        </tr>
    @endforeach
</table>
