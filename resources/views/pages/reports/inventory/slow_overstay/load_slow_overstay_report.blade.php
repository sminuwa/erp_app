{{-- @if ($inventory->isEmpty())
    <p class="text-center">No records found for the selected filters.</p>
@else
    <div class="table-responsive">
        <!-- Overstayed Inventory Table -->
        @if ($type == 'overstayed')
            <h5>Overstayed Inventory (Products not received recently & still available)</h5>
            <table class="display table table-bordered" id="overstayedTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Store</th>
                        <th>Last Received Date</th>
                        <th>Days Since Received</th>
                        <th>Available Quantity</th>
                        <th>Branch</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventory as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $record->product_code }}</td>
                            <td>{{ $record->product_name }}</td>
                            <td>{{ $record->store_code }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->last_received_date)->format('d M, Y') }}</td>
                            <td>{{ $record->days_since_received }}</td>
                            <td>{{ $record->available_quantity }}</td>
                            <td>{{ $record->branch_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <br>
        @if ($type == 'slow_moving')
            <!-- Slow Moving Inventory Table -->
            <h5>Slow Moving Inventory (Products not sold recently & still available)</h5>
            <table class="display table table-bordered" id="slowMovingTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Store</th>
                        <th>Last Sold Date</th>
                        <th>Days Since Last Sold</th>
                        <th>Available Quantity</th>
                        <th>Branch</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventory as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $record->product_code }}</td>
                            <td>{{ $record->product_name }}</td>
                            <td>{{ $record->store_code }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->last_sold_date)->format('d M, Y') }}</td>
                            <td>{{ $record->days_since_sold }}</td>
                            <td>{{ $record->available_quantity }}</td>
                            <td>{{ $record->branch_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endif --}}
{{-- load_slow_overstay_report.blade.php --}}
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-1">
                <h5 style="text-align: center;">
                    {{ $type == 'overstayed' ? 'Overstayed' : ($type == 'slow_moving' ? 'Slow Moving' : 'Slow Moving & Overstayed') }}
                    Inventory Report
                </h5>
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-md-12 mb-2 text-right">
                <button class="btn btn-sm btn-success" onclick="window.print()">Print</button>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="display table table-bordered table-striped" id="inventory-table" data-ordering="true">
                    <thead>
                        <tr>
                            <th>PRODUCT CODE</th>
                            <th>PRODUCT NAME</th>
                            <th>BRANCH</th>
                            <th>STORE</th>
                            <th>AVAILABLE QTY</th>
                            <th>LAST DATE</th>
                            <th>DAYS</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_qty = 0;
                        @endphp

                        @foreach ($inventory as $item)
                            @php
                                $total_qty += $item->available_quantity;

                                // Determine item status based on days and type
                                $status = '';
                                $status_class = '';

                                if ($type == 'all') {
                                    // For combined report, check days to determine status
                                    if (isset($item->days_since_sold) && $item->days_since_sold > 30) {
                                        $status = 'Slow Moving';
                                        $status_class = 'text-warning';
                                    }
                                    if (isset($item->days_since_received) && $item->days_since_received > 30) {
                                        $status = 'Overstayed';
                                        $status_class = 'text-danger';
                                    }
                                } else {
                                    // For specific reports, use the report type
                                    $status = $type == 'overstayed' ? 'Overstayed' : 'Slow Moving';
                                    $status_class = $type == 'overstayed' ? 'text-danger' : 'text-warning';
                                }

                                // Determine which days field to use
                                if (isset($item->days_since_received)) {
                                    $days = $item->days_since_received;
                                    $date = $item->last_received_date ?? 'Never';
                                    $date_label = 'Received';
                                } elseif (isset($item->days_since_sold)) {
                                    $days = $item->days_since_sold;
                                    $date = $item->last_sold_date ?? 'Never';
                                    $date_label = 'Sold';
                                } else {
                                    $days = 999; // Very high default
                                    $date = 'Never';
                                    $date_label = 'Processed';
                                }
                            @endphp
                            <tr>
                                <td>{{ $item->product_code }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->branch_name }}</td>
                                <td>{{ $item->store_name }} ({{ $item->store_code }})</td>
                                <td style="text-align: right">{{ number_format($item->available_quantity, 2) }}</td>
                                <td>
                                    @if ($date && $date != 'Never')
                                        {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}
                                    @else
                                        Never
                                    @endif
                                    <br><small>({{ $date_label }})</small>
                                </td>
                                <td style="text-align: right">{{ $days }}</td>
                                <td class="{{ $status_class }}" style="font-weight: bold;">{{ $status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- <tfoot>
                        <tr style="font-weight: bold; background-color: #f5f5f5;">
                            <td colspan="4" style="text-align: right">TOTAL</td>
                            <td style="text-align: right">{{ number_format($total_qty, 2) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot> --}}
                </table>
            </div>
        </div>
    </div>
</div>

