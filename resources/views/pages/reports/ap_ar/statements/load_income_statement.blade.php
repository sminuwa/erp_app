<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.income.statement.report.print', [$from_month, $to_month, $income_year, $branch_id, $category_id1, $category_id2]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>

<table class="table table-bordered caption" id="example1" data-ordering="false">
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
    ?>
    <thead>
        <tr>
            <th rowspan="2">Account No</th>
            <th rowspan="2">Description</th>
            <th colspan="2" style="text-align: center; align-content: center">Balance</th>
        </tr>
        <tr>
            <th style="text-align: center; align-content: center">Debit (Dr.)</th>
            <th style="text-align: center; align-content: center">Credit (Cr.)</th>
            <th>Balance</th>
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
                    <?php
                    $credit_sum += $revenue->credit;
                    $debit_sum += $revenue->debit;
                    $dif = $debit_sum - $credit_sum;
                    ?>
                    @if ($dif < 0)
                        ({{ number_format($dif, 2) }})
                    @else
                        {{ number_format($dif, 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <th colspan="4" style="text-align: left">TOTAL REVENUE</th>
            @php $total_revenue = $debit_sum-$credit_sum;  @endphp
            <th colspan="3" style="text-align: right;">{{ number_format($total_revenue, 2) }}</th>
        </tr>
        <?php
        $total_cost_of_sale = 0;
        $credit_sum = 0;
        $debit_sum = 0;
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
                <td style="text-align: right">
                    <?php
                    $credit_sum += $sale->credit;
                    $debit_sum += $sale->debit;
                    $dif = $debit_sum - $credit_sum;
                    ?>
                    @if ($dif < 0)
                       ({{ number_format($dif, 2) }})
                    @else
                       {{ number_format($dif, 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <th colspan="4" style="text-align: left">TOTAL COST</th>
            @php $total_cost = $debit_sum - $credit_sum;  @endphp
            <th colspan="3" style="text-align: right;">{{ number_format($total_cost, 2) }}</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: left">GROSS MARGIN</th>
            @php $gross_profit_loss = $total_revenue - abs($total_cost)  @endphp
            <th colspan="3" style="text-align: right;">{{ number_format($gross_profit_loss, 2) }}</th>
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

                <td style="text-align: right">
                    <?php
                    $credit_sum += $expense->credit;
                    $debit_sum += $expense->debit;
                    $dif = $debit_sum - $credit_sum;
                    ?>
                    @if ($dif < 0)
                        ({{ number_format(abs($dif), 2) }})
                    @else
                        {{ number_format($dif, 2) }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <th colspan="4" style="text-align: left">TOTAL EXPENDITURE</th>
            @php $total_expense = $debit_sum-$credit_sum;  @endphp
            <th colspan="3" style="text-align: right;">{{ number_format($total_expense, 2) }}</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: left">TAX DIVIDEND</th>
            <th colspan="3" style="text-align: right;">{{ number_format(0, 2) }}</th>
        </tr>

        <tr>
            <th colspan="4" style="text-align: left">NET MARGIN</th>
            @php $net_profit_loss = abs($gross_profit_loss) - abs($total_expense) + $other_income  @endphp
            <th colspan="3" style="text-align: right;">{{ number_format($net_profit_loss, 2) }}</th>
        </tr>
    </tbody>
</table>
