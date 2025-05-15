{{-- <div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.remittance.report', [$from_date, $to_date, $company_id, $branch_id, $payee_id, $user_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div> --}}
{{-- <table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            DAILY REMITTANCE BETWEEN {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th colspan="3"></th>
            <th colspan="2" style="text-align: center; align-content: center">Total Balance</th>
        </tr>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th>User</th>
            <th style="text-align: center; align-content: center">Total (Dr.)</th>
            <th style="text-align: center; align-content: center">Total (Cr.)</th>
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
            
                @php
                    $total_credit += $ledger->credit;
                    $total_debit += $ledger->debit;
                    $diff = $total_credit - $total_debit;
                @endphp

                <td style="text-align: right;">{{ $diff < 0 ? number_format(abs($total_credit), 2) : '' }}</td>

                <td style="text-align: right;">{{ $diff > 0 ? number_format(abs($total_debit), 2) : '' }}</td>
            </tr>
        @endforeach
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right;">Total</th>
            <th style="text-align: right;">{{ $diff < 0 ? number_format(abs($diff), 2) : '' }}</th>
            <th style="text-align: right;">{{ $diff > 0 ? number_format(abs($diff), 2) : '' }}</th>
        </tr>
    </tfoot>
    </tbody>
</table> --}}
<table class="display table table-bordered caption">
    <thead>
        <tr>
            <th>Date</th>
            <th>User</th>
            <th>Account Number</th>
            <th>Bank Account</th>
            <th>Credit</th>
            <th>Debit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ledgers as $ledger)
        <tr>
            <td>{{ Carbon\Carbon::parse($ledger->date)->toFormattedDateString() }}</td>
            <td>{{ $ledger->user_name }}</td>
            <td>{{ $ledger->number }}</td>
            <td>{{ $ledger->description }}</td>
            <td>{{ number_format($ledger->credit, 2) }}</td>
            <td>{{ number_format($ledger->debit, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">Total</td>
            <td>{{ number_format($credit_sum, 2) }}</td>
            <td>{{ number_format($debit_sum, 2) }}</td>
        </tr>
        {{-- <tr>
            <td colspan="4">Balance: </td>
            <td>Balance: {{ number_format($balance, 2) }}</td>
        </tr> --}}
    </tfoot>
</table>