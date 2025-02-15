@if ($inventory->isEmpty())
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
@endif
