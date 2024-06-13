@extends('layouts.backend.app')
@section('title', 'Opening Balance')

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
                        <h4>Supplier Opening Balance</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Supplier Balance</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('suppliers.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('suppliers.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <form action="{{ isset($route) ? $route : route('suppliers.opening_balance.store') }}"
                            method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
                            <div class="form-group">
                                <label for="supplier_id">Supplier Name</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('supplier_id') ? ' is-invalid' : '' }}"
                                    name="supplier_id" id="supplier_id" required="required">
                                    <option value="">Select...</option>
                                    @if (isset($suppliers))
                                        @foreach ($suppliers as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == optional($model)->supplier_id ? 'selected' : '' }}>
                                                {{ $data->code }}-{{ $data->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('supplier_id'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('supplier_id') }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="text"
                                    class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}" name="amount"
                                    id="amount" value="{{ old('amount', optional($model)->amount) }}" placeholder=""
                                    required="required">
                                @if ($errors->has('amount'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('amount') }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">

                                <input type="hidden" class="form-control " name="updated_by" id="updated_by"
                                    value="{{ Auth::id() }}" placeholder="" required="required">

                            </div>


                            <div class="form-group text-right ">

                                <input type="submit" class="btn btn-primary" value="Save" />

                            </div>
                        </form>
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
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
{{--    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>--}}
    <script type="text/javascript">
        $(function() {
            $(document).on("change", "#bank_id", function(event) {
                $("#bank_branch_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.loadbranches') }}",
                    type: 'GET',
                    data: {
                        bank_id: $("#bank_id").val()
                    }
                }).done(function(msg) {
                    $("#bank_branch_id").html("<option value=''>--select--</option>" + msg);
                });
            });
        });
    </script>
@endpush
