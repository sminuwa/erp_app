{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Invoice - {{ config('app.name', 'Inventory Management System') }}</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <!-- IonIcons -->
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />
    <style>
        @media print {
            @page {
                size: A4;
                margin: 10px;
            }

            body {
                margin: 10px;
                font-size: 10px;
                /* Adjust the font size as needed */
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-11">
                            <img src="{{ asset('assets/backend/img/logo' . '.png') }}" style="width:100px;height:60px;"
                                alt="logo" class="img-circle elevation-3" style="opacity: .8">
                            <span
                                style="font-size:24px;">&nbsp;{{ App\Models\User::userBranchName()->long_name }}</span>

                            <small class="float-right">Date: {{ date('l, d-M-Y h:i:s A') }}</small>

                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4">
                            <address>
                                <h5>HEAD OFFICE</h5>
                                Address <span class="ion-ios-contact-outline"></span>: {{ $company->address }}
                                {{ $company->city }} - {{ $company->zip_code }},
                                {{ $company->country }}<br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $company->mobile }}
                                {{ $company->phone !== null ? ', 0' . $company->phone : '' }}
                                <br>
                                Email <span class="ion-email"></span>: {{ $company->email }}
                            </address>
                        </div>
                        <!-- /.col -->

                        <!-- /.col -->
                        <div class="col-sm-4 offset-3">
                            <address>
                                <h5>BRANCH OFFICE</h5>
                                Address <span class="ion-ios-contact-outline"></span>: {{ $order->branch?->address }}
                                <br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $order->branch->phone }}
                                <br>
                                Email <span class="ion-email"></span>: {{ $order->branch?->email }}
                            </address>
                        </div>
                        <!-- /.col -->
                    </div>
                    <div class="row">
                        <div class="col-sm-4 invoice-col">
                            <address>
                                <b>Customer Name:</b> {{ $order->customer->code }} -
                                {{ $order->customer->name }}<br />
                                <b>Address:</b> <span class="ion-ios-contact-outline"></span>
                                {{ $order->customer->address }}<br>
                                <b>Phone:</b> <span class="ion-android-phone-portrait"></span>
                                {{ $order->customer->phone }}<br>
                            </address>
                        </div>
                        <div class="col-sm-3 invoice-col">
                            <div style="color:aliceblue;">
                                {{ QrCode::size(70)->generate($order->total) }}<br />
                                <span style="font-size:28px;margin-top:-5px">
                                    {{ $order->payment_mode }} Sales
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <b>Invoice No:</b> {{ $order->reference }}<br>
                            <b>Date and Time:
                                {{ \Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}</b><br>
                            <b>Prepared By</b> <span class="ion-card"></span> {{ $order->sold->name ?? ''}}<br />
                            <b>Printed By <span class="ion-printer"></span>
                                &nbsp;&nbsp;{{ Auth::user()->name }}</span><span>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row" style="line-height: 0.4">
                        <div class="col-11 table-responsive">
                            <table class="table table-bordered table-condensed text-left">
                                <thead>
                                    <tr>
                                        <th style="width:10%;">CODE</th>
                                        <th style="width:30%;">DESCRIPTION</th>
                                        <th style="width:5%;">QTY</th>
                                        <th style="width:5%;">UFM</th>
                                        <th style="width:10%;">STORE CODE</th>
                                        <th style="width:10%;">UNIT PRICE</th>
                                        <th style="width:20%;">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $total_discount = 0;
                                    @endphp
                                    @foreach ($order_details as $order_detail)
                                        <tr>
                                            <td>{{ $order_detail->storeProduct->product->code }}</td>
                                            <td>{{ $order_detail->storeProduct->product->name }}</td>
                                            <td align="center">{{ $order_detail->quantity }}</td>
                                            <td align="center">{{ $order_detail->unit }}</td>
                                            <td>{{ $order_detail->storeProduct->store->code }}</td>

                                            <td align="right">
                                                &#8358;{{ number_format($order_detail->sold_price, 2) }}
                                            </td>
                                            <td align="right">
                                                &#8358;{{ number_format($order_detail->sold_price * $order_detail->quantity, 2) }}
                                            </td>
                                        </tr>
                                        @php $total += ($order_detail->sold_price * $order_detail->quantity);  @endphp
                                    @endforeach

                                </tbody>
                            </table>
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <td rowspan="3" style="text-align: left;vertical-align: bottom">
                                        Balance C/F =
                                        @if ($order->customer->amount()->sum('cr') - $order->customer->amount()->sum('dr') < 0)
                                            &#8358;({{ number_format(abs($order->customer->amount()->sum('cr') - $order->customer->amount()->sum('dr')), 2) }})
                                        @else
                                            &#8358;{{ number_format($order->customer->amount()->sum('cr') - $order->customer->amount()->sum('dr'), 2) }}
                                        @endif
                                    </td>
                                    <th style="text-align: right">Sub Total := </th>
                                    <th style="text-align: right;">
                                        &#8358;{{ number_format($total, 2, '.', ',') }}</th>
                                </tr>
                                @if ($order->discount != 0)
                                    <tr>
                                        <th style="text-align: right">Discount := </th>
                                        <th style="text-align: right;">
                                            &#8358;{{ number_format($order->discount, 2, '.', ',') }}
                                        </th>
                                    </tr>
                                @endif
                                @if ($order->refund != 0)
                                    <tr>
                                        <th style="text-align: right">Refund := </th>
                                        <th style="text-align: right;">
                                            &#8358;{{ number_format($order->refund, 2, '.', ',') }}
                                        </th>
                                    </tr>
                                @endif
                                @if ($order->show_vat == 1)
                                    <tr>
                                        <th style="text-align: right">VAT := </th>
                                        <th style="text-align: right;">
                                            &#8358;0.00
                                        </th>
                                    </tr>
                                @elseif(isset($with_vat) && $with_vat == true)
                                    <tr>
                                        <th style="text-align: right">VAT := </th>
                                        <th style="text-align: right;">
                                            &#8358;{{ $vat = $total * .075 }}
                                        </th>
                                    </tr>
                                @endif
                                <tr>
                                    <th style="text-align: right">Total Amount := </th>
                                    <th style="text-align: right;">
                                        &#8358;{{ number_format(($total - $order->discount + $order->refund)+($vat ?? 0), 2, '.', ',') }}
                                    </th>
                                </tr>


                                <tr>
                                    <td colspan="4">Amoun Paid in Words:
                                        <span>{{ $utility->convertNumberToWords($total - $order->discount + $order->refund) }}
                                            Naira</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <p style="line-height: 14px">Goods Received in good condition cannot be returned.
                                <br>Sales invalidated if goods not taken within two (2) days
                            </p>

                            <table class="table table-condensed">
                                <tr>
                                    <td colspan="4" style='border-style:none;'>
                                        ___________________________<br /><br />
                                        Customer's Signature
                                    </td>
                                    <td style="border-style:none;text-align: center">
                                   </td>
                                    <td colspan="4" style="text-align: right;border-style:none;"><span
                                            style='font-size:14px; border-style:none;'></span>
                                        Signature:
                                        _______________________________<br /><br />
                                        for: {{ App\Models\User::UserBranchName()->long_name }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->

        <!-- /.content -->

        <!-- ./wrapper -->

        <!-- REQUIRED SCRIPTS -->
        <!-- jQuery -->
        <script src="{{ asset('assets/backend/plugins/jquery/jquery.min.js') }}"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('assets/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- AdminLTE -->
        <script src="{{ asset('assets/backend/js/adminlte.js') }}"></script>

        <script>
            window.print();
        </script>

</body>


</html> --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Invoice - {{ config('app.name', 'Inventory Management System') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />

    <style>
        /* Print-specific styles */
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }

            body {
                background: #fff;
                font-size: 12px;
                margin: 0;
                padding: 0;
            }

            .container-fluid {
                width: 100%;
                max-width: 210mm;
                margin: auto;
            }

            .invoice {
                padding: 15px;
                border: 1px solid #ddd;
                max-width: 210mm;
                margin: auto;
                background: #fff;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table th,
            .table td {
                border: 1px solid #ddd;
                padding: 5px;
                text-align: left;
            }

            .table th {
                background-color: #f8f9fa;
                font-weight: bold;
            }

            .text-center {
                text-align: center;
            }

            .signature-line {
                margin-top: 30px;
                text-align: left;
                font-weight: bold;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="invoice">
                    <div class="row">
                        <div class="col-11">
                            <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:100px;height:60px;" alt="logo">
                            <span style="font-size:20px;">&nbsp;{{ App\Models\User::userBranchName()->long_name }}</span>
                            <small class="float-right">Date: {{ date('l, d-M-Y h:i:s A') }}</small>
                        </div>
                    </div>

                    <div class="row invoice-info">
                        <div class="col-sm-4">
                            <h5>HEAD OFFICE</h5>
                            <address>
                                {{ $company->address }}, {{ $company->city }} - {{ $company->zip_code }}, {{ $company->country }}<br>
                                Phone: {{ $company->mobile }} {{ $company->phone !== null ? ', 0' . $company->phone : '' }}<br>
                                Email: {{ $company->email }}
                            </address>
                        </div>

                        <div class="col-sm-4 offset-3">
                            <h5>BRANCH OFFICE</h5>
                            <address>
                                Address: {{ $order->branch?->address }}<br>
                                Phone: {{ $order->branch->phone }}<br>
                                Email: {{ $order->branch?->email }}
                            </address>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <b>Customer Name:</b> {{ $order->customer->code }} - {{ $order->customer->name }}<br>
                            <b>Address:</b> {{ $order->customer->address }}<br>
                            <b>Phone:</b> {{ $order->customer->phone }}<br>
                        </div>
                        <div class="col-sm-3 text-center">
                            {{ QrCode::size(70)->generate($order->total) }}<br>
                            <span style="font-size:18px;">{{ $order->payment_mode }} Sales</span>
                        </div>
                        <div class="col-sm-4">
                            <b>Invoice No:</b> {{ $order->reference }}<br>
                            <b>Date:</b> {{ \Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}<br>
                            <b>Prepared By:</b> {{ $order->sold->name ?? '' }}<br>
                            <b>Printed By:</b> {{ Auth::user()->name }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-11 table-responsive">
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
                                            <td align="center">{{ $order_detail->quantity }}</td>
                                            <td align="center">{{ $order_detail->unit }}</td>
                                            <td>{{ $order_detail->storeProduct->store->code }}</td>
                                            <td align="right">&#8358;{{ number_format($order_detail->sold_price, 2) }}</td>
                                            <td align="right">&#8358;{{ number_format($order_detail->sold_price * $order_detail->quantity, 2) }}</td>
                                        </tr>
                                        @php $total += ($order_detail->sold_price * $order_detail->quantity); @endphp
                                    @endforeach
                                </tbody>
                            </table>

                            <table class="table">
                                <tr>
                                    <th>Sub Total:</th>
                                    <td align="right">&#8358;{{ number_format($total, 2) }}</td>
                                </tr>
                                @if ($order->discount != 0)
                                    <tr>
                                        <th>Discount:</th>
                                        <td align="right">&#8358;{{ number_format($order->discount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Total Amount:</th>
                                    <td align="right">
                                        &#8358;{{ number_format(($total - $order->discount), 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Amount in Words:
                                        <span>{{ $utility->convertNumberToWords($total - $order->discount) }} Naira</span>
                                    </td>
                                </tr>
                            </table>

                            <p>Goods received in good condition cannot be returned.<br>Sales invalidated if goods not taken within two (2) days.</p>

                            <div class="signature-line">
                                ___________________________<br>Customer's Signature
                            </div>
                            <div class="signature-line text-right">
                                ___________________________<br>For: {{ App\Models\User::UserBranchName()->long_name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>

