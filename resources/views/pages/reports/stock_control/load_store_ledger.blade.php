<div class="store-ledger-results">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 px-1">
        <div>
            <h5 class="text-primary mb-0 font-weight-bold">{{ $branch->name ?? 'All Branches' }}</h5>
            <small class="text-muted">Store Quantity Report</small>
        </div>
        <a href="{{ route('ajax.print.store.ledger.reports', [$company_id,$branch_id, $store_id, is_array($category_id) ? implode(',', $category_id) : $category_id, $product_id]) }}"
           target="_BLANK" class="btn btn-success btn-sm">
            <i class="fas fa-print mr-1"></i> Print
        </a>
    </div>

    <div class="table-responsive">
        <table class="display table table-bordered table-hover table-striped table-sm mb-0" id="example1">
            <thead class="thead-light">
            <tr>
                <th colspan="5" class="border-0 py-2 text-muted small">
                    Date Processed: {{ Carbon\Carbon::parse(now())->format('l, jS F Y h:i A') }}
                </th>
                <th colspan="4" class="border-0 py-2 text-right text-muted small">
                    Processed by {{ auth()->user()->name }}
                </th>
            </tr>
            <tr>
                <th class="bg-light">Branch</th>
                <th class="bg-light">Store</th>
                <th class="bg-light">Product</th>
                <th class="bg-light">Category</th>
                <th class="bg-light text-right">Qty</th>
                <th class="bg-light text-center">Unit</th>
                <th class="bg-light">Date of receipt in transit store</th>
                <th class="bg-light text-right">Cost price</th>
                <th class="bg-light text-right">Total price</th>
            </tr>
            </thead>
            <tbody>
            @php $grantTotal = 0.0; $total_quantity = 0; @endphp
            @foreach ($stores as $store)
                @php
                    $total = remove_non_numeric($store->cost_price) * remove_non_numeric(round($store->qty_available, 6));
                    $grantTotal += $total;
                    $total_quantity += $store->qty_available;
                @endphp
                <tr>
                    <td>{{ $store->branch_code }}</td>
                    <td>{{ $store->store }}</td>
                    <td>{{ $store->code }} – {{ $store->name }}</td>
                    <td>{{ $store->category }}</td>
                    <td class="text-right">{{ number_format(round($store->qty_available, 6), 6) }}</td>
                    <td class="text-center">{{ $store->product_unit }}</td>
                    <td>{{ $store->date_receipt_transit_store ? \Carbon\Carbon::parse($store->date_receipt_transit_store)->format('d/m/Y') : '—' }}</td>
                    <td class="text-right">{{ number_format(remove_non_numeric($store->cost_price), 2) }}</td>
                    <td class="text-right font-weight-medium">{{ number_format($total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="thead-light">
            <tr>
                <th colspan="4" class="text-right bg-light">Total</th>
                <th class="text-right bg-light">{{ number_format($total_quantity, 2) }}</th>
                <th class="bg-light"></th>
                <th class="bg-light"></th>
                <th class="bg-light"></th>
                <th class="text-right bg-light font-weight-bold">{{ currency_sign() . number_format($grantTotal, 2) }}</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
