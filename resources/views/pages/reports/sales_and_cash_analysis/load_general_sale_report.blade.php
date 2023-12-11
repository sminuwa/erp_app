<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.general.sales.report.print', [$from_date, $to_date, $branch_id, $store_id, $category_id, $product_id, $customer_id, $type]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch == null ? 'All Branches' : $branch->name . "($branch->code)" }} </h3>
        <h5 style="text-align: center;">{{ ucfirst($type) }} Sale Transactions
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>CODE</th>
            <th>ITEM</th>
            <th>STORE</th>
            <th>REFERENCE</th>
            <th>ACCOUNT</th>
            <th>QTY</th>
            <th>COST PRICE(&#8358;)</th>
            <th>SOLD PRICE(&#8358;)</th>
            <th>TOTAL COST(&#8358;)</th>
            <th>TOTAL SALES(&#8358;)</th>
            <th>MARGIN(&#8358;)</th>
        </tr>
    </thead>
    @php
        $total_cost_price = 0;
        $total_sold_price = 0;
        $total_cost = 0;
        $total_sold = 0;
        $total_profit = 0;
        $grand_total_profit = 0;
        $last_order_date = $to_date; // This is to enable us display all credit notes beyond last order date
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->order_id }}-{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->product_code }}</td>
            <td>{{ $sale->product_name }}</td>
            <td>{{ $sale->store_code }}</td>
            <td>{{ $sale->reference }}</td>
            <td>{{ $sale->customer }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">{{ number_format($sale->cost_price, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->sold_price, 2, '.', ',') }}</td>
            <td style="text-align: right">
                {{ number_format($sale->cost_price * $sale->quantity, 2, '.', ',') }}</td>
            <td style="text-align: right">
                {{ number_format($sale->sold_price * $sale->quantity, 2, '.', ',') }}</td>
            <td style="text-align: right">
                @php
                    $total_profit = $sale->sold_price * $sale->quantity - $sale->cost_price * $sale->quantity;
                    $grand_total_profit += $total_profit;
                @endphp
                @if ($total_profit < 0)
                    ({{ number_format(abs($total_profit), 2, '.', ',') }})
                @else
                    {{ number_format($total_profit, 2) }}
                @endif
            </td>
        </tr>
        @php $credit_notes = App\Models\Order::find($sale->order_id)->creditNotes @endphp
        @if ($credit_notes != null)
            @foreach ($credit_notes as $note)
                @foreach ($note->credit_note_items()->get() as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($note->date)->toFormattedDateString() }}</td>
                        <td>{{ $item->storeProduct->product->code }}</td>
                        <td>{{ $item->storeProduct->product->name }}</td>
                        <td>{{ $item->storeProduct->store->code }}</td>
                        <td>{{ $note->reference }}</td>
                        <td>{{ $sale->customer }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="text-align: right">{{ number_format($item->cost_price, 2, '.', ',') }}</td>
                        <td style="text-align: right">-{{ number_format($item->sold_price, 2, '.', ',') }}</td>
                        <td style="text-align: right">
                            {{ number_format($item->cost_price * $item->quantity, 2, '.', ',') }}</td>
                        <td style="text-align: right">
                            {{ number_format(-$item->sold_price * $item->quantity, 2, '.', ',') }}</td>
                        <td style="text-align: right">
                            @php
                                $total_profit += -$item->sold_price * $item->quantity - $item->cost_price * $item->quantity;
                                $grand_total_profit += $total_profit;
                            @endphp
                            @if ($total_profit < 0)
                                ({{ number_format(abs($total_profit), 2, '.', ',') }})
                            @else
                                {{ number_format($total_profit, 2) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        @endif
        @php
            $total_cost_price += $sale->cost_price;
            $total_sold_price += $sale->sold_price;
            $total_cost += $sale->cost_price * $sale->quantity;
            $total_sold += $sale->sold_price * $sale->quantity;
            $last_order_date = $sale->order_date;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="7" style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_cost_price, 2, '.', ',') }}</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_sold_price, 2, '.', ',') }}</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_cost, 2, '.', ',') }}
            </th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_sold, 2, '.', ',') }}
            </th>
            <th style="text-align: right">
                @if ($grand_total_profit < 0)
                    &#8358;({{ number_format(abs($grand_total_profit), 2, '.', ',') }})
                @else
                    &#8358;{{ number_format($grand_total_profit, 2) }}
                @endif
            </th>
        </tr>
    </tfoot>
</table>
