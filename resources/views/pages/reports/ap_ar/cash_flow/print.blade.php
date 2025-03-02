<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Balance Sheet - {{ config('app.name', 'Inventory Management System') }}</title>

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

                            <img src="{{ asset('assets/backend/img/logo' . '.png') }}" style="width:50px;height:50px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
                            </h3>
                            <h5 style="text-align: center;">CASH FLOW BETWEEN
                                {{ Carbon\Carbon::parse($from_date)->toFormattedDateString() }} and {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}</h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="display table table-bordered caption" id="example1" data-ordering="true">
            
                                <tbody>
                                    <tr>
                                        <th style="text-align: left">Total Cash Generated</th>
                                        <td style="text-align: right">{{ number_format($total_generated, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left">Total Bank Transfer</th>
                                        <td style="text-align: right">{{ number_format($total_bank_transfer, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left">Total Cash at Hand</th>
                                        <td style="text-align: right">{{ number_format($total_at_hand, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left">Total Cash in Bank</th>
                                        <td style="text-align: right">{{ number_format($total_cash_in_bank, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left">Total Amount Expended</th>
                                        <td style="text-align: right">{{ number_format($total_amount_expended, 2) }}</td>
                                    </tr>
                            
                                </tbody>
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
