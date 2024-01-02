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
                        <h4>Purchase (Request) Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Purchase (Request)/li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4">
                        @include('cards.purchase_request')
                    </div>
                    <div class="col-sm-8">
                        <div class="card">
                            <div class="card-header">
                                Purchased Products
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered" id="record1">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Code</th>
                                            <th>Description</th>
                                            <th>UTM</th>
                                            <th>QTY</th>
                                            <th>Price (&#8358;)</th>
                                            <th>Subtotal (&#8358;)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach ($record->purchasedProducts()->get() as $product)
                                            <tr>
                                                <th>{{ $loop->index + 1 }}</th>
                                                <td>{{ $product->product->code }}</td>
                                                <td>{{ $product->product->name }}</td>
                                                <td>{{ $product->product->unit }}</td>
                                                <td>{{ number_format($product->quantity, 0, '', ',') }}</td>
                                                <td style="text-align: right">{{ number_format($product->unit_price, 2) }}
                                                </td>
                                                <td style="text-align: right">
                                                    {{ number_format($product->quantity * $product->unit_price, 2) }}
                                                </td>
                                                <td>{{ $product->status == 0 ? 'Open' : ($product->status == 1 ? 'Completed' : 'Closed') }}
                                                </td>
                                                @php $total += $product->unit_price * $product->quantity; @endphp
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: right">Total</th>
                                        <th style="text-align: right">&#8358;{{ number_format($total, 2) }}</th>
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
             $("#record1").DataTable({
                'iDisplayLength':100
            });
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
