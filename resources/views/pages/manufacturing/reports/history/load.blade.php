<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manufacturing History Report Results</h3>
    </div>
    <div class="card-body">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fa fa-industry"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Records</span>
                        <span class="info-box-number">{{ $totals['total_records'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fa fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Quantity</span>
                        <span class="info-box-number">{{ number_format($totals['total_qty'], 4) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fa fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Cost</span>
                        <span class="info-box-number">{{ number_format($totals['total_cost'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($productions->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 50px;">S/N</th>
                        <th>Product Code</th>
                        <th>Product Description</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Unit Cost</th>
                        <th class="text-right">Total Cost</th>
                        <th>Production No.</th>
                        <th>Batch No.</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productions as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $record['product_code'] }}</code></td>
                        <td>{{ $record['product_name'] }}</td>
                        <td class="text-right">{{ number_format($record['quantity'], 4) }}</td>
                        <td class="text-right">{{ number_format($record['unit_cost'], 2) }}</td>
                        <td class="text-right">{{ number_format($record['total_cost'], 2) }}</td>
                        <td><code>{{ $record['reference'] }}</code></td>
                        <td><code>{{ $record['batch_number'] }}</code></td>
                        <td>
                            @if($record['type'] == 'Single')
                                <span class="badge badge-info">Single</span>
                            @else
                                <span class="badge badge-primary">Batch</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold bg-light">
                        <td colspan="3"><strong>Total</strong></td>
                        <td class="text-right"><strong>{{ number_format($totals['total_qty'], 4) }}</strong></td>
                        <td class="text-right"></td>
                        <td class="text-right"><strong>{{ number_format($totals['total_cost'], 2) }}</strong></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No manufacturing records found for the selected criteria.
        </div>
        @endif
    </div>
</div>
