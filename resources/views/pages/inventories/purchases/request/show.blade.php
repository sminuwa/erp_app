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
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
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
                    <div class="col-sm-6">
                        @include('cards.purchase_request')
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-header">
                                Purchased Products 
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered" id="record1">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Product</th>
                                            <th>QTY</th>
                                            <th>Unit Price (&#8358;)</th>
                                            <th>Subtotal (&#8358;)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach ($record->purchasedProducts()->get() as $product)
                                            <tr>
                                                <th>{{ $loop->index + 1 }}</th>
                                                <td>{{ $product->product->name }}</td>
                                                <td>{{ number_format($product->qty_supplied, 0, '', ',') }}</td>
                                                <td style="text-align: right">{{ number_format($product->unit_price, 2) }}
                                                </td>
                                                <td style="text-align: right">
                                                    {{ number_format($product->qty_supplied * $product->unit_price, 2) }}
                                                </td>
                                                <td>{{ $product->status == 1 ? 'Completed' : 'Cancelled' }}</td>
                                                @php $total += $product->unit_price * $product->qty_supplied; @endphp
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
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
                <div class="row">
                    <div class="col-md-6" id="expenses">
                        @if ($record->expenses()->count() > 0)

                            <table class="table table-bordered" id="record1">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total_expense = 0; @endphp
                                    @foreach ($record->expenses()->get() as $expense)
                                        <tr>
                                            @php $total_expense +=$expense->amount; @endphp
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $expense->name }}</td>
                                            <td style="text-align: right;"> {{ number_format($expense->amount, 2) }}</td>
                                            <td style="text-align: right;">
                                                @if ($record->status == 0)
                                                    <button class="btn btn-danger btn-sm" type="button"
                                                        onclick="deleteItem({{ $expense->id }})">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $expense->id }}"
                                                        action="{{ route('delete.purchase.request.expense', $expense->id) }}" method="post"
                                                        style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                <tfoot>
                                    <tr>
                                        <td>Total:</td>
                                        <td colspan="2" style="text-align: right;">
                                            {{ number_format($total_expense, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                </tbody>
                            </table>

                        @endif
                    </div>
                    <div class="col-md-6">
                        @if ($record->status == 0)
                            <button class="btn btn-primary btn-sm text-right right" data-toggle="modal" id="add-product-btn"
                                data-target="#add-product-modal"><span class="fa fa-plus-circle"></span> Expense</button>
                        @endif
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <div class="modal fade" id="add-product-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Expense</h5>
                    <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="create-form" action="{{ route('purchases.request.expense.ajax.create') }}" method="POST">
                        <input type="hidden" name="purchase_id" id="purchase_id" value="{{ $record->id }}" />
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input name="name" id="name" class="form-control" required />
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="number" class="form-control" name="amount" id="amount"
                                        placeholder="Amount" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right ">
                            <button type="submit" class="btn btn-primary"><span class="fa fa-plus-circle">
                                </span>Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
