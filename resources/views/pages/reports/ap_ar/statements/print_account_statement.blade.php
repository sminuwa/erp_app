<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Account Statements - {{ config('app.name', 'Inventory Management System') }}</title>

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
                            <h5 style="text-align: center;">{{ strtoupper($customer->name) }} LEDGER LISTING BETWEEN
                                {{ $from_date }}
                                AND
                                {{ $to_date }}<br /> Runining Balance B/d Before this date {{ $from_date }}
                                was = @if ($balance_b_d < 0)
                                    &#8358;({{ number_format(abs($balance_b_d), 2) }})
                                @else
                                    &#8358;{{ number_format($balance_b_d, 2) }}
                                @endif
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered caption" id="example1" border="1" data-ordering="false" cellpadding="0"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>System/Invoice</th>
                                        <th>Ref</th>
                                        <th>Cr (&#8358;)</th>
                                        <th>Dr (&#8358;)</th>
                                        <th>Running Balance</th>
                                    </tr>
                                </thead>
                                <?php $sum_cr = $sum_cr_b_d;
                                $sum_dr = $sum_dr_b_d;
                                $dif = 0; ?>
                                @foreach ($ledgers as $ledger)
                                    <tr>
                                        <td>{{ $ledger->date->toFormattedDateString() }}</td>
                                        <td>{{ $ledger->description }}</td>
                                        <td>{{ $ledger->systemid }}</td>
                                        <td>{{ $ledger->ref }}</td>
                                        <td style="text-align: right"> &#8358;{{ number_format($ledger->cr, 2) }}</td>
                                        <td style="text-align: right"> &#8358;{{ number_format($ledger->dr, 2) }}</td>
                                        <td style="text-align: right">
                                            <?php $sum_cr += $ledger->cr;
                                            $sum_dr += $ledger->dr;
                                            $dif = $sum_cr - $sum_dr;
                                            $balance =
                                                $ledger
                                                    ->where('id', '<=', $ledger->id)
                                                    ->where('customer_id', $customer->id)
                                                    ->sum('cr') -
                                                $ledger
                                                    ->where('id', '<=', $ledger->id)
                                                    ->where('customer_id', $customer->id)
                                                    ->sum('dr'); ?>
                                            @if ($dif < 0)
                                                &#8358;({{ number_format(abs($dif), 2) }})
                                            @else
                                                &#8358;{{ number_format($dif, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: right;">&#8358;{{ number_format($sum_cr, 2) }}</th>
                                    <th style="text-align: right;">&#8358;{{ number_format($sum_dr, 2) }}</th>
                                    <th style="text-align: right">
                                        @if ($dif < 0)
                                            &#8358;({{ number_format(abs($dif), 2) }})
                                        @else
                                            &#8358;{{ number_format($dif, 2) }}
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <h5 style="text-align: center;">{{ strtoupper($customer->name) }} Closing
                                            Running Balance: = @if ($dif < 0)
                                                &#8358;({{ number_format(abs($dif), 2) }})
                                            @else
                                                &#8358;{{ number_format($dif, 2) }}
                                            @endif
                                        </h5>
                                    </td>
                                </tr>
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
