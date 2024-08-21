<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.last.transaction.report.print', [$branch_id, $customer_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h3 style="text-align: center">
            {{ $branch->name ?? 'All Branches' }}
        </h3>
        <h5 style="text-align: center;">Last Transaction of Customers with their Current Balances</h5>
    </caption>
    <thead>
        <tr>
            <th colspan="4"></th>
            <th colspan="2">BALANCE</th>
        </tr>
        <tr>
            <th>ACCOUNT NO</th>
            <th>CUSTOMER NAMES</th>
            <th>LAST DOCUMENT NO</th>
            <th>LAST DATE</th>
            <th>DR.</th>
            <th>CR.</th>
        </tr>
    </thead>
    @php
        $total_balance = 0;
    @endphp
    @foreach ($sales as $sale)
        @php

            $running_balance = $sale->balance;
            $total_balance += $running_balance;
        @endphp
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->customer }}</td>
            <td>{{ customerLastTransaction($sale->customer_id)?->reference }}</td>
            <td>{{ \Carbon\Carbon::parse(customerLastTransaction($sale->customer_id)?->date)->toFormattedDateString() }}
            </td>
            <td style="text-align: right">
                @if ($running_balance < 0)
                    {{ number_format(abs($running_balance), 2) }}
                @endif
            </td>
            <td style="text-align: right">
                @if ($running_balance > 0)
                    {{ number_format($running_balance, 2) }}
                @endif
            </td>

        </tr>
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="4">TOTAL</th>
            <th style="text-align: right">
                @if ($total_balance < 0)
                    ({{ number_format(abs($total_balance), 2) }})
                @endif
            </th>
            <th style="text-align: right">
                @if ($total_balance > 0)
                    {{ number_format($total_balance, 2) }}
                @endif
            </th>
        </tr>
    </tfoot>
</table>
