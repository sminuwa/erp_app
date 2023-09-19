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

                            <img src="{{ asset('assets/backend/img/logo'.App\Models\User::userBranchAction().".png") }}" style="width:80px;height:80px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{App\Models\User::UserBranchName()->long_name}}
                            </h3>
                            <h5 style="text-align: center;">Ageing Report
                                @if ($from_date != 'all')
                                    From
                                    {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                                    AND
                                    {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                                @endif
                            </h5>
                            <h5 style="text-align: center;">
                                CUSTOMER NAME:
                                {{ $customer_id == 'all' ? 'All' : \App\Models\Customer::find($customer_id)->name }}
                            </h5>
                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered caption" id="example1" border="1" cellpadding="0"
                                cellspacing="0" data-ordering="false">
                                <thead>
                                    <tr>
                                        <th>PHONE</th>
                                        <th>NAME</th>
                                        <th>INVOICE</th>
                                        <th>TRN DATE</th>
                                        <th>AMOUNT</th>
                                        <th>NET DUE DATE</th>
                                        <th>NOT DUE</th>
                                        <th>0-30 DAYS</th>
                                        <th>DUE INVOICE</th>
                                    </tr>
                                </thead>
                                @php
                                    $total_30_days = 0;
                                    $total_not_due = 0;
                                    $total_amount = 0;
                                    $total_due_invoice = 0;
                                    $flag_payment = false;
                                    $flag_payment_amount = 0;
                                @endphp
                                @foreach ($sales as $sale)
                                    @php
                                        $ledger = \App\Models\CustomerLedger::find($sale->id);
                                        $dateNow = \Carbon\Carbon::now();
                                        $amount = 0;
                                        $due_invoice = 0;
                                        if ($sale->dr != 0) {
                                            $flag_payment = true;
                                            $flag_payment_amount = $sale->dr;
                                        }
                                        
                                        $total_amount += $sale->cr != 0 ? $sale->cr : $sale->dr;
                                        $total_not_due += $sale->cr != 0 ? $sale->cr : 0;
                                        if ($sale->cr != 0) {
                                            if (\Carbon\Carbon::parse($ledger->order?->due_date)->lt(\Carbon\Carbon::parse(date('Y-m-d')))) {
                                                //It is due
                                                $amount = $sale->cr != 0 ? $sale->cr : 0 - $sale->dr;
                                                $due_invoice = $amount;
                                                if ($flag_payment == true) {
                                                    //$amount = 0 - $sale->dr;
                                                    $due_invoice = abs($amount) - $flag_payment_amount;
                                                    $flag_payment = false;
                                                }
                                            } else {
                                                //It not is due
                                                $due_invoice = 0;
                                            }
                                        } else {
                                            $amount = 0 - $sale->dr;
                                            $due_invoice = $amount;
                                        }
                                        $total_30_days += $amount;
                                        $total_due_invoice += $due_invoice;
                                    @endphp


                                    <tr>
                                        <td>{{ $sale->phone }}</td>
                                        <td>{{ $sale->name }}</td>
                                        <td>{{ $sale->receipt_no == null ? $sale->systemid : $sale->receipt_no }}</td>
                                        <td>{{ $sale->cr != 0 ? \Carbon\Carbon::parse($ledger->order?->order_date)->toFormattedDateString() : \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}
                                        </td>
                                        <td style="text-align: right">
                                            &#8358;{{ number_format($sale->cr != 0 ? $sale->cr : 0 - $sale->dr, 2, '.', ',') }}
                                        </td>
                                        <td>{{ $sale->cr != 0 ? \Carbon\Carbon::parse($ledger->order?->due_date)->toFormattedDateString() : \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}
                                        </td>
                                        <td style="text-align: right">
                                            &#8358;{{ number_format($sale->cr != 0 ? $sale->cr : 0, 2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right">&#8358;{{ number_format($amount, 2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right">
                                            &#8358;{{ number_format($due_invoice, 2, '.', ',') }}</td>
                                    </tr>
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th style="text-align: right" colspan="4">TOTAL</th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_amount, 2, '.', ',') }}</th>
                                        <th></th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_not_due, 2, '.', ',') }}</th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_30_days, 2, '.', ',') }}</th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_due_invoice, 2, '.', ',') }}</th>
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
