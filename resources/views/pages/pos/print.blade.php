{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Invoice - {{ config('app.name', 'Inventory Management System') }}</title>

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
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-globe"></i> {{ config('app.name') }}
                                <small class="float-right">Date: {{ date('l, d-M-Y h:i:s A') }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            From
                            <address>
                                <strong>{{ config('app.name') }}</strong><br>
                                {{ $company->address }}<br>
                                {{ $company->city }} , {{ $company->country }}<br>
                                Phone: {{ $company->mobile }}
                                {{ $company->phone !== null ? ', ' . $company->phone : '' }}<br>
                                Email: {{ $company->email }}
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            To
                            <address>
                                <strong>{{ $customer->name }}</strong><br>
                                {{ $customer->address }}<br>
                                {{ $customer->city }}<br>
                                Phone: {{ $customer->phone }}<br>
                                Email: {{ $customer->email }}
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <b>Payment Due:</b> {{ Cart::getTotal() }}<br>
                            <b>Order Status:</b> <span class="badge badge-warning">Pending</span><br>
                            <b>Account:</b> {{ $customer->account_number }}
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Unit Cost</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contents as $content)
                                        <tr>
                                            <td>{{ $content->name }}</td>
                                            <td>{{ $content->quantity }}</td>
                                            <td>{{ number_format($content->price, 2) }}</td>
                                            <td>{{ $content->getPriceSum() }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-8"></div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Subtotal:</th>
                                        <td class="text-right">&#8358; {{ Cart::getSubTotal() }}</td>
                                    </tr>

                                    <tr>
                                        <th>Total:</th>
                                        <td class="text-right">&#8358; {{ Cart::getTotal() }}
                                            {{ $util->convertNumberToWords(Cart::getTotal()) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
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

    <title>Invoice - {{ config('app.name', 'Inventory Management System') }}</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <!-- IonIcons -->
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />

    <!-- Custom CSS for A4 Printing -->
    <style>
        @media print {
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
            }
            .container-fluid {
                width: 100%;
                padding: 20px;
            }
            .invoice {
                page-break-after: always;
            }
            .table {
                width: 100%;
                margin-bottom: 1rem;
                color: #212529;
            }
            .table th,
            .table td {
                padding: 0.75rem;
                vertical-align: top;
                border-top: 1px solid #dee2e6;
            }
            .table thead th {
                vertical-align: bottom;
                border-bottom: 2px solid #dee2e6;
            }
            .table-bordered {
                border: 1px solid #dee2e6;
            }
            .table-bordered th,
            .table-bordered td {
                border: 1px solid #dee2e6;
            }
            .table-bordered thead th,
            .table-bordered thead td {
                border-bottom-width: 2px;
            }
            .text-right {
                text-align: right;
            }
            .badge {
                display: inline-block;
                padding: 0.25em 0.4em;
                font-size: 75%;
                font-weight: 700;
                line-height: 1;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
                border-radius: 0.25rem;
            }
            .badge-warning {
                color: #212529;
                background-color: #ffc107;
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
                                <i class="fa fa-globe"></i> {{ config('app.name') }}
                                <small class="float-right">Date: {{ date('l, d-M-Y h:i:s A') }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            From
                            <address>
                                <strong>{{ config('app.name') }}</strong><br>
                                {{ $company->address }}<br>
                                {{ $company->city }} , {{ $company->country }}<br>
                                Phone: {{ $company->mobile }} {{ $company->phone !== null ? ', '.$company->phone : ''  }}<br>
                                Email: {{ $company->email }}
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            To
                            <address>
                                <strong>{{ $customer->name }}</strong><br>
                                {{ $customer->address }}<br>
                                {{ $customer->city }}<br>
                                Phone: {{ $customer->phone }}<br>
                                Email: {{ $customer->email }}
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <b>Payment Due:</b> {{ Cart::getTotal() }}<br>
                            <b>Order Status:</b> <span class="badge badge-warning">Pending</span><br>
                            <b>Account:</b> {{ $customer->account_number }}
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($contents as $content)
                                    <tr>
                                        <td>{{ $content->name }}</td>
                                        <td>{{ $content->quantity }}</td>
                                        <td>{{ number_format($content->price, 2) }}</td>
                                        <td>{{ $content->getPriceSum() }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-8"></div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Subtotal:</th>
                                        <td class="text-right">&#8358; {{ Cart::getSubTotal() }}</td>
                                    </tr>

                                    {{--<tr>
                                        <th style="width:50%">VAT:</th>
                                        <td class="text-right">&#8358; {{ Cart::getSubTotal() }}</td>
                                    </tr>--}}

                                    <tr>
                                        <th>Total:</th>
                                        <td class="text-right">&#8358; {{ Cart::getTotal() }}
                                            {{ $util->convertNumberToWords(Cart::getTotal()) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
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
