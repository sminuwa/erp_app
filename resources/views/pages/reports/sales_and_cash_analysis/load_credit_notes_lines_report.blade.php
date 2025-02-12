<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.credit.note.report.lines.print', [$from_date, $to_date, $company_id,$branch_id, $status]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{$branch->name ?? 'All Branches'}}</h3>
        <h5 style="text-align: center;">Credit Notes Lines Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="5">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="5">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>PROCESSED DATE</th>
            <th>INVOICE</th>
            <th>REFERENCE</th>
            <th>CUST ACCOUNT</th>
            <th>CUST NAME</th>
            <th>AMOUNT</th>
            <th>CREATED BY</th>
            <th>DATE CREATED</th>
            <th>POSTED BY</th>
            <th>STATUS</th>
        </tr>
    </thead>

    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}</td>
            <td><a href="{{ route('orders.show',$sale->order->id) }}" target="_BLANK">{{ $sale->order->reference }}</a></td>
            {{-- <td>{{ $sale->order->reference }}</td> --}}
            <td><a href="{{ route('credit.note.show',$sale->id) }}" target="_BLANK">{{ $sale->reference }}</a></td>
            <td>{{ $sale->customer->code }}</td>
            <td>{{ $sale->customer->name }}</td>
            <td style="text-align: right">{{ number_format($sale->amount, 2, '.', ',') }}</td>
            <td>{{ $sale->createdBy->name ?? null }}</td>
            <td>{{ \Carbon\Carbon::parse($sale->created_at)->toFormattedDateString() }}</td>
            <td>{{ $sale->postedBy->name ?? null }}</td>
            <td>{{ $sale->status == 1 ? 'Completed' : 'Pending' }}</td>
        </tr>
    @endforeach
</table>
