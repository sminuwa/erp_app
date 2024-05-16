<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.total.debt.report.print', [$from_date, $to_date, $customer_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Customer Total Debt Report
            {{-- From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }} --}}
            As at
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th colspan="4"></th>
            <th colspan="2">BALANCE</th>
        </tr>
        <tr>
            <th>ACCOUNT NO</th>
            <th>CUSTOMER NAME</th>
            <th>TOTAL AMOUNT</th>
            <th>TOTAL PAID</th>
            <th>DR.</th>
            <th>CR.</th>
        </tr>
    </thead>
    @php
        $total_sold = 0;
        $total_pay = 0;
        $total_due = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->customer }}</td>
            <td style="text-align: right">{{ number_format($sale->total, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->pay, 2, '.', ',') }}</td>
            <td style="text-align: right">
                @if ($sale->due < 0)
                    {{ number_format(abs($sale->due), 2, '.', ',') }}
                @endif
            </td>
            <td style="text-align: right">
                @if ($sale->due > 0)
                    ({{ number_format(abs($sale->due), 2, '.', ',') }})
                @endif
            </td>
        </tr>
        @php
            $total_sold += $sale->total;
            $total_pay += $sale->pay;
            $total_due += $sale->due;

        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th></th>
            <th style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_pay, 2, '.', ',') }}</th>
            <th style="text-align: right">
                {{ number_format($total_sold, 2, '.', ',') }}</th>
            
            <th style="text-align: right">
                {{ $total_due < 0 ? number_format(abs($total_due), 2, '.', ',') : '' }}
            </th>
            <th style="text-align: right">
                {{ $total_due > 0 ? number_format(abs($total_due), 2, '.', ',') : '' }}
            </th>
        </tr>
    </tfoot>
</table>
