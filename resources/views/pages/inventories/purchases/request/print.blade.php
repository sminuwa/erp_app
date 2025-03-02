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
   @include('pages.order.paper_size')
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
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
                                Address <span class="ion-ios-contact-outline"></span>: {{ $purchase->branch?->address }}
                                <br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $purchase->branch->phone }}
                                <br>
                                Email <span class="ion-email"></span>: {{ $purchase->branch?->email }}
                            </address>
                        </div>
                        <!-- /.col -->
                    </div>
                    <div class="row">
                        <div class="col-sm-4 invoice-col">
                            <address>
                                <b>Supplier Name:</b> {{ $purchase->supplier->code }} - {{ $purchase->supplier->name }}<br />
                                <b>Address:</b> <span class="ion-ios-contact-outline"></span>
                                {{ $purchase->supplier->address }}<br>
                                <b>Phone:</b> <span class="ion-android-phone-portrait"></span>
                                {{ $purchase->supplier->phone }}<br>
                            </address>
                        </div>
                        <div class="col-sm-3 invoice-col">
                            <div>
                                {{ QrCode::size(70)->generate($purchase->reference) }}<br />
                                <span style="font-size:28px;margin-top:-5px">
                                     Purchase Request
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <b>Invoice No:</b> {{ $purchase->reference }}<br>
                            <b>Date and Time: {{ \Carbon\Carbon::parse($purchase->created_at)->toFormattedDateString() }}</b><br>
                            <b>Prepared By</b> <span class="ion-card"></span> {{ $purchase->updatedBy?->name }}<br />
                            <b>Printed By <span class="ion-printer"></span>
                                &nbsp;&nbsp;{{ Auth::user()->name }}</span><span>
                        </div>
                    </div>
                    <!-- /.row -->


                    <div class="row" style="line-height: 0.5">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered text-left" style="width:80%">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Product Code</th>
                                        <th>Description</th>
                                        <th>UTM</th>
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
                                            <td>{{ $purchase_detail->product->code }}</td>
                                            <td>{{ $purchase_detail->product->name }}</td>
                                            <td>{{ $purchase_detail->product->unit }}</td>
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
                                        <th colspan="6" style="text-align: right">Total Amount</th>
                                        <th style="text-align: right;">
                                            &#8358;{{ number_format($total, 2, '.', ',') }}</th>
                                    </tr>

                                    <tr>
                                        <td colspan="4"><span style='font-size:14px;'></span>Total in Words:
                                        </td>
                                        <td colspan="5"><span
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
