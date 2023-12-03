@extends('layouts.backend.app')

@section('title', 'Order')

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
                        <h1>Payment</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Payment</li>
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
                        <div class="row no-print">
                            <div class="col-md-12 text-right">

                                <a href="javascript:history.back()" class="btn btn-warning btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                                <a href="{{ route('create.payment') }}" class="btn btn-secondary btn-sm ">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i> New Receipt
                                </a>

                                <a href="{{ route('payment.print', $payment->id) }}" target="_BLANK"
                                    class="btn btn-dark btn-sm ">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print
                                </a>
                                <a href="{{ route('payment.print.pos', $payment->id) }}" target="_BLANK"
                                    class="btn btn-dark btn-sm ">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print (PoS)
                                </a>

                                @if ($payment->status == 0)
                                    <a href="{{ route('create.payment', ['payment_id' => $payment->id]) }}"
                                        class="btn btn-info btn-sm ">
                                        <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                    </a>
                                    <form class="d-inline" action="{{ route('payment.post', $payment->id) }}"
                                        method="post"
                                        onsubmit="return confirm('Are you sure you want to post this payment?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm ">
                                            <i class="fa fa-check" aria-hidden="true"></i> Post
                                        </button>
                                    </form>

                                    <form class="d-inline" id="delete-form-{{ $payment->id }}"
                                        action="{{ route('payment.delete', $payment->id) }}" method="post"
                                        onsubmit="return confirm('Are you sure you want to close this payment?')">
                                        @csrf
                                        <button class="btn btn-danger btn-sm " type="submit">
                                            <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                        </button>
                                    </form>
                                @endif


                            </div>
                        </div>
                        <div class="invoice p-3 mt-3">
                            <!-- title row -->

                            <!-- info row -->
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    Payment From
                                    <address>
                                        <h5>Payment From</h5>
                                        <p><b>{{ $payment->payer()->code ? $payment->payer()->code . ' - ' . $payment->payer()->name : $payment->payer()->number . ' - ' . $payment->payer()->description }}
                                            </b></p>
                                        <p><b>Mobile :</b> {{ $payment->customer->phone }}</p>
                                        <p><b>Address :</b> {{ $payment->customer->address }}</p>
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    To

                                    <address>
                                        @if ($payment->mode_name == 'Customer')
                                            <strong>{{ $payment->customer->code }} -
                                                {{ $payment->customer->name }}</strong><br>
                                            {{ $payment->customer->address }}<br>
                                            {{ $payment->customer->city }}<br>
                                            Phone: {{ $payment->customer->phone }}<br>
                                            Email: {{ $payment->customer->email }}
                                        @elseif ($payment->mode_name == 'Supplier')
                                            <strong>{{ $payment->supplier->code }} -
                                                {{ $payment->supplier->name }}</strong><br>
                                            {{ $payment->supplier->address }}<br>
                                            {{ $payment->supplier->city }}<br>
                                            Phone: {{ $payment->supplier->phone }}<br>
                                            Email: {{ $payment->supplier->email }}
                                        @else
                                            <strong>{{ $payment->payer()->number }}-{{ $payment->payer()->description }}
                                            </strong><br>
                                        @endif
                                    </address>

                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    <p><b>Receipt No: {{ $payment->payment_no }}</b></p>
                                    <p><b>Payment Date:
                                            {{ \Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}</b></p>
                                    <b>Invoice Status:</b>
                                    {!! $payment->status == 0
                                        ? '<span class="badge badge-warning">Pending</span>'
                                        : ($payment->status == 1
                                            ? '<span class="badge badge-success">Posted</span>'
                                            : '<span class="badge badge-success">Pending</span>') !!}
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-bordered text-left">
                                        <thead>
                                            <tr>
                                                <th>Account</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>


                                            <tr>
                                                <td>{{ $payment->account()->code ?? $payment->account()->number }} -
                                                    {{ $payment->account()->name ?? $payment->account()->description }}
                                                </td>
                                                <td>{{ $payment->description }}</td>
                                                <th style="text-align: right">
                                                    {{ number_format($payment->amount, 2, '.', ',') }}</th>
                                                <td>{{ Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                                </td>

                                            </tr>
                                            <tr>
                                                <td class="text-left text-danger" colspan="2">
                                                    <p>
                                                        <strong>Amount in ward: </strong>

                                                        @php
                                                            $obj = new App\Models\Utility();
                                                            /*$a = new NumberFormatter("en", NumberFormatter::SPELLOUT);*/
                                                        @endphp
                                                        <strong><i class="fa fa-inr"></i>
                                                            {{ $obj->convertNumberToWords($payment->amount) }}</strong>
                                                        {{--                                            {{ $a->format($payment->amount/2.3) }}</strong> --}}
                                                    </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                    </div>
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
    </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->





@endsection



@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/backend/plugins/fastclick/fastclick.js') }}"></script>

    <!-- Sweet Alert Js -->
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>

    <script type="text/javascript">
        function deleteItem(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
            })

            swalWithBootstrapButtons({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('delete-form-' + id).submit();
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons(
                        'Cancelled',
                        'Your data is safe :)',
                        'error'
                    )
                }
            })
        }
    </script>
@endpush
