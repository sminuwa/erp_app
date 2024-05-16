<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.balance.details.report.print', [$from_date, $to_date, $customer_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Customer Balance Details Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
        <h5 style="text-align: center;">
            CUSTOMER NAME: {{ \App\Models\Customer::find($customer_id)->name }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>FOLIO</th>
            <th>PARTICULARS</th>
            <th>AMOUNT OWED</th>
            <th>PAYMENT</th>
            <th>RUNNING BALANCE</th>
        </tr>
    </thead>
    @php
        $running_balance = 0;
        $total_credit = 0;
        $total_debit = 0;
    @endphp
    @foreach ($sales as $sale)
        @php
            $total_credit += $sale->cr;
            $total_debit += $sale->dr;
            $running_balance += $total_credit - $total_debit;
        @endphp
        <tr>

            <td>{{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}</td>
            <td>{{ $sale->receipt_no != null ? $sale->receipt_no : $sale->systemid }}</td>
            <td>{{ $sale->description }}</td>
            <td style="text-align: right">{{ number_format($sale->cr, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->dr, 2, '.', ',') }}</td>
            <td style="text-align: right">
                @if ($running_balance < 0)
                    ({{ number_format(abs($running_balance), 2) }})
                @else
                    {{ number_format($running_balance, 2) }}
                @endif
            </td>

        </tr>
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="3">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_credit, 2, '.', ',') }}</th>
            <th style="text-align: right">
                {{ number_format($total_debit, 2, '.', ',') }}</th>
            <th style="text-align: right">
                @if ($running_balance < 0)
                    ({{ number_format(abs($running_balance), 2) }})
                @else
                    {{ number_format($running_balance, 2) }}
                @endif
            </th>
        </tr>
    </tfoot>
</table>
