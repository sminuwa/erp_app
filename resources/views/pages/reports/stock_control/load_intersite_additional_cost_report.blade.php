<div class="row mb-2">
    <div class="col-12 text-right">
        <a href="{{ route('ajax.print.intersite.additional_cost.reports', [$from_date, $to_date, $company_id, $branch_id, $supplier_id, $status]) }}"
           target="_BLANK" class="btn btn-sm btn-secondary">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Intersite Additional Cost Report
            From
            {{ Carbon\Carbon::parse($from_date)->toFormattedDateString() }} to
            {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
        @if ($company_id != 'all' || $branch_id != 'all')
            <h6 style="text-align: center;">
                @if ($company_id != 'all')
                    Company: {{ \App\Models\Company::find($company_id)->name ?? '' }}
                @endif
                @if ($branch_id != 'all')
                    &nbsp;|&nbsp; Branch: {{ \App\Models\Branch::find($branch_id)->name ?? '' }}
                @endif
            </h6>
        @endif
    </caption>
    <thead>
    <tr>
        <th style="width: 50%" colspan="6">Date
            Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
        </th>
        <th style="width: 50%;text-align:right" colspan="6">Processed By {{ auth()->user()->name }}</th>
    </tr>
    <tr>
        <th>Date</th>
        <th>Reference</th>
        <th>Intersite Ref</th>
        <th>Source Branch</th>
        <th>Dest. Branch</th>
        <th>Supplier</th>
        <th>Cost Mode</th>
        <th>Truck No</th>
        <th>Waybill No</th>
        <th>Status</th>
        <th>Created By</th>
        <th>Amount</th>
    </tr>
    </thead>
    @php $total = 0; @endphp
    @foreach ($records as $record)
        <tr>
            <td>{{ Carbon\Carbon::parse($record->date)->toFormattedDateString() }}</td>
            <td>{{ $record->reference }}</td>
            <td>{{ $record->intersiteTransfer->reference ?? 'N/A' }}</td>
            <td>{{ $record->intersiteTransfer->source->code ?? '' }}</td>
            <td>{{ $record->intersiteTransfer->destination->code ?? '' }}</td>
            <td>{{ $record->supplier->name ?? 'N/A' }}</td>
            <td>{{ $record->cost_mode == 'by_amount' ? 'By Amount' : 'By Qty' }}</td>
            <td>{{ $record->truck_number }}</td>
            <td>{{ $record->waybill_no }}</td>
            <td>
                @if($record->status == 'pending')
                    <span class="badge badge-warning">Pending</span>
                @elseif($record->status == 'posted')
                    <span class="badge badge-success">Posted</span>
                @elseif($record->status == 'reversed')
                    <span class="badge badge-danger">Reversed</span>
                @endif
            </td>
            <td>{{ $record->createdBy->name ?? '' }}</td>
            @php $total += $record->amount; @endphp
            <td style="text-align: right">{{ number_format($record->amount, 2) }}</td>
        </tr>
    @endforeach
    <tfoot>
    <tr>
        <th colspan="11"> Total</th>
        <th style="text-align: right">{{ number_format($total, 2) }}</th>
    </tr>
    </tfoot>
</table>
