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
                        <h4>General Customer Ledger</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                            <li class="breadcrumb-item active">General Ledger</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <form method="POST" class="form-inline">
                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input type="text"
                                    class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}" autocomplete="off"
                                    name="from_date" id="from_date" value="{{ old('from_date') }}" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="to_date">To Date</label>
                                <input type="text" class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}" autocomplete="off"
                                    name="to_date" id="to_date" value="{{ old('to_date') }}" placeholder="">
                            </div>
                            <div class="form-group">
                                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                <label for="category">Customer Category</label> &nbsp;&nbsp; &nbsp;&nbsp;
                                <input type="radio" name="type" value="Credit" id="type1" class="form-control type" />
                                &nbsp;&nbsp;Credit &nbsp;&nbsp; &nbsp;&nbsp;
                                <input type="radio" name="type" value="Walked In" id="type2" class="form-control type" />
                                &nbsp;&nbsp;Walked In
                            </div>

                            <div class="form-group">
                                &nbsp;&nbsp;
                                <label for="customer_id">Customer</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('customer_id') ? ' is-invalid' : '' }}"
                                    name="customer_id" id="customer_id" required>
                                    {{--<option value="all">All</option>--}}
                                    <option value="">Select...</option>
                                    @foreach ($customers as $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}-{{ $data->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group text-right ">
                                <input type="button" class="btn btn-primary" id="generate" name="generate"
                                    value="Generate" />
                            </div>

                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 table-responsive" id="load">

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

    <!-- Sweet Alert Js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.1/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
        $(function() {
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


            $('.type').on("click", function() {
                type = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customers') }}",
                    data: {
                        type: type
                    }
                }).done(function(data) {
                    $("#customer_id").html("<option value=''>Select...</option>" + data);
                });
            });

            $('#generate').on("click", function() {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                customer_id = $('#customer_id').val();
                type = $('input[name="type"]:checked').val()
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.general.customer.ledger') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        customer_id: customer_id,
                        type: type
                    }
                }).done(function(data) {

                    $("#load").html(data);
                    loadDataTable2()
                });
            });
        });
    </script>
@endpush
