<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.remittance.report', [$from_date, $to_date, $branch_id, $payee_id, $user_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            DAILY REMITTANCE BETWEEN {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th>User</th>
            {{-- <th style="text-align: center; align-content: center">Total (Cr.)</th>
            <th style="text-align: center; align-content: center">Total (Dr.)</th> --}}
            <th style="text-align: center; align-content: center">Total</th>
        </tr>

    </thead>
    <tbody>
        @php
            $total_credit = $total_debit = $diff = 0;
        @endphp
        @foreach ($ledgers as $ledger)
            @php
                $credit = number_format($ledger->credit, 2);
                $debit = number_format($ledger->debit, 2);
            @endphp
            <tr>
                <td>{{ $ledger->number }}</td>
                <td>{{ $ledger->description }}</td>
                <td>{{ $ledger->name ?? '' }}</td>
                {{-- <td style="text-align: right">
                    @if ($credit > 0.0)
                         {{ $credit }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($debit > 0.0)
                         {{ $debit }}
                    @endif
                </td> --}}
                @php
                    $total_credit += $ledger->credit;
                    $total_debit += $ledger->debit;
                    $diff = $total_debit - $total_credit;
                @endphp
                @if ($diff > 0)
                    <td style="text-align: right;">{{ number_format($total_credit, 2) }}</td>
                @else
                    <td style="text-align: right;">({{ number_format($total_debit, 2) }})</td>
                @endif
            </tr>
        @endforeach
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right;">Total</th>
            {{-- <th style="text-align: right;">{{ number_format($total_credit, 2) }}</th>
            <th style="text-align: right;">{{ number_format($total_debit, 2) }}</th> --}}
            <th style="text-align: right;">{{ number_format($diff, 2) }}</th>
        </tr>
    </tfoot>
    </tbody>
</table>
