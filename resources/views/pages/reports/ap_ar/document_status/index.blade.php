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
                        <h4>Invoice/Receipt/Payment/Interbank & Journal list</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Status</li>
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
                        <form id="statusForm" method="POST">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        &nbsp;&nbsp;
                                        <label for="branch_id">Branch</label>
                                        <select
                                            class="form-control select2-single ajax-branches {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                            name="branch_id" id="branch_id">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        &nbsp;&nbsp;
                                        <label for="status">Status</label>
                                        <select
                                            class="form-control select2-single ajax-users {{ $errors->has('status') ? ' is-invalid' : '' }}"
                                            name="status" id="status">
                                            <option value="">Select...</option>
                                            <option value="0">Pending</option>
                                            <option value="1">Posted</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="type">Type</label>
                                        <select class="form-control {{ $errors->has('type') ? ' is-invalid' : '' }}"
                                            name="type" id="type" required="required">
                                            <option value="">Select</option>
                                            <option value="Invoice">
                                                Invoice
                                            </option>
                                            <option value="Payment">
                                                Payment
                                            </option>
                                            <option value="Receipt">
                                                Receipt
                                            <option value="Interbank">Interbank
                                            </option>
                                            <option value="Journal">Journal</option>
                                        </select>
                                        @if ($errors->has('type'))
                                            <div class="invalid-feedback">
                                                <strong>{{ $errors->first('type') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                               
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="text" autocomplete="off" name="from_date" id="from_date"
                                            class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                            value="{{ old('from_date') }}" placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="text" autocomplete="off" name="to_date" id="to_date"
                                            placeholder=""
                                            class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                            value="{{ old('to_date') }}" required />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" id="generate" name="generate"
                                    value="Generate" />
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
    <script type="text/javascript">
        $(function() {
           
            $('#statusForm').on('submit', function(e) {
                e.preventDefault()
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                branch_id = $('#branch_id').val();
                type = $('#type').val();
                status = $('#status').val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.document.status.report') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        branch_id: branch_id,
                        type: type,
                        status: status
                    }
                }).done(function(data) {
                    // console.log(data)
                    $("#load").html(data);
                    $('#example1').DataTable({
                        dom: 'Bfrtip',
                        lengthMenu: [25, 50, 75, 100],
                        pageLength: 100,
                        buttons: [{
                                extend: 'copyHtml5',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'excelHtml5',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: ':visible'
                                },
                                messageTop: 'Sales Report',
                                orientation: 'landscape',
                                pageSize: 'LEGAL'
                            },
                            {
                                extend: 'colvis',
                                columns: ':not(.noVis)',
                                collectionLayout: 'fixed two-column',
                                postfixButtons: [{
                                    extend: 'colvisGroup',
                                    text: 'Show all',
                                    show: ':hidden'
                                }]
                            }
                        ],
                        language: {
                            buttons: {
                                colvis: 'Show/Hide columns'
                            }
                        },
                        //buttons: ['excel', 'pdf', 'print'],
                        "footerCallback": function(row, data, start, end, display) {
                            var api = this.api();
                            var json = api.ajax.json();
                            // Remove the formatting to get integer data for summation
                            var intVal = function(i) {
                                return typeof i === 'string' ?
                                    i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                            };

                        }
                    });
                });
            })
        });
    </script>
@endpush
