{{-- <div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.account.balance.report.print', [$date, $type, $branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div> --}}
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            ACCOUNT BALANCES AS AT {{ Carbon\carbon::parse($date)->toFormattedDateString() }}
            @if ($balance < 0)
                {{ number_format(abs($balance), 2) }}Dr.
            @else
                {{ number_format($balance, 2) }}Cr.
            @endif
        </h5>
    </caption>
    <?php
    $sum_cr = $sum_dr = $dif = $total_cr = $total_dr = 0;
    ?>
    <thead>
        <tr>
            <th colspan="{{ $type == 'Customer' ? 3 : 2 }}"></th>
            <th style="text-align: center; align-content: center" colspan="2"></th>
            <th colspan="2" style="text-align: center; align-content: center">Balance</th>
        </tr>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            @if ($type == 'Customer')
                <th>RO</th>
            @endif
            <th style="text-align: center; align-content: center">Debit (Dr.)</th>
            <th style="text-align: center; align-content: center">Credit (Cr.)</th>
            <th>Dr.</th>
            <th>Cr.</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ledgers as $ledger)
            @php
                $credit = number_format(abs($ledger->credit), 2);
                $debit = number_format(abs($ledger->debit), 2);
            @endphp
            <tr>
                <td>{{ $ledger->number }}</td>
                <td>{{ $ledger->description }}</td>
                @if ($type == 'Customer')
                    <td>{{ $ledger->name }}</td>
                @endif
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
                    <?php $sum_cr = $ledger->credit;
                    $sum_dr = $ledger->debit;
                    $total_cr += $sum_cr;
                    $total_dr += $sum_dr;
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
            <th colspan="{{ $type == 'Customer' ? 3 : 2 }}" style="text-align: right">Total</th>
            <th style="text-align: right;">{{ number_format($total_dr, 2) }}</th>
            <th style="text-align: right;">{{ number_format($total_cr, 2) }}</th>
            <th style="text-align: right;">
                {{  $total_cr - $total_dr  < 0 ? number_format(abs($total_cr - $total_dr), 2) : '' }}
            </th>
            <th style="text-align: right;">
                {{ $total_cr - $total_dr > 0 ? number_format(abs($total_cr - $total_dr), 2) : '' }}
            </th>
        </tr>
        {{-- <tr>
            <th colspan="4" style="text-align: right">Balance C/F</th>
            <th style="text-align: right;">{{ number_format($balance, 2) }}</th>
        </tr> --}}
    </tfoot>
</table>
