<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.ageing.report.print', [$from_date, $to_date,$branch_id, $customer_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Ageing Report
            @if ($from_date != 'all')
                From
                {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                AND
                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
            @endif
        </h5>
        <h5 style="text-align: center;">
            CUSTOMER NAME: {{ $customer_id == 'all' ? 'All' : \App\Models\Customer::find($customer_id)->name }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>CODE</th>
            <th>CUSTOMER</th>
            <th>RO</th>
            <th>LAST INVOICE</th>
            <th>DATE</th>
            <th>AGE(days)</th>
            <th>BALANCE</th>
        </tr>
    </thead>
    @php $total = 0 @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->name }}</td>
            <td>{{ $sale->relation_officer }}</td>
            <td>{{ $sale->reference}}</td>
            <td>{{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString()}}
            </td>
            <td>{{ \Carbon\Carbon::parse($sale->date)->diffInDays() }}</td>
            <td style="text-align: right">
                &#8358;{{ number_format($sale->balance, 2, '.', ',') }}
                @php $total += $sale->balance @endphp
            </td>
            
        </tr>
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="6">TOTAL BALANCE</th>
            <td style="text-align: right">
                &#8358;{{ number_format($total, 2, '.', ',') }}
            </td>
        </tr>
    </tfoot>
</table>
