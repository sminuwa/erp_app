@extends('layouts.backend.app')

@section('title', 'Ledger')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
    <style>
        caption {
            caption-side: top;
        }
    </style>
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Intersite Transfer Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Intersite Transfer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a class="btn btn-secondary btn-sm"
                   href="{{ route('intersite.index', $record->id) }}">
                    <span class="fa fa-list"></span> Instersites
                </a>

                <a class="btn btn-secondary btn-sm" title="Print" target="_BLANK"
                   href="{{ route('intersite.print', $record->id) }}">
                    <span class="fa fa-print"></span> Print
                </a>
                @if ($record->status == 0)
                    <form onsubmit="return confirm('Are you sure you want to post this intersite?')"
                          action="{{ route('intersite.post', $record->id) }}"
                          method="post" style="display: inline">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                            <i class="text-white fa fa-check"></i> Post
                        </button>
                    </form>

                    <a class="btn btn-secondary btn-sm"
                       href="{{ route('intersite.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span> Edit
                    </a>

                    <form onsubmit="return confirm('Are you sure you want to delete this intersite?')"
                          action="{{ route('intersite.delete', $record->id) }}"
                          method="post" style="display: inline">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                            <i class="text-danger fa fa-remove"></i> Delete
                        </button>
                    </form>
                @endif
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5>Intersite Transfer Request <small>products by
                                                {{ $record->reference }}</small>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-block">
                                <table class="table table-bordered table-striped">
                                    <tbody>

                                        <tr>
                                            <th>Invoice</th>
                                            <td>{{ $record->refno }}</td>
                                        </tr>
                                        <tr>
                                            <th>Request Date</th>
                                            <td>{{ optional($record->created_at)->toDayDateTimeString() }}</td>
                                        </tr>
                                        <tr>
                                            <th>Truck No</th>
                                            <td>{{ $record->vehicle_no }}</td>
                                        </tr>
                                        <tr>
                                            <th>Transfer to Branch</th>
                                            <td> {{ $record->destinationBranch->code ?? '' }}-{{ $record->destinationBranch->name ?? '' }} </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>{{ $record->status}}</td>
                                        </tr>
                                        <tr>
                                            <th>Requested By</th>
                                            <td>{{ $record->requestedBy->name ?? ''}}</td>
                                        </tr>
                                        <tr>
                                            <th>Approved By</th>
                                            <td>{{ $record->approvedBy->name ?? ''}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

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
                                            <th>QTY</th>
                                            {{-- <th>Price (&#8358;)</th>
                                            <th>Subtotal (&#8358;)</th> --}}
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach ($record->requestProducts()->get() as $product)
                                            <tr>
                                                <th>{{ $loop->index + 1 }}</th>
                                                <td>{{ $product->product->code }}</td>
                                                <td>{{ $product->product->name }}</td>
                                                <td>{{ number_format($product->quantity_requested, 0, '', ',') }}</td>
                                                {{-- <td style="text-align: right">{{ number_format($product->cost_price, 2) }}
                                                </td>
                                                <td style="text-align: right">
                                                    {{ number_format($product->quantity_requested * $product->cost_price, 2) }}
                                                </td> --}}
                                                <td>{{ $product->status == 1 ? 'Completed' : 'Pending' }}</td>
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
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@push('js')
    <!-- DataTables -->
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/backend/plugins/fastclick/fastclick.js') }}"></script>
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable();
            $('body').on('submit', '.create-form', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('.close-modal').trigger('click')
                    },
                    success: function(response) {
                        $('#expenses').html(response)
                        //console.log(response)
                    }
                })
            });


        });

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
