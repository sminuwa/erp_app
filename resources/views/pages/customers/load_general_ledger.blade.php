<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.print.ledger', [$from_date, $to_date, $customer->id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($customer->name) }} LEDGER LISTING BETWEEN {{ $from_date }}
            AND
            {{ $to_date }}<br /> Runining Balance B/d Before this date {{ $from_date }} was = @if ($balance_b_d < 0)
                &#8358;({{ number_format(abs($balance_b_d), 2) }})
            @else
                &#8358;{{ number_format($balance_b_d, 2) }}
            @endif
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>System/Invoice</th>
            <th>Ref</th>
            <th>Cr (&#8358;)</th>
            <th>Dr (&#8358;)</th>
            <th>Running Balance</th>
        </tr>
    </thead>
    <?php $sum_cr = $sum_cr_b_d;
    $sum_dr = $sum_dr_b_d;
    $dif = 0; ?>
    @foreach ($ledgers as $ledger)
        <tr>
            <td>{{ $ledger->date->toFormattedDateString() }}</td>
            <td>{{ $ledger->description }}</td>
            <td>{{ $ledger->systemid }}</td>
            <td>{{ $ledger->ref }}</td>
            <td style="text-align: right"> &#8358;{{ number_format($ledger->cr, 2) }}</td>
            <td style="text-align: right"> &#8358;{{ number_format($ledger->dr, 2) }}</td>
            <td style="text-align: right">
                <?php $sum_cr += $ledger->cr;
                $sum_dr += $ledger->dr;
                $dif = $sum_cr - $sum_dr;
                $balance =
                    $ledger
                        ->where('date', '<=', $ledger->date)
                        ->where('customer_id', $customer->id)
                        ->sum('cr') -
                    $ledger
                        ->where('date', '<=', $ledger->date)
                        ->where('customer_id', $customer->id)
                        ->sum('dr'); ?>
                @if ($dif < 0)
                    &#8358;({{ number_format(abs($dif), 2) }})
                @else
                    &#8358;{{ number_format($dif, 2) }}
                @endif
            </td>
        </tr>
    @endforeach
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th style="text-align: right;">&#8358;{{ number_format($sum_cr, 2) }}</th>
        <th style="text-align: right;">&#8358;{{ number_format($sum_dr, 2) }}</th>
        <th style="text-align: right">
            @if ($dif < 0)
                &#8358;({{ number_format(abs($dif), 2) }})
            @else
                &#8358;{{ number_format($dif, 2) }}
            @endif
        </th>
    </tr>
    <tr>
        <td colspan="7">
            <h5 style="text-align: center;">{{ strtoupper($customer->name) }} Closing Running Balance: = @if ($dif < 0)
                    &#8358;({{ number_format(abs($dif), 2) }})
                @else
                    &#8358;{{ number_format($dif, 2) }}
                @endif
            </h5>
        </td>
    </tr>
</table>
