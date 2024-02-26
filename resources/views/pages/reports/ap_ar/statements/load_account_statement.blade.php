<div class="row">
    {{-- <div class="offset-10">
        <a href="{{ route('ajax.account.statement.report.print', [$from_date, $to_date, $branch_id,$payer_id, $type]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div> --}}
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            ACCOUNT STATEMENTS BETWEEN {{ Carbon\carbon::parse($from_date)->toFormattedDateString() }} AND
            {{ Carbon\carbon::parse($to_date)->toFormattedDateString() }}
            <br /> B/F = @if ($balance_b_d < 0)
                &#8358;({{ number_format(abs($balance_b_d), 2) }})
            @else
                &#8358;{{ number_format($balance_b_d, 2) }}
            @endif
            <br /> B/C = @if ($balance < 0)
                &#8358;({{ number_format(abs($balance), 2) }})
            @else
                &#8358;{{ number_format($balance, 2) }}
            @endif
        </h5>
    </caption>
    <?php $sum_cr = $sum_cr_b_d;
    $sum_dr = $sum_dr_b_d;
    $dif = 0; ?>
    <thead>
        <tr>
            <th rowspan="2" style="width: 5%;">S/N</th>
            <th rowspan="2">Date</th>
            <th rowspan="2">Account No</th>
            <th rowspan="2">Description</th>
            <th rowspan="2">Reference</th>
            <th colspan="2" style="text-align: center; align-content: center">Balance</th>
        </tr>
        <tr>
            <th style="text-align: center; align-content: center">Debit (Dr.)</th>
            <th style="text-align: center; align-content: center">Credit (Cr.)</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ledgers as $ledger)
            @php
                $credit = number_format($ledger->credit, 2);
                $debit = number_format($ledger->debit, 2);
            @endphp
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $ledger->date->toFormattedDateString() }}</td>

                <td>{{ $ledger->payer()->code ?? ($ledger->payer()->number ?? '') }}</td>
                <td>{{ $ledger->description }}</td>
                <td>{{ $ledger->reference }}</td>
                <td style="text-align: right">
                    @if ($debit > 0.0)
                        {{ $debit }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($credit > 0.0)
                        {{ $credit }}
                    @endif
                </td>
                <td style="text-align: right">
                    <?php $sum_cr += $ledger->credit;
                    $sum_dr += $ledger->debit;
                    $dif = $sum_cr - $sum_dr;
                    ?>
                    @if ($dif < 0)
                        ({{ number_format(abs($dif), 2) }})
                    @else
                        {{ number_format($dif, 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>

            <th colspan="5" style="text-align: right">Total</th>
            <th style="text-align: right;">&#8358;{{ number_format($credit_sum, 2) }}</th>
            <th style="text-align: right;">&#8358;{{ number_format($debit_sum, 2) }}</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: right">Balance C/F</th>
            <th colspan="3" style="text-align: right;">&#8358;{{ number_format($dif, 2) }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
