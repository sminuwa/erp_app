<div class="card">
    <div class="card-body">
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
            $grand_total_qty = 0;
            $grand_total_qty_available = 0; // Added for available quantity
        @endphp

        @if ($is_summary)
            <div class="col-md-12 table-responsive">
                <table class="display table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Relation Officer</th>
                            <th>Company</th>
                            <th>Branch</th>
                            <th>Quantity Sold</th>
                            <th>Total Amount</th>
                            <th>Cost</th>
                            <th>Margin</th>
                            <th>Margin (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salesByOfficer as $sales)
                            @php
                                $profit = $sales->total_amount - $sales->cost;
                                $grand_total_amount += $sales->total_amount;
                                $grand_total_cost += $sales->cost;
                                $grand_total_profit += $profit;
                                $grand_total_qty += $sales->total_quantity;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sales->ro_code }} - {{ $sales->ro_name }}</td>
                                <td>{{ $sales->company_name }}</td>
                                <td>{{ $sales->branch_name }}</td>
                                <td>{{ number_format($sales->total_quantity, 2) }}</td>
                                <td>{{ number_format($sales->total_amount, 2) }}</td>
                                <td>{{ number_format($sales->cost, 2) }}</td>
                                <td style="text-align: right">
                                    {{ $profit < 0 ? '(' . number_format(abs($profit), 2, '.', ',') . ')' : number_format($profit, 2) }}
                                </td>
                                <td style="text-align: right">
                                    {{ $sales->total_amount != 0 ? number_format(($profit / $sales->total_amount) * 100, 2) : 0 }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="display table table-bordered table-striped">
                        @foreach ($salesByOfficer as $roId => $officerSales)
                            @php
                                $officer_total_amount = 0;
                                $officer_total_cost = 0;
                                $officer_total_profit = 0;
                                $officer_total_qty = 0;
                                $officer_total_qty_available = 0; // Add this
                            @endphp
                            <!-- Move the officer header outside the table -->
                            <tr>
                                <td colspan="9">
                                    <h4>{{ $officerSales->first()->ro_code }}
                                        - {{ $officerSales->first()->user_name }}</h4>
                                </td>
                            </tr>
                            <thead>
                                <tr>
                                    <th>CODE</th>
                                    <th>CATEGORY</th>
                                    <th>BRANCH</th>
                                    <th>QTY AVAILABLE</th>
                                    <th>QTY SOLD</th>
                                    <th>AMOUNT</th>
                                    <th>COST</th>
                                    <th>MARGIN</th>
                                    <th>MARGIN (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($officerSales as $sale)
                                    @php
                                        $profit = $sale->amount - $sale->cost;
                                        $officer_total_amount += $sale->amount ?? 0; // Fallback for null
                                        $officer_total_cost += $sale->cost ?? 0; // Fallback for null
                                        $officer_total_profit += $profit;
                                        $officer_total_qty += $sale->quantity ?? 0; // Fallback for null
                                        $officer_total_qty_available += $sale->qty_available ?? 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $sale->code ?? 'N/A' }}</td>
                                        <td>{{ $sale->category ?? 'N/A' }}</td>
                                        <td>{{ $sale->branch_code ?? 'N/A' }}</td>
                                        <td style="text-align: right">{{ number_format($sale->qty_available ?? 0, 6) }}
                                        </td>
                                        <td style="text-align: right">{{ number_format($sale->quantity ?? 0, 6) }}</td>
                                        <td style="text-align: right">
                                            {{ number_format($sale->amount ?? 0, 2, '.', ',') }}</td>
                                        <td style="text-align: right">
                                            {{ number_format($sale->cost ?? 0, 2, '.', ',') }}</td>
                                        <td style="text-align: right">
                                            {{ $profit < 0 ? '(' . number_format(abs($profit), 2, '.', ',') . ')' : number_format($profit, 2) }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ ($sale->amount ?? 0) != 0 ? number_format(($profit / ($sale->amount ?? 1)) * 100, 2) : 0 }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tr>
                                <th colspan="3" style="text-align: right">OFFICER TOTAL</th>
                                <th style="text-align: right">{{ number_format($officer_total_qty_available, 6) }}</th>
                                <th style="text-align: right">{{ number_format($officer_total_qty, 6) }}</th>
                                <th style="text-align: right">{{ number_format($officer_total_amount, 2, '.', ',') }}
                                </th>
                                <th style="text-align: right">{{ number_format($officer_total_cost, 2, '.', ',') }}
                                </th>
                                <th style="text-align: right">
                                    {{ $officer_total_profit < 0 ? '(' . number_format(abs($officer_total_profit), 2, '.', ',') . ')' : number_format($officer_total_profit, 2) }}
                                </th>
                                <th style="text-align: right">
                                    {{ $officer_total_amount != 0 ? number_format(($officer_total_profit / $officer_total_amount) * 100, 2) : 0 }}
                                </th>
                            </tr>
                            @php
                                $grand_total_amount += $officer_total_amount;
                                $grand_total_cost += $officer_total_cost;
                                $grand_total_profit += $officer_total_profit;
                                $grand_total_qty += $officer_total_qty;
                                $grand_total_qty_available += $officer_total_qty_available;
                            @endphp
                        @endforeach
                    </table>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12 mt-3 table-responsive">
                <table class="table table-bordered table-striped">
                    <tfoot>
                        <tr>
                            <th style="text-align: right">TOTAL</th>
                            <th style="text-align: right">QTY AVAILABLE</th>
                            <th style="text-align: right">QTY SOLD</th>
                            <th style="text-align: right">TOTAL AMOUNT</th>
                            <th style="text-align: right">TOTAL COST</th>
                            <th style="text-align: right">TOTAL MARGIN</th>
                            <th style="text-align: right">MARGIN %</th>
                        </tr>
                        <tr>
                            <th style="text-align: right">GRAND TOTAL</th>
                            <th style="text-align: right">{{ number_format($grand_total_qty_available, 2, '.', ',') }}
                            </th>
                            <th style="text-align: right">{{ number_format($grand_total_qty, 2, '.', ',') }}</th>
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
