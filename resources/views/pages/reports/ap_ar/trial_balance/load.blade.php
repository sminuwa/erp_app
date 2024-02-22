<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.trial.balance.report', [$from_date, $to_date, $branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            TRIAL BALANCE BETWEEN {{ $from_date }} AND {{ $to_date }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th style="text-align: center; align-content: center">Total (Dr.)</th>
            <th style="text-align: center; align-content: center">Total (Cr.)</th>
            <th style="text-align: center; align-content: center">Balance</th>
        </tr>

    </thead>
    <tbody>
        @php
            $total_credit = $total_debit = 0;
        @endphp
        @foreach ($ledger1 as $ledger)
            @php
                $credit = number_format($ledger->credit, 2);
                $debit = number_format($ledger->debit, 2);
            @endphp
            <tr>
                <td>{{ $ledger->number }}</td>
                <td>{{ $ledger->description }}</td>
                <td style="text-align: right">
                    @if ($debit > 0.0)
                        &#8358; {{ $debit }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($credit > 0.0)
                        &#8358; {{ $credit }}
                    @endif
                </td>

                @php
                    $total_credit += $ledger->credit;
                    $total_debit += $ledger->debit;
                    $diff = $ledger->credit - $ledger->debit;
                @endphp
                @if ($diff >= 0)
                    <td style="text-align: right;">{{ number_format($diff, 2) }}</td>
                @else
                    <td style="text-align: right;">({{ number_format(abs($diff), 2) }})</td>
                @endif
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align: right;">Total</th>
            <th style="text-align: right;">&#8358;{{ number_format($total_credit, 2) }}</th>
            <th style="text-align: right;">&#8358;{{ number_format($total_debit, 2) }}</th>
            <th style="text-align: right;">&#8358;{{ number_format($total_credit - $total_debit, 2) }}</th>
        </tr>
    </tfoot>
</table>

<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            TRIAL BALANCE AS AT {{ $to_date }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th style="text-align: center; align-content: center">Total (Dr.)</th>
            <th style="text-align: center; align-content: center">Total (Cr.)</th>
            <th style="text-align: center; align-content: center">Balance</th>
        </tr>

    </thead>
    <tbody>
        @php
            $total_credit = $total_debit = 0;
        @endphp
        @foreach ($ledger2 as $ledger)
            @php
                $credit = number_format($ledger->credit, 2);
                $debit = number_format($ledger->debit, 2);
            @endphp
            <tr>
                <td>{{ $ledger->number }}</td>
                <td>{{ $ledger->description }}</td>
                <td style="text-align: right">
                    @if ($debit > 0.0)
                        &#8358; {{ $debit }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($credit > 0.0)
                        &#8358; {{ $credit }}
                    @endif
                </td>

                @php
                    $total_credit += $ledger->credit;
                    $total_debit += $ledger->debit;
                    $diff = $ledger->credit - $ledger->debit;
                @endphp
                @if ($diff >= 0)
                    <td style="text-align: right;">{{ number_format($diff, 2) }}</td>
                @else
                    <td style="text-align: right;">({{ number_format(abs($diff), 2) }})</td>
                @endif
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align: right;">Total</th>
            <th style="text-align: right;">&#8358;{{ number_format($total_credit, 2) }}</th>
            <th style="text-align: right;">&#8358;{{ number_format($total_debit, 2) }}</th>
            <th style="text-align: right;">&#8358;{{ number_format($total_credit - $total_debit, 2) }}</th>
        </tr>
    </tfoot>
</table>
