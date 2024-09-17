<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.goods.in.transit.report.print', [$from_date, $to_date, $branch_id, $status]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">Goods in Transit
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="6">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="5">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>DATE</th>
            <th>REFERENCE</th>
            <th>SOURCE</th>
            <th>DESTINATION</th>
            <th>CODE</th>
            <th>ITEM</th>
            <th>QTY</th>
            <th>COST PRICE</th>
            <th>TOTAL COST</th>
            <th>VEHICLE NO</th>
            <th>STATUS</th>
        </tr>
    </thead>
    @php
        $total_cost = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}</td>
            <td>{{ $sale->reference }}</td>
            <td>{{ $sale->source }}</td>
            <td>{{ $sale->destination }}</td>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->product }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">{{ number_format($sale->cost_price, 2) }}</td>
            <td style="text-align: right">
                {{ number_format($sale->cost_price * $sale->quantity, 2, '.', ',') }}</td>
            <td>{{ $sale->vehicle_no }}</td>
            <td>{{ $sale->status == 0 ? 'Pending' : ($sale->status == 1 ? 'In-Transit' : 'Recieved') }}</td>

        </tr>
        @php
            $total_cost += $sale->cost_price * $sale->quantity;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="8" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_cost, 2, '.', ',') }}
            </th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
