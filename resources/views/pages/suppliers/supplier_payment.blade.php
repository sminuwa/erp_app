@extends('layouts.backend.app')

@section('title', 'Approved Orders')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 offset-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Supplier Payment List</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Supplier Payment List</h3>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('suppliers.payment.search') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="search" class="form-control rounded" required
                                                placeholder="Search by Teller or Reference number" name="refno"
                                                aria-label="Search" aria-describedby="search-addon" />
                                            <button type="submit" class="btn btn-outline-primary">search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1"
                                    class="table table-bordered table-striped text-left table-responsive-xl">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Teller No</th>
                                            <th>Ref No</th>
                                            <th>Amount Paid</th>
                                            <th>Payment Mode</th>
                                            <th>Account/Group</th>
                                            <th>Received BY</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Teller No</th>
                                            <th>Ref No</th>
                                            <th>Amount Paid</th>
                                            <th>Payment Mode</th>
                                            <th>Account/Group</th>
                                            <th>Received By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr>

                                                <td>{{ $payment->supplier->name }}</td>
                                                <td>{{ Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $payment->teller_no }}</td>
                                                <td>{{ $payment->Ref }}</td>
                                                <td align="right">{{ number_format($payment->dr, 2, '.', ',') }}</td>
                                                <td>{{ $payment->payment_mode }}</td>
                                                <td>{{ $payment->payment_mode == 'Credit Note' ? \App\Models\Category::find($payment->bank_account_id)->name : optional($payment->bankAccount)->account_name . '(' . optional($payment->bankAccount)->account_no . ')' }}
                                                </td>
                                                <td>{{ optional($payment->user)->name }}</td>
                                                <td align="center">
                                                    @can('supplier.payment.print')
                                                        <a href="{{ route('supplier.payment.print', $payment->id) }}"
                                                            target="_BLANK" class="btn btn-secondary btn-sm">
                                                            <i class="fa fa-print" aria-hidden="true"></i>
                                                        </a>
                                                    @endcan
                                                    @can('supplier.payment.print.pos')
                                                        <a href="{{ route('supplier.payment.print.pos', $payment->id) }}"
                                                            target="_BLANK" class="btn btn-secondary btn-sm">
                                                            <i class="fa fa-print" aria-hidden="true">Pos</i>
                                                        </a>
                                                    @endcan
                                                    @can('suppliers.payment.destroy')
                                                        <button class="btn btn-danger btn-sm" type="button"
                                                            onclick="deleteItem({{ $payment->id }})">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                    @endcan
                                                    <form id="delete-form-{{ $payment->id }}"
                                                        action="{{ route('suppliers.payment.destroy', $payment->id) }}"
                                                        method="post" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#supplier_ledgerform"
                                            class="btn btn-sm btn-secondary" style="margin-left: 2px;">Supplier Ledger </a>
                                    </div>
                                    <div class="col-sm-2 text-danger">
                                        <strong>Total Record is of 1 :
                                            {{ number_format(App\Models\SupplierLedger::where('dr', '>', 0)->count('*'), 0, ',', '') }}</strong>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                        </div>
                        <!-- /.card -->

                    </div>
                    <!--/.col (left) -->

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div> <!-- Content Wrapper end -->
    <div class="modal fade" id="supplier_ledgerform" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supplier Ledger</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="get" action="{{ route('ajax.general.supplier.ledger') }}" id="ledger_form"
                        target="_BLANK">
                        @csrf
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="text" class="form-control datepicker-entry" name="from_date" id="from_date"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="text" class="form-control datepicker-entry" name="to_date" id="to_date"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            &nbsp;&nbsp;
                            <label for="supplier_id">Supplier</label>
                            <select class="form-control select2-single" name="supplier_id" id="supplier_id" required>
                                {{-- <option value="all">All</option> --}}
                                <option value="">Select...</option>
                                @foreach (App\Models\Supplier::orderBy('name')->get() as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}-{{ $data->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="print" value="print" />
                        <input type="hidden" name="modal" value="modal" />
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                                Close
                            </button>
                            <button type="submit" class="btn btn-info px-3"><i class="icon-trash"></i> Generate
                            </button>
                        </div>
                        @method('post')
                    </form>
                </div>
            </div>
        </div>
    </div>

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


    <script>
        $(function() {

            $("#example1").DataTable({
                'iDisplayLength': 100
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
            $(".show").on('click', function() {
                order_id = $(this).attr('data-val');
                $.ajax({
                    type: 'get',
                    url: "{{ route('orders.load') }}",
                    data: {
                        order_id: order_id
                    }
                }).done(function(data) {
                    $('.display').html();
                    $('.display').html(data);
                });
            });
        });
    </script>


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
