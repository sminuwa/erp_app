<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.balance.sheet.report', [$to_date, $branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            BALANCE SHEET AS AT {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th colspan="4"></th>
            <th style="text-align: center; align-content: center" colspan="2">Balance</th>
        </tr>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th style="text-align: center; align-content: center">Total (Dr.)</th>
            <th style="text-align: center; align-content: center">Total (Cr.)</th>
            <th>Dr.</th>
            <th>Cr.</th>
        </tr>

    </thead>
    <tbody>
        @php
            $total_credit = $total_debit = 0;
        @endphp
        @foreach ($ledger1 as $ledger)
            @php
                $credit = number_format(abs($ledger->credit), 2);
                $debit = number_format(abs($ledger->debit), 2);
            @endphp
            <tr>
                <td>{{ $ledger->number }}</td>
                <td>{{ $ledger->description }}</td>
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

                @php
                    $total_credit += $ledger->credit;
                    $total_debit += $ledger->debit;
                    $diff = $ledger->debit -$ledger->credit;
                @endphp
                <td style="text-align: right;">
                    @if ($diff < 0)
                        {{ number_format(abs($diff), 2) }}
                    @endif
                </td>
                <td style="text-align: right;">
                    @if ($diff > 0)
                        {{ number_format(abs($diff), 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align: right;">Total</th>
            <th style="text-align: right;">{{ number_format($total_credit, 2) }}</th>
            <th style="text-align: right;">{{ number_format($total_debit, 2) }}</th>
            <th style="text-align: right;">
                {{ $total_debit - $total_credit < 0 ? number_format(abs($total_debit - $total_credit), 2) : '' }}</th>
            <th style="text-align: right;">
                {{ $total_debit - $total_credit > 0 ? number_format($total_debit - $total_credit, 2) : '' }}</th>
        </tr>
    </tfoot>
</table>
