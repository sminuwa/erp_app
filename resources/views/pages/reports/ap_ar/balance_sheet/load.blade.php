{{-- <div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.balance.sheet.report', [$to_date, $company_id, $branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">
            {{ strtoupper($branch->name ?? 'All Branches') }} <br>
            BALANCE SHEET AS AT {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th style="text-align: center;">Debit (Dr.)</th>
            <th style="text-align: center;">Credit (Cr.)</th>
        </tr>
    </thead>
    <tbody>
        <!-- Assets -->
        <tr>
            <th colspan="4">Assets</th>
        </tr>
        @php $total_assets = 0; @endphp
        @foreach ($assets as $asset)
            @php $balance = $asset->debit - $asset->credit; @endphp
            @if ($balance != 0)
                <tr>
                    <td>{{ $asset->number }}</td>
                    <td>{{ $asset->description }}</td>
                    <td style="text-align: right;">
                        {{ $balance > 0 ? number_format($balance, 2) : '' }}
                    </td>
                    <td style="text-align: right;">
                        {{ $balance < 0 ? number_format(abs($balance), 2) : '' }}
                    </td>
                </tr>
            @endif
            @php $total_assets += $balance; @endphp
        @endforeach
        <tr>
            <th colspan="2">Total Assets</th>
            <th style="text-align: right;">{{ number_format(max($total_assets, 0), 2) }}</th>
            <th style="text-align: right;">{{ number_format(max(-$total_assets, 0), 2) }}</th>
        </tr>

        <!-- Liabilities -->
        <tr>
            <th colspan="4">Liabilities</th>
        </tr>
        @php $total_liabilities = 0; @endphp
        @foreach ($liabilities as $liability)
            @php $balance = $liability->credit - $liability->debit; @endphp
            <tr>
                <td>{{ $liability->number }}</td>
                <td>{{ $liability->description }}</td>
                <td style="text-align: right;">
                    {{ $balance < 0 ? number_format($balance, 2) : '' }}
                </td>
                <td style="text-align: right;">
                    {{ $balance > 0 ? number_format(abs($balance), 2) : '' }}
                </td>
            </tr>
            @php $total_liabilities += $balance; @endphp
        @endforeach
        <tr>
            <th colspan="2">Total Liabilities</th>
            <th style="text-align: right;">
                @if ($total_liabilities < 0)
                    {{ number_format(max($total_liabilities, 0), 2) }}
                @endif
            </th>
            <th style="text-align: right;">
                @if ($total_liabilities > 0)
                    {{ number_format(max($total_liabilities, 0), 2) }}
                @endif
            </th>
        </tr>

        <!-- Equity -->
        <tr>
            <th colspan="4">Equity</th>
        </tr>
        @php $total_equity = 0; @endphp
        @foreach ($equity as $eq)
            @php
                if ($eq->number == 'E300004') {
                    $balance = $eq->credit - $eq->debit + $retainedEarningsFromLastYear; // + $retainedEarningsFromSelectedYear;
                } else {
                    $balance = $eq->credit - $eq->debit;
                }
                $total_equity += $balance;
            @endphp
            <tr>
                <td>{{ $eq->number }}</td>
                <td>{{ $eq->description }}</td>
                <td style="text-align: right;">
                    {{ $balance < 0 ? number_format($balance, 2) : '' }}
                </td>
                <td style="text-align: right;">
                    {{ $balance > 0 ? number_format(abs($balance), 2) : '' }}
                </td>
            </tr>
        @endforeach
        <tr>
            <th colspan="2">Total Equity</th>
            <th style="text-align: right;">
                @if ($total_equity < 0)
                    {{ number_format(max($total_equity, 0), 2) }}
                @endif
            </th>
            <th style="text-align: right;">
                @if ($total_equity > 0)
                    {{ number_format(max($total_equity, 0), 2) }}
                @endif
            </th>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Net Profit/Loss for Current Year</th>
            <th style="text-align: right;">
                {{ $retainedEarningsFromSelectedYear < 0 ? number_format(abs($retainedEarningsFromSelectedYear), 2) : '' }}
            </th>
            <th style="text-align: right;">
                {{ $retainedEarningsFromSelectedYear > 0 ? number_format(abs($retainedEarningsFromSelectedYear), 2) : '' }}
            </th>
        </tr>
        <tr>
            <th colspan="2">Total Liabilities + Equity + Profit</th>
            <td></td>
            <th style="text-align: right;">
                
                {{ number_format($total_equity + $total_liabilities + $retainedEarningsFromSelectedYear, 2) }}
            </th>
        </tr>
        @php
            $diff = $total_assets - ($total_liabilities + $total_equity + $retainedEarningsFromSelectedYear);
        @endphp
        @if (abs($diff) > 10)
            <tr>
                <th colspan="2">Disparity</th>
                <th colspan="2" style="text-align: center; color: red;">
                    {{ number_format(abs($diff), 2) }}
                </th>
            </tr>
        @endif
    </tfoot>
</table>

@if (abs($total_assets - ($total_liabilities + $total_equity + $retainedEarningsFromSelectedYear)) < 10)
    <div class="alert alert-success text-center mt-3">
        ✅ Balance Sheet is Balanced
    </div>
@else
    <div class="alert alert-danger text-center mt-3">
        ❌ Balance Sheet Not Balanced!
    </div>
@endif --}}
<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.balance.sheet.report', [$to_date, $company_id, $branch_id]) }}" target="_BLANK"
           class="btn-success btn btn-sm">Print</a>
    </div>
</div>

<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">
            {{ strtoupper($branch->name ?? 'All Branches') }} <br>
            BALANCE SHEET AS AT {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>

    <thead>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th style="text-align: center;">Debit (Dr.)</th>
            <th style="text-align: center;">Credit (Cr.)</th>
        </tr>
    </thead>

    <tbody>
        {{-- ASSETS --}}
        <tr><th colspan="4">Assets</th></tr>
        @php $total_assets = 0; @endphp
        @foreach ($assets as $asset)
            @php $balance = $asset->debit - $asset->credit; @endphp
            @if ($balance != 0)
                <tr>
                    <td>{{ $asset->number }}</td>
                    <td>{{ $asset->description }}</td>
                    <td style="text-align:right;">{{ $balance > 0 ? number_format(abs($balance), 2) : '' }}</td>
                    <td style="text-align:right;">{{ $balance < 0 ? number_format(abs($balance), 2) : '' }}</td>
                </tr>
            @endif
            @php $total_assets += $balance; @endphp
        @endforeach
        <tr>
            <th colspan="2">Total Assets</th>
            <th style="text-align:right;">{{ $total_assets > 0 ? number_format(abs($total_assets), 2) : '' }}</th>
            <th style="text-align:right;">{{ $total_assets < 0 ? number_format(abs($total_assets), 2) : '' }}</th>
        </tr>

        {{-- LIABILITIES --}}
        <tr><th colspan="4">Liabilities</th></tr>
        @php $total_liabilities = 0; @endphp
        @foreach ($liabilities as $liability)
            @php $balance = $liability->credit - $liability->debit; @endphp
            <tr>
                <td>{{ $liability->number }}</td>
                <td>{{ $liability->description }}</td>
                <td style="text-align:right;">{{ $balance < 0 ? number_format(abs($balance), 2) : '' }}</td>
                <td style="text-align:right;">{{ $balance > 0 ? number_format(abs($balance), 2) : '' }}</td>
            </tr>
            @php $total_liabilities += $balance; @endphp
        @endforeach
        <tr>
            <th colspan="2">Total Liabilities</th>
            <th style="text-align:right;">{{ $total_liabilities < 0 ? number_format(abs($total_liabilities), 2) : '' }}</th>
            <th style="text-align:right;">{{ $total_liabilities > 0 ? number_format(abs($total_liabilities), 2) : '' }}</th>
        </tr>

        {{-- EQUITY --}}
        <tr><th colspan="4">Equity</th></tr>
        @php $total_equity = 0; @endphp
        @foreach ($equity as $eq)
            @php
                // Equity is credit-natured
                $balance = ($eq->credit - $eq->debit);
                if ($eq->number == 'E300004') {
                    // Special retained earnings from last year line merged here
                    $balance = ($eq->credit - $eq->debit) + $retainedEarningsFromLastYear;
                }
                $total_equity += $balance;
            @endphp
            <tr>
                <td>{{ $eq->number }}</td>
                <td>{{ $eq->description }}</td>
                <td style="text-align:right;">{{ $balance < 0 ? number_format(abs($balance), 2) : '' }}</td>
                <td style="text-align:right;">{{ $balance > 0 ? number_format(abs($balance), 2) : '' }}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="2">Total Equity</th>
            <th style="text-align:right;">{{ $total_equity < 0 ? number_format(abs($total_equity), 2) : '' }}</th>
            <th style="text-align:right;">{{ $total_equity > 0 ? number_format(abs($total_equity), 2) : '' }}</th>
        </tr>
    </tbody>

    <tfoot>
        {{-- Current Year Net Profit/Loss (credit-natured if profit) --}}
        <tr>
            <th colspan="2">Net Profit/Loss for Current Year</th>
            <th style="text-align:right;">{{ $retainedEarningsFromSelectedYear < 0 ? number_format(abs($retainedEarningsFromSelectedYear), 2) : '' }}</th>
            <th style="text-align:right;">{{ $retainedEarningsFromSelectedYear > 0 ? number_format(abs($retainedEarningsFromSelectedYear), 2) : '' }}</th>
        </tr>

        {{-- RHS total: Liabilities + Equity + Profit (show as positive on Credit side) --}}
        <tr>
            <th colspan="2">Total Liabilities + Equity + Profit</th>
            <td></td>
            <th style="text-align:right;">
                {{ number_format(abs($total_liabilities + $total_equity + $retainedEarningsFromSelectedYear), 2) }}
            </th>
        </tr>

        @php
            $diff = $total_assets - ($total_liabilities + $total_equity + $retainedEarningsFromSelectedYear);
        @endphp
        @if (abs($diff) > 10)
            <tr>
                <th colspan="2">Disparity</th>
                <th colspan="2" style="text-align:center; color:red;">
                    {{ number_format(abs($diff), 2) }}
                </th>
            </tr>
        @endif
    </tfoot>
</table>

@if (abs($total_assets - ($total_liabilities + $total_equity + $retainedEarningsFromSelectedYear)) < 10)
    <div class="alert alert-success text-center mt-3">✅ Balance Sheet is Balanced</div>
@else
    <div class="alert alert-danger text-center mt-3">❌ Balance Sheet Not Balanced!</div>
@endif

