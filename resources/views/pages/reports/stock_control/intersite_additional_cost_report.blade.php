@extends('layouts.backend.app')

@section('title', 'Report')

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
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Intersite Additional Cost Reports</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Intersite Additional Cost Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form method="POST">
                    <div class="row mb-2">
                        <div class="col-sm-3">
                            <label for="from_date">From Date</label>
                            <input type="text" autocomplete="off"
                                class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                name="from_date" id="from_date" value="{{ old('from_date') }}" placeholder="">
                        </div>
                        <div class="col-sm-3">
                            <label for="to_date">To Date</label>
                            <input type="text" autocomplete="off"
                                class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                name="to_date" id="to_date" value="{{ old('to_date') }}" placeholder="">
                        </div>
                        <div class="col-sm-3">
                            <label for="supplier_id">Supplier</label>
                            <select class="form-control select2-single {{ $errors->has('supplier_id') ? ' is-invalid' : '' }}"
                                name="supplier_id" id="supplier_id">
                                <option value="all">All</option>
                                @foreach ($suppliers as $data)
                                    <option value="{{ $data->id }}">{{ $data->code }} - {{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label for="status">Status</label>
                            <select class="form-control {{ $errors->has('status') ? ' is-invalid' : '' }}"
                                name="status" id="status">
                                <option value="all">All</option>
                                <option value="pending">Pending</option>
                                <option value="posted">Posted</option>
                                <option value="reversed">Reversed</option>
                            </select>
                        </div>
                        <div class="col-sm-1 d-flex align-items-end">
                            <input type="button" class="btn btn-primary" id="generate" name="generate"
                                value="Generate" />
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-12 table-responsive" id="load">

                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('js')
    <script type="text/javascript">
        $(function() {
            $('#generate').on("click", function() {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                supplier_id = $('#supplier_id').val();
                status = $('#status').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.intersite.additional_cost.reports') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        supplier_id: supplier_id,
                        status: status
                    }
                }).done(function(data) {
                    $("#load").html(data);
                    loadDataTable2()
                });
            });
        });
    </script>
@endpush
