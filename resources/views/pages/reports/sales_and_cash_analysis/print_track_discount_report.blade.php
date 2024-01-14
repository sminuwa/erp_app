<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Debtor Balance Report - {{ config('app.name', 'Inventory Management System') }}</title>

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

                            <img src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="width:100px;height:60px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{App\Models\User::UserBranchName()->long_name}}
                            </h3>
                            <h5 style="text-align: center;">DISCOUNT GRANTED ON UNIT PRICE
                                BETWEEN
                                {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                                AND
                                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                            </h5>
                            <h5>Discount Range: &#8358;{{ $lower }} - &#8358;{{ $upper }}</h5>
                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered caption" id="example1" border="1" cellpadding="0"
                                cellspacing="0" data-ordering="false">
                                <thead>
                                    <tr>
                                        <th>SALE DATE</th>
                                        <th>INVOICE NO</th>
                                        <th>ITEM NAME</th>
                                        <th>QTY</th>
                                        <th>ACTUAL</th>
                                        <th>SELLING</th>
                                        <th>DICOUNT</th>
                                        <th>STORE</th>
                                        <th>GROUP</th>
                                        <th>SOLD BY</th>
                                    </tr>
                                </thead>
                                @php
                                    $total_selling = 0;
                                    $total_sold = 0;
                                    $total_discount = 0;
                                @endphp
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}
                                        </td>
                                        <td>{{ $sale->invoice_no }}</td>
                                        <td>{{ $sale->item }}</td>
                                        <td>{{ $sale->quantity }}</td>
                                        <td style="text-align: right">
                                            &#8358;{{ number_format($sale->selling_price, 2, '.', ',') }}</td>
                                        <td style="text-align: right">
                                            &#8358;{{ number_format($sale->sold_price, 2, '.', ',') }}</td>
                                        <td style="text-align: right">
                                            &#8358;{{ number_format($sale->sold_price - $sale->sold_price, 2, '.', ',') }}
                                        </td>
                                        <td>{{ $sale->store }}</td>
                                        <td>{{ $sale->group }}</td>
                                        <td>{{ $sale->user }}</td>
                                    </tr>
                                    @php
                                        $total_selling += $sale->selling_price;
                                        $total_sold += $sale->sold_price;
                                        $total_discount += $sale->sold_price - $sale->sold_price;
                                        
                                    @endphp
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th style="text-align: right" colspan="4">TOTAL</th>
                                        <th style="text-align: right">
                                            {{ number_format($total_selling, 0, '.', ',') }}</th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_sold, 2, '.', ',') }}
                                        </th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_discount, 2, '.', ',') }}
                                        </th>
                                        <th colspan="3"></th>
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
