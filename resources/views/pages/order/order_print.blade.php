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

</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-11">
                            <img src="{{ asset('assets/backend/img/logo.png') }}" style="width:100px;height:60px;"
                                alt="logo" class="img-circle elevation-3" style="opacity: .8">
                            <span style="font-size:24px;">&nbsp;{{App\Models\User::userBranchName()->long_name}}</span>

                            <small class="float-right">Date: {{ date('l, d-M-Y h:i:s A') }}</small>

                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4">
                            <address>
                                <h5>HEAD OFFICE</h5>
                                Address <span class="ion-ios-contact-outline"></span>: {{ $company->address }}
                                {{ $company->city }} - {{ $company->zip_code }},
                                {{ $company->country }}<br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $company->mobile }}
                                {{ $company->phone !== null ? ', 0' . $company->phone : '' }}
                                <br>
                                Email <span class="ion-email"></span>: {{ $company->email }}
                            </address>
                        </div>
                        <!-- /.col -->

                        <!-- /.col -->
                        <div class="col-sm-4 offset-3">
                            <address>
                                <h5>BRANCH OFFICE</h5>
                                Address <span class="ion-ios-contact-outline"></span>: {{ $order->branch?->address }}
                                <br>
                                Phone <span class="ion-android-phone-portrait"></span>:
                                {{ $order->branch->phone }}
                                <br>
                                Email <span class="ion-email"></span>: {{ $order->branch?->email }}
                            </address>
                        </div>
                        <!-- /.col -->
                    </div>
                    <div class="row">
                        <div class="col-sm-4 invoice-col">
                            <address>
                                <b>Customer Name:</b> {{ $order->customer->code }} - {{ $order->customer->name }}<br />
                                <b>Address:</b> <span class="ion-ios-contact-outline"></span>
                                {{ $order->customer->address }}<br>
                                <b>Phone:</b> <span class="ion-android-phone-portrait"></span>
                                {{ $order->customer->phone }}<br>
                            </address>
                        </div>
                        <div class="col-sm-3 invoice-col">
                            <div style="color:aliceblue;">
                                {{ QrCode::size(70)->generate($order->total) }}<br />
                                <span style="font-size:28px;margin-top:-5px">
                                    {{ $order->payment_mode }} Sales
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <b>Invoice No:</b> {{ $order->reference }}<br>
                            <b>Date and Time: {{ \Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}</b><br>
                            <b>Prepared By</b> <span class="ion-card"></span> {{ $order->sold->name ?? ''}}<br />
                            <b>Printed By <span class="ion-printer"></span>
                                &nbsp;&nbsp;{{ Auth::user()->name }}</span><span>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row" style="line-height: 0.4">
                        <div class="col-11 table-responsive">
                            <table class="table table-bordered table-condensed text-left">
                                <thead>
                                    <tr>
                                        <th style="width:10%;">CODE</th>
                                        <th style="width:30%;">DESCRIPTION</th>
                                        <th style="width:5%;">QTY</th>
                                        <th style="width:5%;">UFM</th>
                                        <th style="width:10%;">STORE CODE</th>
                                        <th style="width:10%;">UNIT PRICE</th>
                                        <th style="width:20%;">TOTAL</th>
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
                                        <td>{{ $order_detail->product->code ?? '' }}</td>
                                        <td>{{ $order_detail->product->name ?? '' }}</td>
                                        <td>{{ $order_detail->unit ?? '' }}</td>
                                        <td>{{ $order_detail->store->code ?? '' }}</td>
                                        <td align="center">{{ $order_detail->quantity }}</td>
                                        <td align="right">{{ number_format($order_detail->unit_cost, 2) }}
                                        </td>
                                        <td align="right">
                                            {{ number_format($order_detail->unit_cost * $order_detail->quantity, 2) }}
                                        </td>
                                    </tr>
                                    @php $total += ($order_detail->unit_cost * $order_detail->quantity);  @endphp
                                @endforeach
                                </tbody>
                            </table>
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <td rowspan="6" style="text-align: left;vertical-align: bottom">
                                        Balance C/F =
                                        @if ($order->customer->amount()->sum('cr') - $order->customer->amount()->sum('dr') < 0)
                                            &#8358;({{ number_format(abs($order->customer->amount()->sum('cr') - $order->customer->amount()->sum('dr')), 2) }})
                                        @else
                                            &#8358;{{ number_format($order->customer->amount()->sum('cr') - $order->customer->amount()->sum('dr'), 2) }}
                                        @endif
                                    </td>
                                    <th style="text-align: right">Total Amount := </th>
                                    <th style="text-align: right;">
                                        &#8358;{{ number_format($total, 2, '.', ',') }}</th>
                                </tr>
                                {{--@if ($order->discount != 0)
                                    <tr>
                                        <th style="text-align: right">Discount := </th>
                                        <th style="text-align: right;">
                                            &#8358;{{ number_format($order->discount, 2, '.', ',') }}
                                        </th>
                                    </tr>
                                @endif--}}


                                <tr>
                                    <td colspan="4">Amoun Paid in Words:
                                        <span>{{ $utility->convertNumberToWords($total) }} Naira</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <p style="line-height: 14px">Goods Received in good condition cannot be returned.
                                <br>Sales invalidated if goods not taken within two (2) days
                            </p>

                            <table class="table table-condensed">
                                <tr>
                                    <td colspan="4" style='border-style:none;'>
                                        ___________________________<br /><br />
                                        Customer's Signature
                                    </td>
                                    <td style="border-style:none;text-align: center">
                                        {{-- @php
                                            $uc = substr($order->invoice_no, 0, 6) . substr($order->invoice_no, 6, 10) + 3000;
                                        @endphp
                                        {{ QrCode::size(100)->backgroundColor(255, 55, 0)->generate("$total\n$uc\n\n.") }} --}}
                                    </td>
                                    <td colspan="4" style="text-align: right;border-style:none;"><span
                                            style='font-size:14px; border-style:none;'></span>
                                        Signature:
                                        _______________________________<br /><br />
                                        for: {{App\Models\User::UserBranchName()->long_name}}
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
