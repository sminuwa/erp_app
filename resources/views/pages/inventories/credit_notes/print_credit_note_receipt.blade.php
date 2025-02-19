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

                            <img src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="width:100px;height:60px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{App\Models\User::UserBranchName()->long_name}}
                            </h3>
                            <h4>CREDIT NOTE</h4>
                            <small class="float-right">Date: {{ $credit_note->createdBy->name }}</small>
                            <small class="float-right">View Date: {{ date('l, d-M-Y h:i:s A') }}</small><br/>

                        </div>
                        <!-- /.col -->
                        <h4>CUSTOMER NAME: <small>{{$order->customer->code}}-{{$order->customer->name}}</small></h4>
                    </div>

                    <div class="row">
                        <div class="col-12 table-responsive">
                            
                            <table class="table table-bordered text-left">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>UTM</th>
                                        <th>Store</th>
                                        <th>Quantity</th>
                                        <th>Unit Cost</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $total_discount = 0;
                                    @endphp
                                    @foreach ($order_details as $order_detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $order_detail->store_product->product->code ?? "" }}</td>
                                            <td>{{ $order_detail->store_product->product->name ?? "" }}</td>
                                            <td>{{ $order_detail->unit ?? "" }}</td>
                                            <td>{{ $order_detail->store_product->store->code ?? "" }}</td>
                                            <td align="center">{{ $order_detail->quantity }}</td>
                                            <td align="right">{{ number_format($order_detail->sold_price, 2) }}
                                            </td>
                                            <td align="right">
                                                {{ number_format($order_detail->sold_price * $order_detail->quantity, 2) }}
                                            </td>
                                        </tr>
                                        @php $total += ($order_detail->sold_price * $order_detail->quantity);  @endphp
                                    @endforeach
                                    <tr>
                                        <th colspan="7" align="right">Total</th>
                                        <th style="text-align: right">{{ number_format($total, 2, '.', ',') }}</th>

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

    @include('pages.order.paper_size')
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
                            <img src="{{ asset('assets/backend/img/logo' . App\Models\User::userBranchAction() . '.png') }}"
                                style="width:100px;height:60px;" alt="Albabello Logo">
                            <h3>{{ App\Models\User::UserBranchName()->long_name }}</h3>
                            <h4>CREDIT NOTE</h4>
                            <p><small>Created By: {{ $credit_note->createdBy->name }}</small></p>
                            <p><small>View Date: {{ date('l, d-M-Y h:i:s A') }}</small></p>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="row">
                        <div class="col-12">
                            <h4>CUSTOMER NAME: <small>{{ $order->customer->code }} -
                                    {{ $order->customer->name }}</small></h4>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>UTM</th>
                                        <th>Store</th>
                                        <th>Quantity</th>
                                        <th>Unit Cost</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach ($order_details as $order_detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $order_detail->store_product->product->code ?? 'N/A' }}</td>
                                            <td>{{ $order_detail->store_product->product->name ?? 'N/A' }}</td>
                                            <td>{{ $order_detail->unit ?? 'N/A' }}</td>
                                            <td>{{ $order_detail->store_product->store->code ?? 'N/A' }}</td>
                                            <td align="center">{{ $order_detail->quantity }}</td>
                                            <td align="right">&#8358;{{ number_format($order_detail->sold_price, 2) }}
                                            </td>
                                            <td align="right">
                                                &#8358;{{ number_format($order_detail->sold_price * $order_detail->quantity, 2) }}
                                            </td>
                                        </tr>
                                        @php $total += ($order_detail->sold_price * $order_detail->quantity); @endphp
                                    @endforeach
                                    <tr>
                                        <th colspan="7" align="right">Total</th>
                                        <th style="text-align: right">&#8358;{{ number_format($total, 2, '.', ',') }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="row">
                        <div class="col-6">
                            <p><b>Date Created:</b>
                                {{ \Carbon\Carbon::parse($credit_note->created_at)->toFormattedDateString() }}</p>
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
