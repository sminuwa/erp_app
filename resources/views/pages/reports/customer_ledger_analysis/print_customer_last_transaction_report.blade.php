<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Customer Total Debt Report - {{ config('app.name', 'Inventory Management System') }}</title>

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
                            <h3 style="text-align: center">
                                {{ $branch->name ?? 'All Branches' }}
                            </h3>
                            <caption style="caption-size:top">
                                <h5 style="text-align: center;">Last Transaction of Customers with their Current
                                    Balances
                                </h5>

                            </caption>
                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="display table table-bordered caption" id="example1" border="1" cellpadding="0"
                                cellspacing="0" data-ordering="false">
                                <thead>
                                    <tr>
                                        <th style="width: 50%" colspan="3">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
                                        </th>
                                        <th style="width: 50%;text-align:right" colspan="3">Pricessed By {{ auth()->user()->name }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4"></th>
                                        <th colspan="2">BALANCE</th>
                                    </tr>
                                    <tr>
                                        <th>ACCOUNT NO</th>
                                        <th>CUSTOMER NAMES</th>
                                        <th>LAST INVOICE</th>
                                        <th>LAST DATE</th>
                                        <th>DR.</th>
                                        <th>CR.</th>
                                    </tr>
                                </thead>
                                @php
                                    $total_balance = 0;
                                @endphp
                                @foreach ($sales as $sale)
                                    @php
                            
                                        $running_balance = $sale->balance;
                                        $total_balance += $running_balance;
                                    @endphp
                                    <tr>
                                        <td>{{ $sale->code }}</td>
                                        <td>{{ $sale->customer }}</td>
                                        <td>{{ customerLastTransaction($sale->customer_id)?->reference }}</td>
                                        <td>{{ \Carbon\Carbon::parse(customerLastTransaction($sale->customer_id)?->date)->toFormattedDateString() }}
                                        </td>
                                        <td style="text-align: right">
                                            @if ($running_balance < 0)
                                                {{ number_format(abs($running_balance), 2) }}
                                            @endif
                                        </td>
                                        <td style="text-align: right">
                                            @if ($running_balance > 0)
                                                {{ number_format($running_balance, 2) }}
                                            @endif
                                        </td>
                            
                                    </tr>
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th style="text-align: right" colspan="4">TOTAL</th>
                                        <th style="text-align: right">
                                            @if ($total_balance < 0)
                                                ({{ number_format(abs($total_balance), 2) }})
                                            @endif
                                        </th>
                                        <th style="text-align: right">
                                            @if ($total_balance > 0)
                                                {{ number_format($total_balance, 2) }}
                                            @endif
                                        </th>
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
