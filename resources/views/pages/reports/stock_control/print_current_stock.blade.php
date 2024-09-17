<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Stock - {{ config('app.name', 'Inventory Management System') }}</title>

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
                                style="width:50px;height:50px;" alt="Albabello Logo" class="img-circle elevation-3"
                                style="opacity: .8">
                            <caption style="caption-size:top">
                                <h3 style="text-align: center;">
                                    {{ $branch == null ? 'All Branches' : $branch->name."($branch->code)" }} </h3>
                                <h5 style="text-align: center;">CURRENT STOCK REPORT </h5>
                            </caption>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="display table table-bordered caption" id="example1">

                                <thead>
                                    <tr>
                                        <th style="width: 50%" colspan="6">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
                                        </th>
                                        <th style="width: 50%;text-align:right" colspan="6">Processed By {{ auth()->user()->name }}</th>
                                    </tr>
                                    <tr>
                                        <th>CODE</th>
                                        <th>PRODUCT</th>
                                        <th>STORE</th>
                                        <th>QTY</th>
                                        <th>COST PRICE ()</th>
                                        <th>R PRICE ()</th>
                                        <th>W PRICE ()</th>
                                        <th>TOTAL COST ()</th>
                                        <th>TOTAL R ()</th>
                                        <th>TOTAL W ()</th>
                                        <th>R MARGIN</th>
                                        <th>W MARGIN</th>
                                    </tr>
                                </thead>
                                @foreach ($stores as $store)
                                    <tr>
                                        <td> {{ $store->product_code }} </td>
                                        <td> {{ $store->name }} </td>
                                        <td>{{ $store->store_code }} </td>
                                        <td> {{ $store->qty_available }} </td>
                                        <td style="text-align: right;">
                                            {{ number_format(str_replace(',', '', $store->cost_price), 2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format(str_replace(',', '', $store->retail_selling_price), 2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format(str_replace(',', '', $store->whole_selling_price), 2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right;">
                                            @php $total_cost = str_replace(',', '', $store->qty_available) * str_replace(',', '', $store->cost_price); @endphp
                                            {{ number_format($total_cost, 2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right;">
                                            @php $total_r_price = str_replace(',', '', $store->qty_available) * str_replace(',', '', $store->retail_selling_price); @endphp
                                            {{ number_format($total_r_price, 2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right;">
                                            @php $total_w_price = str_replace(',', '', $store->qty_available) * str_replace(',', '', $store->whole_selling_price); @endphp
                                            {{ number_format($total_w_price, 2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($total_r_price - $total_cost, 2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($total_w_price - $total_cost, 2, '.', ',') }}
                                        </td>

                                    </tr>
                                @endforeach
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
