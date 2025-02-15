{{-- <div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">Sales By Branch
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

        @foreach ($salesByBranch as $branchId => $branchSales)
            @php
                $branch_total_amount = 0;
                $branch_total_cost = 0;
                $branch_total_profit = 0;
            @endphp

            <div class="row">
                <div class="col-md-12 mt-3">
                    <h4>{{ $branchSales->first()->branch_name }} ({{ $branchSales->first()->branch_code }})</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="display table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>CODE</th>
                                <th>CATEGORY</th>
                                <th>QTY AVAILABLE</th>
                                <th>QTY SOLD</th>
                                <th>AMOUNT</th>
                                <th>COST</th>
                                <th>MARGIN</th>
                                <th>MARGIN (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($branchSales as $sale)
                                @php
                                    $profit = $sale->amount - $sale->cost;
                                    $branch_total_amount += $sale->amount;
                                    $branch_total_cost += $sale->cost;
                                    $branch_total_profit += $profit;
                                @endphp
                                <tr>
                                    <td>{{ $sale->code }}</td>
                                    <td>{{ $sale->category }}</td>
                                    <td style="text-align: right">{{ number_format($sale->qty_available, 6) }}</td>
                                    <td style="text-align: right">{{ number_format($sale->quantity, 6) }}</td>
                                    <td style="text-align: right">{{ number_format($sale->amount, 2, '.', ',') }}</td>
                                    <td style="text-align: right">{{ number_format($sale->cost, 2, '.', ',') }}</td>
                                    <td style="text-align: right">
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
                                <th colspan="4" style="text-align: right">BRANCH TOTAL</th>
                                <th style="text-align: right">{{ number_format($branch_total_amount, 2, '.', ',') }}</th>
                                <th style="text-align: right">{{ number_format($branch_total_cost, 2, '.', ',') }}</th>
                                <th style="text-align: right">
                                    {{ $branch_total_profit < 0 ? '(' . number_format(abs($branch_total_profit), 2, '.', ',') . ')' : number_format($branch_total_profit, 2) }}
                                </th>
                                <th style="text-align: right">
                                    {{ $branch_total_amount != 0 ? number_format(($branch_total_profit / $branch_total_amount) * 100, 2) : 0 }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @php
                $grand_total_amount += $branch_total_amount;
                $grand_total_cost += $branch_total_cost;
                $grand_total_profit += $branch_total_profit;
            @endphp
        @endforeach

        <div class="row">
            <div class="col-md-12 mt-3 table-responsive">
                <table class="display table table-bordered table-striped">
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align: right">GRAND TOTAL</th>
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
</div> --}}
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">
                    Sales Report From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                </h5>
            </div>
        </div>

        @php
            $grand_total_amount = 0;
            $grand_total_cost = 0;
            $grand_total_profit = 0;
        @endphp

@foreach ($salesByGroup as $groupKey => $groupSales)
    <div class="row">
        <div class="col-md-12 mt-3">
            <h4>
                @if ($group_by_category)
                    Category: {{ $groupSales->first()->category_code }} - {{ $groupSales->first()->category_name }}
                @elseif ($group_by_product)
                    Product: {{ $groupSales->first()->product_code }} - {{ $groupSales->first()->product_name }}
                @else
                    Branch: {{ $groupSales->first()->branch_code }} - {{ $groupSales->first()->branch_name }}
                @endif
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="display table table-bordered table-striped">
                <thead>
                    <tr>
                        @if (!$group_by_category && !$group_by_product)
                            <th>Category Code</th>
                            <th>Category Name</th>
                        @endif
                        @if ($group_by_category)
                            <th>Branch Code</th>
                            <th>Product Code</th>
                            <th>Product Name</th>
                        @endif
                        @if ($group_by_product)
                            <th>Branch Code</th>
                        @endif
                        <th>QTY Available</th>
                        <th>QTY Sold</th>
                        <th>Amount</th>
                        <th>Cost</th>
                        <th>Margin</th>
                        <th>Margin (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupSales as $sale)
                        <tr>
                            @if (!$group_by_category && !$group_by_product)
                                <td>{{ $sale->category_code }}</td>
                                <td>{{ $sale->category_name }}</td>
                            @endif
                            @if ($group_by_category)
                                <td>{{ $sale->branch_code }}</td>
                                <td>{{ $sale->product_code }}</td>
                                <td>{{ $sale->product_name }}</td>
                            @endif
                            @if ($group_by_product)
                                <td>{{ $sale->branch_code }}</td>
                            @endif
                            <td style="text-align: right">{{ number_format($sale->qty_available, 6) }}</td>
                            <td style="text-align: right">{{ number_format($sale->quantity, 6) }}</td>
                            <td style="text-align: right">{{ number_format($sale->amount, 2, '.', ',') }}</td>
                            <td style="text-align: right">{{ number_format($sale->cost, 2, '.', ',') }}</td>
                            <td style="text-align: right">{{ number_format($sale->amount - $sale->cost, 2) }}</td>
                            <td style="text-align: right">
                                {{ $sale->amount != 0 ? number_format((($sale->amount - $sale->cost) / $sale->amount) * 100, 2) : 0 }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach


