@extends('layouts.backend.app')

@section('title', 'Relation Officer Report')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Relation Officer & Customer Report</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Relation Officer Report</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input type="date" class="form-control" name="from_date" id="from_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="to_date">To Date</label>
                                <input type="date" class="form-control" name="to_date" id="to_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="branch_id">Branch</label>
                                <select class="form-control select2-single ajax-branches" name="branch_id" id="branch_id"
                                    required>
                                    <option value="">Select Branch</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-primary" id="generate">Generate Report</button>
                        </div>
                    </div>
                </form>

                <div class="row mt-3">
                    <div class="col-sm-12 table-responsive" id="load">
                        <img src="{{ asset('assets/backend/img/loader.png') }}"
                            style="width:80px;height:80px;display:none;text-align:center" id="img-loader">
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
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let branch_id = $('#branch_id').val();

                if (!from_date || !to_date) {
                    alert('Please fill all dates fields.');
                    return;
                }

                $('#img-loader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.ro.customer.report') }}",
                    data: {
                        from_date: from_date,
                        to_date: to_date,
                        branch_id: branch_id
                    },
                    success: function(data) {
                        $("#load").html(data);
                        $('#img-loader').hide();
                        loadDataTable();
                    }
                });
            });
        });
    </script>
    {{-- <script type="text/javascript">
        $(document).ready(function() {
            // Initialize DataTable, but exclude the `non-datatable-rows` tbody
            let table = $('#ro-report').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "columnDefs": [{
                        "orderable": false,
                        "targets": 0
                    } // Disable sorting on expand column
                ],
                "drawCallback": function(settings) {
                    // Reinitialize expand/collapse functionality after DataTable redraws
                    $('#ro-report tbody').on('click', 'tr.clickable', function() {
                        let row = $(this).next('tr');
                        let icon = $(this).find(".toggle-icon");

                        if (row.hasClass('show')) {
                            row.removeClass('show');
                            icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                        } else {
                            row.addClass('show');
                            icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                        }
                    });
                }
            });
        });
    </script> --}}
@endpush

