<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manufacturing Teams Report</h3>
    </div>
    <div class="card-body">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fa fa-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Records</span>
                        <span class="info-box-number">{{ $totals['total_records'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Quantity</span>
                        <span class="info-box-number">{{ number_format($totals['total_qty'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fa fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Cost</span>
                        <span class="info-box-number">{{ number_format($totals['total_cost'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($productions->count() > 0)
            @foreach($productionsByTeam as $teamName => $teamProductions)
            <div class="card card-outline card-primary mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-users"></i> {{ $teamName }}
                        <span class="badge badge-info ml-2">{{ $teamProductions->count() }} records</span>
                        <span class="badge badge-success ml-1">Qty: {{ number_format($teamProductions->sum('quantity'), 4) }}</span>
                        <span class="badge badge-warning ml-1">Cost: {{ number_format($teamProductions->sum('total_cost'), 2) }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="50">S/N</th>
                                <th>Product Code</th>
                                <th>Product Description</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Total Cost</th>
                                <th>Batch Number</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teamProductions as $index => $production)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $production['product_code'] }}</code></td>
                                <td>{{ $production['product_name'] }}</td>
                                <td class="text-right">{{ number_format($production['quantity'], 4) }}</td>
                                <td class="text-right">{{ number_format($production['unit_cost'], 2) }}</td>
                                <td class="text-right">{{ number_format($production['total_cost'], 2) }}</td>
                                <td><code>{{ $production['batch_number'] }}</code></td>
                                <td>
                                    @if($production['type'] == 'Single')
                                        <span class="badge badge-info">Single</span>
                                    @else
                                        <span class="badge badge-primary">Batch</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <td colspan="3"><strong>Team Subtotal</strong></td>
                                <td class="text-right"><strong>{{ number_format($teamProductions->sum('quantity'), 4) }}</strong></td>
                                <td class="text-right">-</td>
                                <td class="text-right"><strong>{{ number_format($teamProductions->sum('total_cost'), 2) }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endforeach

            <!-- Grand Total Table -->
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa fa-calculator"></i> Grand Total</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Records</th>
                            <td class="text-right">{{ $totals['total_records'] }}</td>
                        </tr>
                        <tr>
                            <th>Total Quantity</th>
                            <td class="text-right">{{ number_format($totals['total_qty'], 4) }}</td>
                        </tr>
                        <tr class="table-success">
                            <th>Total Cost</th>
                            <td class="text-right"><strong>{{ number_format($totals['total_cost'], 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No production records found for the selected criteria.
        </div>
        @endif
    </div>
</div>
