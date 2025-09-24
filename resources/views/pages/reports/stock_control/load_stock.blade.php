{{-- <div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.current.stock.report.print', [$company_id,$branch_id, $store_id, is_array($category_id) ? implode(',', $category_id) : $category_id, $product_id]) }}"
           target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<div class="table-responsive">
    <table class="display table table-bordered caption" id="example1">
        <caption style="caption-size:top">
            <h3 style="text-align: center;">{{ $branch == null ? 'All Branches' : $branch->name . "($branch->code)" }}
            </h3>
            <h5 style="text-align: center;">CURRENT STOCK REPORT </h5>
        </caption>
        <thead>
        <tr>
            <th style="width: 50%" colspan="6">Date Processed:
                {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="7">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>BRANCH</th>
            <th>STORE</th>
            <th>PRODUCT</th>
            <th>QTY</th>
            <th>UNIT</th>
            <th>COST PRICE</th>
            <th>R PRICE</th>
            <th>W PRICE</th>
            <th>TOTAL COST</th>
            <th>TOTAL R</th>
            <th>TOTAL W</th>
            <th>R MARGIN</th>
            <th>W MARGIN</th>
        </tr>
        </thead>
        @foreach ($stores as $store)
            <tr>
                <td> {{ $store->branch_code }} </td>
                <td>{{ $store->store_code }} </td>
                <td> {{ $store->product_code }} - {{ $store->name }} </td>
                <td> {{ number_format(round($store->qty_available, 6), 6) }} </td>
                <td>{{ $store->product_unit }}</td>
                @php $cost_price = $store->cost_price; @endphp
                <td style="text-align: right;"> {{ number_format($cost_price, 2) }}</td>
                <td style="text-align: right;">
                    @php $retail_price = remove_non_numeric($store->retail_selling_price); @endphp
                    {{ number_format($retail_price, 2) }}
                </td>
                <td style="text-align: right;">
                    @php $whole_price = remove_non_numeric($store->whole_selling_price); @endphp
                    {{ number_format($whole_price, 2) }}
                </td>
                <td style="text-align: right;">
                    @php $total_cost = remove_non_numeric(round($store->qty_available,6)) * remove_non_numeric($store->cost_price); @endphp
                    {{ number_format($total_cost, 2) }}
                </td>
                <td style="text-align: right;">
                    @php $total_r_price = remove_non_numeric(round($store->qty_available,6)) * remove_non_numeric($store->retail_selling_price); @endphp
                    {{ number_format($total_r_price, 2) }}
                </td>
                <td style="text-align: right;">
                    @php $total_w_price = remove_non_numeric(round($store->qty_available,6)) * remove_non_numeric($store->whole_selling_price); @endphp
                    {{ number_format($total_w_price, 2) }}
                </td>
                <td style="text-align: right;">
                    {{ number_format($retail_price - $cost_price, 2) }}
                </td>
                <td style="text-align: right;">
                    {{ number_format($whole_price - $cost_price, 2) }}
                </td>
            </tr>
        @endforeach
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div> --}}
<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.current.stock.report.print', [$company_id,$branch_id, $store_id, is_array($category_id) ? implode(',', $category_id) : $category_id, $product_id]) }}"
           target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>

@php
    // Running totals
    $sum_qty = 0; $sum_total_cost = 0; $sum_total_r = 0; $sum_total_w = 0;
    $sum_r_margin = 0; $sum_w_margin = 0; $sum_total_r_margin = 0; $sum_total_w_margin = 0;
@endphp

<div class="table-responsive">
    <table class="display table table-bordered caption" id="example1">
        <caption style="caption-size:top">
            <h3 style="text-align: center;">{{ $branch == null ? 'All Branches' : $branch->name . "($branch->code)" }}</h3>
            <h5 style="text-align: center;">CURRENT STOCK REPORT </h5>
        </caption>
        <thead>
        <tr>
            <th style="width: 50%" colspan="9">Date Processed:
                {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="8">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>BRANCH</th>
            <th>STORE</th>
            <th>PRODUCT</th>
            <th>QTY</th>
            <th>UNIT</th>
            <th>COST PRICE</th>
            <th>R PRICE</th>
            <th>W PRICE</th>
            <th>TOTAL COST</th>
            <th>TOTAL R</th>
            <th>TOTAL W</th>
            <th>R MARGIN</th>
            <th>W MARGIN</th>
            <th>TOTAL R MARGIN</th>
            <th>TOTAL W MARGIN</th>
            <th>R MARGIN %</th>
            <th>W MARGIN %</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($stores as $store)
            @php
                $qty          = round($store->qty_available, 6);
                $unit         = $store->product_unit;
                $cost_price   = remove_non_numeric($store->cost_price);
                $retail_price = remove_non_numeric($store->retail_selling_price);
                $whole_price  = remove_non_numeric($store->whole_selling_price);

                $total_cost   = $qty * $cost_price;
                $total_r      = $qty * $retail_price;
                $total_w      = $qty * $whole_price;

                $r_margin     = $retail_price - $cost_price;            // per-unit retail margin
                $w_margin     = $whole_price  - $cost_price;            // per-unit wholesale margin

                // Total margin calculations (R MARGIN * QTY and W MARGIN * QTY)
                $total_r_margin = $r_margin * $qty;
                $total_w_margin = $w_margin * $qty;

                // Margin percentages
                $r_margin_pct = ($retail_price > 0) ? (($r_margin / $retail_price) * 100) : 0;
                $w_margin_pct = ($whole_price > 0) ? (($w_margin / $whole_price) * 100) : 0;

                // Accumulate totals
                $sum_qty         += $qty;
                $sum_total_cost  += $total_cost;
                $sum_total_r     += $total_r;
                $sum_total_w     += $total_w;
                $sum_r_margin    += ($total_r - $total_cost);   // total retail margin
                $sum_w_margin    += ($total_w - $total_cost);   // total wholesale margin
                $sum_total_r_margin += $total_r_margin;         // sum of total R margins
                $sum_total_w_margin += $total_w_margin;         // sum of total W margins
            @endphp
            <tr>
                <td>{{ $store->branch_code }}</td>
                <td>{{ $store->store_code }}</td>
                <td>{{ $store->product_code }} - {{ $store->name }}</td>
                <td>{{ number_format($qty, 6) }}</td>
                <td>{{ $unit }}</td>

                <td style="text-align:right;">{{ number_format($cost_price, 2) }}</td>
                <td style="text-align:right;">{{ number_format($retail_price, 2) }}</td>
                <td style="text-align:right;">{{ number_format($whole_price,  2) }}</td>

                <td style="text-align:right;">{{ number_format($total_cost, 2) }}</td>
                <td style="text-align:right;">{{ number_format($total_r,    2) }}</td>
                <td style="text-align:right;">{{ number_format($total_w,    2) }}</td>

                <td style="text-align:right;">{{ number_format($r_margin, 2) }}</td>
                <td style="text-align:right;">{{ number_format($w_margin, 2) }}</td>
                <td style="text-align:right;">{{ number_format($total_r_margin, 2) }}</td>
                <td style="text-align:right;">{{ number_format($total_w_margin, 2) }}</td>
                <td style="text-align:right;">{{ number_format($r_margin_pct, 2) }}%</td>
                <td style="text-align:right;">{{ number_format($w_margin_pct, 2) }}%</td>
            </tr>
        @endforeach
        </tbody>

        @php
            // Footer summary margin percentages
            $footer_r_margin_pct = ($sum_total_r > 0) ? (($sum_r_margin / $sum_total_r) * 100) : 0;
            $footer_w_margin_pct = ($sum_total_w > 0) ? (($sum_w_margin / $sum_total_w) * 100) : 0;
        @endphp

        <tfoot>
        <tr>
            <th colspan="3" style="text-align:right;">TOTALS</th>
            <th>{{ number_format($sum_qty, 6) }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>

            <th style="text-align:right;">{{ number_format($sum_total_cost, 2) }}</th>
            <th style="text-align:right;">{{ number_format($sum_total_r,    2) }}</th>
            <th style="text-align:right;">{{ number_format($sum_total_w,    2) }}</th>

            {{-- Total Margin (Retail) --}}
            <th style="text-align:right;">{{ number_format($sum_r_margin, 2) }}</th>

            {{-- Total Margin (Wholesale) --}}
            <th style="text-align:right;">{{ number_format($sum_w_margin, 2) }}</th>

            {{-- New Total R Margin and Total W Margin columns --}}
            <th style="text-align:right;">{{ number_format($sum_total_r_margin, 2) }}</th>
            <th style="text-align:right;">{{ number_format($sum_total_w_margin, 2) }}</th>

            {{-- Margin Percentages --}}
            <th style="text-align:right;">{{ number_format($footer_r_margin_pct, 2) }}%</th>
            <th style="text-align:right;">{{ number_format($footer_w_margin_pct, 2) }}%</th>
        </tr>
        </tfoot>
    </table>
</div>
