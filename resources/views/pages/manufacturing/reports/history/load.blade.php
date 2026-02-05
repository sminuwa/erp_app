<div class="card">
    <div class="card-header">
        <h3 class="card-title">Report Results</h3>
    </div>
    <div class="card-body">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fa fa-industry"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Single Manufacturing</span>
                        <span class="info-box-number">{{ $totals['single_count'] }}</span>
                        <span class="progress-description">Qty: {{ number_format($totals['single_qty'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Single Manufacturing Cost</span>
                        <span class="info-box-number">{{ number_format($totals['single_cost'], 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fa fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Batch Productions</span>
                        <span class="info-box-number">{{ $totals['batch_count'] }}</span>
                        <span class="progress-description">Qty: {{ number_format($totals['batch_qty'], 0) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fa fa-cogs"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">WIP Value</span>
                        <span class="info-box-number">{{ number_format($totals['batch_wip'], 2) }}</span>
                        <span class="progress-description">Converted: {{ number_format($totals['batch_converted'], 4) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($singleManufacturing->count() > 0)
        <h5>Single Product Manufacturing</h5>
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>BOM</th>
                    <th>Finish Product</th>
                    <th>Team</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Material Cost</th>
                    <th class="text-right">Labor Cost</th>
                    <th class="text-right">Total Cost</th>
                    <th class="text-right">Unit Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($singleManufacturing as $record)
                <tr>
                    <td>{{ $record->reference }}</td>
                    <td>{{ date('d M Y', strtotime($record->manufacturing_date)) }}</td>
                    <td>{{ $record->bom->reference ?? 'N/A' }}</td>
                    <td>{{ $record->bom->finishProduct->name ?? 'N/A' }}</td>
                    <td>{{ $record->team->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($record->quantity, 4) }}</td>
                    <td class="text-right">{{ number_format($record->material_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($record->labor_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($record->total_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($record->unit_cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="5">Total</td>
                    <td class="text-right">{{ number_format($totals['single_qty'], 4) }}</td>
                    <td class="text-right">{{ number_format($singleManufacturing->sum('material_cost'), 2) }}</td>
                    <td class="text-right">{{ number_format($singleManufacturing->sum('labor_cost'), 2) }}</td>
                    <td class="text-right">{{ number_format($totals['single_cost'], 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <br>
        @endif

        @if($batchProductions->count() > 0)
        <h5>Batch Productions</h5>
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>Batch Number</th>
                    <th>Date</th>
                    <th>BOM</th>
                    <th>Finish Product</th>
                    <th>Team</th>
                    <th class="text-right">Batches</th>
                    <th class="text-right">Material Cost</th>
                    <th class="text-right">WIP Value</th>
                    <th class="text-right">Converted Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batchProductions as $record)
                <tr>
                    <td>{{ $record->batch_number }}</td>
                    <td>{{ date('d M Y', strtotime($record->production_date)) }}</td>
                    <td>{{ $record->bom->reference ?? 'N/A' }}</td>
                    <td>{{ $record->bom->finishProduct->name ?? 'N/A' }}</td>
                    <td>{{ $record->team->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($record->quantity, 0) }}</td>
                    <td class="text-right">{{ number_format($record->material_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($record->wip_value, 2) }}</td>
                    <td class="text-right">{{ number_format($record->converted_qty, 4) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="5">Total</td>
                    <td class="text-right">{{ number_format($totals['batch_qty'], 0) }}</td>
                    <td class="text-right">{{ number_format($batchProductions->sum('material_cost'), 2) }}</td>
                    <td class="text-right">{{ number_format($totals['batch_wip'], 2) }}</td>
                    <td class="text-right">{{ number_format($totals['batch_converted'], 4) }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        @if($singleManufacturing->count() == 0 && $batchProductions->count() == 0)
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No manufacturing records found for the selected criteria.
        </div>
        @endif
    </div>
</div>
