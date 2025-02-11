{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Credit Note - {{ config('app.name', 'Inventory Management System') }}</title>

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
                        <div class="col-12" style="text-align: center">

                            <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:100px;height:60px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{App\Models\User::UserBranchName()->long_name}}
                            </h3>
                            <h4>CREDIT NOTE</h4>
                            <small class="float-right">View Date: {{ date('l, d-M-Y h:i:s A') }}</small><br/>

                        </div>
                        <!-- /.col -->
                        <h4>CUSTOMER NAME: <small>{{$payment->customer->name}}</small></h4>
                    </div>

                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>DATE</th>
                                        <th>REF NO</th>
                                        <th>CUSTOMER NAME</th>
                                        <th>AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                        </td>
                                        <td>
                                            {{ $payment->reference_no }}
                                        </td>
                                        <td>
                                            {{ $payment->customer->name }}
                                        </td>
                                        <td  align="right">
                                            &#8358; {{ number_format($payment->amount, 2) }}</td>
                                    </tr>
        
                                    <tr>
                                        <td class="text-right text-danger" colspan="3">
                                            <p>
                                                <strong>TOTAL: </strong>
                                            </p>
                                        </td>
                                        <td class="text-right text-danger">
                                            <p>
                                                <strong>&#8358;{{ number_format($payment->amount, 2) }}</strong>
                                            </p>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table">
                                <tr>
                                    <td style='border-style:none;'>Created By:
                                        {{ $payment->createdBy->name }}<br /><br>
                                        Posted By: {{ $payment->postedBy->name }}
                                    </td>
                                </tr>
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


</html> --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Credit Note - {{ config('app.name', 'Inventory Management System') }}</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <!-- IonIcons -->
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

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
                padding: 20px;
                border: 1px solid #ddd;
                background: #fff;
                max-width: 210mm;
                margin: auto;
                box-shadow: none;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table th,
            .table td {
                border: 1px solid #ddd;
                padding: 8px;
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
                <!-- Main content -->
                <div class="invoice">
                    <!-- Header -->
                    <div class="row text-center">
                        <div class="col-12">
                            <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:100px;height:60px;"
                                alt="Albabello Logo">
                            <h3>{{ App\Models\User::UserBranchName()->long_name }}</h3>
                            <h4>CREDIT NOTE</h4>
                            <p><small>View Date: {{ date('l, d-M-Y h:i:s A') }}</small></p>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="row">
                        <div class="col-12">
                            <h4>CUSTOMER NAME: <small>{{ $payment->customer->name }}</small></h4>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>DATE</th>
                                        <th>REF NO</th>
                                        <th>CUSTOMER NAME</th>
                                        <th>AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}</td>
                                        <td>{{ $payment->reference_no }}</td>
                                        <td>{{ $payment->customer->name }}</td>
                                        <td align="right">&#8358; {{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right text-danger" colspan="3">
                                            <strong>TOTAL:</strong>
                                        </td>
                                        <td class="text-right text-danger">
                                            <strong>&#8358;{{ number_format($payment->amount, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table">
                                <tr>
                                    <td style="border-style:none;">
                                        <b>Created By:</b> {{ $payment->createdBy->name }}<br>
                                        <b>Posted By:</b> {{ $payment->postedBy->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-style:none;">
                                        <b>Printed On:</b> {{ \Carbon\Carbon::now()->toFormattedDateString() }}<br>
                                        <b>Printed By:</b> {{ Auth::user()->name }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="row">
                        <div class="col-6">
                            <p><b>Date Created:</b>
                                {{ \Carbon\Carbon::parse($payment->created_at)->toFormattedDateString() }}</p>
                            <p><b>Printed On:</b> {{ \Carbon\Carbon::now()->toFormattedDateString() }}</p>
                            <p><b>Printed By:</b> {{ Auth::user()->name }}</p>
                        </div>
                        <div class="col-6 text-right">
                            <p><b>Signature:</b> ______________________________</p>
                            <p><b>For:</b> {{ App\Models\User::UserBranchName()->long_name }}</p>
                        </div>
                    </div>
                </div><!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->

    <script>
        window.print();
    </script>

</body>

</html>
