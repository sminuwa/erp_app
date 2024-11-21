<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.income.statement.report.print', [$from_month, $to_month, $income_year, $company_id, $branch_id, $category_id1, $category_id2]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>

<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            INCOME STATEMENT FROM {{ $from_month == 'all' ? 'January' : monthName($from_month) }} AND
            {{ $to_month == 'all' ? 'December' : monthName($to_month) }}
        </h5>
    </caption>
    <?php
    $total_revenue = 0;
    $credit_sum = 0;
    $debit_sum = 0;
    $other_income = 0;
    $dr = 0;
    $cr = 0;
    ?>
    <thead>
        <tr>
            <th colspan="4"></th>
            <th colspan="2" style="text-align: center; align-content: center">Balance</th>
        </tr>
        <tr>
            <th>Account No</th>
            <th>Description</th>
            <th style="text-align: center; align-content: center">Debit (Dr.)</th>
            <th style="text-align: center; align-content: center">Credit (Cr.)</th>
            <th style="text-align: center; align-content: center">Dr.</th>
            <th style="text-align: center; align-content: center">Cr.</th>
        </tr>

    </thead>
    <tbody>
        <tr>
            <th colspan="6">
                <h3>SALES REVENUE</h3>
            </th>
        </tr>
        @foreach ($revenues as $revenue)
            @php
                $credit = number_format(abs($revenue->credit), 2);
                $debit = number_format(abs($revenue->debit), 2);
                $credit_sum += $revenue->credit;
                $debit_sum += $revenue->debit;
                $dif = $revenue->credit - $revenue->debit;
            @endphp
            <tr>
                <td>{{ $revenue->number }}</td>
                <td>{{ $revenue->description }}</td>
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
                    @if ($dif < 0)
                        @php $dr += $revenue->debit @endphp
                        {{ number_format(abs($dif), 2) }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($dif > 0)
                        @php $cr += $revenue->credit @endphp
                        {{ number_format(abs($dif), 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <th colspan="2" style="text-align: left">TOTAL REVENUE</th>
            @php $total_revenue = $credit_sum - $debit_sum;  @endphp
            <th style="text-align: right;">{{ number_format(abs($debit_sum), 2) }}</th>
            <th style="text-align: right;">{{ number_format(abs($credit_sum), 2) }}</th>
            <th style="text-align: right;">
                @if ($total_revenue < 0)
                    {{ number_format(abs($total_revenue), 2) }}
                @endif
            </th>
            <th style="text-align: right;">
                @if ($total_revenue > 0)
                    {{ number_format(abs($total_revenue), 2) }}
                @endif
            </th>
        </tr>
        <?php
        $total_cost_of_sale = 0;
        $credit_sum = 0;
        $debit_sum = 0;
        $dr = 0;
        $cr = 0;
        ?>
        <tr>
            <th colspan="6">
                <h3>COST OF SALES</h3>
            </th>
        </tr>
        @foreach ($cost_of_sales as $sale)
            @php
                $credit = number_format(abs($sale->credit), 2);
                $debit = number_format(abs($sale->debit), 2);
            @endphp
            <tr>
                <td>{{ $sale->number }}</td>
                <td>{{ $sale->description }}</td>
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

                <?php
                $credit_sum += $sale->credit;
                $debit_sum += $sale->debit;
                $dif = $sale->credit - $sale->debit;
                ?>
                <td style="text-align: right">
                    @if ($dif < 0)
                        @php $dr += $sale->debit @endphp
                        {{ number_format(abs($dif), 2) }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($dif > 0)
                        @php $cr += $sale->credit @endphp
                        {{ number_format(abs($dif), 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <th colspan="2" style="text-align: left">TOTAL COST</th>
            @php $total_cost =  $credit_sum - $debit_sum;  @endphp
            <th style="text-align: right;">{{ number_format(abs($debit_sum), 2) }}</th>
            <th style="text-align: right;">{{ number_format(abs($credit_sum), 2) }}</th>
            {{-- <th style="text-align: right;">{{ number_format(abs($dr), 2) }}</th>
            <th style="text-align: right;">{{ number_format(abs($cr), 2) }}</th> --}}
            <th style="text-align: right;">
                {{ $total_cost < 0 ? number_format(abs($total_cost), 2) : '' }}
            </th>
            <th style="text-align: right;">
                {{ $total_cost > 0 ? number_format(abs($total_cost), 2) : '' }}
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: left">GROSS MARGIN</th>
            @php $gross_profit_loss = $total_revenue - abs($total_cost)  @endphp
            <th style="text-align: right;">
                {{ $gross_profit_loss < 0 ? number_format(abs($gross_profit_loss), 2) : '' }}
            </th>
            <th style="text-align: right;">
                {{ $gross_profit_loss > 0 ? number_format(abs($gross_profit_loss), 2) : '' }}
            </th>
        </tr>
        <tr>
            <th colspan="6">
                <h3>OTHER INCOME</h3>
            </th>
        </tr>
        <tr>
            <th colspan="6">
                <h3>EXPENDITURE</h3>
            </th>
        </tr>
        <?php
        $total_expense = 0;
        $credit_sum = 0;
        $debit_sum = 0;
        $dr = 0;
        $cr = 0;
        ?>
        @foreach ($expenses as $expense)
            @php
                $credit = number_format(abs($expense->credit), 2);
                $debit = number_format(abs($expense->debit), 2);
            @endphp
            <tr>
                <td>{{ $expense->number }}</td>
                <td>{{ $expense->description }}</td>
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
                <?php
                $credit_sum += $expense->credit;
                $debit_sum += $expense->debit;
                $dif = $expense->credit - $expense->debit;
                ?>
                <td style="text-align: right">
                    @if ($dif < 0)
                        @php $dr += $expense->debit @endphp
                        {{ number_format(abs($dif), 2) }}
                    @endif
                </td>
                <td style="text-align: right">
                    @if ($dif > 0)
                        @php $cr += $expense->credit @endphp
                        {{ number_format(abs($dif), 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <th colspan="2" style="text-align: left">TOTAL EXPENDITURE</th>
            @php $total_expense = $credit_sum - $debit_sum;  @endphp
            <th style="text-align: right;">{{ number_format(abs($debit_sum), 2) }}</th>
            <th style="text-align: right;">{{ number_format(abs($credit_sum), 2) }}</th>
            <th style="text-align: right;">
                {{ $total_expense < 0 ? number_format(abs($total_expense), 2) : '' }}
            </th>
            <th style="text-align: right;">
                {{ $total_expense > 0 ? number_format(abs($total_expense), 2) : '' }}
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: left">TAX DIVIDEND</th>
            <th style="text-align: right;">{{ number_format(0, 2) }}</th>
            <th style="text-align: right;">{{ number_format(0, 2) }}</th>
        </tr>

        {{-- <tr>
            <th colspan="4" style="text-align: left">NET MARGIN</th>
            @php $net_profit_loss = abs($gross_profit_loss) - abs($total_expense) + $other_income  @endphp
            <th style="text-align: right;">{{ $net_profit_loss < 0 ? number_format(abs($net_profit_loss), 2) : '' }}
            </th>
            <th style="text-align: right;">{{ $net_profit_loss > 0 ? number_format(abs($net_profit_loss), 2) : '' }}
            </th>
        </tr> --}}
        <tr>
            <th colspan="4" style="text-align: left">NET MARGIN</th>
            @php

                $net_profit_loss = $gross_profit_loss - abs($total_expense) + $other_income;
            @endphp
            <th style="text-align: right;">{{ $net_profit_loss < 0 ? number_format(abs($net_profit_loss), 2) : '' }}
            </th>
            <th style="text-align: right;">{{ $net_profit_loss > 0 ? number_format(abs($net_profit_loss), 2) : '' }}
            </th>
        </tr>
    </tbody>
</table>
