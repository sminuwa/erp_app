@extends('layouts.backend.app')

@section('title', 'Users')

@push('css')
    <!-- DataTables -->
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
                        <h4>Manage Users</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Users</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('users.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('users.create') }}">
                    <span class="fa fa-plus-circle"> New User</span>
                </a>
            @endcan
            @can('users.import.form')
                <a class="btn btn-secondary btn-sm" href="{{ route('users.import.form') }}">
                    <span class="fa fa-upload"> Upload Users</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        @include('tables.user')
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Date Range Modal -->
    <div class="modal fade" id="dateRangeModal" tabindex="-1" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.updateUserDateRange') }}">
                @csrf
                <input type="hidden" name="user_id" id="modal_user_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Set Entry Date for <span id="modal_user_name"></span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_date_range_start" class="form-label">Start Date</label>
                            <input type="text" class="form-control datepicker" id="modal_date_range_start"
                                name="date_range_start">
                        </div>
                        <div class="mb-3">
                            <label for="modal_date_range_end" class="form-label">End Date</label>
                            <input type="text" class="form-control datepicker" id="modal_date_range_end"
                                name="date_range_end">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="reset_user_range"
                                name="reset_range">
                            <label class="form-check-label" for="reset_user_range">
                                Reset Date Range to No Limit
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Dates</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    {{--    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> --}}
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable({
                'iDisplayLength': 100
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.set-entry-date').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const userName = this.dataset.userName;
                    const dateStart = this.dataset.dateStart;
                    const dateEnd = this.dataset.dateEnd;

                    document.getElementById('modal_user_id').value = userId;
                    document.getElementById('modal_user_name').textContent = userName;
                    document.getElementById('modal_date_range_start').value = dateStart;
                    document.getElementById('modal_date_range_end').value = dateEnd;
                });
            });
        });
    </script>
@endpush
