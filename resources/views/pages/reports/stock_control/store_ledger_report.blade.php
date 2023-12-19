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
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Store Ledger Reports</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Store Ledger Report</li>
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
                        <form method="POST" class="form-inline">
                            <div class="form-group">
                                &nbsp;&nbsp;
                                <label for="branch_id">Branch</label>
                                <select class="form-control select2-single ajax-branches {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                    name="branch_id" id="branch_id" required>
                                   
                                </select>
                            </div>
                            <div class="form-group">
                                &nbsp;&nbsp;
                                <label for="store_id">Store</label>
                                <select class="form-control select2-single ajax-stores {{ $errors->has('store_id') ? ' is-invalid' : '' }}"
                                    name="store_id" id="store_id">
                                   
                                </select>
                            </div>
                            <div class="form-group">
                                &nbsp;&nbsp;
                                <label for="category_id">Category</label>
                                <select class="form-control select2-single ajax-categories {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
                                    name="category_id" id="category_id">
                                   
                                </select>
                            </div>
                            <div class="form-group">
                                &nbsp;&nbsp;
                                <label for="product_id">Product</label>
                                <select
                                    class="form-control select2-single ajax-products {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                                    name="product_id" id="product_id">
                                    
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
                    <div class="col-sm-12 table-responsive" id="load">

                    </div>
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
            function formatMoney(n, c, d, t) {
                var c = isNaN(c = Math.abs(c)) ? 0 : c,
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
                product_id = $('#product_id').val();
                store_id = $('#store_id').val();
                category_id = $('#category_id').val();
                branch_id = $('#branch_id').val();
                
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.store.ledger.reports') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_id: product_id,
                        store_id: store_id,
                        category_id: category_id,
                        branch_id: branch_id
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
                                extend: 'print',
                                messageTop: 'Store Ledger',
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
