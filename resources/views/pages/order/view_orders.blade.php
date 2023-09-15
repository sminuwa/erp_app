<div class="card-body table-responsive">
    <table id="example1" class="table table-bordered">
        <tr>
            <th>QTY</th>
            <th>Item</th>
            <th>Store</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
        @foreach ($order->order_items()->where('status',1)->get() as $item)
            <tr>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->storeProduct->product->name }}</td>
                <td>{{ $item->storeProduct->store->name }}</td>
                <td align="right">{{ number_format($item->sold_price, 2) }}</td>
                <td align="right">{{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" align="right"><strong>Total</strong></td><td align="right">{{number_format($order->total,2)}}</td>
        </tr>
    </table>
</div>
