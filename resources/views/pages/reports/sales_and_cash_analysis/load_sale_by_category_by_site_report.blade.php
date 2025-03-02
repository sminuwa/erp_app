
<!-- Search Bar for Filtering -->
<div class="row">
    <div class="col-md-12">
        <input type="text" id="searchBox" class="form-control" placeholder="Search...">
    </div>
</div>
{{-- <div class="card card_pdf">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 text-center mb-3">
                <h5>Sales Report From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}</h5>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="row mb-3">
            <div class="col-md-12 text-right">
                <button class="btn btn-success" id="exportExcel">Excel</button>
                <button class="btn btn-danger" id="exportPDF">PDF</button>
                <button class="btn btn-primary" id="printReport">Print</button>
            </div>
        </div>

        @php
            $grand_total_qty_available = 0;
            $grand_total_qty_sold = 0;
            $grand_total_amount = 0;
            $grand_total_cost = 0;
            $grand_total_profit = 0;
        @endphp

        @foreach ($salesByGroup as $groupKey => $groupSales)
            @php
                $total_qty_available = 0;
                $total_qty_sold = 0;
                $total_amount = 0;
                $total_cost = 0;
                $total_profit = 0;
            @endphp

            <div class="row">
                <div class="col-md-12 mt-3">
                    <h4>
                        @if ($group_by_category)
                            Category: {{ $groupSales->first()->category_code }} -
                            {{ $groupSales->first()->category_name }}
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
                    <table class="table table-bordered table-striped">
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
                                @php
                                    $profit = $sale->amount - $sale->cost;
                                    $total_qty_available += $sale->qty_available;
                                    $total_qty_sold += $sale->quantity;
                                    $total_amount += $sale->amount;
                                    $total_cost += $sale->cost;
                                    $total_profit += $profit;
                                @endphp
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
                                    <td style="text-align: right">{{ number_format($profit, 2, '.', ',') }}</td>
                                    <td style="text-align: right">
                                        {{ $sale->amount != 0 ? number_format(($profit / $sale->amount) * 100, 2) : '0.00' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <!-- TOTAL ROW -->

                        <tr>
                            <th colspan="{{ !$group_by_category && !$group_by_product ? 5 : ($group_by_category && $group_by_product ? 7 : ($group_by_category ? 6 : 4)) }}"
                                style="text-align: right">TOTAL</th>
                            <th style="text-align: right">{{ number_format($total_cost, 2, '.', ',') }}</th>
                            <th style="text-align: right">{{ number_format($total_profit, 2, '.', ',') }}</th>
                            <th style="text-align: right">
                                {{ $total_amount != 0 ? number_format(($total_profit / $total_amount) * 100, 2) : '0.00' }}
                            </th>
                        </tr>

                    </table>
                </div>
            </div>

            @php
                $grand_total_qty_available += $total_qty_available;
                $grand_total_qty_sold += $total_qty_sold;
                $grand_total_amount += $total_amount;
                $grand_total_cost += $total_cost;
                $grand_total_profit += $total_profit;
            @endphp
        @endforeach

        <!-- GRAND TOTAL ROW -->
        <div class="row">
            <div class="col-md-12 mt-3 table-responsive">
                <table class="display table table-bordered table-striped">
                    <tfoot>
                        <tr>
                            <th colspan="6" style="text-align: right">GRAND TOTAL</th>

                            <th style="text-align: right">{{ number_format($grand_total_cost, 2, '.', ',') }}</th>
                            <th style="text-align: right">{{ number_format($grand_total_profit, 2, '.', ',') }}</th>
                            <th style="text-align: right">
                                {{ $grand_total_amount != 0 ? number_format(($grand_total_profit / $grand_total_amount) * 100, 2) : '0.00' }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div> --}}
<div class="card card_pdf">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 text-center mb-3">
                <h5>Sales Report From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}</h5>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="row mb-3">
            <div class="col-md-12 text-right">
                <button class="btn btn-success" id="exportExcel">Excel</button>
                <button class="btn btn-danger" id="exportPDF">PDF</button>
                <button class="btn btn-primary" id="printReport">Print</button>
            </div>
        </div>

        @php
            $grand_total_qty_available = 0;
            $grand_total_qty_sold = 0;
            $grand_total_amount = 0;
            $grand_total_cost = 0;
            $grand_total_profit = 0;
        @endphp

        @foreach ($salesByGroup as $groupKey => $groupSales)
            @php
                $total_qty_available = 0;
                $total_qty_sold = 0;
                $total_amount = 0;
                $total_cost = 0;
                $total_profit = 0;
            @endphp

            <div class="row">
                <div class="col-md-12 mt-3">
                    <h4>
                        @if ($group_by_category)
                            Category: {{ $groupSales->first()->category_code }} -
                            {{ $groupSales->first()->category_name }}
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
                    <table class="table table-bordered table-striped">
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
                                @php
                                    $profit = $sale->amount - $sale->cost;
                                    $total_qty_available += $sale->qty_available;
                                    $total_qty_sold += $sale->quantity;
                                    $total_amount += $sale->amount;
                                    $total_cost += $sale->cost;
                                    $total_profit += $profit;
                                @endphp
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
                                    <td style="text-align: right">{{ number_format($profit, 2, '.', ',') }}</td>
                                    <td style="text-align: right">
                                        {{ $sale->amount != 0 ? number_format(($profit / $sale->amount) * 100, 2) : '0.00' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <!-- TOTAL ROW -->
                        <tfoot>
                            <tr>
                                <th colspan="{{ !$group_by_category && !$group_by_product ? 2 : ($group_by_category && $group_by_product ? 4 : ($group_by_category ? 3 : 1)) }}"
                                    style="text-align: right">TOTAL</th>
                                <th style="text-align: right">{{ number_format($total_qty_available, 6) }}</th>
                                <th style="text-align: right">{{ number_format($total_qty_sold, 6) }}</th>
                                <th style="text-align: right">{{ number_format($total_amount, 2, '.', ',') }}</th>
                                <th style="text-align: right">{{ number_format($total_cost, 2, '.', ',') }}</th>
                                <th style="text-align: right">{{ number_format($total_profit, 2, '.', ',') }}</th>
                                <th style="text-align: right">
                                    {{ $total_amount != 0 ? number_format(($total_profit / $total_amount) * 100, 2) : '0.00' }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @php
                $grand_total_qty_available += $total_qty_available;
                $grand_total_qty_sold += $total_qty_sold;
                $grand_total_amount += $total_amount;
                $grand_total_cost += $total_cost;
                $grand_total_profit += $total_profit;
            @endphp
        @endforeach

        <!-- GRAND TOTAL ROW -->
        <div class="row">
            <div class="col-md-12 mt-3 table-responsive">
                <table class="display table table-bordered table-striped">
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align: right">GRAND TOTAL</th>
                            <th style="text-align: right">{{ number_format($grand_total_qty_available, 6) }}</th>
                            <th style="text-align: right">{{ number_format($grand_total_qty_sold, 6) }}</th>
                            <th style="text-align: right">{{ number_format($grand_total_amount, 2, '.', ',') }}</th>
                            <th style="text-align: right">{{ number_format($grand_total_cost, 2, '.', ',') }}</th>
                            <th style="text-align: right">{{ number_format($grand_total_profit, 2, '.', ',') }}</th>
                            <th style="text-align: right">
                                {{ $grand_total_amount != 0 ? number_format(($grand_total_profit / $grand_total_amount) * 100, 2) : '0.00' }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


