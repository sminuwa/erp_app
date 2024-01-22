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
                        <h1>Return & Debit Details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Return & Debit</li>
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
                                @can('return.debit.create')
                                    <a href="{{ route('return.debit.create') }}" class="btn btn-secondary btn-sm ">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i> New R&D
                                    </a>
                                @endcan
                                @if ($returndebit->status == 1)
                                    @can('return.debit.print')
                                        <a href="{{ route('return.debit.print', $returndebit->id) }}" target="_BLANK"
                                            class="btn btn-dark btn-sm ">
                                            <i class="fa fa-print" aria-hidden="true"></i> Print
                                        </a>
                                    @endcan
                                    
                                @endif
                                @if ($returndebit->status == 0)
                                    @can('return.debit.edit')
                                        <a href="{{ route('return.debit.edit', $returndebit->id) }}" class="btn btn-info btn-sm ">
                                            <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                        </a>
                                    @endcan
                                    @can('return.debit.post')
                                        <form class="d-inline" action="{{ route('return.debit.post', $returndebit->id) }}" method="post"
                                            onsubmit="return confirm('Are you sure you want to post this R&D?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm ">
                                                <i class="fa fa-check" aria-hidden="true"></i> Post
                                            </button>
                                        </form>
                                    @endcan
                                    @can('return.debit.delete')
                                        <form class="d-inline" id="delete-form-{{ $returndebit->id }}"
                                            action="{{ route('return.debit.delete', $returndebit->id) }}" method="post"
                                            onsubmit="return confirm('Are you sure you want to close this R&D?')">
                                            @csrf
                                            <button class="btn btn-danger btn-sm " type="submit">
                                                <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                        <div class="invoice p-3 mt-3">
                            <!-- title row -->

                            <!-- info row -->
                            <div class="row invoice-info">
                                
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    Purchase:
                                    <address>
                                        <strong>{{ $returndebit->supplier->code }} - {{ $returndebit->supplier->name }}</strong><br>
                                        {{ $returndebit->supplier->address }}<br>
                                        {{ $returndebit->supplier->city }}<br>
                                        Phone: {{ $returndebit->supplier->phone }}<br>
                                        
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    <b>Reference No: {{ $returndebit->reference }}</b><br><br>
                                    <b>Status:</b>
                                    {!! $returndebit->status == 0
                                        ? '<span class="badge badge-warning">Pending</span>'
                                        : ($returndebit->status == 1
                                            ? '<span class="badge badge-success">Posted</span>'
                                            : '<span class="badge badge-success">Pending</span>') !!}
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Reference</th>
                                                <th>Product</th>
                                                <th>Original QTY</th>
                                                <th>Changed QTY</th>
                                                <th>Original Unit Cost</th>
                                                <th>Current Unit Cost</th>
                                                <th>Total Original Cost</th>
                                                <th>Total Current Cost</th>
                                                <th>Margin</th>

                                            </tr>
                                        </thead>
                                        @php $current_total =  $original_total =  0; @endphp
                                        <tbody>
                                            @foreach ($returndebit->returnItems as $item)
                                                <tr>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($returndebit->date)->toFormattedDateString() }}
                                                    </td>
                                                    <td>
                                                        {{ $returndebit->reference }}
                                                    </td>
                                                    <td>
                                                        {{ $item->product->code }}-{{ $item->product->name }}
                                                    </td>
                                                    <td>
                                                        {{ $item->original_quantity_purchased }}
                                                    </td>
                                                    <td>
                                                        {{ $item->current_quantity }}
                                                    </td>
                                                    <td align="right">
                                                        {{ number_format($item->original_unit_cost, 2) }}
                                                    </td>
                                                    <td align="right">
                                                        {{ number_format($item->current_unit_cost, 2) }}
                                                    </td>
                                                    <td align="right">
                                                        {{ number_format($item->original_unit_cost * $item->original_quantity_purchased, 2) }}
                                                    </td>
                                                    <td align="right">
                                                        {{ number_format($item->current_unit_cost * $item->current_quantity, 2) }}
                                                    </td>
                                                    <td align="right">
                                                        {{ number_format($item->original_unit_cost * $item->original_quantity_purchased - $item->current_unit_cost * $item->current_quantity, 2) }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $current_total += $item->current_unit_cost * $item->current_quantity;
                                                    $original_total += $item->original_unit_cost * $item->original_quantity_purchased;
                                                @endphp
                                            @endforeach

                                            <tr>
                                                <td class="text-right text-danger" colspan="7">
                                                    <p>
                                                        <strong>TOTAL: </strong>
                                                    </p>
                                                </td>
                                                <td class="text-right text-danger">
                                                    <p>
                                                        <strong>&#8358;{{ number_format($original_total, 2) }}</strong>
                                                    </p>
                                                </td>
                                                <td class="text-right text-danger">
                                                    <p>
                                                        <strong>&#8358;{{ number_format($current_total, 2) }}</strong>
                                                    </p>
                                                </td>
                                                <td>
                                                    <p>
                                                        <strong>&#8358;{{ number_format($original_total - $current_total, 2) }}</strong>
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
