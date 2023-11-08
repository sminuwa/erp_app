<div class="card-body table-responsive">
    <table id="example1" class="table table-bordered">
        <tr>
            <th>Code</th>
            <th>Item</th>
            <th>UTM</th>
            <th>Store Code</th>
            <th>QTY</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
        @foreach ($order->order_items()->where('status',1)->get() as $item)
            <tr>
                <td>{{ $item->storeProduct->product->code ?? '' }}</td>
                <td>{{ $item->storeProduct->product->name ?? '' }}</td>
                <td>{{ $item->storeProduct->product->unit ?? '' }}</td>
                <td>{{ $item->storeProduct->store->code ?? ''}}</td>
                <td>{{ $item->quantity }}</td>
                <td align="right">{{ number_format($item->sold_price, 2) }}</td>
                <td align="right">{{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6" align="right"><strong>Total</strong></td><td align="right">{{number_format($order->total,2)}}</td>
        </tr>
    </table>
</div>
