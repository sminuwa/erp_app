<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>List of Invoice Lines Report - {{ config('app.name', 'Inventory Management System') }}</title>

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
                            <h3>
                                {{ $branch->name ?? 'All Branches' }}
                            </h3>
                            <h5 style="text-align: center;">LIST OF INVOICE LINES
                                BETWEEN
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
                                        <th style="width: 50%" colspan="4">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
                                        </th>
                                        <th style="width: 50%;text-align:right" colspan="4">Pricessed By {{ auth()->user()->name }}</th>
                                    </tr>
                                    <tr>
                                        <th>DATE</th>
                                        <th>REFERENCE</th>
                                        <th>ORDER ACCOUNT</th>
                                        <th>ITEME</th>
                                        <th>QTY</th>
                                        <th>UNIT PRICE</th>
                                        <th>TOTAL</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($sales as $sale)
                                    @foreach ($sale->order_items as $item)
                                        @php
                                            $total += $sale->total;
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}
                                            </td>
                                            <td>{{ $sale->reference }}</td>
                                            <td>{{ $sale->customer->code }}</td>
                                            <td>{{ $item->storeProduct->product->code }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td style="text-align: right">
                                                {{ number_format($item->sold_price, 2, '.', ',') }}</td>
                                            <td style="text-align: right">
                                                {{ number_format($item->total, 2, '.', ',') }}</td>
                                            <td>{{ $sale->status == 1 ? 'Completed' : 'Pending' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th style="text-align: right" colspan="6">Total</th>
                                        <th style="text-align: right">{{ number_format($total, 2, '.', ',') }}
                                        </th>
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
