@extends('layouts.backend.app')

@section('title', 'Cash Transfer Report')

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
            <a href="{{ route('deposits.create') }}" class="btn btn-sm btn-secondary"
            style="margin-left: 2px;"><span class="ion-social-euro"> </span> New Deposit</a>
            <a href="{{ route('withdraw.create') }}" class="btn btn-sm btn-secondary"
            style="margin-left: 2px;"><span class="ion-jet"> </span> New Withdraw</a>
            <a href="{{ route('cash_movements.create') }}" class="btn btn-sm btn-secondary"
            style="margin-left: 2px;"><span class="ion-model-s"> </span> New Withdraw & Deposit</a>
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Expense Items Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
                            <li class="breadcrumb-item active">Expense Report</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-8">
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
                                &nbsp;&nbsp;
                                <label for="item_id">Expense Name</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('item_id') ? ' is-invalid' : '' }}"
                                    name="item_id" id="item_id" required>
                                    <option value="all">All</option>
                                    @foreach ($items as $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}
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
                    <div class="col-sm-8 table-responsive" id="load">

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

            $('#generate').on("click", function() {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                item_id = $('#item_id').val();
                //type = $('input[name="type"]:checked').val()
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.expense.item.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        item_id: item_id
                    }
                }).done(function(data) {
                    
                    $("#load").html(data);
                    $('#example1').DataTable({
                        lengthMenu: [25, 50, 75, 100],
                        pageLength: 20
                    });
                });
            });
        });
    </script>
@endpush
