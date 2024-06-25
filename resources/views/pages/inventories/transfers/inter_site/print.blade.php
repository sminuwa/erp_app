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

                            <img src="{{ asset('assets/backend/img/logo' . '.png') }}" style="width:30px;height:30px;"
                                alt="Albabello Logo" class="img-circle elevation-3" style="opacity: .8">
                            <h3>
                                {{ App\Models\User::UserBranchName()->long_name }}
                            </h3>
                            <h4>INTERSITE STOCK TRANSFER</h4>
                            <small class="float-right">View Date: {{ date('l, d-M-Y h:i:s A') }}</small>

                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row" style="line-height: 0.4">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Reference</th>
                                        <td colspan="3">{{ $intersite->reference }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <td>{{ Carbon\Carbon::parse($intersite->date)->toFormattedDateString() }}</td>
                                        <th>Truck No</th>
                                        <td>{{ $intersite->vehicle_no }}</td>
                                    </tr>
                                    <tr>

                                    </tr>
                                    <tr>
                                        <th>Source</th>
                                        <td> {{ $intersite->source->code ?? '' }}-{{ $intersite->source->name ?? '' }}
                                        </td>
                                        <th>Destination</th>
                                        <td> {{ $intersite->destination->code ?? '' }}-{{ $intersite->destination->name ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <td>{{ $intersite->createdBy->name ?? '' }}</td>
                                        <th>Posted By</th>
                                        <td colspan="s">{{ $intersite->postedBy->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Received By</th>
                                        <td colspan="3">{{ $intersite->receivedBy->name ?? '' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    Request Products
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table table-bordered" id="record1">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Code</th>
                                                <th>Description</th>
                                                <th>Store</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total = 0; @endphp
                                            @foreach ($intersite->products as $product)
                                                <tr>
                                                    <th>{{ $loop->index + 1 }}</th>
                                                    <td>{{ $product->product->code }}</td>
                                                    <td>{{ $product->product->name }}</td>
                                                    <td>{{ $product->store->name ?? '' }}</td>
                                                    <td>{{ $product->quantity }}</td>


                                                    @php $total += $product->cost_price * $product->quantity_requested; @endphp
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            {{-- <th style="text-align: right">Total</th>
                                            <th style="text-align: right">&#8358;{{ number_format($total, 2) }}</th> --}}
                                            <th></th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    Received Products
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table table-bordered" id="record1">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Product</th>
                                                <th>Store</th>
                                                <th>Quantity</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total = 0; @endphp
                                            @foreach ($intersite->receivedProducts as $product)
                                                <tr>
                                                    <th>{{ $loop->iteration }}</th>
                                                    <td>{{ $product->product->code }} - {{ $product->product->name }}
                                                    </td>
                                                    <td>{{ $product->store->code }} - {{ $product->store->name }}</td>
                                                    <td>{{ $product->quantity }}</td>
                                                    @php $total += $product->cost_price * $product->quantity_requested; @endphp
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div> --}}
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
