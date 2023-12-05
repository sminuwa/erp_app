<!DOCTYPE html>
<html lang="en">

<head>
    <link href="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" rel="shortcut icon">
    <script>
        window.print()
    </script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'PT Sans', sans-serif;
        }

        @page {
            size: 2.8in 11in;
            margin-top: 0cm;
            margin-left: 0cm;
            margin-right: 0cm;
        }

        table {
            width: 100%;
        }

        tr {
            width: 100%;

        }

        h1 {
            text-align: center;
            vertical-align: middle;
        }

        #logo {
            width: 60%;
            text-align: center;
            -webkit-align-content: center;
            align-content: center;
            padding: 5px;
            margin: 2px;
            display: block;
            margin: 0 auto;
        }

        header {
            width: 100%;
            text-align: center;
            -webkit-align-content: center;
            align-content: center;
            vertical-align: middle;
        }

        .items thead {
            text-align: center;
        }

        .center-align {
            text-align: center;
        }

        .bill-details td {
            font-size: 12px;
        }

        .receipt {
            font-size: medium;
        }

        .items .heading {
            font-size: 12.5px;
            text-transform: uppercase;
            border-top: 1px solid black;
            margin-bottom: 4px;
            border-bottom: 1px solid black;
            vertical-align: middle;
        }

        .items thead tr th:first-child,
        .items tbody tr td:first-child {
            width: 47%;
            min-width: 47%;
            max-width: 47%;
            word-break: break-all;
            text-align: left;
        }

        .items td {
            font-size: 12px;
            text-align: right;
            vertical-align: bottom;
        }

        .price::before {
            content: "\20A6";
            font-family: Arial;
            text-align: right;
        }

        .sum-up {
            text-align: right !important;
        }

        .total {
            font-size: 13px;
            border-top: 1px dashed black !important;
            border-bottom: 1px dashed black !important;
        }

        .total.text,
        .total.price {
            text-align: right;
        }

        .total.price::before {
            content: "\20A6";
        }

        .line {
            border-top: 1px solid black !important;
        }

        .heading.rate {
            width: 20%;
        }

        .heading.amount {
            width: 25%;
        }

        .heading.qty {
            width: 5%
        }

        p {
            padding: 1px;
            margin: 0;
        }

        section,
        footer {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <header>
        <img id="logo" class="media" data-src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}"
            src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="height:60px;width:60px;" />
        {{App\Models\User::UserBranchName()->long_name}}<br />
        <small>{{ optional($order)->branch->address }}</small><br />
        <small>{{ optional($order)->branch->phone }}</small><br />
    </header>
    <small>Invoice Number : {{ $order->invoice_no }}</small>
    <table class="bill-details">
        <tbody>
            <tr>
                <td colspan="3"><strong>Date :
                    </strong><span>{{ \Carbon\Carbon::parse($order->order_date)->toDayDateTimeString() }}</span></td>
            </tr>
            <tr>
                <td><strong>Name:</strong> <span>{{ $order->customer->name }}</span></td>
                <td><strong>Phone # :</strong> <span>{{ $order->customer->phone }}</span></td>
            </tr>
            <tr>
                <th class="center-align" colspan="2"><span class="receipt"><strong> {{ $order->payment_mode }}
                            Sales</strong></span></th>
            </tr>
        </tbody>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="heading">Code</th>
                <th class="heading">Item</th>
                <th class="heading">Qty</th>
                <th class="heading">Unit</th>
                <th class="heading">Store</th>
                <th class="heading">Unit Price</th>
                <th class="heading">Sub Total</th>
            </tr>
        </thead>
        @php $total = 0; @endphp
        <tbody>
            @foreach ($order_details as $order_detail)
                <tr>
                    <td>{{ $order_detail->storeProduct->product->code }}</td>
                    <td>{{ $order_detail->storeProduct->product->name }}</td>
                    <td style="text-align:center">{{ $order_detail->quantity }}</td>
                    <td>{{ $order_detail->storeProduct->product->unit }}</td>
                    <td>{{ $order_detail->storeProduct->store->code }}</td>
                    
                    <td style="text-align:right">&#8358;{{ number_format($order_detail->sold_price, 2) }}
                    </td>
                    <td style="text-align:right">
                        &#8358;{{ number_format($order_detail->sold_price * $order_detail->quantity, 2) }}
                    </td>
                </tr>
                @php $total += ($order_detail->sold_price * $order_detail->quantity);  @endphp
            @endforeach
            <tr>
                <td colspan="6" class="sum-up line">SubTotal: </td>
                <td class="line price">{{ number_format($total, 2) }}</td>
            </tr>
            @if ($order->discount != 0)
                <tr>
                    <td colspan="6" class="sum-up line">Discount: </td>
                    <td class="line price">{{ number_format($order->discount, 2) }}</td>
                </tr>
            @endif
            @if ($order->refund != 0)
                <tr>
                    <td colspan="6" class="sum-up line">Refund: </td>
                    <td class="line price">{{ number_format($order->refund, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="6" class="sum-up line">Total Amount</td>
                <td class="line price">{{ number_format($total+$order->discount-$order->refund, 2) }}</td>
            </tr>
            {{-- <tr>
                <td colspan="6">Balance C/F =
                    @if ($order->customer->amount()->sum('cr') - $order->customer->amount()->sum('dr') < 0)
                        &#8358;({{ number_format(abs($order->customer->amount()->sum('cr') - $order->customer->amount()->sum('dr')), 2) }})
                    @else
                        &#8358;{{ number_format($order->customer->amount()->sum('cr') - $order->customer->amount()->sum('dr'), 2) }}
                    @endif
                </td>
            </tr> --}}
        </tbody>
    </table>
    <section>
        <p>
            Date/Printed By : <span>{{ \Carbon\Carbon::parse(\Carbon\Carbon::now())->toDayDateTimeString() }}
                {{ Auth::user()->name }}</span>
        </p>
        <p style="text-align:center">
            Thank you for your patronage!
        </p>
    </section>
    <footer style="text-align:center">
        <p>{{App\Models\User::UserBranchName()->short_name}}</p>
        <span data-lucide="inbox">{{ optional($company)->email }}</span>
        <span data-lucide="phone">{{ optional($company)->phone }}</span>
        </ul>
    </footer>
</body>

</html>
