<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.trial.balance.report', [$from_date, $to_date, $company_id, $branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            TRIAL BALANCE BETWEEN {{ Carbon\Carbon::parse($from_date)->toFormattedDateString() }} AND
            {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th colspan="4"></th>
            <th colspan="2" style="text-align: center; align-content: center">Balance</th>
        </tr>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th style="text-align: center; align-content: center">Total (Dr.)</th>
            <th style="text-align: center; align-content: center">Total (Cr.)</th>
            <th style="text-align: center; align-content: center">Dr.</th>
            <th style="text-align: center; align-content: center">Cr.</th>
        </tr>

    </thead>
    <tbody>
        @php
            $total_credit = $total_debit = $dr = $cr = 0;
        @endphp
        @foreach ($ledger2 as $ledger)
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
                    $diff = $ledger->credit - $ledger->debit;
                @endphp
                <td style="text-align: right">
                    @if ($diff < 0)
                        @php $dr+=$diff @endphp
                        {{ number_format(abs($diff), 2) }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($diff > 0)
                        @php $cr+=$diff @endphp
                        {{ number_format($diff, 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
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
                    $diff = $ledger->credit - $ledger->debit;
                @endphp
                <td style="text-align: right">
                    @if ($diff < 0)
                        @php $dr+=$diff @endphp
                        {{ number_format(abs($diff), 2) }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($diff > 0)
                        @php $cr+=$diff @endphp
                        {{ number_format($diff, 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
        @foreach ($ledger3 as $ledger)
            @php
                $credit = number_format(abs($ledger->credit), 2);
                $debit = number_format(abs($ledger->debit), 2);
            @endphp
            <tr>
                <td>A150001</td>
                <td>General Customer Control Account</td>
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
                    $diff = $ledger->credit - $ledger->debit;
                @endphp
                <td style="text-align: right">
                    @if ($diff < 0)
                        @php $dr+=$diff @endphp
                        {{ number_format(abs($diff), 2) }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($diff > 0)
                        @php $cr+=$diff @endphp
                        {{ number_format($diff, 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
        @foreach ($ledger4 as $ledger)
            @php
                $credit = number_format(abs($ledger->credit), 2);
                $debit = number_format(abs($ledger->debit), 2);
            @endphp
            <tr>
                <td>L220010</td>
                <td>Accounts Payable Control</td>
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
                    $diff = $ledger->credit - $ledger->debit;
                @endphp
                <td style="text-align: right">
                    @if ($diff < 0)
                        @php $dr+=$diff @endphp
                        {{ number_format(abs($diff), 2) }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($diff > 0)
                        @php $cr+=$diff @endphp
                        {{ number_format($diff, 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align: right;">Total</th>
            <th style="text-align: right;">{{ number_format($total_debit, 2) }}</th>
            <th style="text-align: right;">{{ number_format($total_credit, 2) }}</th>
            <th style="text-align: right;">
                {{ number_format(abs($dr), 2) }}</th>
            <th style="text-align: right;">
                {{ number_format(abs($cr), 2) }}</th>
        </tr>
    </tfoot>
</table>
