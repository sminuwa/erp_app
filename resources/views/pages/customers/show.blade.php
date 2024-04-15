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
                        <h4>Customer Ledger</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Customer Ledger</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="offset-6">
                        <form method="POST" class="form-inline">
                            <input type="hidden" name="customer_id" id="customer_id" value="{{ $record->id }}" />
                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input type="text"
                                    class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                    autocomplete="off" name="from_date" id="from_date" value="{{ old('from_date') }}"
                                    placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="to_date">To Date</label>
                                <input type="text"
                                    class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                    autocomplete="off" name="to_date" id="to_date" value="{{ old('to_date') }}"
                                    placeholder="">
                            </div>
                            <div class="form-group text-right ">
                                <input type="button" class="btn btn-primary" id="generate" name="generate"
                                    value="Generate" />
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        @include('cards.customer')
                    </div>

                    <div class="col-sm-8 table-responsive" id="load">
                        <table class="table table-bordered" id="example1" data-ordering="false" border="1"
                            cellpadding="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Ref</th>
                                    <th>Dr</th>
                                    <th>Cr</th>
                                    <th>Running Balance</th>
                                </tr>
                            </thead>
                            <?php $sum_cr = 0;
                            $sum_dr = 0;
                            $dif = 0; ?>
                            @foreach ($record->ledgers()->orderBy('date')->get() as $ledger)
                                <tr>
                                    <td>{{ $ledger->date->toFormattedDateString() }}</td>
                                    <td>{{ $ledger->description }}</td>
                                    <td>{{ $ledger->ref }}</td>
                                    <td style="text-align: right"> {{ number_format($ledger->dr, 2) }}</td>
                                    <td style="text-align: right">{{ number_format($ledger->cr, 2) }}</td>
                                    <td style="text-align: right">
                                        <?php $sum_cr += $ledger->cr;
                                        $sum_dr += $ledger->dr;
                                        $dif = $sum_cr - $sum_dr;
                                        $balance =
                                            $ledger
                                                ->where('id', '<=', $ledger->id)
                                                ->where('customer_id', $record->id)
                                                ->sum('cr') -
                                            $ledger
                                                ->where('id', '<=', $ledger->id)
                                                ->where('customer_id', $record->id)
                                                ->sum('dr'); ?>
                                        @if ($balance < 0)
                                            {{ number_format(abs($dif), 2) }}Dr.
                                        @else
                                            {{ number_format($dif, 2) }}Cr.
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tfoot>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Ref</th>
                                    <th style="text-align: right;">&#8358;{{ number_format($sum_cr, 2) }}</th>
                                    <th style="text-align: right;">&#8358;{{ number_format($sum_dr, 2) }}</th>
                                    <th style="text-align: right">
                                        @if ($dif < 0)
                                            &#8358;{{ number_format(abs($dif), 2) }}Dr.
                                        @else
                                            &#8358;{{ number_format($dif, 2) }}Cr.
                                        @endif
                                    </th>

                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <h5 style="text-align: center;">{{ strtoupper($record->name) }} Closing Running
                                            Balance: = @if ($dif < 0)
                                                &#8358;{{ number_format(abs($dif), 2) }}Dr.
                                            @else
                                                &#8358;{{ number_format($dif, 2) }}Cr.
                                            @endif
                                        </h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
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
            $('#example1').DataTable({
                lengthMenu: [25, 50, 75, 100],
                pageLength: 100
            });

            $('#generate').on("click", function() {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                customer_id = $('#customer_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.customer.ledger') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        customer_id: customer_id
                    }
                }).done(function(data) {
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
                                extend: 'pdfHtml5',
                                orientation: 'landscape',
                                pageSize: 'LEGAL',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'print',
                                messageTop: 'Customer Ledger',
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

                            // Total over all pages

                            if (api.column(4).data().length) {
                                var total = api
                                    .column(4)
                                    .data()
                                    .reduce(function(a, b) {
                                        return intVal(a) + intVal(b);
                                    })
                            } else {
                                total = 0
                            };

                            // Total over this page

                            if (api.column(4).data().length) {
                                var pageTotal = api
                                    .column(4, {
                                        page: 'current'
                                    })
                                    .data()
                                    .reduce(function(a, b) {
                                        return intVal(a) + intVal(b);
                                    })
                            } else {
                                pageTotal = 0
                            };

                            // Update footer
                            $(api.column(4).footer()).html(
                                "Page Total: " + formatMoney(pageTotal) +
                                "<br> (Grand Total: " +
                                formatMoney(total) + ")"
                            );


                            //Another Column


                            // Total over all pages

                            if (api.column(5).data().length) {
                                var total = api
                                    .column(5)
                                    .data()
                                    .reduce(function(a, b) {
                                        return intVal(a) + intVal(b);
                                    })
                            } else {
                                total = 0
                            };

                            // Total over this page

                            if (api.column(5).data().length) {
                                var pageTotal = api
                                    .column(5, {
                                        page: 'current'
                                    })
                                    .data()
                                    .reduce(function(a, b) {
                                        return intVal(a) + intVal(b);
                                    })
                            } else {
                                pageTotal = 0
                            };

                            // Update footer
                            $(api.column(5).footer()).html(
                                "Page Total: " + formatMoney(pageTotal) +
                                "<br> (Grand Total: " +
                                formatMoney(total) + ")"
                            );


                        }
                    });
                });
            });

        });
    </script>
@endpush
