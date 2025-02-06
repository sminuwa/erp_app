<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Invoice/GRN Lines - {{ config('app.name', 'Inventory Management System') }}</title>

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

                            <img src="{{ asset('assets/backend/img/logo' . '.png') }}"
                                style="width:100px;height:60px;" alt="Albabello Logo" class="img-circle elevation-3"
                                style="opacity: .8">
                            <h3 style="text-align: center;">
                                {{ $branch->name ?? 'All Branches' }}
                            </h3>
                            <h5 style="text-align: center;">Invoice/GRN Lines Report
                                From
                                {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                                AND
                                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="display table table-bordered caption" id="example1" border="1" cellpadding="0"
                                cellspacing="0" data-ordering="false">
                                <thead>
                                    <tr>
                                        <th style="width: 50%" colspan="5">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
                                        </th>
                                        <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
                                    </tr>
                                    <tr>
                                        <th>DATE</th>
                                        <th>DOCUMENT NO</th>
                                        <th>PRODUCT</th>
                                        <th>PRODUCT PRICE</th>
                                        <th>QTY</th>
                                        <th>TOTAL COST</th>
                                        <th>STORE</th>
                                        <th>ATC/WAYBILL</th>
                                        <th>SUPPLIER</th>
                                    </tr>
                                </thead>
                                @php
                                    $total_cost = 0;
                                @endphp
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($sale->purchase_date)->toFormattedDateString() }}
                                        </td>
                                        <td>{{ $sale->reference }}</td>
                                        <td>{{ $sale->product }}</td>
                                        <td style="text-align: right">{{ number_format($sale->unit_price, 2) }}</td>
                                        <td>{{ $sale->quantity }}</td>
                                        <td style="text-align: right">
                                            {{ number_format($sale->unit_price * $sale->quantity, 2, '.', ',') }}
                                        </td>
                                        <td>{{ $sale->store }}</td>
                                        <td>{{ $sale->atc_no }}</td>
                                        <td>{{ $sale->supplier }}</td>

                                    </tr>
                                    @php
                                        $total_cost += $sale->unit_price * $sale->quantity;
                                    @endphp
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right">TOTAL</th>
                                        <th style="text-align: right">
                                            {{ number_format($total_cost, 2, '.', ',') }}
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
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
