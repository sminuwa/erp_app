<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Daily Sales Report- {{ config('app.name', 'Inventory Management System') }}</title>

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

                            <img src="{{ asset('assets/backend/img/logo' . App\Models\User::userBranchAction() . '.png') }}"
                                style="width:80px;height:80px;" alt="Albabello Logo" class="img-circle elevation-3"
                                style="opacity: .8">
                            <h3 style="text-align: center;">
                                {{ $branch == null ? 'All Branches' : $branch->name . "($branch->code)" }} </h3>
                            <h5 style="text-align: center;">{{ $type == '%' ? 'All' : $type }} SALES
                                REPORT
                                BETWEEN
                                {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                                AND
                                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-bordered caption" id="example1" data-ordering="false">
                                <thead>
                                    <tr>
                                        <th>DATE</th>
                                        <th>CODE</th>
                                        <th>ITEM</th>
                                        <th>STORE</th>
                                        <th>REFERENCE</th>
                                        <th>ACCOUNT</th>
                                        <th>QTY</th>
                                        <th>COST PRICE(&#8358;)</th>
                                        <th>SOLD PRICE(&#8358;)</th>
                                        <th>TOTAL COST(&#8358;)</th>
                                        <th>TOTAL SALES(&#8358;)</th>
                                        <th>MARGIN(&#8358;)</th>
                                    </tr>
                                </thead>
                                @php
                                    $total_cost_price = 0;
                                    $total_sold_price = 0;
                                    $total_cost = 0;
                                    $total_sold = 0;
                                    $total_profit = 0;
                                    $grand_total_profit = 0;
                                    $last_order_date = $to_date; // This is to enable us display all credit notes beyond last order date
                                @endphp
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->order_id }}-{{ \Carbon\Carbon::parse($sale->order_date)->toFormattedDateString() }}
                                        </td>
                                        <td>{{ $sale->product_code }}</td>
                                        <td>{{ $sale->product_name }}</td>
                                        <td>{{ $sale->store_code }}</td>
                                        <td>{{ $sale->reference }}</td>
                                        <td>{{ $sale->customer }}</td>
                                        <td>{{ $sale->quantity }}</td>
                                        <td style="text-align: right">
                                            {{ number_format($sale->cost_price, 2, '.', ',') }}</td>
                                        <td style="text-align: right">
                                            {{ number_format($sale->sold_price, 2, '.', ',') }}</td>
                                        <td style="text-align: right">
                                            {{ number_format($sale->cost_price * $sale->quantity, 2, '.', ',') }}</td>
                                        <td style="text-align: right">
                                            {{ number_format($sale->sold_price * $sale->quantity, 2, '.', ',') }}</td>
                                        <td style="text-align: right">
                                            @php
                                                $total_profit = $sale->sold_price * $sale->quantity - $sale->cost_price * $sale->quantity;
                                                $grand_total_profit += $total_profit;
                                            @endphp
                                            @if ($total_profit < 0)
                                                ({{ number_format(abs($total_profit), 2, '.', ',') }})
                                            @else
                                                {{ number_format($total_profit, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                    @php $credit_notes = App\Models\Order::find($sale->order_id)->creditNotes @endphp
                                    @if ($credit_notes != null)
                                        @foreach ($credit_notes as $note)
                                            @foreach ($note->credit_note_items()->get() as $item)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($note->date)->toFormattedDateString() }}
                                                    </td>
                                                    <td>{{ $item->storeProduct->product->code }}</td>
                                                    <td>{{ $item->storeProduct->product->name }}</td>
                                                    <td>{{ $item->storeProduct->store->code }}</td>
                                                    <td>{{ $note->reference }}</td>
                                                    <td>{{ $sale->customer }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td style="text-align: right">
                                                        {{ number_format($item->cost_price, 2, '.', ',') }}</td>
                                                    <td style="text-align: right">
                                                        -{{ number_format($item->sold_price, 2, '.', ',') }}</td>
                                                    <td style="text-align: right">
                                                        {{ number_format($item->cost_price * $item->quantity, 2, '.', ',') }}
                                                    </td>
                                                    <td style="text-align: right">
                                                        {{ number_format(-$item->sold_price * $item->quantity, 2, '.', ',') }}
                                                    </td>
                                                    <td style="text-align: right">
                                                        @php
                                                            $total_profit += -$item->sold_price * $item->quantity - $item->cost_price * $item->quantity;
                                                            $grand_total_profit += $total_profit;
                                                        @endphp
                                                        @if ($total_profit < 0)
                                                            ({{ number_format(abs($total_profit), 2, '.', ',') }})
                                                        @else
                                                            {{ number_format($total_profit, 2) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endif
                                    @php
                                        $total_cost_price += $sale->cost_price;
                                        $total_sold_price += $sale->sold_price;
                                        $total_cost += $sale->cost_price * $sale->quantity;
                                        $total_sold += $sale->sold_price * $sale->quantity;
                                        $last_order_date = $sale->order_date;
                                    @endphp
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th colspan="7" style="text-align: right">TOTAL</th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_cost_price, 2, '.', ',') }}</th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_sold_price, 2, '.', ',') }}</th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_cost, 2, '.', ',') }}
                                        </th>
                                        <th style="text-align: right">
                                            &#8358;{{ number_format($total_sold, 2, '.', ',') }}
                                        </th>
                                        <th style="text-align: right">
                                            @if ($grand_total_profit < 0)
                                                &#8358;({{ number_format(abs($grand_total_profit), 2, '.', ',') }})
                                            @else
                                                &#8358;{{ number_format($grand_total_profit, 2) }}
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
