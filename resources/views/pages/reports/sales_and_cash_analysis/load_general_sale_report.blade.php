<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.general.sales.report.print', [$from_date, $to_date, $company_id,$branch_id, $store_id, $category_id, $product_id, $customer_id, $type]) }}"
           target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
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
        <th style="width: 50%" colspan="7">Date
            Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
        </th>
        <th style="width: 50%;text-align:right" colspan="6">Processed By {{ auth()->user()->name }}</th>
    </tr>
    <tr>
        <th>DATE</th>
        <th>CODE</th>
        <th>ITEM</th>
        <th>STORE</th>
        <th>REFERENCE</th>
        <th>ACCOUNT</th>
        <th>QTY</th>
        <th>UNIT</th>
        <th>COST PRICE()</th>
        <th>SOLD PRICE()</th>
        <th>TOTAL COST()</th>
        <th>TOTAL SALES()</th>
        <th>MARGIN()</th>
        <th>MARGIN %</th>
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
            <td>{{ $sale->product_unit }}</td>
            <td style="text-align: right">{{ number_format($sale->cost_price, 2, '.', ',') }}</td>
            <td style="text-align: right">{{ number_format($sale->sold_price, 2, '.', ',') }}</td>
            <td style="text-align: right">
                {{ number_format(str_replace(',', '', $sale->cost_price) * $sale->quantity, 2, '.', ',') }}</td>
            <td style="text-align: right">
                {{ number_format(str_replace(',', '', $sale->sold_price) * $sale->quantity, 2, '.', ',') }}</td>
            <td style="text-align: right">
                @php
                    $total_profit = str_replace(',', '', $sale->sold_price) * $sale->quantity - str_replace(',', '', $sale->cost_price) * $sale->quantity;
                    $grand_total_profit += $total_profit;

                    $total_cost_price += str_replace(',', '', $sale->cost_price);
                    $total_sold_price += str_replace(',', '', $sale->sold_price);
                    $total_cost += str_replace(',', '', $sale->cost_price) * $sale->quantity;
                    $total_sold += str_replace(',', '', $sale->sold_price) * $sale->quantity;
                @endphp

                {{ number_format($total_profit, 2) }}

            </td>
            <td>
                {{ number_format(($total_profit / (str_replace(',', '', $sale->sold_price) * $sale->quantity))*100,2)  }}
            </td>
        </tr>
        @php $credit_notes = App\Models\Order::find($sale->order_id)->creditNotes @endphp
        @if ($credit_notes != null)
            @php  $total_profit_note = 0; @endphp
            @foreach ($credit_notes as $note)
                @foreach ($note->credit_note_items()->where('store_product_id', $sale->store_product_id)->get() as $item)
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
                            {{ number_format(str_replace(',', '', $item->cost_price) * $item->quantity, 2, '.', ',') }}
                        </td>
                        <td style="text-align: right">
                            {{ number_format(str_replace(',', '', $item->sold_price) * $item->quantity, 2, '.', ',') }}
                        </td>
                        <td style="text-align: right">
                            @php
                                $total_profit_note = str_replace(',', '', $item->sold_price) * $item->quantity - str_replace(',', '', $item->cost_price) * $item->quantity;
                                $grand_total_profit += $total_profit_note;
                            @endphp
                            @if ($total_profit < 0)
                                ({{ number_format(abs($total_profit_note), 2, '.', ',') }})
                            @else
                                {{ number_format($total_profit_note, 2) }}
                            @endif
                        </td>
                        <td>
                            {{ number_format(($total_profit_note / (str_replace(',', '', $item->sold_price) * $item->quantity))*100,2)  }}
                        </td>
                    </tr>
                    @php
                        $total_cost_price += str_replace(',', '', $item->cost_price);
                        $total_sold_price += str_replace(',', '', $item->sold_price);
                        $total_cost += str_replace(',', '', $item->cost_price) * $item->quantity;
                        $total_sold += str_replace(',', '', $item->sold_price) * $item->quantity;
                    @endphp
                @endforeach
            @endforeach
        @endif
    @endforeach
    <tfoot>
    <tr>
        <th colspan="7" style="text-align: right">TOTAL</th>
        <th style="text-align: right">
            {{ number_format($total_cost_price, 2, '.', ',') }}</th>
        <th style="text-align: right">
            {{ number_format($total_sold_price, 2, '.', ',') }}</th>
        <th style="text-align: right">
            {{ number_format($total_cost, 2, '.', ',') }}
        </th>
        <th style="text-align: right">
            {{ number_format($total_sold, 2, '.', ',') }}
        </th>
        <th style="text-align: right">
            @if ($grand_total_profit < 0)
                ({{ number_format(abs($grand_total_profit), 2, '.', ',') }})
            @else
                {{ number_format($grand_total_profit, 2) }}
            @endif
        </th>
        <th>

        </th>
    </tr>
    </tfoot>
</table>
