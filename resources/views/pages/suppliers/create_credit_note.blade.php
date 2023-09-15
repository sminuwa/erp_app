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
                        <h4>Credit Note</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.payments') }}">Supplier Payment</a></li>
                            <li class="breadcrumb-item active">Credit Note</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('suppliers.credit.note') }}">
                <span class="fa fa-list"></span>
            </a>
            <a href="{{ route('suppliers.payments') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="fa fa-list"> </span> Supplier Payments</a>
            <a href="{{ route('bank.ledger') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                    class="ion-model-s"> </span> Bank Ledger</a>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#supplier_ledgerform"
                class="btn btn-sm btn-secondary float-md-right" style="margin-left: 2px;">Supplier Ledger </a>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 ">
                        @include('forms.credit_note_form')
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
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
    <script>
        $(function() {
            
            $('#supplier_id').on("change", function() {
                supplier_id = $('#supplier_id').val();
                //purchase_id = $('#purchase_id').val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.supplier.balance') }}",
                    data: {
                        supplier_id: supplier_id,
                        //purchase_id: purchase_id
                    }
                }).done(function(data) {
                    balance = 0;
                    if (data < 0)
                        balance = "(" + formatMoney(Math.abs(data)) + ")";
                    else
                        balance = formatMoney(data);
                    $("#balance").val(balance);
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
