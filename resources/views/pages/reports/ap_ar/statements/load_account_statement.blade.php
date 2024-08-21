<div class="row">
    {{-- <div class="offset-10">
        <a href="{{ route('ajax.account.statement.report.print', [$from_date, $to_date, $branch_id,$payer_id, $type]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div> --}}
</div>
<div class="table-responsive">
    <table class="display table table-bordered caption">
        <caption style="caption-size:top">
            <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
                ACCOUNT STATEMENTS BETWEEN {{ Carbon\carbon::parse($from_date)->toFormattedDateString() }} TO
                {{ Carbon\carbon::parse($to_date)->toFormattedDateString() }}
                <br /> B/F = @if ($balance_b_d < 0)
                    {{ number_format(abs($balance_b_d), 2) }}Dr.
                @else
                    {{ number_format($balance_b_d, 2) }}Cr.
                @endif
                <br /> B/C = @if ($balance < 0)
                    {{ number_format(abs($balance), 2) }}Dr.
                @else
                    {{ number_format($balance, 2) }}Cr.
                @endif
            </h5>
        </caption>
        <?php $sum_cr = $sum_cr_b_d;
        $sum_dr = $sum_dr_b_d;
        $dif = 0; ?>
        <thead>
            {{-- <tr>
                <th colspan="7"></th>
                <th colspan="2" style="text-align: center; align-content: center">Balance</th>
            </tr> --}}
            <tr>
                <th rowspan="2" style="width: 5%;">S/N</th>
                <th rowspan="2">Date</th>
                <th rowspan="2">Account No</th>
                <th rowspan="2">Description</th>
                <th rowspan="2">Reference</th>
            </tr>
            <tr>
                <th style="text-align: center; align-content: center">Debit (Dr.)</th>
                <th style="text-align: center; align-content: center">Credit (Cr.)</th>
                <th>Dr.</th>
                <th>Cr.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ledgers as $ledger)
                @php
                    $credit = $ledger->credit;
                    $debit = $ledger->debit;
                @endphp
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $ledger->date->toFormattedDateString() }}</td>

                    <td>{{ $ledger->payer()->code ?? ($ledger->payer()->number ?? '') }}</td>
                    <td>{{ transactionDecription($ledger->reference) != '' ? transactionDecription($ledger->reference) : $ledger->description }}
                    </td>
                    <td>{{ $ledger->reference }}</td>
                    <td style="text-align: right">
                        @if ($debit > 0.0)
                            {{ number_format(abs($debit), 2) }}
                        @endif
                    </td>
                    <td style="text-align: right">
                        @if ($credit > 0.0)
                            {{ number_format(abs($credit), 2) }}
                        @endif
                    </td>

                    <?php $sum_cr += $ledger->credit;
                    $sum_dr += $ledger->debit;
                    $dif = $sum_cr - $sum_dr;
                    ?>
                    <td style="text-align: right">
                        @if ($dif < 0)
                            {{ number_format(abs($dif), 2) }}
                        @endif
                    </td>
                    <td style="text-align: right">
                        @if ($dif > 0)
                            {{ number_format($dif, 2) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>

                <th colspan="5" style="text-align: right">Total</th>
                <th style="text-align: right;">{{ number_format(abs($debit_sum), 2) }}</th>
                <th style="text-align: right;">{{ number_format(abs($credit_sum), 2) }}</th>
                <th style="text-align: right;">{{ $dif < 0 ? number_format(abs($dif), 2) : '' }}</th>
                <th style="text-align: right;">{{ $dif > 0 ? number_format(abs($dif), 2) : '' }}</th>
            </tr>
            {{-- <tr>
                <th colspan="4" style="text-align: right">Balance C/F</th>

            </tr> --}}
        </tfoot>
    </table>
</div>