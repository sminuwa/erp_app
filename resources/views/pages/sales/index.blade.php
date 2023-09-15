@extends('layouts.backend.app')

@section('title', 'Total Sales')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 offset-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Total Sales</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    TOTAL SALES REPORT
                                    <small class="text-danger pull-right">
                                        <span class="badge badge-info">Total Sales :
                                            {{ number_format($balance->sum('total'), 2, '.', ',') }} </span>
                                        <span class="badge badge-success">Paid :
                                            {{ number_format($balance->sum('pay'), 2, '.', ',') }} </span>
                                        <span class="badge badge-warning">Due :
                                            {{ number_format($balance->sum('due'), 2, '.', ',') }} </span>
                                    </small>
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped text-left">
                                    <thead>
                                        <tr>
                                            <th>Serial</th>
                                            <th>Product</th>
                                            <th>Store</th>
                                            <th>Customer Name</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Serial</th>
                                            <th>Product</th>
                                            <th>Store</th>
                                            <th>Customer Name</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Time</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $order->product_name }}</td>
                                                <td>{{ $order->store }}</td>
                                                <td>{{ $order->customer_name }}({{$order->invoice_no}})</td>
                                                <td align="center">{{ $order->quantity }}</td>
                                                <td align="right">{{ number_format($order->total, 2) }}</td>
                                                <td>{{ date('h:i:s A', strtotime($order->created_at)) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!--/.col (left) -->

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div> <!-- Content Wrapper end -->
@endsection




@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/backend/plugins/fastclick/fastclick.js') }}"></script>

    <!-- Sweet Alert Js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.1/dist/sweetalert2.all.min.js"></script>


    <script>
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
                        messageTop: 'Candidates Report',
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
                        "Page Total: " + formatMoney(pageTotal) + "<br> (Grand Total: " +
                        formatMoney(total) + ")"
                    );

                }
            });
        });
    </script>


    <script type="text/javascript">
        function deleteItem(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
            })

            swalWithBootstrapButtons({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('delete-form-' + id).submit();
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons(
                        'Cancelled',
                        'Your data is safe :)',
                        'error'
                    )
                }
            })
        }
    </script>
@endpush
