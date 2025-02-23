<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.order.lines.report.print', [$from_date, $to_date, $company_id, $branch_id, $status]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">List of Order Lines Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="2">Date
                Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>DATE</th>
            <th>REFERENCE</th>
            <th>ORDER ACCOUNT</th>
            <th>ITEME</th>
            <th>QTY</th>
            {{-- <th>UNIT PRICE</th> --}}
            {{-- <th>TOTAL</th> --}}
            <th>STATUS</th>
        </tr>
    </thead>
     @php
        $total = 0;
    @endphp
    {{-- @foreach ($sales as $sale)
        @foreach ($sale->order_items as $item)
            @php
                $total += $sale->total;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
                
                <td><a href="{{ route('order.invoice.show',$sale->order_id) }}" target="_BLANK">{{ $sale->reference }}</a></td>
                <td>{{ $sale->customer_code }}</td>
                <td>{{ $item->product_code ?? ''}}</td>
                <td>{{ $item->quantity }}</td>
                <td style="text-align: right">{{ number_format($item->unit_cost, 2, '.', ',') }}</td>
                <td style="text-align: right">{{ number_format($item->total, 2, '.', ',') }}</td>
                <td>{{ $sale->status == 1 ? 'Completed' : 'Pending' }}</td>
            </tr>
        @endforeach
    @endforeach --}}
    @foreach ($sales as $sale)
        @php
            $total += $sale->total;
        @endphp
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
            <td><a href="{{ route('order.invoice.show', $sale->order_id) }}" target="_BLANK">{{ $sale->reference }}</a>
            </td>
            <td>{{ $sale->customer_code }}</td>
            <td>{{ $sale->product_code ?? '' }}</td>
            <td>{{ $sale->quantity }}</td>
            {{-- <td style="text-align: right">{{ number_format($sale->unit_cost, 2, '.', ',') }}</td> --}}
            {{-- <td style="text-align: right">{{ number_format($sale->total, 2, '.', ',') }}</td> --}}
            <td>{{ $sale->status == 0 ? 'Pending' : 'Completed' }}</td>
        </tr>
    @endforeach
    {{-- <tfoot>
    <tr>
        <th style="text-align: right" colspan="4">Total</th>
        <th style="text-align: right">{{ number_format($total, 2, '.', ',') }}</th>
        <th></th>
    </tr>
    </tfoot> --}}
</table>
