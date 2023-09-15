<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.general.sales.report.print', [$from_date, $to_date, $store_id, $category_id, $product_id, $customer_id, $payment_mode, $credit_walkedin]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ ucfirst($payment_mode) }} Sale Transactions
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
            <th>CUST NAME</th>
            <th>ITEM</th>
            <th>STORE</th>
            <th>QTY</th>
            <th>COST PRICE</th>
            <th>SELLING PRICE</th>
            <th>TOTAL COST</th>
            <th>TOTAL SALES</th>
            <th>GROSS PROFIT</th>
        </tr>
    </thead>
    @php
        $total_cost_price = 0;
        $total_sold_price = 0;
        $total_cost = 0;
        $total_sold = 0;
        $total_profit = 0;
        $total_note = 0;
        $grand_total_profit = 0;
        $last_order_date = $to_date; // This is to enable us display all credit notes beyond last order date
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
            <td>{{ $sale->invoice_no }}</td>
            <td>{{ $sale->customer }}</td>
            <td>{{ $sale->product }}</td>
            <td>{{ $sale->store }}</td>
            <td>{{ $sale->quantity }}</td>
            <td style="text-align: right">&#8358;{{ number_format($sale->cost_price, 2, '.', ',') }}</td>
            <td style="text-align: right">&#8358;{{ number_format($sale->sold_price, 2, '.', ',') }}</td>
            <td style="text-align: right">
                &#8358;{{ number_format($sale->cost_price * $sale->quantity, 2, '.', ',') }}</td>
            <td style="text-align: right">
                &#8358;{{ number_format($sale->sold_price * $sale->quantity, 2, '.', ',') }}</td>
            <td style="text-align: right">
                @php
                    $total_profit = $sale->sold_price * $sale->quantity - $sale->cost_price * $sale->quantity;
                    $grand_total_profit += $total_profit;
                @endphp
                @if ($total_profit < 0)
                    &#8358;({{ number_format(abs($total_profit), 2, '.', ',') }})
                @else
                    &#8358;{{ number_format($total_profit, 2) }}
                @endif
            </td>
        </tr>
        @php
            $notes = \App\Models\SupplierLedger::where(['date' => date_format(date_create($sale->order_date), 'Y-m-d'), 'payment_mode' => 'Credit Note'])->get();
        @endphp

        @foreach ($notes as $note)
            @if ($note != null)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}</td>
                    <td>{{ $note->Ref }}</td>
                    <td>{{ $note->supplier->name }}</td>
                    <td>Credit Note</td>
                    <td>{{ \App\Models\Category::find($note->bank_account_id)->name }}</td>
                    <td>-</td>
                    <td style="text-align: right">&#8358;{{ number_format($note->dr, 2, '.', ',') }}</td>
                    <td style="text-align: right">&#8358;{{ number_format($note->dr, 2, '.', ',') }}</td>
                    <td style="text-align: right">
                        &#8358;{{ number_format($note->dr, 2, '.', ',') }}</td>
                    <td style="text-align: right">
                        &#8358;{{ number_format($note->dr, 2, '.', ',') }}</td>
                    <td style="text-align: right">
                        &#8358;{{ number_format($note->dr, 2, '.', ',') }}
                    </td>
                </tr>
                @php
                    $total_note += $note->total_note;
                @endphp
            @endif
        @endforeach
        @php
            $total_cost_price += $sale->cost_price + $total_note;
            $total_sold_price += $sale->sold_price + $total_note;
            $total_cost += $sale->cost_price * $sale->quantity + $total_note;
            $total_sold += $sale->sold_price * $sale->quantity + $total_note;
            $total_profit = $sale->sold_price * $sale->quantity - $sale->cost_price * $sale->quantity + $total_note;
            $grand_total_profit += $total_note;
            $last_order_date = $sale->order_date;
        @endphp
    @endforeach
    @php
        $notes = \App\Models\SupplierLedger::where('date', '>', date_format(date_create($last_order_date), 'Y-m-d'))
            ->where('date', '<=', $to_date)
            ->where(['payment_mode' => 'Credit Note'])
            ->get();
    @endphp

    @foreach ($notes as $note)
        @if ($note != null)
            <tr>
                <td>{{ \Carbon\Carbon::parse($note->date)->toFormattedDateString() }}</td>
                <td>{{ $note->Ref }}</td>
                <td>{{ $note->supplier->name }}</td>
                <td>Credit Note</td>
                <td>{{ \App\Models\Category::find($note->bank_account_id)->name }}</td>
                <td>-</td>
                <td style="text-align: right">&#8358;{{ number_format($note->dr, 2, '.', ',') }}</td>
                <td style="text-align: right">&#8358;{{ number_format($note->dr, 2, '.', ',') }}</td>
                <td style="text-align: right">
                    &#8358;{{ number_format($note->dr, 2, '.', ',') }}</td>
                <td style="text-align: right">
                    &#8358;{{ number_format($note->dr, 2, '.', ',') }}</td>
                <td style="text-align: right">
                    &#8358;{{ number_format($note->dr, 2, '.', ',') }}
                </td>
            </tr>
            @php
                $total_note += $note->total_note;
            @endphp
        @endif
        @php
            $total_cost_price += $total_note;
            $total_sold_price += $total_note;
            $total_cost += $total_note;
            $total_sold += $total_note;
            $grand_total_profit += $total_note;
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th colspan="6" style="text-align: right">TOTAL</th>
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
