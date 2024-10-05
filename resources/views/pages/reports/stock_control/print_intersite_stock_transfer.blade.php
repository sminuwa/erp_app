<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Intersite Stock Transfer - {{ config('app.name', 'Inventory Management System') }}</title>

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

                            <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:50px;height:50px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{ App\Models\User::UserBranchName()->long_name }}
                            </h3>
                            <h5 style="text-align: center;">INTERSITE STOCK TRANSFER REPORT
                            </h5>
                            <h5 style="text-align: center;">DATE BETWEEN
                                {{ Carbon\Carbon::parse($from_date)->toFormattedDateString() }} AND
                                {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="display table table-bordered caption" id="example1" border="1"
                                cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 50%" colspan="5">Date Processed:
                                            {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
                                        </th>
                                        <th style="width: 50%;text-align:right" colspan="4">Processed By
                                            {{ auth()->user()->name }}</th>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>From Branch</th>
                                        <th>To Branch</th>
                                        <th>Rerefence No</th>
                                        <th>Created By</th>
                                        <th>Date Created</th>
                                        <th>QTY</th>
                                        <th>Cost Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                @php $total =0; @endphp
                                @foreach ($transfers as $transfer)
                                    <tr>
                                        <td>{{ Carbon\Carbon::parse($transfer->date)->toFormattedDateString() }}</td>
                                        <td>{{ $transfer->product_code }}</td>
                                        <td>{{ $transfer->product_name }}</td>
                                        <td>{{ \App\Models\Branch::find($transfer->source_branch_id)->code ?? '' }}
                                        </td>
                                        <td>{{ \App\Models\Branch::find($transfer->destination_branch_id)->code ?? '' }}
                                        </td>
                                        <td>{{ $transfer->reference }}</td>
                                        <td>{{ $transfer->created_by }}</td>
                                        <td>{{ Carbon\Carbon::parse($transfer->created_at)->toFormattedDateString() }}
                                        </td>
                                        <td style="text-align: right">{{ $transfer->quantity }}</td>
                                        <td style="text-align: right">{{ number_format($transfer->cost_price, 2) }}
                                        </td>
                                        @php $total += $transfer->cost_price * $transfer->quantity; @endphp
                                        <td style="text-align: right">
                                            {{ number_format($transfer->cost_price * $transfer->quantity, 2) }}</td>

                                    </tr>
                                @endforeach
                                <tfoot>
                                    <tr>

                                        <th colspan="8"> Total</th>
                                        <th style="text-align: right">{{ number_format($total, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">
                            <div class="receipt-header receipt-header-mid receipt-footer">
                                <div class="col-xs-10 col-sm-10 col-md-10 text-left">
                                    <div class="receipt-right">

                                        <p><b>Printed By :</b> {{ Auth::user()->name }}</p>
                                        <p><b>Printed On :</b> {{ \Carbon\Carbon::now()->toFormattedDateString() }}</p>
                                        <br>


                                    </div>
                                </div>

                            </div>
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
