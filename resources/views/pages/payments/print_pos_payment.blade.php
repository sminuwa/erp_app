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
        <small>{{ optional($payment->customer)->branch->address }}</small><br />
        <small>{{ optional($payment->customer)->branch->phone }}</small><br />
    </header>
    <div class="row">
        <div class="receipt-header receipt-header-mid">
            <div style="float:left">
                <div class="receipt-right">
                    <h5>Payment From</h5>
                    <p><b>{{ $payment->payer()->code ? $payment->payer()->code.' - '.$payment->payer()->name : ($payment->payer()->number.' - '.$payment->payer()->description) }} </b></p>
                    <p><b>Mobile :</b> {{ $payment->customer->phone }}</p>
                    <p><b>Address :</b> {{ $payment->customer->address }}</p>
                </div>
            </div>
            <div style="float:right">
                <div class="receipt-right">
                    <p><b>Receipt No: {{ $payment->receipt_no }}</b></p>
                    <p><b>Payment Date:
                            {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}</b></p>
                </div>
            </div>
        </div>
    </div>
    <table class="bill-details">
        <tbody>
            <tr>
                <th class="center-align" colspan="2"><span class="receipt"><strong> PAYMENT </strong></span>
                </th>
            </tr>
        </tbody>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="heading qty">Account</th>
                <th class="heading qty">Description</th>
                <th class="heading name">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $payment->account()->code ?? $payment->account()->number }} - {{ $payment->account()->name ?? $payment->account()->description }}</td>
                <td>
                    @if ($payment->description == null)
                        Payment for {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                    @else
                        {{ $payment->description }}
                    @endif
                </td>
                <td align="right">
                    &#8358;{{ number_format($payment->amount, 2) }}
                </td>
            </tr>
            <tr>
                <td class="text-right">
                    <p>
                        <strong>Amount: </strong>
                    </p>
                </td>
                <td align="right">
                    <p>
                        <strong><i class="fa fa-inr"></i>{{ number_format($payment->amount, 2) }}</strong>
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
                            {{ $obj->convertNumberToWords($payment->amount) }}</strong>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <section>
        <p>
            Date : <span>{{ \Carbon\Carbon::parse(\Carbon\Carbon::now())->toDayDateTimeString() }}
                </span>
        </p>
        <p><b>Created By :</b> {{ $payment->createdBy?->name }}</p>
        <p style="text-align:center">
            Thank you for your patronage!
        </p>
    </section>
    <footer style="text-align:center">
        <p>{{App\Models\User::UserBranchName()->long_name}}</p>
        @php
            $uc = $payment->description;
        @endphp
        {{ QrCode::size(70)->backgroundColor(255, 55, 0)->generate("$payment->amount\n$uc\n\n.") }}<br />
        </ul>
    </footer>
</body>

</html>
