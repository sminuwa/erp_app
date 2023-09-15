<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.stock.ledger.report.print', [$from_date, $to_date, $store_id, $category_id, $product_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<h3>STORE/ITEM: {{ $product->store }}/{{ $product->item }}</h3>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <thead>
        <tr>
            <th>DATE</th>
            <th>INVOICE</th>
            <th>IN QTY</th>
            <th>QTY SOLD</th>
            <th>QTY PURCHASE</th>
            <th>STOCK ADJUST</th>
            <th>QTY TRANS</th>
            <th>QTY RECV</th>
            <th>CUSTOMER NAME</th>
            <th>BALANCE</th>
        </tr>
    </thead>
    <tbody>
        @php echo $result @endphp
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9" align="right">Current Stock Balance B/F</td>
            <td>{{ number_format($qty_in_stock, 0) }}</td>
        </tr>
    </tfoot>
</table>
