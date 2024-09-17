<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.ageing.report.print', [$from_date, $to_date, $branch_id, $customer_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
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
            <th style="width: 50%" colspan="4">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th colspan="6"></th>
            <th colspan="2" style="text-align: center">BALANCE</th>
        </tr>
        <tr>
            <th>CODE</th>
            <th>CUSTOMER</th>
            <th>RO</th>
            <th>LAST INVOICE</th>
            <th>DATE</th>
            <th>AGE(days)</th>
            <th>Dr.</th>
            <th>Cr.</th>
        </tr>

    </thead>
    @php $total_dr = $total_cr = 0; @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->name }}</td>
            <td>{{ $sale->relation_officer }}</td>
            {{-- <td>{{ customerLastTransaction($sale->customer_id)->reference ?? '' }}</td> --}}
            <td>{{ $sale->reference }}</td>
            <td>
                {{-- @if (customerLastTransaction($sale->customer_id) != null)
                    {{ \Carbon\Carbon::parse(customerLastTransaction($sale->customer_id)->date)->toFormattedDateString() }}
                    @else
                    {{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}
                @endif --}}
                {{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}
            </td>
            <td>
                {{-- @if (customerLastTransaction($sale->customer_id) != null)
                    {{ \Carbon\Carbon::parse(customerLastTransaction($sale->customer_id)->date)->diffInDays() }}
                    @else
                    {{ $sale->age }}
                @endif --}}
                {{ $sale->age }}
            </td>

            <td style="text-align: right">
                @if ($sale->balance < 0)
                    {{ number_format(abs($sale->balance), 2, '.', ',') }}
                    @php $total_dr += $sale->balance; @endphp
                @endif
            </td>

            <td style="text-align: right">
                @if ($sale->balance > 0)
                    {{ number_format(abs($sale->balance), 2, '.', ',') }}
                    @php $total_cr += $sale->balance; @endphp
                @endif
            </td>

        </tr>
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="6">TOTAL BALANCE</th>
            <td style="text-align: right">
                {{ $total_dr - $total_cr < 0 ? number_format(abs($total_dr - $total_cr), 2, '.', ',') : '' }}
            </td>
            <td style="text-align: right">
                {{ $total_dr - $total_cr > 0 ? number_format($total_dr - $total_cr, 2, '.', ',') : '' }}
            </td>

        </tr>
    </tfoot>
</table>
