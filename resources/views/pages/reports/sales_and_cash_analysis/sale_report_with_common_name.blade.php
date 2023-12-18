@extends('layouts.backend.app')

@section('title', 'Sales Report')

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
                        <h4>Customer Sales Transactions with Common Names</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Sales with Common  Names Report</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form method="POST" class="form-inline form-check-inline">
                    <div class="row">
                        <div class="form-group  col-sm-2">
                            <label for="from_date">From Date</label>
                            <input type="text" autocomplete="off"
                                class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                name="from_date" id="from_date" value="{{ old('from_date') }}" placeholder="">
                        </div>
                        <div class="form-group  col-sm-2">
                            <label for="to_date">To Date</label>
                            <input type="text" autocomplete="off"
                                class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                name="to_date" id="to_date" value="{{ old('to_date') }}" placeholder="">
                        </div>
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
                    </div>
                    <div class="row">
                        
                        <div class="form-group">
                            &nbsp;&nbsp;
                            <label for="customer_id">Customer</label>
                            <input type="text" name="customer" id="customer" class="form-control" placeholder="Type the name"/> 
                        </div>
                        <div class="form-group">
                            &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
                            <input type="radio" name="matching" value="exact" class="form-control" />
                            &nbsp;&nbsp; &nbsp;Exact Name &nbsp; &nbsp;&nbsp;
                            <input type="radio" name="matching" value="similar" class="form-control" checked />
                            &nbsp;&nbsp;Similar Name
                        </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-group text-right">
                            <input type="button" class="btn btn-primary" id="generate" name="generate" value="Generate" />
                        </div>
                    </div>
                </form>
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
    <!-- Sweet Alert Js -->
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
                branch_id = $('#branch_id').val();
                product_id = $('#product_id').val();
                store_id = $('#store_id').val();
                category_id = $('#category_id').val();
                customer = $('#customer').val();
                payment_mode = $('#payment_mode').val();
                credit_walkedin = $('input[name="credit_walkedin"]:checked').val();
                matching = $('input[name="matching"]:checked').val();
                
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customer.sale.reports') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        product_id: product_id,
                        store_id: store_id,
                        credit_walkedin: credit_walkedin,
                        category_id: category_id,
                        customer: customer,
                        payment_mode: payment_mode,
                        matching:matching
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
                                messageTop: 'Stock Transfer',
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
            });
        });
    </script>
@endpush
