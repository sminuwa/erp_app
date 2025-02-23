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
    {{--    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> --}}
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />
  @include('pages.order.paper_size')
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
                                <img src="{{ asset('assets/backend/img/logo.png') }}"
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
                                {{ $company->phone ?? null }}
                                <br>
                                Email <span class="ion-email"></span>: {{ $company->email ?? null }}
                            </address>
                        </div>
                        <div class="col-sm-8" style="text-align: right">
                            To
                            <address>
                                <strong>{{ $purchase->supplier->name ?? null }}</strong><br>
                                Address <span class="ion-ios-contact-outline"></span>:
                                {{ $purchase->supplier->address ?? null }}<br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $purchase->supplier->phone ?? null }}<br>
                                Email <span class="ion-email"></span>: {{ $purchase->supplier->email ?? null }}
                            </address>
                            <b>Truck No: {{ $purchase->truck_no ?? null }}</b><br>
                            <b>Reference: {{ $purchase->reference ?? null }}</b><br>
                            <b>ATC/WayBill No.: {{ $purchase->atc_no ?? null }}</b><br>
                            <b>Date: {{ $purchase->purchase_date->toFormattedDateString() ?? null }}</b><br>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            {{ QrCode::size(100)->generate($purchase->reference) }}
                            <h3>Purchase GRN</h3>
                        </div>

                    </div>
                    <div class="row" style="line-height: 0.6">
                        <div class="col-12">
                            <table class="table table-bordered text-left">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>UTM</th>
                                        <th>Quantity</th>
                                        <th>Rate</th>
                                        <th>Store</th>
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
                                            <td>{{ $purchase_detail->product->code }}</td>
                                            <td>{{ $purchase_detail->product->name }}</td>
                                            <td>{{ $purchase_detail->product->unit }}</td>
                                            <td align="center">{{ $purchase_detail->quantity }}</td>
                                            <td align="right">
                                                &#8358;{{ number_format($purchase_detail->unit_price, 2) }}</td>
                                            <td>{{ $purchase_detail->store->code ?? null }}</td>
                                            <td align="right">
                                                &#8358;{{ number_format($purchase_detail->unit_price * $purchase_detail->quantity, 2) }}
                                            </td>
                                        </tr>
                                        @php $total += ($purchase_detail->unit_price * $purchase_detail->quantity);  @endphp
                                    @endforeach
                                    <tr>
                                        <th colspan="7" style="text-align: right">Total Amount</th>
                                        <th style="text-align: right;">
                                            &#8358;{{ number_format($total, 2, '.', ',') }}</th>
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
                                $uc = substr($purchase->reference, 0, 6);
                            @endphp

                        </div>
                    </div>
                    <table class="table">
                        <tr>
                            <td style='border-style:none;'>Created By:
                                {{ $purchase->createdBy->name }}<br /><br>
                                Posted By: {{ $purchase->postedBy->name }}
                            </td>
                        </tr>
                        <tr>
                            <td style='border-style:none;'>Printed On:
                                {{ \Carbon\Carbon::now()->toFormattedDateString() }}<br /><br><br>
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
