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
                        <h1>Receipt</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Receipt</li>
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
                                @can('create.payment.reciept')
                                    <a href="{{ route('create.payment.reciept') }}" class="btn btn-secondary btn-sm ">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i> New Receipt
                                    </a>
                                @endcan



                                @if ($receipt->status == 0)
                                    @can('create.payment.reciept')
                                        <a href="{{ route('create.payment.reciept', ['receipt_id' => $receipt->id]) }}"
                                            class="btn btn-info btn-sm ">
                                            <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                        </a>
                                    @endcan
                                    @can('receipt.payment.post')
                                        <form class="d-inline" action="{{ route('receipt.payment.post', $receipt->id) }}"
                                            method="post"
                                            onsubmit="return confirm('Are you sure you want to post this receipt?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm ">
                                                <i class="fa fa-check" aria-hidden="true"></i> Post
                                            </button>
                                        </form>
                                    @endcan
                                    @can('receipt.payment.delete')
                                        <form class="d-inline" id="delete-form-{{ $receipt->id }}"
                                            action="{{ route('receipt.payment.delete', $receipt->id) }}" method="post"
                                            onsubmit="return confirm('Are you sure you want to close this receipt?')">
                                            @csrf
                                            <button class="btn btn-danger btn-sm " type="submit">
                                                <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                            </button>
                                        </form>
                                    @endcan
                                @else
                                    @can('receipt.payment.print')
                                        <a href="{{ route('receipt.payment.print', $receipt->id) }}" target="_BLANK"
                                            class="btn btn-dark btn-sm ">
                                            <i class="fa fa-print" aria-hidden="true"></i> Print
                                        </a>
                                    @endcan
                                    @can('receipt.payment.print.pos')
                                        <a href="{{ route('receipt.payment.print.pos', $receipt->id) }}" target="_BLANK"
                                            class="btn btn-dark btn-sm ">
                                            <i class="fa fa-print" aria-hidden="true"></i> Print (PoS)
                                        </a>
                                    @endcan
                                @endif


                            </div>
                        </div>
                        <div class="invoice p-3 mt-3">
                            <!-- title row -->

                            <!-- info row -->
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    From
                                    <address>
                                        <strong>{{ config('app.name') }}</strong><br>
                                        {{ $company?->address }}<br>
                                        {{ $company?->city }} , {{ $company?->country }}<br>
                                        Phone:
                                        {{ $company?->mobile }}
                                        {{ $company?->phone !== null ? ', 0' . $company?->phone : '' }}
                                        <br>
                                        Email: {{ $company?->email }}
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    To

                                    <address>
                                        @if ($receipt->mode_name == 'Customer')
                                            <strong>{{ $receipt->customer->code }} -
                                                {{ $receipt->customer->name }}</strong><br>
                                            {{ $receipt->customer->address }}<br>
                                            {{ $receipt->customer->city }}<br>
                                            Phone: {{ $receipt->customer->phone }}<br>
                                            Email: {{ $receipt->customer->email }}
                                        @elseif ($receipt->mode_name == 'Supplier')
                                            <strong>{{ $receipt->supplier->code }} -
                                                {{ $receipt->supplier->name }}</strong><br>
                                            {{ $receipt->supplier->address }}<br>
                                            {{ $receipt->supplier->city }}<br>
                                            Phone: {{ $receipt->supplier->phone }}<br>
                                            Email: {{ $receipt->supplier->email }}
                                        @else
                                            <strong>{{ $receipt->payer()->number }}-{{ $receipt->payer()->description }}
                                            </strong><br>
                                        @endif
                                    </address>

                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    <b>Invoice No: {{ $receipt->receipt_no }}</b><br><br>
                                    <b>Invoice Status:</b>
                                    {!! $receipt->status == 0
                                        ? '<span class="badge badge-warning">Pending</span>'
                                        : ($receipt->status == 1
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
                                                <td>{{ $receipt->account()->code ?? $receipt->account()->number }} -
                                                    {{ $receipt->account()->name ?? $receipt->account()->description }}
                                                </td>
                                                <td>{{ $receipt->description }}</td>
                                                <th style="text-align: right">
                                                    {{ currency_sign() . number_format($receipt->amount, 2, '.', ',') }}
                                                </th>
                                                <td>{{ Carbon\Carbon::parse($receipt->date)->toFormattedDateString() }}
                                                </td>

                                            </tr>
                                            <tr>
                                                <td class="text-left text-danger" colspan="4">
                                                    <p>
                                                        <strong>Amount in ward: </strong>

                                                        @php
                                                            $obj = new App\Models\Utility();
                                                            /*$a = new NumberFormatter("en", NumberFormatter::SPELLOUT);*/
                                                        @endphp
                                                        <strong>
                                                            {{ convertNumberToWords($receipt->amount) }}</strong>
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
