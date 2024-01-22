<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Waybill - {{ config('app.name', 'Inventory Management System') }}</title>

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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <img src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="width:100px;height:60px;"
                                    alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                                <small class="float-right">Date: {{ date('l, d-M-Y h:i:s A') }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row">
                        <div class="col-sm-8">
                            <address>
                                <h5>BRANCH OFFICE</h5>
                                Address <span class="ion-ios-contact-outline"></span>: {{ $order->branch->address }}
                                <br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $order->branch->phone }}
                                <br>
                                Email <span class="ion-email"></span>: {{ $order->branch->email }}
                            </address>
                        </div>
                        <div class="col-sm-4" style="text-align: right">
                            <h2>WAYBILL</h2>
                            <address>
                                <strong>Printed Date & Time:</strong>{{ date('l, d-M-Y h:i:s A') }}<br>
                                <strong>Waybill No: </strong>:
                                {{ $order->invoice_no }}<br>
                                <strong>Transaction Date:
                                </strong>{{ \Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}<br>
                                <strong>Delivery/Sale Mode: </strong>{{ $order->payment_mode }}<br>

                            </address>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 style="text-align: center;">WAYBILL</h3>
                            <div class="card">
                                <div class="card-title">Bill To</div>
                                <div class="card-body" style="text-align: center">
                                    <h4>{{ strtoupper($order->customer->name) }}</h4>
                                </div>
                                <div class="card-title">Delivered To</div>
                                <div class="card-body" style="text-align: center">
                                    <h4></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4" style="text-align: left;font-size:18px;">
                            <b>Drivers's Name:</b><br><br>
                            <b>Location ID:</b> <br><br>
                            <b>Warehouse:</b> <br><br>
                            <b>Vehicle Reg No:</b> <br><br>
                            <b>Transporter:</b> <br><br>
                        </div>
                    </div>
                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered text-left">
                                <thead>
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Store</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $total_discount = 0;
                                    @endphp
                                    @foreach ($order_details as $order_detail)
                                        <tr>
                                            <td>{{ $order->invoice_no }}</td>
                                            <td>{{ $order_detail->storeProduct->product->name }}</td>
                                            <td align="center">{{ $order_detail->quantity }}</td>
                                            <td>{{ $order_detail->storeProduct->store->name }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="4" style="text-align: right;">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th rowspan="4">
                                                        <br /><br /><br/><br/>Name<br /><br /><br /><br/><br/>Signature<br /><br /><br /><br/><br/>Date
                                                    </th>
                                                    <th>Store Officer<br /></th>
                                                    <th>Driver<br /></th>
                                                    <th>Security<br /></th>
                                                    <th>Warehouse Mgt<br /></th>
                                                </tr>
                                                <tr>
                                                    <td><br /></td>
                                                    <td><br /></td>
                                                    <td><br /></td>
                                                    <td><br /></td>
                                                </tr>
                                                <tr>
                                                    <td><br /></td>
                                                    <td><br /></td>
                                                    <td><br /></td>
                                                    <td><br /></td>
                                                </tr>
                                                <tr>
                                                    <td><br /></td>
                                                    <td><br /></td>
                                                    <td><br /></td>
                                                    <td><br /></td>
                                                </tr>
                                            </table>
                                        </th>
                                    </tr>

                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-sm-8">
                                    <p>The above goods were recieved in good condition</p>
                                </div>
                                <div class="col-sm-4" style="text-align: right">
                                    @php
                                        $uc = substr($order->invoice_no, 0, 6);
                                    @endphp
                                    {{ QrCode::size(100)->backgroundColor(255, 60, 55)->generate("$total\n$uc\n$order->invoice\n\n.") }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    Name<br>
                                    _______________________________________<br>
                                </div>
                                <div class="col-sm-2">
                                    Signature<br>
                                    _______________________________________<br>

                                </div>
                                <div class="col-sm-3">
                                    Date<br>
                                    _______________________________________<br>

                                </div>
                                <div class="col-sm-3">
                                    Remark<br>
                                    _______________________________________<br>

                                </div>
                            </div>
                            
                            <table class="table" style="margin-top: 10px;">
                                <tr>
                                    <td style='border-style:none;'>
                                        Printed By: 
                                    </td>
                                    <td>{{ Auth::user()->name }}</td>
                                </tr>
                                <td></td>
                                <td><strong>No collection of goods After three Days. Thank you for doing business with us.</strong></td>
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
