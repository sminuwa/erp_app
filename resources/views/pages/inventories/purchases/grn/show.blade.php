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
                        <h4>Purchase Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Purchase</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container">

                @if(session()->has('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                    <div class="btn-group">
                        <a class="btn btn-secondary btn-sm" href="{{ route('purchases.index', $record->id) }}">
                            <span class="fa fa-list"></span> List
                        </a>
                         @if ($record->status == 0)
                            <a class="btn btn-secondary btn-sm" href="{{ route('purchases.edit', $record->id) }}">
                                <span class="fa fa-pencil"></span> Edit
                            </a>
                            <form onsubmit="return confirm('Are you sure you want to approve?')"
                                  action="{{ route('purchase.approve', $record->id) }}" method="post" style="display: inline">
                                {{ csrf_field() }}
                                {{ method_field('POST') }}
                                <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                    <i class="text-white fa fa-check"></i> Post
                                </button>
                            </form>
                            <form onsubmit="return confirm('Are you sure you want to delete?')"
                                  action="{{ route('purchases.destroy', $record->id) }}" method="post" style="display: inline">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                    <i class="text-danger fa fa-remove"></i> Delete
                                </button>
                            </form>

                         @else

                         @endif
                        <a class="btn btn-secondary btn-sm" href="{{ route('purchase.print', $record->id) }}" target="_blank">
                            <span class="fa fa-print"></span> Print
                        </a>

                    </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-default">
                            <div class="card-header">
                                <h5>GRN No.:: {{ $record->reference }} ({{ $record->status === 0 ? 'Pending' : 'Posted' }})</h5>
                            </div>
                            <div class="card-block">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                    <tr>
                                        <th>Supplier</th>
                                        <td colspan="5">{{ optional($record->supplier)->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Purchase Date</th>
                                        <td>{{ optional($record->purchase_date)->toDayDateTimeString() }}</td>
                                        <th>ATC/WayBill No</th>
                                        <td >{{ $record->atc_no }}</td>
                                        <th>Truck No</th>
                                        <td>{{ $record->truck_no }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{--@include('cards.purchase')--}}
                    </div>
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                Purchased Products
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered" id="record1" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Code</th>
                                            <th>Product</th>
                                            <th>UTM</th>
                                            <th>QTY</th>
                                            <th>Price (&#8358;)</th>
                                            <th>Subtotal (&#8358;)</th>
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

                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="expenses">
                        @if ($record->expenses()->count() > 0)

                            <table class="table table-bordered" id="record1">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>General Account</th>
                                        <th>Description</th>
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
                                            <td>{{ $expense->supplier->name ?? '' }}</td>
                                            <td>{{ $expense->description }}</td>
                                            <td style="text-align: right;"> {{ number_format($expense->amount, 2) }}</td>
                                            <td style="text-align: right;">
                                                @if ($record->status == 0)
                                                    <button class="btn btn-danger btn-sm" type="button"
                                                        onclick="deleteItem({{ $expense->id }})">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $expense->id }}"
                                                        action="{{ route('delete.purchase.expense', $expense->id) }}"
                                                        method="post" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td colspan="2">Total</td>
                                        <td style="text-align: right;">
                                            {{ number_format($total_expense, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                </tbody>
                            </table>

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
                    <h5 class="modal-title">Additional Invoices</h5>
                    <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="create-form" action="{{ route('purchases.expense.ajax.create') }}" method="POST">
                        <input type="hidden" name="purchase_id" id="purchase_id" value="{{ $record->id }}" />
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Account</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control select2-single" required>
                                        <option value="">Select...</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">
                                                {{ $supplier->code }}{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" required></textarea>
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
                                </span> Add Invoice</button>
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
