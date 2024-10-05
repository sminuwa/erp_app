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
        <img id="logo" class="media" data-src="{{ asset('assets/backend/img/logo.png') }}"
            src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="height:60px;width:60px;" />
        {{App\Models\User::UserBranchName()->long_name}}<br />
        <small>{{ optional($payment->supplier)->branch->address }}</small><br />
        <small>{{ optional($payment->supplier)->branch->phone }}</small><br />
    </header>
    <div class="row">
        <div class="receipt-header receipt-header-mid">
            <div style="float:left">
                <div class="receipt-right">
                    <h5>Payment To</h5>
                    <p><b>Code: {{ $payment->supplier->code }} </b></p>
                    <p><b>Name: {{ $payment->supplier->name }} </b></p>
                    <p><b>Mobile :</b> {{ $payment->supplier->phone }}</p>
                    <p><b>Address :</b> {{ $payment->supplier->address }}</p>
                </div>
            </div>
            <div style="float:right">
                <div class="receipt-right">
                    <p><b>Receipt No: {{ $payment->Ref }}</b></p>
                    <p><b>Payment Date:
                            {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}</b></p>
                    <p><b>Payment Mode: {{ $payment->payment_mode }}</b></p>
                </div>
            </div>
        </div>
    </div>
    <table class="bill-details">
        <tbody>
            <tr>
                <th class="center-align" colspan="2"><span class="receipt"><strong> PAYMENT RECEIPT </strong></span>
                </th>
            </tr>
        </tbody>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="heading qty">Description</th>
                <th class="heading name">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if ($payment->ref == null)
                        Payment for {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                    @else
                        {{ $payment->ref }}
                    @endif
                </td>
                <td align="right">
                    &#8358;{{ number_format($payment->dr, 2) }}
                </td>
            </tr>
            <tr>
                <td class="text-right">
                    <p>
                        <strong>Amount Paid: </strong>
                    </p>
                    <p>
                        <strong>Balance Due: </strong>
                    </p>
                </td>
                <td align="right">
                    <p>
                        <strong><i class="fa fa-inr"></i>{{ number_format($payment->dr, 2) }}</strong>
                    </p>
                    <p>
                        @php
                            $balance_b_c = $payment->supplier->supplierLedgers()->sum('cr') - $payment->supplier->supplierLedgers()->sum('dr');
                        @endphp
                        <strong><i class="fa fa-inr"></i>
                            @if ($balance_b_c < 0)
                                &#8358;({{ number_format(abs($balance_b_c), 2) }})
                            @else
                                &#8358;{{ number_format($balance_b_c, 2) }}
                            @endif

                        </strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="text-left text-danger" colspan="2">
                    <p>
                        <strong>Amount in ward: </strong>

                        @php
                            $obj = new App\Models\Utility();
                        @endphp
                        <strong><i class="fa fa-inr"></i>
                            {{ $obj->convertNumberToWords($payment->dr) }}</strong>
                    </p>
                </td>
            </tr>
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
        <p>ALBABELLO TRADING COMPANY NIG LTD</p>
        @php
            $uc = $payment->ref;
        @endphp
        {{ QrCode::size(70)->backgroundColor(255, 55, 0)->generate("$payment->dr\n$uc\n\n.") }}<br />
        </ul>
    </footer>
</body>

</html>
