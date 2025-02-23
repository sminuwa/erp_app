<div class="row">
    <div class="offset-10">
        <form action="{{ route('ajax.product.valuation.report.print') }}" method="post" target="_blank" class="d-inline">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product_id }}">
            <input type="hidden" name="store_id" value="{{ $store_id }}">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">
            <input type="hidden" name="category_id" value="{{ $category_id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <button type="submit" class="btn btn-success btn-sm" name="submit">Print</button>
        </form>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">Product Valuation Report
            Date:
            {{ \Carbon\Carbon::parse($date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            @if($store_group != '')
            <th>STORE</th>
            @endif
            @if($category_id == '')
            <th>CATEGORY</th>
            @endif
            <th>PRODUCT CODE</th>
            <th>PRODUCT NAME</th>
            <th>QTY</th>
            <th>COST PRICE</th>
            <th>TOTAL </th>
        </tr>
    </thead>
    @php
        $total_cost = 0;
    @endphp
    @foreach ($stock_cards as $stock_card)
        <tr>
            @if($store_group != '')
            <td>{{ $stock_card->store->code ?? ''  }}</td>
            @endif
            @if($category_id == '')
            <td>{{ $stock_card->product->category->name ?? '' }}</td>
            @endif
            <td>{{ $stock_card->product->code ?? '' }}</td>
            <td>{{ $stock_card->product->name ?? '' }}</td>
            <td>{{ number_format($stock_card->quantity, 2) }}</td>
            <td>{{ number_format(intval($stock_card->cost), 2) }}</td>
            <td style="text-align: right">
                {{ number_format(intval($stock_card->quantity) * intval($stock_card->cost), 2) }}
            </td>
        </tr>
        @php
            $total_cost += intval($stock_card->cost) * intval($stock_card->quantity);
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="
                @if($store_group != '') 4 @else 3 @endif
            " style="text-align: right">TOTAL</th>
            <th class="text-center" colspan="3">{{ number_format($total_cost, 2, '.', ',') }}</th>
        </tr>
    </tfoot>
</table>
