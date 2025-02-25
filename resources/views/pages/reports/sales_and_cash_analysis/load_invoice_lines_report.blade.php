<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.invoice.list.report.print', [$from_date, $to_date, $company_id,$branch_id, $status]) }}"
           target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">List of Invoice Lines Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
    <tr>
        <th style="width: 50%" colspan="6">Date
            Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
        </th>
        <th style="width: 50%;text-align:right" colspan="6">Processed By {{ auth()->user()->name }}</th>
    </tr>
    <tr>
        <th>DATE</th>
        <th>REFERENCE</th>
        <th>ORDER ACCOUNT</th>
        <th>ITEME</th>
        <th>QTY</th>
        <th>UNIT PRICE</th>
        <th>TOTAL PRICE</th>
        <th>COST PRICE</th>
        <th>TOTAL COST</th>
        <th>MARGIN</th>
        <th>% MARGIN</th>
        <th>STATUS</th>
    </tr>
    </thead>
    @php
        $total = 0;
        $total_cost = 0;
    @endphp
    @foreach ($sales as $sale)
        @foreach ($sale->order_items as $item)
            @php
                $total += $sale->total;
                $total_cost += $item->cost_price * $item->quantity;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
                <td><a href="{{ route('orders.show',$sale->id) }}" target="_BLANK">{{ $sale->reference }}</a></td>
                <td>{{ $sale->customer->code }}</td>
                <td>{{ $item->storeProduct->product->code }}</td>
                <td>{{ $item->quantity }}</td>
                <td style="text-align: right">{{ number_format($item->sold_price, 2, '.', ',') }}</td>
                <td style="text-align: right">{{ number_format($item->total, 2, '.', ',') }}</td>
                <td style="text-align: right">{{ number_format($item->cost_price, 2, '.', ',') }}</td>
                <td style="text-align: right">{{ number_format($item->cost_price * $item->quantity, 2, '.', ',') }}
                </td>
                <td style="text-align: right">
                    {{ number_format($item->total - $item->cost_price * $item->quantity, 2, '.', ',') }}</td>
                <td style="text-align: right">
                    {{ $item->total>0?number_format((($item->total - $item->cost_price * $item->quantity) / $item->total) * 100, 2, '.', ','):'0.00' }}
                </td>
                <td>{{ $sale->status == 1 ? 'Completed' : 'Pending' }}</td>
            </tr>
        @endforeach
    @endforeach
    <tfoot>
    <tr>
        <th style="text-align: right" colspan="6">Total</th>
        <th style="text-align: right">{{ number_format($total, 2, '.', ',') }}</th>
        <th></th>
        <th style="text-align: right">{{ number_format($total_cost, 2, '.', ',') }}</th>
        <th style="text-align: right">{{ number_format($total - $total_cost, 2, '.', ',') }}</th>
        <th style="text-align: right">
            %{{ number_format((($total - $total_cost) / ($total>0?$total:1)) * 100, 2, '.', ',') }}
        </th>
        <th></th>
        <th></th>
    </tr>
    </tfoot>
</table>
