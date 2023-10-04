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
                            <li class="breadcrumb-item active">Reciept Payment List</li>
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
                                <h3 class="card-title">Reciept Payment List</h3>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <a href="{{ route('receipt.payments') }}" class="btn btn-sm btn-secondary"
                                    style="margin-left: 2px;"><span class="fa fa-list"> </span> List</a>
                                    <a href="{{ route('create.payment.reciept') }}" class="btn btn-sm btn-secondary"
                                        style="margin-left: 2px;"><span class="fa fa-plus-circle"> </span> New Reciept
                                        Payment</a>
                                        <a href="{{ route('bank.ledger') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                                            class="ion-model-s"> </span> Bank Ledger</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('receipt.payment.search') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="search" class="form-control rounded" required placeholder="Search by Receipt number"
                                                name="refno" aria-label="Search" aria-describedby="search-addon" />
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
                                            <th>Receipt No</th>
                                            <th>Amount Paid</th>
                                            <th>Payment Mode</th>
                                            <th>Account Info</th>
                                            <th>Received BY</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Receipt No</th>
                                            <th>Amount Paid</th>
                                            <th>Payment Mode</th>
                                            <th>Account Info</th>
                                            <th>Received By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr>

                                                <td>{{ optional($payment->customer)->name }}</td>
                                                <td>{{ Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $payment->receipt_no }}</td>
                                                <td align="right">{{ number_format($payment->dr, 2, '.', ',') }}</td>
                                                <td>{{ $payment->payment_mode }}</td>
                                                <td>{{ optional($payment->bankAccount)->account_name }}({{ optional($payment->bankAccount)->account_no }})
                                                </td>
                                                <td>{{ optional($payment->user)->name }}</td>
                                                <td align="center">
                                                    <a href="{{ route('receipt.payment.print', $payment->id) }}"
                                                        target="_BLANK" class="btn btn-secondary btn-sm">
                                                        <i class="fa fa-print" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{{ route('receipt.payment.print.pos', $payment->id) }}"
                                                        target="_BLANK" class="btn btn-secondary btn-sm">
                                                        <i class="fa fa-print" aria-hidden="true">PoS</i>
                                                    </a>
                                                    <a href="javascript:void(0)" data-toggle="modal"
                                                        data-target="#payment_edit{{ $payment->id }}"
                                                        data-val="{{ $payment->id }}"
                                                        class="btn btn-primary btn-sm edit">
                                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                                    </a>
                                                    <button class="btn btn-danger btn-sm" type="button"
                                                        onclick="deleteItem({{ $payment->id }})">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $payment->id }}"
                                                        action="{{ route('receipt.payment.destroy', $payment->id) }}"
                                                        method="post" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="payment_edit{{ $payment->id }}"
                                                style="display: none;" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Payment from
                                                                {{ optional($payment->customer)->name }} | Invoice:
                                                                {{ $payment->receipt_no }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form
                                                                action="{{ route('receipt.payment.update', $payment->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="payment_id" id="payment_id"
                                                                    value="{{ $payment->id }}" />
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="amount_paid">Amount Paid</label>
                                                                            <input type="text"
                                                                                class="form-control {{ $errors->has('amount_paid') ? ' is-invalid' : '' }}"
                                                                                name="amount_paid" id="amount_paid"
                                                                                value="{{ old('amount_paid', $payment->dr) }}">
                                                                            @if ($errors->has('amount_paid'))
                                                                                <div class="invalid-feedback">
                                                                                    <strong>{{ $errors->first('amount_paid') }}</strong>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="payment_date">Payment Date</label>
                                                                            <input type="text"
                                                                                class="form-control datepicker {{ $errors->has('payment_date') ? ' is-invalid' : '' }}"
                                                                                name="payment_date" id="payment_date"
                                                                                value="{{ old('payment_date', $payment->date) == '' ? date('Y-m-d') : old('payment_date', $payment->date) }}"
                                                                                required="required">
                                                                            @if ($errors->has('payment_date'))
                                                                                <div class="invalid-feedback">
                                                                                    <strong>{{ $errors->first('payment_date') }}</strong>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="payment_mode">Payment Mode</label>
                                                                            <select
                                                                                class="form-control {{ $errors->has('payment_mode') ? ' is-invalid' : '' }}"
                                                                                name="payment_mode" id="payment_mode"
                                                                                required="required">
                                                                                <option value="">Select...</option>
                                                                                <option value="Cash"
                                                                                    {{ 'Cash' == $payment->payment_mode ? 'selected' : '' }}>
                                                                                    Cash</option>
                                                                                <option value="Cheque"
                                                                                    {{ 'Cheque' == $payment->payment_mode ? 'selected' : '' }}>
                                                                                    Cheque</option>
                                                                            </select>
                                                                            @if ($errors->has('payment_mode'))
                                                                                <div class="invalid-feedback">
                                                                                    <strong>{{ $errors->first('payment_mode') }}</strong>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="bank_account_id">Account
                                                                                Number</label>
                                                                            <select
                                                                                class="form-control select2-single {{ $errors->has('bank_account_id') ? ' is-invalid' : '' }}"
                                                                                name="bank_account_id"
                                                                                id="bank_account_id" required="required">
                                                                                <option value="">Select...</option>
                                                                                @foreach ($accounts as $account)
                                                                                    <option value="{{ $account->id }}"
                                                                                        {{ $account->id == $payment->bank_account_id ? 'selected' : '' }}>
                                                                                        {{ $account->account_no }}-{{ $account->account_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            @if ($errors->has('bank_account_id'))
                                                                                <div class="invalid-feedback">
                                                                                    <strong>{{ $errors->first('bank_account_id') }}</strong>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="teller_no">Receipt No</label>
                                                                            <input type="text" class="form-control"
                                                                                name="teller_no" id="teller_no"
                                                                                value="{{ old('teller_no', $payment->receipt_no) }}">
                                                                            @if ($errors->has('teller_no'))
                                                                                <div class="invalid-feedback">
                                                                                    <strong>{{ $errors->first('teller_no') }}</strong>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="offset-10">
                                                                        <button type="submut" class="btn btn-success"><i
                                                                                class="fa fa-save"></i>
                                                                            Save
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-dark"
                                                                    data-dismiss="modal"><i class="fa fa-times"></i>
                                                                    Close
                                                                </button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach
                                    </tbody>

                                </table>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <a href="javascript:void(0)" data-toggle="modal"
                                            data-target="#customer_ledgerform" class="btn btn-sm btn-secondary"
                                            style="margin-left: 2px;">Customer Ledger </a>
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
