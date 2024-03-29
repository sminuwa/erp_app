<div class="row">
    <div class="offset-10">

    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">Product Valuation Report
            Date:
            {{ \Carbon\Carbon::parse($date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>STORE</th>
            <th>ITEM CODE</th>
            <th>ITEM NAME</th>
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
            <td>{{ $stock_card->store->code ?? 'All'  }}</td>
            <td>{{ $stock_card->product->code ?? 'All' }}</td>
            <td>{{ $stock_card->product->name ?? 'All' }}</td>
            <td>{{ number_format($stock_card->credit - $stock_card->debit, 2) }}</td>
            <td>{{ number_format($stock_card->cost, 2) }}</td>
            <td style="text-align: right">
                {{ number_format(($stock_card->credit - $stock_card->debit) * $stock_card->cost, 2) }}
            </td>
        </tr>
        {{--@php
            $total_cost += $stock_card->cost_price * $stock_card->quantity;
        @endphp--}}
    @endforeach
<!--    <tfoot>
        <tr>
            <th colspan="2" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_cost, 2, '.', ',') }}
            </th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>-->
</table>
