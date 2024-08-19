<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>Ledger - {{ config('app.name', 'Inventory Management System') }}</title>

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
                            <h3>
                                {{ $branch->name }}
                            </h3>
                            <h5 style="text-align: center;">STOCK LEDGER REPORT BETWEEN
                                {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                                AND
                                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
                            </h5>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div style="text-align: center;">
                                <h3>STORE/PRODUCT:
                                    {{ App\Models\Store::find($store_id)->name ?? 'All Stores' }}/{{ App\Models\Product::find($product_id)->name }}
                                </h3>
                            </div>
                            <table class="display table table-bordered caption" id="example1" data-ordering="false">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Store</th>
                                        <th>Branch</th>
                                        <th>QTY</th>
                                        <th>Account</th>
                                        <th>QTY Before</th>
                                        <th>QTY After</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $qty_in_stock = 0;
                                        $available_qty = 0;
                                        $qty_after = $qty_before = $qty_available;
                                        //$available_qty = $qty_available;
                                    @endphp

                                    @foreach ($records as $record)
                                        <tr>
                                            <td>{{ Carbon\Carbon::parse($record->date)->toFormattedDateString() }}</td>
                                            <td>{{ $record->refno }}</td>
                                            <td>{{ $record->store_code }}</td>
                                            <td>{{ $record->branch_code }}</td>
                                            <td>
                                                @php
                                                    $qty_after = $qty_before;
                                                @endphp
                                                @if ($record->cr > 0)
                                                    {{ $quantity = $record->cr }}
                                                    @php
                                                        $qty_before = $qty_after - $quantity;
                                                    @endphp
                                                @else
                                                    -{{ $quantity = $record->dr }}
                                                    @php
                                                        $qty_before = $qty_after + $quantity;
                                                    @endphp
                                                @endif
                                            </td>


                                            <td>
                                                @if ($record->model_name == 'Customer')
                                                    {{ App\Models\Customer::find($record->model_id)->code }}
                                                    {{-- @elseif($record->model_name == 'GeneralAccount')
                                                    {{ App\Models\GeneralAccount::find($record->model_id)->description }} --}}
                                                @elseif($record->model_name == 'Supplier')
                                                    {{ App\Models\Supplier::find($record->model_id)->code }}
                                                @endif
                                            </td>

                                            <td>{{ $qty_before }}</td>
                                            <td>{{ $qty_after }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <td colspan="7" align="right">Current Stock Balance B/F</td>
                                        <td>{{ number_format($qty_after, 0) }}</td>
                                    </tr>
                                </tfoot> --}}
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
