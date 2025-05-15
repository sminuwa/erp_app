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
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />

    @include('pages.order.paper_size')

</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('assets/backend/img/logo.png') }}" alt="Company Logo">
            <h2>{{ config('app.name', 'Inventory Management System') }}</h2>
        </div>

        <!-- Invoice Information -->
        <div class="invoice">
            <div class="row invoice-info">
                <div class="col-6">
                    <h5>HEAD OFFICE</h5>
                    <p>
                        {{ $company->address ?? '' }}, {{ $company->city ?? '' }}, {{ $company->country ?? '' }}<br>
                        Phone: {{ $company->mobile ?? '' }} {{ $company->phone ?? '' }}<br>
                        Email: {{ $company->email ?? '' }}
                    </p>
                </div>
                <div class="col-6 text-right">
                    <h5>PURCHASE INVOICE</h5>
                    <p>
                        <strong>Truck No:</strong> {{ $purchase->truck_no }}<br>
                        <strong>Reference:</strong> {{ $purchase->reference }}<br>
                        <strong>ATC/WayBill No.:</strong> {{ $purchase->atc_no }}<br>
                        <strong>Date:</strong> {{ $purchase->purchase_date->toFormattedDateString() }}<br>
                        <strong>Printed By:</strong> {{ Auth::user()->name }}
                    </p>
                </div>
            </div>

            <!-- Supplier & QR Code -->
            <div class="row invoice-info">
                <div class="col-6">
                    <h5>Supplier Details</h5>
                    <p>
                        <strong>{{ $purchase->supplier->name }}</strong><br>
                        Address: {{ $purchase->supplier->address }}<br>
                        Phone: {{ $purchase->supplier->phone }}<br>
                        Email: {{ $purchase->supplier->email }}
                    </p>
                </div>
                <div class="col-6 text-right">
                    {!! QrCode::size(70)->generate($purchase->reference) !!}
                    <h3>Purchase GRN</h3>
                </div>
            </div>

            <!-- Invoice Table -->
            <table class="table">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Code</th>
                        <th>Product Name</th>
                        <th>UOM</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Store</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach ($purchase_details as $purchase_detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $purchase_detail->product->code }}</td>
                            <td>{{ $purchase_detail->product->name }}</td>
                            <td>{{ $purchase_detail->product->unit }}</td>
                            <td align="center">{{ $purchase_detail->quantity }}</td>
                            <td align="right">&#8358;{{ number_format($purchase_detail->unit_price, 2) }}</td>
                            <td>{{ $purchase_detail->store->code }}</td>
                            <td align="right">
                                &#8358;{{ number_format($purchase_detail->unit_price * $purchase_detail->quantity, 2) }}
                            </td>
                        </tr>
                        @php $total += ($purchase_detail->unit_price * $purchase_detail->quantity); @endphp
                    @endforeach
                    <tr>
                        <th colspan="7" class="text-right">Total Amount</th>
                        <th class="text-right">&#8358;{{ number_format($total, 2, '.', ',') }}</th>
                    </tr>
                </tbody>
            </table>

            <p><strong>Amount in Words:</strong> {{ $utility->convertNumberToWords($total) }} Naira</p>
            <br/>
            <!-- Signatures -->
            <div class="signature-box">
                <div>Authorized Signature</div>
                <div>Supplier's Signature</div>
            </div>

            <p>Created By: {{ $purchase->createdBy->name }}</p>
            <p>Posted By: {{ $purchase->postedBy->name ?? ''}}</p>
            <p>Printed On: {{ \Carbon\Carbon::now()->toFormattedDateString() }}</p>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
