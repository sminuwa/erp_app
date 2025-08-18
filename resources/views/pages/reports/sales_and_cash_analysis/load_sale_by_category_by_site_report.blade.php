
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
</div> --}}
<div class="card card_pdf">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 text-center mb-3">
                <h5>Sales Report From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}</h5>
                @if(isset($budget_year) && isset($quarter))
                    <p class="text-muted">Quantity Budget Comparison: {{ $budget_year }} Q{{ $quarter }}</p>
                @endif
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
            $grand_total_budget = 0;
            $grand_total_variance = 0;
        @endphp

        @foreach ($salesByGroup as $groupKey => $groupSales)
            @php
                $total_qty_available = 0;
                $total_qty_sold = 0;
                $total_amount = 0;
                $total_cost = 0;
                $total_profit = 0;
                $total_budget = 0;
                $total_variance = 0;
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
                                <th>QTY Budget</th>
                                <th>QTY Variance</th>
                                <th>Achievement %</th>
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
                                    $budget_qty = $sale->budget_qty ?? 0;
                                    $variance_qty = $sale->budget_variance_qty ?? ($sale->quantity - $budget_qty);
                                    $achievement_percent = $sale->achievement_percent ?? ($budget_qty > 0 ? round(($sale->quantity / $budget_qty) * 100, 2) : null);
                                    
                                    $total_qty_available += $sale->qty_available;
                                    $total_qty_sold += $sale->quantity;
                                    $total_amount += $sale->amount;
                                    $total_cost += $sale->cost;
                                    $total_profit += $profit;
                                    $total_budget += $budget_qty;
                                    $total_variance += $variance_qty;
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
                                    <td style="text-align: right">{{ number_format($budget_qty, 6) }}</td>
                                    <td style="text-align: right; color: {{ $variance_qty >= 0 ? 'green' : 'red' }}">
                                        {{ number_format($variance_qty, 6) }}
                                    </td>
                                    <td style="text-align: right; 
                                        @if($achievement_percent !== null)
                                            color: {{ $achievement_percent >= 100 ? 'green' : ($achievement_percent >= 75 ? 'orange' : 'red') }}
                                        @endif
                                    ">
                                        @if($achievement_percent !== null)
                                            {{ number_format($achievement_percent, 2) }}%
                                            @if($achievement_percent >= 100)
                                                <i class="fas fa-check-circle" style="color: green;"></i>
                                            @elseif($achievement_percent >= 75)
                                                <i class="fas fa-exclamation-triangle" style="color: orange;"></i>
                                            @else
                                                <i class="fas fa-times-circle" style="color: red;"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
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
                            @php
                                $total_achievement_percent = $total_budget > 0 ? round(($total_qty_sold / $total_budget) * 100, 2) : null;
                            @endphp
                            <tr>
                                <th colspan="{{ !$group_by_category && !$group_by_product ? 2 : ($group_by_category && $group_by_product ? 4 : ($group_by_category ? 3 : 1)) }}"
                                    style="text-align: right">TOTAL</th>
                                <th style="text-align: right">{{ number_format($total_qty_available, 6) }}</th>
                                <th style="text-align: right">{{ number_format($total_qty_sold, 6) }}</th>
                                <th style="text-align: right">{{ number_format($total_budget, 6) }}</th>
                                <th style="text-align: right; color: {{ $total_variance >= 0 ? 'green' : 'red' }}">
                                    {{ number_format($total_variance, 6) }}
                                </th>
                                <th style="text-align: right; 
                                    @if($total_achievement_percent !== null)
                                        color: {{ $total_achievement_percent >= 100 ? 'green' : ($total_achievement_percent >= 75 ? 'orange' : 'red') }}
                                    @endif
                                ">
                                    @if($total_achievement_percent !== null)
                                        {{ number_format($total_achievement_percent, 2) }}%
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </th>
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
                $grand_total_budget += $total_budget;
                $grand_total_variance += $total_variance;
            @endphp
        @endforeach

        <!-- GRAND TOTAL ROW -->
        <div class="row">
            <div class="col-md-12 mt-3 table-responsive">
                @php
                    $grand_achievement_percent = $grand_total_budget > 0 ? round(($grand_total_qty_sold / $grand_total_budget) * 100, 2) : null;
                @endphp
                <table class="display table table-bordered table-striped">
                    <tfoot>
                        <tr style="background-color: #f8f9fa; font-weight: bold;">
                            <th colspan="2" style="text-align: right">GRAND TOTAL</th>
                            <th style="text-align: right">{{ number_format($grand_total_qty_available, 6) }}</th>
                            <th style="text-align: right">{{ number_format($grand_total_qty_sold, 6) }}</th>
                            <th style="text-align: right">{{ number_format($grand_total_budget, 6) }}</th>
                            <th style="text-align: right; color: {{ $grand_total_variance >= 0 ? 'green' : 'red' }}">
                                {{ number_format($grand_total_variance, 6) }}
                            </th>
                            <th style="text-align: right; 
                                @if($grand_achievement_percent !== null)
                                    color: {{ $grand_achievement_percent >= 100 ? 'green' : ($grand_achievement_percent >= 75 ? 'orange' : 'red') }}
                                @endif
                            ">
                                @if($grand_achievement_percent !== null)
                                    {{ number_format($grand_achievement_percent, 2) }}%
                                    @if($grand_achievement_percent >= 100)
                                        <i class="fas fa-trophy" style="color: gold;"></i>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </th>
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

        <!-- Quantity Budget Performance Summary -->
        @if($grand_total_budget > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Quantity Budget Performance Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="mb-1">{{ number_format($grand_achievement_percent, 1) }}%</h4>
                                    <p class="text-muted mb-0">Overall Achievement</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="mb-1" style="color: {{ $grand_total_variance >= 0 ? 'green' : 'red' }}">
                                        {{ number_format(abs($grand_total_variance), 0) }}
                                    </h4>
                                    <p class="text-muted mb-0">
                                        {{ $grand_total_variance >= 0 ? 'Over' : 'Under' }} Budget (Qty)
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="mb-1">{{ number_format($grand_total_budget, 0) }}</h4>
                                    <p class="text-muted mb-0">Total Budget (Qty)</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="mb-1">{{ number_format($grand_total_qty_sold, 0) }}</h4>
                                    <p class="text-muted mb-0">Total Qty Sold</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Performance Status -->
                        <div class="row mt-3">
                            <div class="col-md-12 text-center">
                                @if($grand_achievement_percent >= 100)
                                    <span class="badge badge-success badge-lg">
                                        <i class="fas fa-trophy"></i> Target Exceeded
                                    </span>
                                @elseif($grand_achievement_percent >= 90)
                                    <span class="badge badge-info badge-lg">
                                        <i class="fas fa-thumbs-up"></i> Near Target
                                    </span>
                                @elseif($grand_achievement_percent >= 75)
                                    <span class="badge badge-warning badge-lg">
                                        <i class="fas fa-exclamation-triangle"></i> Below Target
                                    </span>
                                @else
                                    <span class="badge badge-danger badge-lg">
                                        <i class="fas fa-times-circle"></i> Significantly Below Target
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}

.card-header h6 {
    color: #495057;
    font-weight: 600;
}

/* Performance color coding */
.achievement-excellent { color: #28a745 !important; }
.achievement-good { color: #17a2b8 !important; }
.achievement-warning { color: #ffc107 !important; }
.achievement-poor { color: #dc3545 !important; }
</style>


