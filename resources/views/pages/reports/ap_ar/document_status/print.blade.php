<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Daily Remittance - {{ config('app.name', 'Inventory Management System') }}</title>

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
                                style="width:50px;height:50px;" alt="Albabello Logo" class="img-circle elevation-3"
                                style="opacity: .8">
                            <h3>
                                {{ $branch->name ?? 'All Branches' }}
                            </h3>
                            <h5 style="text-align: center;">DOCUMENT STATUS BETWEEN
                                {{ Carbon\Carbon::parse($from)->toFormattedDateString() }} AND
                                {{ Carbon\Carbon::parse($to)->toFormattedDateString() }}</h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">

                            @if ($type == 'Payment' || $type == 'Receipt')
                                <table id="example1"
                                    class="display table table-bordered table-striped text-left table-responsive-xl">
                                    <caption style="caption-size:top">
                                        <h5 style="text-align: center;">{{ strtoupper($branch->name) }} <br>
                                            AP-AR STATUS BETWEEN
                                            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                                            AND
                                            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                                        </h5>
                                    </caption>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Payment No</th>
                                            <th>Payee</th>
                                            <th>Account</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Created By</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr>

                                                <td>{{ Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $payment->receipt_no }}</td>
                                                <td>
                                                    {{ $payment->payer()->code ? $payment->payer()->code . ' - ' . $payment->payer()->name : $payment->payer()->number . ' - ' . $payment->payer()->description }}
                                                </td>
                                                <td>
                                                    {{ $payment->account()->code ? $payment->account()->code . ' - ' . $payment->account()->name : $payment->account()->number . ' - ' . $payment->account()->description }}
                                                </td>

                                                <td align="right">{{ number_format($payment->amount, 2, '.', ',') }}
                                                </td>
                                                <td>{{ $payment->description }}</td>
                                                <td>{{ optional($payment->createdBy)->name }}</td>
                                                <td class="@if ($payment->status == 0) bg-warning @endif">
                                                    {{ $payment->status == 1 ? 'Posted' : 'Pending' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                            @if ($type == 'Interbank')
                                <table id="example1"
                                    class="table table-bordered table-striped text-left table-responsive-xl">
                                    <caption style="caption-size:top">
                                        <h5 style="text-align: center;">{{ strtoupper($branch->name) }} <br>
                                            AP-AR STATUS BETWEEN
                                            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                                            AND
                                            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                                        </h5>
                                    </caption>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Receipt No</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Source</th>
                                            <th>Destination</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($payments as $interbank)
                                            <tr class="@if ($interbank->status == 0) bg-warning @endif">

                                                <td>{{ Carbon\Carbon::parse($interbank->date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $interbank->reference }}</td>
                                                <td align="right">{{ number_format($interbank->amount, 2, '.', ',') }}
                                                </td>
                                                <td>{{ $interbank->description }}</td>
                                                <td>{{ $interbank->source->description }}</td>
                                                <td>{{ $interbank->destination->description }}</td>
                                                <td>{{ optional($interbank->createdBy)->name }}</td>
                                                <td class="@if ($interbank->status == 0) bg-warning @endif">
                                                    {{ $interbank->status == 1 ? 'Posted' : 'Pending' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                            @if ($type == 'Journal')
                                <table id="example1"
                                    class="table table-bordered table-striped text-left table-responsive-xl">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Description</th>
                                            <th>Created By</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $record)
                                            <tr>
                                                <td>{{ Carbon\Carbon::parse($record->date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $record->reference }}</td>
                                                <td>{{ $record->description ?? null }}</td>
                                                <td>{{ optional($record->createdBy)->name }}</td>
                                                <td>
                                                    {{ $record->status == 1 ? 'Posted' : 'Pending' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            @endif
                            @if ($type == 'Invoice')
                                <table id="example1"
                                    class="table table-bordered table-striped text-left table-responsive-xl"
                                    data-ordering="true">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Invoice No</th>
                                            <th>Total</th>
                                            <th>Amount Paid</th>
                                            <th>Amount Due</th>
                                            <th>Due Date</th>
                                            <th>Payment Mode</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total = 0;
                                            $total_pay = 0;
                                            $total_due = 0;
                                        @endphp
                                        @foreach ($payments as $order)
                                            @php
                                                $total = $total + $order->total;
                                                $total_pay = $total_pay + $order->pay;
                                                $total_due = $total_due + $order->due;
                                            @endphp
                                            <tr class="@if ($order->status == 0) bg-warning @endif">
                                                <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $order->customer->name }}</td>
                                                <td>{{ $order->reference }}</td>
                                                <td align="right">
                                                    {{ number_format($order->total, 2, '.', ',') }}
                                                </td>
                                                <td align="right">{{ number_format($order->pay, 2, '.', ',') }}
                                                </td>
                                                <td align="right">{{ number_format($order->due, 2, '.', ',') }}
                                                </td>
                                                <td>{{ Carbon\Carbon::parse($order->due_date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $order->payment_mode }}</td>
                                                <td>
                                                    {{ $order->status == 1 ? 'Posted' : 'Pending' }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

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
