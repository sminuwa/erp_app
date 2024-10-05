<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!--  This file has been downloaded from bootdey.com @bootdey on twitter -->
    <!--  All snippets are MIT license http://bootdey.com/license -->
    <title>Payment Receipt - ALBABELLO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="col-md-12">
        <div class="row">

            <div class="receipt-main col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                <div class="row">
                    <div class="receipt-header">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                            <div class="receipt-left">
                                <img src="{{ asset('assets/backend/img/logo.png') }}"
                                    style="width:71px;height:71px;border-radius: 43px;" alt="Albabello Logo"
                                    class="img-circle elevation-3 img-responsive" style="opacity: .8">
                                <strong>{{App\Models\User::UserBranchName()->long_name}}</strong>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                            <div class="receipt-right">
                                <h5>AL-BABELLO</h5>
                                <p>{{ optional($payment->customer)->branch->address }} <i class="fa fa-location-arrow"></i></p>
                                <p>{{ optional($payment->customer)->branch->email }}<i class="fa fa-envelope-o"></i></p>
                                <p>{{ optional($payment->customer)->branch->phone }} <i class="fa fa-phone"></i></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="receipt-header receipt-header-mid">
                        <div class="col-xs-7 col-sm-7 col-md-7 text-left">
                            <div class="receipt-right">
                                <h5>Payment From</h5>
                                <p><b>{{ $payment->payer()->code ? $payment->payer()->code.' - '.$payment->payer()->name : ($payment->payer()->number.' - '.$payment->payer()->description) }} </b></p>
                                <p><b>Mobile :</b> {{ $payment->customer->phone }}</p>
                                <p><b>Address :</b> {{ $payment->customer->address }}</p>
                            </div>
                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-5">
                            <div class="receipt-right">
                                <p><b>Receipt No: {{ $payment->receipt_no }}</b></p>
                                <p><b>Payment Date:
                                        {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}</b></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 style="text-align: center;font-weight:700">PAYMENT</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $payment->account()->code ?? $payment->account()->number }} - {{ $payment->account()->name ?? $payment->account()->description }}</td>
                                <td class="col-md-9">
                                    @if ($payment->description == null)
                                        Payment for {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                    @else
                                        {{ $payment->description }}
                                    @endif
                                </td>
                                <td class="col-md-3" align="right"><i class="fa fa-inr"></i>
                                    &#8358; {{ number_format($payment->amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-right">
                                    <p>
                                        <strong>Amount: </strong>
                                    </p>

                                </td>
                                <td align="right">
                                    <p>
                                        <strong><i
                                                class="fa fa-inr"></i>{{ number_format($payment->amount, 2) }}</strong>
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
                </div>

                <div class="row">
                    <div class="receipt-header receipt-header-mid receipt-footer">
                        <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                            <div class="receipt-right">
                                <p><b>Created By :</b> {{ $payment->createdBy?->name }}</p>
                                <p><b>Date Created :</b> {{ Carbon\Carbon::parse($payment->created_at)->toFormattedDateString() }}</p>
                                <p><b>Printed By :</b> {{ Auth::user()->name }}</p>
                                <p><b>Printed On :</b> {{ \Carbon\Carbon::now()->toFormattedDateString() }}</p>
                                <p><b>Signatire :</b> ______________________________________</p>
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;For:
                                    ALBABELLO </span>
                                <br /><br />
                                <h5 style="color: rgb(140, 140, 140);text-align:center;">Thanks for Patronage!</h5>
                            </div>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 text-right">
                            <div class="receipt-left">
                                @php
                                    $uc = $payment->receipt_no;
                                @endphp
                                {{ QrCode::size(70)->backgroundColor(255, 55, 0)->generate("$payment->dr\n$uc\n\n.") }}<br />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style type="text/css">
        body {
            background: #eee;
            margin-top: 20px;
        }

        .text-danger strong {
            color: #9f181c;
        }

        .receipt-main {
            background: #ffffff none repeat scroll 0 0;
            border-bottom: 1px solid;
            border-top: 1px solid;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 40px 30px !important;
            position: relative;
            box-shadow: 0 1px 21px #acacac;
            color: #333333;
            font-family: open sans;
        }

        .receipt-main p {
            color: #333333;
            font-family: open sans;
            line-height: 1.42857;
        }

        .receipt-footer h1 {
            font-size: 15px;
            font-weight: 400 !important;
            margin: 0 !important;
        }

        .receipt-main::after {
            background: #414143 none repeat scroll 0 0;
            content: "";
            height: 5px;
            left: 0;
            position: absolute;
            right: 0;
            top: -13px;
        }

        .receipt-right h5 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 7px 0;
        }

        .receipt-right p {
            font-size: 12px;
            margin: 0px;
        }

        .receipt-right p i {
            text-align: center;
            width: 18px;
        }

        .receipt-main td {
            padding: 9px 20px !important;
        }

        .receipt-main th {
            padding: 13px 10px !important;
        }

        .receipt-main td {
            font-size: 13px;
            font-weight: initial !important;
        }

        .receipt-main td p:last-child {
            margin: 0;
            padding: 0;
        }

        .receipt-main td h2 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
        }

        .receipt-header-mid .receipt-left h1 {
            font-weight: 100;
            margin: 34px 0 0;
            text-align: right;
            text-transform: uppercase;
        }

        .receipt-header-mid {
            margin: 24px 0;
            overflow: hidden;
        }

        #container {
            background-color: #dcdcdc;
        }
    </style>

    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>
