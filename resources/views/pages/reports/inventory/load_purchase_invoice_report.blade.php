<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.purchase.invoice.report.print', [$from_date, $to_date, $company_id,$branch_id, $supplier_id, $status]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">Purchase Invoice
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="4">Date Processed:
                {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>PROCESSED DATE</th>
            <th>DOCUMENT NO</th>
            <th>TOTAL COST</th>
            <th>ATC/WAYBILL</th>
            <th>SUPPLIER</th>
            <th>CREATED BY</th>
            <th>DATE CREATED</th>
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
            <td style="text-align: right">
                {{ number_format($sale->total, 2, '.', ',') }}</td>
            <td>{{ $sale->atc_no }}</td>
            <td>{{ $sale->supplier }}</td>
            <td>{{ $sale->name }}</td>
            <td>{{ \Carbon\Carbon::parse($sale->created_at)->toFormattedDateString() }}</td>
            <td>{{ $sale->status == 1 ? 'Completed' : 'Pending' }}</td>

        </tr>
        @php
            $total_cost += $sale->total;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="2" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_cost, 2, '.', ',') }}
            </th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
