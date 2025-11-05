{{-- <div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">
                    Sales Report From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                </h5>
                @if (isset($budget_year) && isset($quarter))
                    <p class="text-muted text-center">Quantity Budget Comparison: {{ $budget_year }}
                        Q{{ $quarter }}</p>
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

        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="display table table-bordered table-striped" data-ordering="true" id="example1">
                    <thead>
                        <tr>
                            @if ($group_by_category)
                                <th>CODE</th>
                                <th>CATEGORY</th>
                            @endif
                            @if ($group_by_product)
                                <th>CODE</th>
                                <th>PRODUCT</th>
                            @endif
                            <th>QTY AVAILABLE</th>
                            <th>QTY SOLD</th>
                            <th>QTY BUDGET</th>
                            <th>QTY VARIANCE</th>
                            <th>ACHIEVEMENT %</th>
                            <th>UNIT</th>
                            <th>AMOUNT</th>
                            <th>COST</th>
                            <th>PROFIT</th>
                            <th>MARGIN (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_qty_available = 0;
                            $total_qty_sold = 0;
                            $total_qty_budget = 0;
                            $total_qty_variance = 0;
                            $total_amount = 0;
                            $total_cost = 0;
                            $total_profit = 0;
                        @endphp

                        @foreach ($sales as $sale)
                            @php
                                $profit = $sale->amount - $sale->cost;
                                $budget_qty = $sale->budget_qty ?? 0;
                                $variance_qty = $sale->budget_variance_qty ?? $sale->quantity - $budget_qty;
                                $achievement_percent =
                                    $sale->achievement_percent ??
                                    ($budget_qty > 0 ? round(($sale->quantity / $budget_qty) * 100, 2) : null);

                                $total_qty_available += $sale->qty_available;
                                $total_qty_sold += $sale->quantity;
                                $total_qty_budget += $budget_qty;
                                $total_qty_variance += $variance_qty;
                                $total_amount += $sale->amount;
                                $total_cost += $sale->cost;
                                $total_profit += $profit;
                            @endphp
                            <tr>
                                @if ($group_by_category)
                                    <td>{{ $sale->category_code }}</td>
                                    <td>{{ $sale->category }}</td>
                                @endif
                                @if ($group_by_product)
                                    <td>{{ $sale->product_code ?? '-' }}</td>
                                    <td>{{ $sale->product_name ?? '-' }}</td>
                                @endif
                                <td style="text-align: right">{{ number_format($sale->qty_available, 6) }}</td>
                                <td style="text-align: right">{{ number_format($sale->quantity, 6) }}</td>
                                <td style="text-align: right">{{ number_format($budget_qty, 6) }}</td>
                                <td style="text-align: right; color: {{ $variance_qty >= 0 ? 'green' : 'red' }}">
                                    {{ number_format($variance_qty, 6) }}
                                </td>
                                <td
                                    style="text-align: right; 
                                    @if ($achievement_percent !== null) color: {{ $achievement_percent >= 100 ? 'green' : ($achievement_percent >= 75 ? 'orange' : 'red') }} @endif
                                ">
                                    @if ($achievement_percent !== null)
                                        {{ number_format($achievement_percent, 2) }}%
                                        @if ($achievement_percent >= 100)
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
                                <td>{{ $sale->product_unit }}</td>
                                <td style="text-align: right">{{ number_format($sale->amount, 2, '.', ',') }}</td>
                                <td style="text-align: right">{{ number_format($sale->cost, 2, '.', ',') }}</td>
                                <td style="text-align: right">{{ number_format($profit, 2, '.', ',') }}</td>
                                <td style="text-align: right">
                                    {{ $sale->amount != 0 ? number_format(($profit / $sale->amount) * 100, 2) : 0 }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $total_achievement_percent =
                                $total_qty_budget > 0 ? round(($total_qty_sold / $total_qty_budget) * 100, 2) : null;
                        @endphp
                        <tr style="font-weight: bold; background-color: #f5f5f5;">
                            @if ($group_by_category && !$group_by_product)
                                <td colspan="2" style="text-align: right">TOTAL</td>
                            @elseif ($group_by_product && !$group_by_category)
                                <td colspan="2" style="text-align: right">TOTAL</td>
                            @elseif ($group_by_category && $group_by_product)
                                <td colspan="4" style="text-align: right">TOTAL</td>
                            @else
                                <td style="text-align: right">TOTAL</td>
                            @endif
                            <td style="text-align: right">{{ number_format($total_qty_available, 6) }}</td>
                            <td style="text-align: right">{{ number_format($total_qty_sold, 6) }}</td>
                            <td style="text-align: right">{{ number_format($total_qty_budget, 6) }}</td>
                            <td style="text-align: right; color: {{ $total_qty_variance >= 0 ? 'green' : 'red' }}">
                                {{ number_format($total_qty_variance, 6) }}
                            </td>
                            <td
                                style="text-align: right; 
                                @if ($total_achievement_percent !== null) color: {{ $total_achievement_percent >= 100 ? 'green' : ($total_achievement_percent >= 75 ? 'orange' : 'red') }} @endif
                            ">
                                @if ($total_achievement_percent !== null)
                                    {{ number_format($total_achievement_percent, 2) }}%
                                    @if ($total_achievement_percent >= 100)
                                        <i class="fas fa-trophy" style="color: gold;"></i>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td></td>
                            <td style="text-align: right">{{ number_format($total_amount, 2, '.', ',') }}</td>
                            <td style="text-align: right">{{ number_format($total_cost, 2, '.', ',') }}</td>
                            <td style="text-align: right">{{ number_format($total_profit, 2, '.', ',') }}</td>
                            <td style="text-align: right">
                                {{ $total_amount != 0 ? number_format(($total_profit / $total_amount) * 100, 2) : 0 }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Quantity Budget Performance Summary -->
        @if ($total_qty_budget > 0)
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
                                        <h4 class="mb-1">{{ number_format($total_achievement_percent, 1) }}%</h4>
                                        <p class="text-muted mb-0">Overall Achievement</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-1"
                                            style="color: {{ $total_qty_variance >= 0 ? 'green' : 'red' }}">
                                            {{ number_format(abs($total_qty_variance), 0) }}
                                        </h4>
                                        <p class="text-muted mb-0">
                                            {{ $total_qty_variance >= 0 ? 'Over' : 'Under' }} Budget (Qty)
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-1">{{ number_format($total_qty_budget, 0) }}</h4>
                                        <p class="text-muted mb-0">Total Budget (Qty)</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-1">{{ number_format($total_qty_sold, 0) }}</h4>
                                        <p class="text-muted mb-0">Total Qty Sold</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Status -->
                            <div class="row mt-3">
                                <div class="col-md-12 text-center">
                                    @if ($total_achievement_percent >= 100)
                                        <span class="badge badge-success badge-lg">
                                            <i class="fas fa-trophy"></i> Target Exceeded
                                        </span>
                                    @elseif($total_achievement_percent >= 90)
                                        <span class="badge badge-info badge-lg">
                                            <i class="fas fa-thumbs-up"></i> Near Target
                                        </span>
                                    @elseif($total_achievement_percent >= 75)
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
</div> --}}
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">
                    Sales Report From {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                    To {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                </h5>
                @if (isset($budget_year) && isset($quarter))
                    <p class="text-muted text-center">Quantity Budget Comparison: {{ $budget_year }}
                        Q{{ $quarter }}</p>
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

        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="display table table-bordered table-striped" data-ordering="true" id="example1">
                    <thead>
                        <tr>
                            @if($group_by_product)
                                <!-- When grouping by product, show product columns -->
                                <th>PRODUCT CODE</th>
                                <th>PRODUCT NAME</th>
                                <th>CATEGORY</th>
                            @else
                                <!-- When grouping by category only -->
                                <th>CATEGORY CODE</th>
                                <th>CATEGORY NAME</th>
                            @endif
                            @if($branch_id_display == 'all')
                                <th>BRANCH</th>
                            @endif
                            <th>QTY AVAILABLE</th>
                            <th>QTY SOLD</th>
                            <th>QTY BUDGET</th>
                            <th>QTY VARIANCE</th>
                            <th>ACHIEVEMENT %</th>
                            <th>UNIT</th>
                            <th>AMOUNT</th>
                            <th>COST</th>
                            <th>PROFIT</th>
                            <th>MARGIN (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_qty_available = 0;
                            $total_qty_sold = 0;
                            $total_qty_budget = 0;
                            $total_qty_variance = 0;
                            $total_amount = 0;
                            $total_cost = 0;
                            $total_profit = 0;
                        @endphp

                        @foreach ($sales as $sale)
                            @php
                                $profit = $sale->amount - $sale->cost;
                                $budget_qty = $sale->budget_qty ?? 0;
                                $variance_qty = $sale->budget_variance_qty ?? $sale->quantity - $budget_qty;
                                $achievement_percent =
                                    $sale->achievement_percent ??
                                    ($budget_qty > 0 ? round(($sale->quantity / $budget_qty) * 100, 2) : null);

                                // Debug info - remove in production
                                // \Log::info("Sale Record:", [
                                //     'branch' => $sale->branch_name,
                                //     'category' => $sale->category,
                                //     'product' => $sale->product_name ?? 'N/A',
                                //     'qty_available' => $sale->qty_available,
                                //     'quantity' => $sale->quantity
                                // ]);

                                $total_qty_available += $sale->qty_available;
                                $total_qty_sold += $sale->quantity;
                                $total_qty_budget += $budget_qty;
                                $total_qty_variance += $variance_qty;
                                $total_amount += $sale->amount;
                                $total_cost += $sale->cost;
                                $total_profit += $profit;
                            @endphp
                            <tr>
                                @if($group_by_product)
                                    <td>{{ $sale->product_code ?? '-' }}</td>
                                    <td>{{ $sale->product_name ?? '-' }}</td>
                                    <td>{{ $sale->category }}</td>
                                @else
                                    <td>{{ $sale->category_code }}</td>
                                    <td>{{ $sale->category }}</td>
                                @endif
                                
                                @if($branch_id_display == 'all')
                                    <td>{{ $sale->branch_name }} ({{ $sale->branch_code }})</td>
                                @endif
                                
                                <td style="text-align: right">{{ number_format($sale->qty_available, 6) }}</td>
                                <td style="text-align: right">{{ number_format($sale->quantity, 6) }}</td>
                                <td style="text-align: right">{{ number_format($budget_qty, 6) }}</td>
                                <td style="text-align: right; color: {{ $variance_qty >= 0 ? 'green' : 'red' }}">
                                    {{ number_format($variance_qty, 6) }}
                                </td>
                                <td
                                    style="text-align: right; 
                                    @if ($achievement_percent !== null) color: {{ $achievement_percent >= 100 ? 'green' : ($achievement_percent >= 75 ? 'orange' : 'red') }} @endif
                                ">
                                    @if ($achievement_percent !== null)
                                        {{ number_format($achievement_percent, 2) }}%
                                        @if ($achievement_percent >= 100)
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
                                <td>{{ $sale->product_unit }}</td>
                                <td style="text-align: right">{{ number_format($sale->amount, 2, '.', ',') }}</td>
                                <td style="text-align: right">{{ number_format($sale->cost, 2, '.', ',') }}</td>
                                <td style="text-align: right">{{ number_format($profit, 2, '.', ',') }}</td>
                                <td style="text-align: right">
                                    {{ $sale->amount != 0 ? number_format(($profit / $sale->amount) * 100, 2) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $total_achievement_percent =
                                $total_qty_budget > 0 ? round(($total_qty_sold / $total_qty_budget) * 100, 2) : null;
                                
                            // Calculate colspan dynamically
                            $colspan = 2; // Base columns (code + name)
                            if($group_by_product) {
                                $colspan = 3; // product code + product name + category
                            }
                            if($branch_id_display == 'all') {
                                $colspan += 1; // add branch column
                            }
                        @endphp
                        <tr style="font-weight: bold; background-color: #f5f5f5;">
                            <td colspan="{{ $colspan }}" style="text-align: right">TOTAL</td>
                            <td style="text-align: right">{{ number_format($total_qty_available, 6) }}</td>
                            <td style="text-align: right">{{ number_format($total_qty_sold, 6) }}</td>
                            <td style="text-align: right">{{ number_format($total_qty_budget, 6) }}</td>
                            <td style="text-align: right; color: {{ $total_qty_variance >= 0 ? 'green' : 'red' }}">
                                {{ number_format($total_qty_variance, 6) }}
                            </td>
                            <td
                                style="text-align: right; 
                                @if ($total_achievement_percent !== null) color: {{ $total_achievement_percent >= 100 ? 'green' : ($total_achievement_percent >= 75 ? 'orange' : 'red') }} @endif
                            ">
                                @if ($total_achievement_percent !== null)
                                    {{ number_format($total_achievement_percent, 2) }}%
                                    @if ($total_achievement_percent >= 100)
                                        <i class="fas fa-trophy" style="color: gold;"></i>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td></td>
                            <td style="text-align: right">{{ number_format($total_amount, 2, '.', ',') }}</td>
                            <td style="text-align: right">{{ number_format($total_cost, 2, '.', ',') }}</td>
                            <td style="text-align: right">{{ number_format($total_profit, 2, '.', ',') }}</td>
                            <td style="text-align: right">
                                {{ $total_amount != 0 ? number_format(($total_profit / $total_amount) * 100, 2) : 0 }}%
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Quantity Budget Performance Summary -->
        @if ($total_qty_budget > 0)
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
                                        <h4 class="mb-1">{{ number_format($total_achievement_percent, 1) }}%</h4>
                                        <p class="text-muted mb-0">Overall Achievement</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-1"
                                            style="color: {{ $total_qty_variance >= 0 ? 'green' : 'red' }}">
                                            {{ number_format(abs($total_qty_variance), 0) }}
                                        </h4>
                                        <p class="text-muted mb-0">
                                            {{ $total_qty_variance >= 0 ? 'Over' : 'Under' }} Budget (Qty)
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-1">{{ number_format($total_qty_budget, 0) }}</h4>
                                        <p class="text-muted mb-0">Total Budget (Qty)</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="mb-1">{{ number_format($total_qty_sold, 0) }}</h4>
                                        <p class="text-muted mb-0">Total Qty Sold</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Status -->
                            <div class="row mt-3">
                                <div class="col-md-12 text-center">
                                    @if ($total_achievement_percent >= 100)
                                        <span class="badge badge-success badge-lg">
                                            <i class="fas fa-trophy"></i> Target Exceeded
                                        </span>
                                    @elseif($total_achievement_percent >= 90)
                                        <span class="badge badge-info badge-lg">
                                            <i class="fas fa-thumbs-up"></i> Near Target
                                        </span>
                                    @elseif($total_achievement_percent >= 75)
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
    .achievement-excellent {
        color: #28a745 !important;
    }

    .achievement-good {
        color: #17a2b8 !important;
    }

    .achievement-warning {
        color: #ffc107 !important;
    }

    .achievement-poor {
        color: #dc3545 !important;
    }
</style>
