<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('ajax.customer.sale.report.print', [$from_date, $to_date, $company_id,$branch_id,$store_id, $category_id, $product_id, $customer,$matching]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ ucfirst($customer) }} Sales Transactions
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
        <h5 style="font-weight: 900;">
            Keyword Search: {{ $customer }}<br>
            Store/Product:
            {{ $store_id == 'all' ? 'All stores' : \App\Models\Store::find($store_id)->name }}/{{ $product_id == 'all' ? 'All products' : \App\Models\Product::find($product_id)->name }}<br />
        </h5>
    </caption>

    <thead>
        <tr>
            <th style="width: 50%" colspan="2">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>CODE</th>
            <th>CUSTOMER NAMES</th>
            <th>TOTAL SALES</th>
        </tr>
    </thead>
    @php
        $total_sold = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->customer }}</td>
            <td style="text-align: right">
                {{ number_format($sale->total, 2, '.', ',') }}</td>
        </tr>
        @php
            $total_sold += $sale->total;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="2">TOTAL</th>
            <th style="text-align: right">
                {{ number_format($total_sold, 2, '.', ',') }}</th>
        </tr>
    </tfoot>
</table>
