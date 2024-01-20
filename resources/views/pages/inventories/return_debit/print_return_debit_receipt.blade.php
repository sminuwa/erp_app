<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Return & Debit - {{ config('app.name', 'Inventory Management System') }}</title>

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
                                {{ App\Models\User::UserBranchName()->long_name }}
                            </h3>
                            <h4>RETURN AND DEBIT</h4>
                            <small class="float-right">View Date: {{ date('l, d-M-Y h:i:s A') }}</small><br />

                        </div>
                        <!-- /.col -->
                        <h4>SUPPLIER NAME: <small>{{ $payment->purchase->supplier->name }}</small></h4>
                    </div>

                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Product</th>
                                        <th>Original QTY</th>
                                        <th>Changed QTY</th>
                                        <th>Original Unit Cost</th>
                                        <th>Current Unit Cost</th>
                                        <th>Total Original Cost</th>
                                        <th>Total Current Cost</th>
                                        <th>Margin</th>

                                    </tr>
                                </thead>
                                @php $current_total =  $original_total =  0; @endphp
                                <tbody>
                                    @foreach ($payment->returnItems as $item)
                                        <tr>
                                            <td>
                                                {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                            </td>
                                            <td>
                                                {{ $payment->reference }}
                                            </td>
                                            <td>
                                                {{ $item->product->code }}-{{ $item->product->name }}
                                            </td>
                                            <td>
                                                {{ $item->original_quantity_purchased }}
                                            </td>
                                            <td>
                                                {{ $item->current_quantity }}
                                            </td>
                                            <td align="right">
                                                {{ number_format($item->original_unit_cost, 2) }}
                                            </td>
                                            <td align="right">
                                                {{ number_format($item->current_unit_cost, 2) }}
                                            </td>
                                            <td align="right">
                                                {{ number_format($item->original_unit_cost * $item->original_quantity_purchased, 2) }}
                                            </td>
                                            <td align="right">
                                                {{ number_format($item->current_unit_cost * $item->current_quantity, 2) }}</td>
                                            <td align="right">
                                                {{ number_format($item->original_unit_cost * $item->original_quantity_purchased - $item->current_unit_cost * $item->current_quantity, 2) }}
                                            </td>
                                        </tr>
                                        @php
                                            $current_total += $item->current_unit_cost * $item->current_quantity;
                                            $original_total += $item->original_unit_cost * $item->original_quantity_purchased;
                                        @endphp
                                    @endforeach

                                    <tr>
                                        <td class="text-right text-danger" colspan="7">
                                            <p>
                                                <strong>TOTAL: </strong>
                                            </p>
                                        </td>
                                        <td class="text-right text-danger">
                                            <p>
                                                <strong>&#8358;{{ number_format($original_total, 2) }}</strong>
                                            </p>
                                        </td>
                                        <td class="text-right text-danger">
                                            <p>
                                                <strong>&#8358;{{ number_format($current_total, 2) }}</strong>
                                            </p>
                                        </td>
                                        <td>
                                            <p>
                                                <strong>&#8358;{{ number_format($original_total-$current_total, 2) }}</strong>
                                            </p>
                                        </td>
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
