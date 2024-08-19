<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.purchase.transaction.check.report.print', [$from_date, $to_date, $store_id, $category_id, $product_id, $supplier_id, $purchase_mode]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ ucfirst($purchase_mode) }} Purchases Transaction check
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>INVOICE</th>
            <th>ITEM</th>
            <th>QTY B. PURCH</th>
            <th>QTY PURCH</th>
            <th>QTY A. PURCH</th>
            <th>STORE</th>
            <th>SUPPLIER</th>
        </tr>
    </thead>
    @php
        $total_qty = 0;
        $total_qty_before = 0;
        $total_qty_after = 0;
        $sum_cr = 0;
        $qty_before = 0;
        $qty_after = 0;
    @endphp
    @foreach ($sales as $sale)
        @php
            $sum_cr = \App\Models\StockCard::where(DB::raw('DATE(date)'), '<', \Carbon\Carbon::parse($sale->purchase_date))->where(['store_id'=>$sale->source_store_id,'product_id'=>$sale->product_id])->sum('cr');
            $sum_dr = \App\Models\StockCard::where(DB::raw('DATE(date)'), '<', \Carbon\Carbon::parse($sale->purchase_date))->where(['store_id'=>$sale->source_store_id,'product_id'=>$sale->product_id])->sum('dr');
            $qty_before = $sum_cr - $sum_dr;
        @endphp
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->purchase_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->invoice }}</td>
            <td>{{ $sale->product }}</td>
            <td style="text-align: center">{{ number_format($qty_before, 0, '.', ',') }}</td>
            <td style="text-align: center">{{ number_format($sale->quantity, 0, '.', ',') }}</td>
            <td style="text-align: center">{{ number_format($qty_before + $sale->quantity, 0, '.', ',') }}</td>
            <td>{{ $sale->store }}</td>
            <td>{{ $sale->supplier }}</td>

        </tr>
        @php
            $total_qty += $sale->quantity;
            $total_qty_before += $qty_before;
            $total_qty_after += ($qty_before + $sale->quantity);
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right">TOTAL SUMMATION</th>
            <th style="text-align: center">
                {{ number_format($total_qty_before, 0, '.', ',') }}
            </th>
            <th style="text-align: center">
                {{ number_format($total_qty, 0, '.', ',') }}
            </th>
            <th style="text-align: center">
                {{ number_format($total_qty_after, 0, '.', ',') }}
            </th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
