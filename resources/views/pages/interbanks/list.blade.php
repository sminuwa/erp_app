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
                            <li class="breadcrumb-item active">Payment List</li>
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
                                <h3 class="card-title">Inter Banks Transfer</h3>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <a href="{{ route('interbank.list') }}" class="btn btn-sm btn-secondary"
                                        style="margin-left: 2px;"><span class="fa fa-list"> </span> List</a>
                                    <a href="{{ route('create.interbank') }}" class="btn btn-sm btn-secondary"
                                        style="margin-left: 2px;"><span class="fa fa-plus-circle"> </span> New
                                        Transfer</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('payment.search') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="search" class="form-control rounded" required
                                                placeholder="Search by Receipt number" name="refno" aria-label="Search"
                                                aria-describedby="search-addon" />
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
                                            <th>Date</th>
                                            <th>Receipt No</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Source</th>
                                            <th>Destination</th>
                                            <th>Transfer By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Date</th>
                                            <th>Receipt No</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Source</th>
                                            <th>Destination</th>
                                            <th>Transfer By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr>

                                                <td>{{ Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $payment->receipt_no }}</td>
                                                <td align="right">{{ number_format($payment->amount, 2, '.', ',') }}</td>
                                                <td>{{ $payment->description }}</td>
                                                <td>{{ $payment->source->description }}</td>
                                                <td>{{ $payment->destination->description }}</td>
                                                <td>{{ optional($payment->transfered_by)->name }}</td>
                                                <td align="center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="{{ route('receipt.payment.print', $payment->id) }}"
                                                               class="dropdown-item">
                                                                <i class="fa fa-print" aria-hidden="true"></i> Print
                                                            </a>
                                                            <a href="{{ route('receipt.payment.print.pos', $payment->id) }}"
                                                               class="dropdown-item">
                                                                <i class="fa fa-print" aria-hidden="true"></i> PoS
                                                            </a>
                                                            @if($payment->status == 0)
                                                                <a href="{{ route('create.payment.reciept', ['receipt_id'=>$payment->id]) }}"
                                                                   class="dropdown-item">
                                                                    <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                                                </a>
                                                                <a href="{{ route('create.payment.reciept', ['receipt_id'=>$payment->id]) }}"
                                                                   class="dropdown-item">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                                </a>
                                                            @else
                                                                <a href="{{ route('receipt.payment.reverse',[$payment->id]) }}"
                                                                   class="dropdown-item">
                                                                    <i class="fa fa-reply" aria-hidden="true"></i> Reverse
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                        @endforeach
                                    </tbody>

                                </table>
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
    <div class="modal fade" id="customer_ledgerform" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Ledger</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="get" action="{{ route('ajax.general.customer.ledger') }}" id="ledger_form"
                        target="_BLANK">
                        @csrf
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="text" class="form-control datepicker" name="from_date" id="from_date"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="text" class="form-control datepicker" name="to_date" id="to_date"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="form-group">
                            &nbsp;&nbsp;
                            <label for="customer_id">Customer</label>
                            <select class="form-control select2-single" name="customer_id" id="customer_id" required>
                                {{-- <option value="all">All</option> --}}
                                <option value="">Select...</option>
                                @foreach (App\Models\Customer::where('type', 'credit')->get() as $data)
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
                pageLength: 10,
                "ordering": false
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": true,
                "autoWidth": false
            });
            /*$(".show").on('click', function() {
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
            });*/
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
