<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.balance.sheet.report', [$to_date, $company_id, $branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>

{{-- <table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
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
        @php
            $total_assets = 0;
        @endphp
        @foreach ($assets as $asset)
            @if ($asset->debit - $asset->credit != 0)
                <tr>
                    <td>{{ $asset->number }}</td>
                    <td>{{ $asset->description }}</td>
                    <td style="text-align: right;">
                        {{ $asset->debit - $asset->credit > 0 ? number_format(abs($asset->debit - $asset->credit), 2) : '' }}
                    </td>
                    <td>{{ $asset->debit - $asset->credit < 0 ? number_format(abs($asset->debit - $asset->credit), 2) : '' }}
                    </td>
                </tr>
            @endif
            @php
                $total_assets += $asset->debit - $asset->credit;
            @endphp
        @endforeach
        <tr>
            <th colspan="2">Total Assets</th>
            <th style="text-align: right;">{{ number_format(abs($total_assets), 2) }}</th>
            <td></td>
        </tr>

        <!-- Liabilities -->
        <tr>
            <th colspan="4">Liabilities</th>
        </tr>
        @php
            $total_liabilities = 0;
        @endphp
        @foreach ($liabilities as $liability)
            <tr>
                <td>{{ $liability->number }}</td>
                <td>{{ $liability->description }}</td>
                <td style="text-align: right;">
                    {{ $liability->debit - $liability->credit > 0 ? number_format(abs($liability->debit - $liability->credit), 2) : '' }}
                </td>
                <td style="text-align: right;">
                    {{ $liability->debit - $liability->credit < 0 ? number_format(abs($liability->debit - $liability->credit), 2) : '' }}
                </td>
            </tr>
            @php
                $total_liabilities += $liability->debit - $liability->credit;
            @endphp
        @endforeach
        <tr>
            <th colspan="2">Total Liabilities</th>
            <td style="text-align: right;">
                {{ $total_liabilities > 0 ? number_format(abs($total_liabilities), 2) : '' }}
            </td>
            <th style="text-align: right;">
                {{ $total_liabilities < 0 ? number_format(abs($total_liabilities), 2) : '' }}
            </th>
        </tr>

        <!-- Equity -->
        <tr>
            <th colspan="4">Equity</th>
        </tr>
        @php
            $total_equity = 0;
            $equity_value = 0;
        @endphp
        @foreach ($equity as $eq)
            <tr>
                <td>{{ $eq->number }}</td>
                <td>{{ $eq->description }}</td>

                @php

                @endphp
                @if ($eq->number == 'E300004')
                    @php $equity_value = abs($eq->credit - $eq->debit) + $retainedEarningsFromLastYear; @endphp
                @else
                    @php $equity_value = $eq->credit - $eq->debit; @endphp
                @endif
                <td style="text-align: right;">
                    {{ $equity_value > 0 ? number_format(abs($equity_value), 2) : '' }}</td>
                <td style="text-align: right;">
                    {{ $equity_value < 0 ? number_format(abs($equity_value), 2) : '' }}</td>
                @php
                    $total_equity += $equity_value;
                @endphp
            </tr>
        @endforeach

        <tr>
            <th colspan="2">Total Equity</th>
            <th style="text-align: right;">{{ $total_equity > 0 ? number_format(abs($total_equity), 2) : '' }}</th>
            <th style="text-align: right;">{{ $total_equity < 0 ? number_format(abs($total_equity), 2) : '' }}</th>
        </tr>
        <!-- Retained Earnings -->
        
    </tbody>
    <tfoot>
        
        <tr>
            <th colspan="2">Net Profit/Loss for Current Year</th>
            <th style="text-align: right;">
                {{ $retainedEarningsFromSelectedYear > 0 ? number_format(abs($retainedEarningsFromSelectedYear), 2) : '' }}
            </th>
            <th style="text-align: right;">
                {{ $retainedEarningsFromSelectedYear < 0 ? number_format(abs($retainedEarningsFromSelectedYear), 2) : '' }}
            </th>
        </tr>
        <tr>
            <th colspan="2">Total </th>
            <td></td>
            <th style="text-align: right;">
                {{ number_format(abs($retainedEarningsFromSelectedYear) + abs($total_equity) + abs($total_liabilities), 2) }}
            </th>
        </tr>
    </tfoot>
</table> --}}
<table class="display table table-bordered caption" id="example1" data-ordering="true">
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
        <tr><th colspan="4">Assets</th></tr>
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
        <tr><th colspan="4">Liabilities</th></tr>
        @php $total_liabilities = 0; @endphp
        @foreach ($liabilities as $liability)
            @php $balance = $liability->debit - $liability->credit; @endphp
            <tr>
                <td>{{ $liability->number }}</td>
                <td>{{ $liability->description }}</td>
                <td style="text-align: right;">
                    {{ $balance > 0 ? number_format($balance, 2) : '' }}
                </td>
                <td style="text-align: right;">
                    {{ $balance < 0 ? number_format(abs($balance), 2) : '' }}
                </td>
            </tr>
            @php $total_liabilities += $balance; @endphp
        @endforeach
        <tr>
            <th colspan="2">Total Liabilities</th>
            <th style="text-align: right;">{{ number_format(max($total_liabilities, 0), 2) }}</th>
            <th style="text-align: right;">{{ number_format(max(-$total_liabilities, 0), 2) }}</th>
        </tr>

        <!-- Equity -->
        <tr><th colspan="4">Equity</th></tr>
        @php $total_equity = 0; @endphp
        @foreach ($equity as $eq)
            @php
                if ($eq->number == 'E300004') {
                    $balance = $eq->credit - $eq->debit + $retainedEarningsFromLastYear + $retainedEarningsFromSelectedYear;
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
            <th style="text-align: right;">{{ number_format(max($total_equity, 0), 2) }}</th>
            <th style="text-align: right;">{{ number_format(max(-$total_equity, 0), 2) }}</th>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Net Profit/Loss for Current Year</th>
            <th style="text-align: right;">
                {{ $retainedEarningsFromSelectedYear > 0 ? number_format($retainedEarningsFromSelectedYear, 2) : '' }}
            </th>
            <th style="text-align: right;">
                {{ $retainedEarningsFromSelectedYear < 0 ? number_format(abs($retainedEarningsFromSelectedYear), 2) : '' }}
            </th>
        </tr>
        <tr>
            <th colspan="2">Total Liabilities + Equity</th>
            <td></td>
            <th style="text-align: right;">
                {{ number_format(abs($total_equity + $total_liabilities + $retainedEarningsFromSelectedYear), 2) }}
            </th>
        </tr>
        @php
            $diff = $total_assets - ($total_liabilities + $total_equity + $retainedEarningsFromSelectedYear);
        @endphp
        @if (abs($diff) > 0.01)
        <tr>
            <th colspan="2">Disparity</th>
            <th colspan="2" style="text-align: center; color: red;">
                {{ number_format(abs($diff), 2) }}
            </th>
        </tr>
        @endif
    </tfoot>
</table>

@if (abs($total_assets - ($total_liabilities + $total_equity + $retainedEarningsFromSelectedYear)) < 0.01)
    <div class="alert alert-success text-center mt-3">
        ✅ Balance Sheet is Balanced
    </div>
@else
    <div class="alert alert-danger text-center mt-3">
        ❌ Balance Sheet Not Balanced!
    </div>
@endif


