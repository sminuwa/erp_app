<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="text-right">
                    <a href="{{ route('ajax.relation_officer.report.print', [$from_date, $to_date, $company_id, is_array($branch_id) ? implode(',', $branch_id) : $branch_id, is_array($category_id1) ? implode(',', $category_id1) : $category_id1,is_array($user_id) ? implode(',', $user_id) : $user_id]) }}"
                        target="_BLANK" class="btn-success btn btn-sm">Print</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">Sales By Relation Officer
                    From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                </h5>
            </div>
        </div>

        @php
            $grand_total_amount = 0;
            $grand_total_cost = 0;
            $grand_total_profit = 0;
        @endphp
        <table class="display table table-bordered table-striped">
            @foreach ($salesByBranch as $branchId => $branchSales)
                <div class="row">
                    <div class="col-md-12 mt-3 table-responsive">
                        <thead>
                            <tr>
                                <td colspan="9">
                                    <h4>{{ $branchSales->first()->branch_name }}
                                        ({{ $branchSales->first()->branch_code }})
                                    </h4>
                                </td>
                            </tr>

                            <tr>
                                <th>CODE</th>
                                <th>CATEGORY</th>
                                <th>RO</th>
                                <th>QTY AVAILABLE</th>
                                <th>QTY SOLD</th>
                                <th>AMOUNT</th>
                                <th>COST</th>
                                <th>MARGIN</th>
                                <th>MARGIN (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $branch_total_amount = 0;
                                $branch_total_cost = 0;
                                $branch_total_profit = 0;
                            @endphp
                            @foreach ($branchSales as $sale)
                                <tr>
                                    <td>{{ $sale->code }}</td>
                                    <td>{{ $sale->category }}</td>
                                    <td>{{ $sale->ro_code }}-{{ $sale->user_name }}</td>
                                    <td style="text-align: right">{{ number_format($sale->qty_available, 6) }}</td>
                                    <td style="text-align: right">{{ number_format($sale->quantity, 6) }}</td>
                                    <td style="text-align: right">{{ number_format($sale->amount, 2, '.', ',') }}</td>
                                    <td style="text-align: right">{{ number_format($sale->cost, 2, '.', ',') }}</td>
                                    <td style="text-align: right">
                                        @php
                                            $profit = $sale->amount - $sale->cost;
                                            $branch_total_profit += $profit;
                                            $branch_total_amount += $sale->amount;
                                            $branch_total_cost += $sale->cost;
                                        @endphp
                                        {{ $profit < 0 ? '(' . number_format(abs($profit), 2, '.', ',') . ')' : number_format($profit, 2) }}
                                    </td>
                                    <td style="text-align: right">
                                        {{ $sale->amount != 0 ? number_format(($profit / $sale->amount) * 100, 2) : 0 }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" style="text-align: right">BRANCH TOTAL</th>
                                <th style="text-align: right">{{ number_format($branch_total_amount, 2, '.', ',') }}
                                </th>
                                <th style="text-align: right">{{ number_format($branch_total_cost, 2, '.', ',') }}</th>
                                <th style="text-align: right">
                                    {{ $branch_total_profit < 0 ? '(' . number_format(abs($branch_total_profit), 2, '.', ',') . ')' : number_format($branch_total_profit, 2) }}
                                </th>
                                <th style="text-align: right">
                                    {{ $branch_total_amount != 0 ? number_format(($branch_total_profit / $branch_total_amount) * 100, 2) : 0 }}
                                </th>
                            </tr>
                        </tfoot>

                    </div>
                </div>

                @php
                    $grand_total_amount += $branch_total_amount;
                    $grand_total_cost += $branch_total_cost;
                    $grand_total_profit += $branch_total_profit;
                @endphp
            @endforeach
        </table>
        <div class="row">
            <div class="col-md-12 mt-3 table-responsive">
                <table class="display table table-bordered table-striped">
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align: right">GRAND TOTAL</th>
                            <th style="text-align: right">{{ number_format($grand_total_amount, 2, '.', ',') }}</th>
                            <th style="text-align: right">{{ number_format($grand_total_cost, 2, '.', ',') }}</th>
                            <th style="text-align: right">
                                {{ $grand_total_profit < 0 ? '(' . number_format(abs($grand_total_profit), 2, '.', ',') . ')' : number_format($grand_total_profit, 2) }}
                            </th>
                            <th style="text-align: right">
                                {{ $grand_total_amount != 0 ? number_format(($grand_total_profit / $grand_total_amount) * 100, 2) : 0 }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
