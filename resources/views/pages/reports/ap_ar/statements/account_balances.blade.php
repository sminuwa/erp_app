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
                        <h4>Account Balances/Statements</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Account Balances/Statements</li>
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
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="type">Payee Category</label>
                                        <select
                                            class="form-control {{ $errors->has('type') ? ' is-invalid' : '' }}"
                                            name="type" id="type" required="required">
                                            <option value="all">All</option>
                                            <option
                                                value="Customer" {{ 'Customer' == $model->model_name ? 'selected' : '' }}>
                                                Customer
                                            </option>
                                            <option
                                                value="Supplier" {{ 'Supplier' == $model->model_name ? 'selected' : '' }}>
                                                Supplier
                                            <option value="GeneralAccount"
                                                {{ 'GeneralAccount' == $model->model_name ? 'selected' : '' }}>General
                                                Accounts
                                            </option>
                                        </select>
                                        @if ($errors->has('type'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('type') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        @if (isset($model) && $model->model_name == 'Customer')
                                                <?php $payers = \App\Models\Customer::orderBy('code', 'asc')->get(); ?>
                                        @elseif(isset($model) && $model->model_name == 'Supplier')
                                                <?php $payers = \App\Models\Supplier::orderBy('code', 'asc')->get(); ?>
                                        @else
                                                <?php $payers = \App\Models\GeneralAccount::orderBy('number', 'asc')->get(); ?>
                                        @endif
                                        <label for="payer_id">Payee</label>
                                        <select
                                            class="form-control select2-single {{ $errors->has('payer_id') ? ' is-invalid' : '' }}"
                                            name="payer_id" id="payer_id" required="required">
                                            <option value="all">All</option>
                                            @if (isset($payers))
                                                @foreach ($payers as $payer)
                                                    <option value="{{ $payer->id }}"
                                                        {{ $payer->id == $model->model_id ? 'selected' : '' }}>
                                                        {{ $payer->code ?? $payer->number }} -
                                                        {{ $payer->name ?? $payer->description }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('payer_id'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('payer_id') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="text" autocomplete="off" name="from_date" id="from_date"
                                               class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                               value="{{ old('from_date') }}" placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="text" autocomplete="off" name="to_date" id="to_date" placeholder=""
                                               class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                               value="{{ old('to_date') }}" required/>
                                    </div>

                                </div>
                            </div>
                            
                            <div class="text-right form-group col-sm-12">
                                <input type="button" class="btn btn-primary" id="generate" name="generate"
                                       value="Generate"/>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 table-responsive" id="load"></div>
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
  
    
    <script type="text/javascript">
        $(function () {
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


            $('#type').on("change", function () {
                $("#payer_id").html(" < option value = '' > Loading... < /option>");
               
                $.ajax({
                    url: "{{ route('ajax.load.payers') }}",
                    type: 'GET',
                    data: {
                        type: $(this).val()
                    }
                }).done(function (msg) {
                   
                    $("#payer_id").html(msg);
                    
                });
            });

            $('#generate').on("click", function () {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                payer_id = $('#payer_id').val();
                type = $('#type').val();
                
                
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.account.balance.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        payer_id: payer_id,
                        type: type
                    }
                }).done(function (data) {
                   
                    $("#load").html(data);
                    $('#example1').DataTable({
                        lengthMenu: [25, 50, 75, 100],
                        pageLength: 20,
                        
                    });
                });
            });
        });
    </script>
@endpush
