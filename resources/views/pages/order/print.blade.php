<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Invoice - {{ config('app.name', 'Inventory Management System') }}</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />

    @include('pages.order.paper_size')
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/backend/img/logo.png') }}" alt="Company Logo">
            <h2>{{ App\Models\User::userBranchName()->long_name }}</h2>
        </div>

        <div class="invoice">
            <div class="row invoice-info">
                <div class="col-6">
                    <h5>HEAD OFFICE</h5>
                    <p>
                        {{ $company->address }}, {{ $company->city }} - {{ $company->zip_code }}, {{ $company->country }}<br>
                        Phone: {{ $company->mobile }} {{ $company->phone ? ', 0' . $company->phone : '' }}<br>
                        Email: {{ $company->email }}
                    </p>
                </div>
                <div class="col-6 text-right">
                    <h5>INVOICE</h5>
                    <p>
                        <strong>Invoice No:</strong> {{ $order->reference }}<br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}<br>
                        <strong>Printed By:</strong> {{ Auth::user()->name }}
                    </p>
                </div>
            </div>

            <div class="row invoice-info">
                <div class="col-6">
                    <h5>Customer Details</h5>
                    <p>
                        <strong>{{ $order->customer->name }}</strong><br>
                        Address: {{ $order->customer->address }}<br>
                        Phone: {{ $order->customer->phone }}
                    </p>
                </div>
                <div class="col-6 text-right">
                    <strong>Payment Mode:</strong> {{ $order->payment_mode }}<br>
                    {!! QrCode::size(70)->generate($order->total) !!}
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>CODE</th>
                        <th>DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UFM</th>
                        <th>STORE CODE</th>
                        <th>UNIT PRICE</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach ($order_details as $order_detail)
                        <tr>
                            <td>{{ $order_detail->storeProduct->product->code }}</td>
                            <td>{{ $order_detail->storeProduct->product->name }}</td>
                            <td style="text-align: center">{{ $order_detail->quantity }}</td>
                            <td astyle="text-align: center">{{ $order_detail->unit }}</td>
                            <td>{{ $order_detail->storeProduct->store->code }}</td>
                            <td style="text-align: right">&#8358;{{ number_format($order_detail->sold_price, 2) }}</td>
                            <td style="text-align: right">&#8358;{{ number_format($order_detail->sold_price * $order_detail->quantity, 2) }}</td>
                        </tr>
                        @php $total += ($order_detail->sold_price * $order_detail->quantity); @endphp
                    @endforeach
                </tbody>
            </table>

            <table class="table">
                <tr>
                    <th>Sub Total:</th>
                    <td style="text-align: right">&#8358;{{ number_format($total, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Amount:</th>
                    <td style="text-align: right">&#8358;{{ number_format(($total - $order->discount), 2) }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Amount in Words:</strong>
                        <span>{{ $utility->convertNumberToWords($total - $order->discount) }} Naira</span>
                    </td>
                </tr>
            </table>
            <p>Goods received in good condition cannot be returned.<br>Sales invalidated if goods not taken within two (2) days.</p><br/>
            <div class="signature">
                <div>Customer Signature</div>
                <div>For: {{ App\Models\User::UserBranchName()->long_name }}</div>
            </div>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
