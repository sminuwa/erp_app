<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
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

</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <img src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="width:80px;height:80px;"
                                    alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                                <small class="float-right">Date: {{ date('l, d-M-Y h:i:s A') }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row">
                        <div class="col-sm-4">
                            <address>
                                <strong>{{ config('app.name') }}  </strong><br>
                                Address <span class="ion-ios-contact-outline"></span>: {{ $company->address }}
                                {{ $company->city }} , {{ $company->country }}<br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $company->mobile }}
                                {{ $company->phone !== null ? ', 0' . $company->phone : '' }}
                                <br>
                                Email <span class="ion-email"></span>: {{ $company->email }}
                                <br>
                                <b>Request No: {{ $purchase->reference }}</b><br>
                                <b>Reference: {{ $purchase->invoice }}</b><br>
                                <b>Date: {{ $purchase->purchase_date->toFormattedDateString() }}</b><br>
                            </address>
                        </div>

                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 style="text-align: center;">Purchase Request</h3>
                        </div>

                    </div>
                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered text-left">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Rate</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $total_discount = 0;
                                    @endphp
                                    @foreach ($purchase_details as $purchase_detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $purchase_detail->product->name }}</td>
                                            <td align="center">{{ $purchase_detail->quantity }}</td>
                                            <td align="right">
                                                &#8358;{{ number_format($purchase_detail->unit_price, 2) }}</td>
                                            <td align="right">
                                                &#8358;{{ number_format($purchase_detail->unit_price * $purchase_detail->quantity, 2) }}
                                            </td>
                                        </tr>
                                        @php $total += ($purchase_detail->unit_price * $purchase_detail->quantity);  @endphp
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: right">Total Amount</th>
                                        <th style="text-align: right;">
                                            &#8358;{{ number_format($total, 2, '.', ',') }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right">Discount</th>
                                        <th style="text-align: right;">
                                            &#8358;{{ number_format(0, 2, '.', ',') }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right">Amount Paid</th>
                                        <th style="text-align: right;">
                                            &#8358;{{ number_format($purchase->totalPaid(), 2, '.', ',') }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="text-align: right">Balance</th>
                                        <th style="text-align: right;">
                                            @if ($total - $purchase->totalPaid() < 0)
                                                &#8358;({{ number_format(abs($total - $purchase->totalPaid()), 2) }})
                                            @else
                                                &#8358;{{ number_format($total - $purchase->totalPaid(), 2) }}
                                            @endif
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><span style='font-size:14px;'></span>Amoun Paid in Words:
                                        </td>
                                        <td colspan="3"><span
                                                style=''><strong>{{ $utility->convertNumberToWords($total) }}
                                                    Naira</strong></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br />
                            <div class="row">
                                <div class="col-sm-4">
                                    Signature<br>
                                    _______________________________________<br><br>
                                    For: {{App\Models\User::UserBranchName()->long_name}}

                                </div>
                                <div class="col-sm-4">
                                    Supplier's Signature<br>
                                    _______________________________________<br>
                                </div>
                                <div class="col-sm-4" style="text-align: right">
                                    @php
                                        $uc = substr($purchase->invoice_no, 0, 6);
                                    @endphp
                                    {{ QrCode::size(100)->backgroundColor(255, 55, 0)->generate("$total\n$uc\n$purchase->invoice\n\n.") }}
                                </div>
                            </div>
                            <table class="table">
                                <tr>
                                    <td style='border-style:none;'>Printed On:
                                        {{ \Carbon\Carbon::now()->toFormattedDateString() }}<br /><br><br>
                                        Printed By: {{ Auth::user()->name }}
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


</html>
