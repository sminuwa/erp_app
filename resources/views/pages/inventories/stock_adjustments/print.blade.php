<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Stock Adjustment - {{ config('app.name', 'ERP System') }}</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <!-- IonIcons -->
    {{--    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> --}}
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />
    <style>
        @media print {
            @page {
                size: A5;
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
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <img src="{{ asset('assets/backend/img/logo' . App\Models\User::userBranchAction() . '.png') }}"
                                    width="80" alt="Albabello Logo" class="" style="opacity: .8">
                                <small class="float-right">Date: {{ date('l, d-M-Y h:i:s A') }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row">
                        <div class="col-sm-4">
                            <address>
                                <strong>{{ config('app.name') }} </strong><br>
                                Address <span class="ion-ios-contact-outline"></span>: {{ $company->address ?? null }}
                                {{ $company->city ?? null }} , {{ $company->country ?? null }}<br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $company->mobile ?? null }}
                                {{ ($company->phone ?? null) !== null ? ', 0' . ($company->phone ?? null) : '' }}
                                <br>
                                Email <span class="ion-email"></span>: {{ $company->email ?? null }}
                            </address>
                        </div>
                        <div class="col-sm-8" style="text-align: right">
                            To
                            <address>
                                <strong>{{ $record->supplier->name ?? null }}</strong><br>
                                Address <span class="ion-ios-contact-outline"></span>:
                                {{ $record->supplier->address ?? null }}<br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $record->supplier->phone ?? null }}<br>
                                Email <span class="ion-email"></span>: {{ $record->supplier->email ?? null }}
                            </address>
                            <b>Truck No: {{ $record->truck_no ?? null }}</b><br>
                            <b>Reference: {{ $record->reference ?? null }}</b><br>
                            <b>ATC/WayBill No.: {{ $record->atc_no }}</b><br>
                            <b>Date: {{ $record->date->toFormattedDateString() }}</b><br>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            {{ QrCode::size(100)->generate($record->reference) }}
                            <h3>STOCK ADJUSTMENT</h3>
                        </div>

                    </div>
                    <div class="row" style="line-height: 0.4">
                        <div class="col-12">
                            <table class="table table-bordered text-left">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Store</th>
                                        <th>Product</th>
                                        <th>Expiry Date</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $total_discount = 0;
                                    @endphp
                                    @foreach ($record->products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $product->store->code . ' - ' . $product->store->name }}</td>
                                            <td>{{ $product->product->code . ' - ' . $product->product->name }}</td>
                                            <td>{{ $product->expiry_date }}</td>
                                            <td>{{ $product->quantity }}</td>
                                            <td class="text-right">
                                                {{ currency_sign() . number_format($product->cost_price, 2) }}</td>
                                            <td align="right">
                                                {{ currency_sign() . number_format($product->cost_price * $product->quantity, 2) }}
                                            </td>
                                        </tr>
                                        @php $total += ($product->cost_price * $product->quantity);  @endphp
                                    @endforeach
                                    <tr>
                                        <th colspan="6" style="text-align: right">Total Amount</th>
                                        <th style="text-align: right;">
                                            {{ currency_sign() . number_format($total, 2) }}</th>
                                    </tr>

                                    <tr>
                                        <td colspan="2"><span style='font-size:14px;'></span>Amount Paid in Words:
                                        </td>
                                        <td colspan="3"><span
                                                style=''><strong>{{ convertNumberToWords($total) }}
                                                    Naira</strong></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <!-- /.col -->
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            Signature<br>
                            _______________________________________<br><br>
                            For: {{ App\Models\User::UserBranchName()->long_name }}

                        </div>
                        <div class="col-sm-4">
                            Supplier's Signature<br>
                            _______________________________________<br>
                        </div>
                        <div class="col-sm-4" style="text-align: right">
                            @php
                                $uc = substr($record->reference, 0, 6);
                            @endphp

                        </div>
                    </div>
                    <table class="table">
                        <tr>
                            <td style='border-style:none;'>Created By:
                                {{ $record->createdBy->name }}<br /><br>
                                Posted By: {{ $record->postedBy->name }}
                            </td>
                        </tr>
                        <tr>
                            <td style='border-style:none;'>Printed On:
                                {{ \Carbon\Carbon::now()->toFormattedDateString() }}<br /><br>
                                Printed By: {{ Auth::user()->name }}
                            </td>
                        </tr>
                    </table>
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
