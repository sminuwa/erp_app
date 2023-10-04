@extends('layouts.backend.app')

@section('title', 'Customer')

@push('css')
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
                        <h4>Payment Receipt </h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('receipt.payments') }}">Receipts</a></li>
                            <li class="breadcrumb-item active">New Receipt</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a href="{{ route('create.payment.reciept') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="fa fa-plus-circle"> </span> New
                Receipt</a>
            <a class="btn btn-secondary btn-sm" href="{{ route('receipt.payments') }}">
                <span class="fa fa-list"> Receipts</span>
            </a>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#customer_ledgerform"
                class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span class="fa fa-money"> General Ledger</span>
            </a>
            @if (Session::get('prev_id') != null)
                <a href="{{ route('receipt.payment.print', Session::get('prev_id')) }}" target="_BLANK"
                    class="btn btn-sm btn-primary" style="margin-left: 2px;"><span class="fa fa-print"> Print</span> </a>
                <a href="{{ route('receipt.payment.print.pos', Session::get('prev_id')) }}" target="_BLANK"
                    class="btn btn-secondary btn-sm">
                    <i class="fa fa-print" aria-hidden="true">PoS</i>
                </a>
            @endif
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 ">
                        @include('pages.receipts.receipt_payment_form')
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
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
                                @foreach (App\Models\Customer::where('type', 'credit')->where('branch_id', 'LIKE', App\Models\User::userBranchAction())->get() as $data)
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
    <script>
        $(function() {
            $('#type').on("change", function() {
                $("#payer_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.load.payers') }}",
                    type: 'GET',
                    data: {
                        type: $(this).val()
                    }
                }).done(function(msg) {
                    $("#payer_id").html(msg);
                });
            });

            function formatMoney(n, c, d, t) {
                var c = isNaN(c = Math.abs(c)) ? 2 : c,
                    d = d == undefined ? "." : d,
                    t = t == undefined ? "," : t,
                    s = n < 0 ? "-" : "",
                    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ?
                    d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };
        });
    </script>
@endpush
