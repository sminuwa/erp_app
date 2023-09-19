<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
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
                        <div class="col-12" style="text-align: center">

                            <img src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="width:60px;height:60px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{App\Models\User::UserBranchName()->long_name}}
                            </h3>
                            <h4>BANK {{ $record->type == 'Both' ? ' WITHDRAW & DEPOSIT' : strtoupper($record->type) }}</h4>
                            <small class="float-right">View Date: {{ date('l, d-M-Y h:i:s A') }}</small>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered text-left">
                                @if ($record->type == 'Deposit')
                                    <thead>
                                        <tr>
                                            <th>DATE</th>
                                            <th>DEPOSITOR NAME</th>
                                            <th>A/C NUMBER</th>
                                            <th>A/C NAME</th>
                                            <th>WITHDRAW SLIP</th>
                                            <th>AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $record->date_deposit }}</td>
                                            <td>{{ $record->depositor->name }}</td>
                                            <td>{{ $record->fromAccount->account_name }}</td>
                                            <td>{{ $record->fromAccount->account_no }}</td>
                                            <td>{{ $record->slip_no }}</td>
                                            <td>{{ number_format($record->amount, 2) }}</td>
                                        </tr>
                                    </tbody>
                                @endif
                                @if ($record->type == 'Withdraw')
                                    <thead>
                                        <tr>
                                            <th>DATE</th>
                                            <th>WITHRAWER NAME</th>
                                            <th>A/C NUMBER</th>
                                            <th>A/C NAME</th>
                                            <th>WITHDRAW SLIP</th>
                                            <th>AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $record->date_withdraw }}</td>
                                            <td>{{ $record->withdrawer->name }}</td>
                                            <td>{{ $record->fromAccount->account_name }}</td>
                                            <td>{{ $record->fromAccount->account_no }}</td>
                                            <td>{{ $record->slip_no }}</td>
                                            <td>{{ number_format($record->amount, 2) }}</td>
                                        </tr>
                                    </tbody>
                                @endif
                                @if ($record->type == 'Both')
                                    <thead>
                                        <tr>
                                            <th>DATE</th>
                                            <th>WITHD NAME</th>
                                            <th>A/C NUMBER</th>
                                            <th>A/C NAME</th>
                                            <th>DEP NAME</th>
                                            <th>A/C NUMBER</th>
                                            <th>A/C NAME</th>
                                            <th>WITHDRAW SLIP</th>
                                            <th>AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($record->date_withdraw)->toFormattedDateString() }}
                                            </td>
                                            <td>{{ $record->withdrawer->name }}</td>
                                            <td>{{ $record->fromAccount->account_name }}</td>
                                            <td>{{ $record->fromAccount->account_no }}</td>
                                            <td>{{ $record->depositor->name }}</td>
                                            <td>{{ $record->toAccount->account_name }}</td>
                                            <td>{{ $record->toAccount->account_no }}</td>
                                            <td>{{ $record->slip_no }}</td>
                                            <td>{{ number_format($record->amount, 2) }}</td>
                                        </tr>
                                    </tbody>
                                @endif
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
