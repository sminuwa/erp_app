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

                            <img src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="width:50px;height:50px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{App\Models\User::UserBranchName()->long_name}}
                            </h3>
                            <h5 style="text-align: center;">AVAILABLE STOCK REPORT<br/>
                                BRANCH: <small>{{Auth::user()->branch->name}}</small>
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered caption" id="example1" border="1" cellpadding="0"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ITEM</th>
                                        <th>QTY</th>
                                        <th>COST PRICE</th>
                                        <th>RETAIL PRICE</th>
                                        <th>WHOLE PRICE</th>
                                        <th>STORE</th>
                                    </tr>
                                </thead>
                                @php
                                    $total_selling_price = 0;
                                    $total_cost_price = 0;
                                @endphp
                                @foreach ($stores as $store)
                                    <tr>
                                        <td> {{ $store->name }} </td>
                                        <td> {{ $store->qty_available }} </td>
                                        @php
                                            $total_selling_price += $store->retail_selling_price;
                                            $total_cost_price += $store->cost_price;
                                        
                                        @endphp
                                        <td style="text-align: right;"> &#8358;{{ number_format($store->cost_price,2,'.',',') }}
                                        </td>
                                        <td style="text-align: right;"> &#8358;{{ number_format($store->retail_selling_price,2,'.',',') }}
                                        </td>
                                        <td style="text-align: right;"> &#8358;{{ number_format($store->whole_selling_price,2,'.',',') }}
                                        </td>
                                        
                                        <td>{{ $store->store }} </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right;">&#8358;{{number_format($total_cost_price,2,'.',',')}}</th>
                                        <th style="text-align: right;">&#8358;{{number_format($total_selling_price,2,'.',',')}}</th>
                                        
                                        <th></th>
                                    </tr>
                                </tfoot> --}}
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
