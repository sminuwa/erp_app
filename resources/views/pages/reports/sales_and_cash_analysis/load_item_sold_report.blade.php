<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.total.item.sold.report.print', [$from_date, $to_date, $branch_id, $category_id, $product_id, $customer_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{$branch->name ?? 'All Branches'}}</h3>
        <h5 style="text-align: center;">TOTAL ITEMS SOLD TO CUSTOMERS
            FROM
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
        <h5 style="text-align: center;">
            GROUP/PRODUCT NAME: {{$category_id =='all'?"All Categoris":\App\Models\Category::find($category_id)->name}}/{{$product_id =='all'?"All products":\App\Models\Product::find($product_id)->name}}
        </h5>
    </caption>

    <thead>
        <tr>
            <th>CODE</th>
            <th>ITEM</th>
            <th>QUANTITY</th>
            <th>CUSTOMER</th>
        </tr>
    </thead>
    @php
        
        $total_qty = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->product }}</td>
            <td style="text-align: center">{{ $sale->quantity }}</td>
            <td>{{ $sale->customer }}</td>
        </tr>
        @php
            $total_qty += $sale->quantity;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="2">TOTAL</th>
            <th style="text-align: center">
                {{ number_format($total_qty, 0) }}</th>
            <th style="text-align: right">

            </th>
        </tr>
    </tfoot>
</table>
