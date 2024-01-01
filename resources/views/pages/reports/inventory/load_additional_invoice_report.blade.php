<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.additional.invoice.report.print', [$from_date, $to_date, $branch_id, $supplier_id, $status]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">Additional Invoices
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>INVOICE</th>
            <th>NAME</th>
            <th>DESCRIPTION</th>
            <th>WAYBILL</th>
            <th>SUPP NAME</th>
            <th>AMOUNT</th>
            <th>STATUS</th>
        </tr>
    </thead>
    @php
        $total_cost = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->purchase_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->reference }}</td>
            <td>{{ $sale->name }}</td>
            <td>{{ $sale->description }}</td>
            <td>{{ $sale->wbno }}</td>
            <td>{{ $sale->supplier }}</td>
            <td style="text-align: right">
                {{ number_format($sale->amount, 2, '.', ',') }}</td>
            <td>{{ $sale->status == 1 ? 'Completed' : 'Pending' }}</td>



        </tr>
        @php
            $total_cost += $sale->amount;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="6" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_cost, 2, '.', ',') }}
            </th>
            <th></th>
        </tr>
    </tfoot>
</table>
