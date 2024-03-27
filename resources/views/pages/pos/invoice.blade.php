@extends('layouts.backend.app')

@section('title', 'Invoice')

@push('css')
    <style>
        .modal-lg {
            max-width: 50% !important;
        }
    </style>
@endpush

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Invoice</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">DashBoard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pos.index') }}">PoS</a></li>
                            <li class="breadcrumb-item active">Invoice</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Main content -->
                        <div class="invoice p-3 mb-3">
                            <!-- title row -->
                            <div class="row">
                                <div class="col-12">
                                    <h4>
                                        <i class="fa fa-globe"></i> {{ config('app.name') }}
                                        <small class="float-right">Date: {{ date('l, d-M-Y h:i:s A') }}</small>
                                    </h4>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- info row -->
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    From
                                    <address>
                                        <strong>{{ config('app.name') }}</strong><br>
                                        {{ $company->address }}<br>
                                        {{ $company->city }} , {{ $company->country }}<br>
                                        Phone: {{ $company->mobile }}
                                        {{ $company->phone !== null ? ', ' . $company->phone : '' }}<br>
                                        Email: {{ $company->email }}
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    To
                                    <address>
                                        <strong>{{ $customer->name }}</strong><br>
                                        {{ $customer->address }}<br>
                                        {{ $customer->city }}<br>
                                        Phone: {{ $customer->phone }}<br>
                                        Email: {{ $customer->email }}
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    <b>Total Amount:</b> {{ Cart::getTotal() }}<br>
                                    <b>Order Status:</b> <span class="badge badge-warning">Pending</span><br>
                                    <b>Account:</b> {{ $customer->account_number }}
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Quantity</th>
                                                <th>Unit Cost</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contents as $content)
                                                <tr>
                                                    <td>{{ $content->name }}</td>
                                                    <td>{{ $content->quantity }}</td>
                                                    <td style="text-align: right">{{ number_format($content->price, 2) }}
                                                    </td>
                                                    <td style="text-align: right">
                                                        {{ number_format($content->getPriceSum(), 2) }}</td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <div class="row">
                                <!-- accepted payments column -->
                                <div class="col-8"></div>
                                <!-- /.col -->
                                <div class="col-4">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tr>
                                                <th style="width:50%">Subtotal:</th>
                                                <td class="text-right">&#8358;
                                                    {{ number_format(Cart::getSubTotal(), 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th style="width:50%">VAT:</th>
                                                <td class="text-right">&#8358;
                                                    {{ number_format(0, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total:</th>
                                                <td class="text-right">&#8358;
                                                    {{ number_format(Cart::getTotal(), 2) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- this row will not appear when printing -->
                            @can('invoice.print')
                                <div class="row no-print">
                                    <div class="col-12">
                                        <a href="{{ route('invoice.print', $customer->id) }}" target="_blank"
                                            class="btn btn-default"><i class="fa fa-print"></i> Print</a>
                                        <button type="button" data-toggle="modal" data-target="#exampleModal"
                                            class="btn btn-success float-right"><i class="fa fa-credit-card"></i>
                                            Submit Payment
                                        </button>
                                    </div>
                                </div>
                            @endcan
                        </div>
                        <!-- /.invoice -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!--payment modal -->
    <form action="{{ route('invoice.final_invoice') }}" method="post">
        @csrf
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Invoice of {{ $customer->name }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <p class="text-info float-right mb-3">Payable Amount :
                                    {{ number_format(Cart::getTotal(), 2) }}</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputState">Payment Method</label>
                                <select name="payment_mode" class="form-control" required>
                                    <option value="" disabled selected>Choose a Payment Method</option>
                                    @if ($sale_mode != 'Cash')
                                        <option value={{ $sale_mode }} selected>{{ $sale_mode }}</option>
                                    @else
                                        <option value="Cash">Cash</option>
                                        <option value="Cheque">Cheque</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputCity">Pay</label>
                                <input type="number" step=".01" name="pay" value="{{ Cart::getTotal() }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!--/.payment modal -->



@endsection



@push('js')
@endpush
