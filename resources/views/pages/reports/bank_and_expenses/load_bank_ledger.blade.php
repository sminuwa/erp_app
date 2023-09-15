<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.bank.print.ledger', [$from_date, $to_date, $bank_account->id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">BANK NAME: {{ strtoupper($bank_account->account_name) }} LEDGER LISTING BETWEEN {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}<br /> Runining Balance B/d Before this date {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }} was = @if ($balance_b_d < 0)
                &#8358;({{ number_format(abs($balance_b_d), 2) }})
            @else
                &#8358;{{ number_format($balance_b_d, 2) }}
            @endif
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Date</th>
            <th>Transaction Ref.</th>
            <th>Cr (&#8358;)</th>
            <th>Dr (&#8358;)</th>
            <th>Bank Balance</th>
        </tr>
    </thead>
    <?php $sum_cr = $sum_cr_b_d;
    $sum_dr = $sum_dr_b_d;
    $dif = 0; ?>
    @foreach ($ledgers as $ledger)
        <tr>
            <td>{{ \Carbon\Carbon::parse($ledger->trans_date)->toFormattedDateString() }}</td>
            <td>{{ $ledger->ref_no }}</td>
            <td style="text-align: right"> &#8358;{{ number_format($ledger->cr, 2) }}</td>
            <td style="text-align: right"> &#8358;{{ number_format($ledger->dr, 2) }}</td>
            <td style="text-align: right">
                <?php $sum_cr += $ledger->cr;
                $sum_dr += $ledger->dr;
                $dif = $sum_cr - $sum_dr;
                $balance =
                    $ledger
                        ->where('id', '<=', $ledger->id)
                        ->where('bank_account_id', $bank_account->id)
                        ->sum('cr') -
                    $ledger
                        ->where('id', '<=', $ledger->id)
                        ->where('bank_account_id', $bank_account->id)
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
            <h5 style="text-align: center;">{{ strtoupper($bank_account->account_name) }} Closing Running Balance: = @if ($dif < 0)
                    &#8358;({{ number_format(abs($dif), 2) }})
                @else
                    &#8358;{{ number_format($dif, 2) }}
                @endif
            </h5>
        </td>
    </tr>
</table>
