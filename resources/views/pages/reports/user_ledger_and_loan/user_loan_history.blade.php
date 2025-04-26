@extends('layouts.backend.app')

@section('title', 'User Activity Logs Report')

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
                        <h4>Loan Collection History Report</small></h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Loan History</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form method="POST">
                    <div class="row">
                        <div class="form-group  col-sm-2">
                            &nbsp;&nbsp;
                            <label for="user_id">Collectors</label>
                            <select
                                class="form-control select2-single {{ $errors->has('user_id') ? ' is-invalid' : '' }}"
                                name="user_id" id="user_id" required>
                                <option value="">Select...</option>
                                @foreach ($users as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group text-right  col-sm-2">
                            <input type="button" class="btn btn-primary" id="generate" name="generate" value="Generate" />
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-10 table-responsive" id="load">
                        <img src="{{ asset('assets/backend/img/loader.png') }}"
                            style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection

@push('js')
    <!-- Sweet Alert Js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.1/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#generate').on("click", function() {
                user_id = $('#user_id').val();
                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.user.loan.history.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_id: user_id
                    }
                }).done(function(data) {
                    $('#img-loader').hide();
                    $("#load").html(data);
                    loadDataTable2()
                });
            });
        });
    </script>
@endpush
