<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.last.transaction.report.print', [$from_date, $to_date, $customer_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Balances of Customers that have Not Transacted for a Period of Time
        </h5>
        <h5 style="text-align: center;">
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>LAST DATE</th>
            <th>CUSTOMER NAMES</th>
            <th>BALANCE</th>
        </tr>
    </thead>
    @php
        $running_balance = 0;
    @endphp
    @foreach ($sales as $sale)
        @php
           
            $running_balance += $sale->balance;
        @endphp
        <tr>

            <td>{{ \Carbon\Carbon::parse($sale->last_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->customer }}</td>
            <td style="text-align: right">
                @if ($running_balance < 0)
                    &#8358;({{ number_format(abs($running_balance), 2) }})
                @else
                    &#8358;{{ number_format($running_balance, 2) }}
                @endif
            </td>

        </tr>
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="2">TOTAL</th>
            <th style="text-align: right">
                @if ($running_balance < 0)
                    &#8358;({{ number_format(abs($running_balance), 2) }})
                @else
                    &#8358;{{ number_format($running_balance, 2) }}
                @endif
            </th>
        </tr>
    </tfoot>
</table>
