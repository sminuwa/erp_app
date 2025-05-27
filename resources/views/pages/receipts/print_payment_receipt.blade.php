{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - ALBABELLO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />
    @include('pages.order.paper_size')
    
    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            padding: 20px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .receipt-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .receipt-left img {
            width: 71px;
            height: 71px;
            border-radius: 43px;
            margin-bottom: 10px;
        }
        .receipt-right {
            text-align: right;
        }
        .receipt-right p {
            margin: 5px 0;
        }
        .receipt-right i {
            margin-left: 5px;
        }
        .receipt-title {
            text-align: center;
            font-weight: 700;
            margin: 20px 0;
            font-size: 24px;
        }
        .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .payment-info {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .amount-row {
            text-align: right;
        }
        .amount-words {
            color: #dc3545;
            padding: 10px;
        }
        .footer-info {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .thank-you {
            text-align: center;
            color: #8c8c8c;
            margin-top: 30px;
        }
        .qr-code {
            text-align: right;
        }
        
    </style>
</head>

<body>
    <div class="receipt-container">
        <!-- Header with Logo and Company Info -->
        <div class="receipt-header">
            <div class="receipt-left">
                <img src="{{ asset('assets/backend/img/logo.png') }}" alt="Albabello Logo" class="img-circle elevation-3 img-responsive" style="opacity: .8">
                <div><strong>{{ App\Models\User::UserBranchName()->long_name }}</strong></div>
            </div>
            <div class="receipt-right">
                <h5>AL-BABELLO</h5>
                <p>{{ optional($payment->customer)->branch->address }}</p>
                <p>{{ optional($payment->customer)->branch->email }}</p>
                <p>{{ optional($payment->customer)->branch->phone }}</p>
            </div>
        </div>

        <!-- Customer and Receipt Info -->
        <div class="customer-info">
            <div class="customer-details">
                <h5>Payment From</h5>
                <p><b>{{ $payment->payer()->code ? $payment->payer()->code . ' - ' . $payment->payer()->name : $payment->payer()->number . ' - ' . $payment->payer()->description }}</b></p>
                <p><b>Mobile:</b> {{ $payment->customer->phone }}</p>
                <p><b>Address:</b> {{ $payment->customer->address }}</p>
            </div>
            <div class="payment-info">
                <p><b>Receipt No:</b> {{ $payment->receipt_no }}</p>
                <p><b>Payment Date:</b> {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}</p>
            </div>
        </div>
        
        <!-- Receipt Title -->
        <h4 class="receipt-title">RECEIPT</h4>
        
        <!-- Payment Details Table -->
        <table>
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $payment->account()->code ?? $payment->account()->number }} - 
                        {{ $payment->account()->name ?? $payment->account()->description }}</td>
                    <td>
                        @if ($payment->description == null)
                            Payment for {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                        @else
                            {{ $payment->description }}
                        @endif
                    </td>
                    <td class="amount-row">&#8358; {{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="amount-row"><strong>Amount:</strong></td>
                    <td class="amount-row"><strong>&#8358; {{ number_format($payment->amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Amount in words:</strong>
            @php
                $obj = new App\Models\Utility();
            @endphp
            <strong>{{ $obj->convertNumberToWords($payment->amount + 0.55) }}</strong>
        </div>

        <!-- Footer Information -->
        <div class="footer-info">
            <div class="receipt-details">
                <p><b>Date Created:</b> {{ Carbon\Carbon::parse($payment->created_at)->toFormattedDateString() }}</p>
                <p><b>Created By:</b> {{ $payment->createdBy?->name }}</p>
                <p><b>Printed By:</b> {{ Auth::user()->name }}</p>
                <p><b>Printed On:</b> {{ \Carbon\Carbon::now()->toFormattedDateString() }}</p>
                
                <div class="signature-line">
                    <p><b>Signature:</b> ______________________________________</p>
                    <p>For: ALBABELLO</p>
                </div>
            </div>
            <div class="qr-code">
                @php
                    $uc = $payment->receipt_no;
                @endphp
                {{ QrCode::size(100)->generate("$payment->dr\n$uc\n\n.") }}
            </div>
        </div>
        
        <!-- Thank You Message -->
        <div class="thank-you">
            <h5>Thanks for Patronage!</h5>
        </div>
    </div>

    <script type="text/javascript">
        window.print();
    </script>
</body>
</html> --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Receipt - ALBABELLO</title>
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />
    @include('pages.order.paper_size')

    <style>
        /* Base styles */
        body {
            font-family: "Tahoma", sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
        }

        .container {
            margin: 10mm auto;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .header h2 {
            margin-top: 10px;
        }

        .invoice-info {
            margin-bottom: 20px;
        }

        .invoice-info h5 {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .table th {
            background: #f8f8f8;
            font-weight: bold;
        }

        .amount-row {
            text-align: right;
        }

        .amount-words {
            padding: 10px;
            font-style: italic;
        }

        .footer-info {
            margin-top: 20px;
            font-size: 10pt;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .golden-signature-line {
            margin-top: 20px;
            border-top: 2px solid #FFD700;
            /* Golden color */
            padding-top: 10px;
            text-align: center;
        }

        .qr-code {
            text-align: right;
            margin-top: 0;
            /* Align with signature line */
        }

        .thank-you {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        /* Conditional styles based on papersize */
        @isset($papersize)
            @if ($papersize == 'A4')
                body {
                    font-size: 11pt;
                }

                .container {
                    width: 210mm;
                    min-height: 297mm;
                    padding: 20mm;
                }

                .header img {
                    width: 100px;
                    height: auto;
                }

                .header h2 {
                    font-size: 18pt;
                }

                .table {
                    font-size: 10pt;
                }

                @media print {
                    @page {
                        size: A4;
                        margin: 10mm;
                    }

                    body,
                    .container {
                        width: 100%;
                        height: 100%;
                        margin: 0;
                        padding: 0;
                        box-shadow: none;
                        background: none;
                    }

                    .header img {
                        filter: grayscale(100%);
                    }

                    .no-print {
                        display: none;
                    }
                }
            @endif

            @if ($papersize == 'A5')
                body {
                    font-size: 9.5pt;
                }

                .container {
                    width: 140mm;
                    min-height: 200mm;
                    padding: 8mm;
                    border: none;
                }

                .header {
                    margin-bottom: 5px;
                }

                .header img {
                    width: 60px;
                    height: auto;
                }

                .header h2 {
                    font-size: 12pt;
                    margin-top: 5px;
                }

                .invoice-info {
                    font-size: 9pt;
                    margin-bottom: 5px;
                }

                .invoice-info h5 {
                    font-size: 9.5pt;
                    margin-bottom: 3px;
                }

                .table {
                    font-size: 8.5pt;
                }

                .table th,
                .table td {
                    border: 1px solid #aaa;
                    padding: 3px;
                }

                .table th {
                    background-color: #f2f2f2;
                    text-align: center;
                }

                .footer-info {
                    font-size: 9pt;
                }

                @media print {
                    @page {
                        size: A5 portrait;
                        margin: 5mm;
                    }

                    body,
                    .container {
                        width: 100%;
                        height: 100%;
                        margin: 0;
                        padding: 0;
                        box-shadow: none;
                        background: none;
                    }

                    .header img {
                        filter: grayscale(100%);
                    }

                    .no-print {
                        display: none;
                    }

                    .invoice {
                        transform: scale(0.90);
                        transform-origin: top left;
                    }
                }
            @endif
        @endisset
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/backend/img/logo.png') }}" alt="Albabello Logo">
            <h2>{{ App\Models\User::UserBranchName()->long_name }}</h2>
        </div>

        <div class="invoice">
            <div class="row invoice-info">
                <div class="col-6">
                    <h5>Payment From</h5>
                    <p><b>{{ $payment->payer()->code ? $payment->payer()->code . ' - ' . $payment->payer()->name : $payment->payer()->number . ' - ' . $payment->payer()->description }}</b>
                    </p>
                    <p><b>Mobile:</b> {{ $payment->customer->phone }}</p>
                    <p><b>Address:</b> {{ $payment->customer->address }}</p>
                </div>
                <div class="col-6 text-right">
                    <h5>AL-BABELLO</h5>
                    <p>{{ optional($payment->customer)->branch->address }}</p>
                    <p>{{ optional($payment->customer)->branch->email }}</p>
                    <p>{{ optional($payment->customer)->branch->phone }}</p>
                    <h5>RECEIPT</h5>
                    <p><b>Receipt No:</b> {{ $payment->receipt_no }}</p>
                    <p><b>Payment Date:</b> {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}</p>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payment->account()->code ?? $payment->account()->number }} -
                            {{ $payment->account()->name ?? $payment->account()->description }}</td>
                        <td>
                            @if ($payment->description == null)
                                Payment for {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                            @else
                                {{ $payment->description }}
                            @endif
                        </td>
                        <td class="amount-row">₦ {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="amount-row"><strong>Amount:</strong></td>
                        <td class="amount-row"><strong>₦ {{ number_format($payment->amount, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="amount-words">
                <strong>Amount in words:</strong>
                @php
                    $obj = new App\Models\Utility();
                @endphp
                <strong>{{ $obj->convertNumberToWords($payment->amount + 0.55) }}</strong>
            </div>

            <div class="footer-info">
                <div class="receipt-details">
                    <p><b>Date Created:</b> {{ Carbon\Carbon::parse($payment->created_at)->toFormattedDateString() }}
                    </p>
                    <p><b>Created By:</b> {{ $payment->createdBy?->name }}</p>
                    <p><b>Printed By:</b> {{ Auth::user()->name }}</p>
                    <p><b>Printed On:</b> {{ \Carbon\Carbon::now()->toFormattedDateString() }}</p>
                </div>
                <div class="qr-code">
                    @php
                        $uc = $payment->receipt_no;
                    @endphp
                    {{ QrCode::size(100)->generate("$payment->dr\n$uc\n\n.") }}
                </div>
            </div>

            <div class="golden-signature-line">
                <p><b>Signature:</b> ______________________________________</p>
                <p>For: ALBABELLO</p>
            </div>

            <div class="thank-you">
                <h5>Thanks for Patronage!</h5>
            </div>
        </div>
    </div>

    <script type="text/javascript" class="no-print">
        window.print();
    </script>
</body>

</html>
