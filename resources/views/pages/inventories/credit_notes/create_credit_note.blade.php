@extends('layouts.backend.app')

@section('title', 'Customer')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
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
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.payments') }}">Supplier Payment</a>
                            </li>
                            <li class="breadcrumb-item active">Credit Note</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('suppliers.credit.note') }}">
                <span class="fa fa-list"> Credit Notes</span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6" id="load">

                    </div>
                    <div class="col-sm-6">
                        <div class="container mt-5">
                            <div class="card">
                                <div class="card-header">
                                    Previous Invoices
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Invoices</h5>
                                    <input type="text" id="search" class="form-control mt-3" placeholder="Search">
                                    <div id="table-container">
                                        <table class="table" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>Reference No</th>
                                                    <th>Customer</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">
                                                @foreach ($orders as $order)
                                                    <tr>
                                                        <td><a href="javascript:void(0)" class="invoice" onclick="load()"
                                                                data-val="{{ $order->invoice_no }}">{{ $order->invoice_no }}</a>
                                                        </td>
                                                        <td>{{ $order->customer->name }}</td>
                                                        <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}
                                                        </td>
                                                        <td style="text-align: right"><a href="javascript:void(0)"
                                                                onclick="load()" class="invoice"
                                                                data-val="{{ $order->invoice_no }}"><span
                                                                    class=""></span>Select</a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            $('#search').keyup(function() {
                var searchText = $(this).val();
                $.ajax({
                    url: "{{ route('load.order.invoices') }}",
                    method: 'GET',
                    data: {
                        search: searchText
                    },
                    success: function(response) {
                        if (response == null)
                            $('#table-body').html("");
                        $('#table-body').html(response);
                        
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
            var delay = (function() {
                var timer = 0;
                return function(callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();

            // function load() {
            $('.invoice').on('click', function() {
                var invoice_no = $(this).attr('data-val');
                $('#load').html("<h3>Please wait... while it is loading...</h3>");
                $.ajax({
                    url: "{{ route('load.order.cart') }}",
                    method: 'GET',
                    data: {
                        invoice_no: invoice_no
                    },
                    success: function(response) {

                        $('#load').html(response);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            // }
           

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
