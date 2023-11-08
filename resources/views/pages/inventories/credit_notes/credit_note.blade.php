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
                            <li class="breadcrumb-item active">Credit Note List</li>
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
                                <h3 class="card-title">Credit Note List</h3>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <a href="{{ route('credit.note.create') }}" class="btn btn-sm btn-secondary"
                                        style="margin-left: 2px;"><span class="fa fa-plus-circle"> </span> New Credit
                                        Note</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('customers.credit.note.search') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="search" class="form-control rounded" required
                                                placeholder="Search by Receipt or Cheque number" name="refno"
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
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Reference</th>
                                            <th>Amount</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr>

                                                <td>{{ Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                                <td>{{ $payment->reference }}</td>
                                                <td>{{ $payment->customer->name ?? '' }}</td>

                                                <td align="right">{{ number_format($payment->amount, 2, '.', ',') }}</td>
                                                <td>{{ $payment->createdBy->name ?? '' }}</td>
                                                <td align="center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                                id="dropdownMenuButton" data-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="{{ route('credit.note.show', $payment->id) }}"
                                                               class="dropdown-item">
                                                                <i class="fa fa-eye" aria-hidden="true"></i> View
                                                            </a>
                                                            <a href="{{ route('credit.note.print', $payment->id) }}"
                                                               target="_BLANK" class="dropdown-item">
                                                                <i class="fa fa-print" aria-hidden="true"></i> Print
                                                            </a>
                                                            @if ($payment->status == 0)
                                                                <a href="{{ route('credit.note.edit', $payment->id) }}"
                                                                   class="dropdown-item">
                                                                    <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                                                </a>

                                                                <form action="{{ route('credit.note.post', $payment->id) }}" method="post" onsubmit="return confirm('Are you sure you want to post this order?')">
                                                                    @csrf
                                                                    <button type="submit"
                                                                            class="dropdown-item">
                                                                        <i class="fa fa-close" aria-hidden="true"></i> Post
                                                                    </button>
                                                                </form>


                                                                <form id="delete-form-{{ $payment->id }}" action="{{ route('credit.note.post', $payment->id) }}" method="post" onsubmit="return confirm('Are you sure you want to close this order?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="dropdown-item" type="submit">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                                    </button>
                                                                </form>

                                                            @endif

                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                            <div class="modal fade" id="payment_edit{{ $payment->id }}"
                                                style="display: none;" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Credit note to
                                                                {{ $payment->customer->name ?? '' }} | Cheque No:
                                                                {{ $payment->Ref }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form
                                                                action="{{ route('customers.credit.note.update', $payment->id) }}"
                                                                method="POST" target="_BLANK">
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
                                                                            <label for="teller_no">Teller No</label>
                                                                            <input type="text" class="form-control"
                                                                                name="teller_no" id="teller_no"
                                                                                value="{{ old('teller_no', $payment->teller_no) }}">
                                                                            @if ($errors->has('teller_no'))
                                                                                <div class="invalid-feedback">
                                                                                    <strong>{{ $errors->first('teller_no') }}</strong>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="cheque_no">Cheque No</label>
                                                                            <input type="text" class="form-control"
                                                                                name="cheque_no" id="cheque_no" readonly
                                                                                value="{{ old('cheque_no', $payment->Ref) }}">
                                                                            @if ($errors->has('cheque_no'))
                                                                                <div class="invalid-feedback">
                                                                                    <strong>{{ $errors->first('cheque_no') }}</strong>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="group_id">Group Name</label>
                                                                            <select
                                                                                class="form-control select2-single {{ $errors->has('group_id') ? ' is-invalid' : '' }}"
                                                                                name="group_id" id="group_id"
                                                                                required="required">
                                                                                <option value="">Select...</option>
                                                                                @if (isset($categories))
                                                                                    @foreach ($categories as $data)
                                                                                        <option
                                                                                            value="{{ $data->id }}"
                                                                                            {{ $data->id == old('group_id', $payment->bank_account_id) ? 'selected' : '' }}>
                                                                                            {{ $data->name }}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                            @if ($errors->has('group_id'))
                                                                                <div class="invalid-feedback">
                                                                                    <strong>{{ $errors->first('group_id') }}</strong>
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

            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
            $(document).on('click', '".show"',function() {
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
@endpush
