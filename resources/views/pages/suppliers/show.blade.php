@extends('layouts.backend.app')
@section('title', 'Manage Supplier')

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
                        <h4>Supplier Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                            <li class="breadcrumb-item active">Supplier Detail</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('suppliers.create') }}">
                <span class="fa fa-plus"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('products.edit', $record->id) }}">
                <span class="fa fa-pencil"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('suppliers.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <form onsubmit="return confirm('Are you sure you want to delete?')"
                action="{{ route('products.destroy', $record->id) }}" method="post" style="display: inline">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                    <i class="text-danger fa fa-remove"></i>
                </button>
            </form>
            <div class="container-fluid">
                <div class="row">
                    <div class="offset-6">
                        <form method="POST" class="form-inline">
                            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $record->id }}" />
                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input type="text"
                                    class="form-control datepicker {{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                    name="from_date" id="from_date" value="{{ old('from_date') }}" placeholder=""
                                    autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="to_date">To Date</label>
                                <input type="text"
                                    class="form-control datepicker {{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                    name="to_date" id="to_date" value="{{ old('to_date') }}" placeholder=""
                                    autocomplete="off">
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
                        @include('cards.supplier')
                    </div>
                    <div class="col-sm-8 table-responsive" id="load">
                        <table class="table table-bordered" id="example1" data-ordering="false">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Purchase/Payment<br /> Mode</th>
                                    <th>Ref</th>
                                    <th>Cr</th>
                                    <th>Dr</th>
                                    <th>Running Balance</th>
                                </tr>
                            </thead>
                            <?php $sum_cr = 0;
                            $sum_dr = 0;
                            $dif = 0; ?>

                            @foreach ($record->supplierLedgers()->orderBy('date')->get()
        as $ledger)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($ledger->date)->toFormattedDateString() }}</td>
                                    <td>{{ $ledger->description }}</td>
                                    <td>{{ $ledger->dr > 0 ? $ledger->payment_mode : optional($ledger->purchase)->purchase_mode }}
                                    </td>
                                    <td>{{ $ledger->Ref }}</td>
                                    <td style="text-align: right">&#8358;{{ number_format($ledger->cr, 2) }}</td>
                                    <td style="text-align: right">&#8358;{{ number_format($ledger->dr, 2) }}</td>
                                    <?php $sum_cr += $ledger->cr;
                                    $sum_dr += $ledger->dr;
                                    $dif = $sum_cr - $sum_dr; ?>
                                    <td style="text-align: right">
                                        @if ($dif < 0)
                                            &#8358;({{ number_format(abs($dif), 2) }})
                                        @else
                                            &#8358;{{ number_format($dif, 2) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: right">&#8358;{{ number_format($sum_cr, 2) }}</th>
                                    <th style="text-align: right">&#8358;{{ number_format($sum_dr, 2) }}</th>
                                    <th style="text-align: right">
                                        @if ($sum_cr - $sum_dr < 0)
                                            &#8358;({{ number_format(abs($sum_cr - $sum_dr), 2) }})
                                        @else
                                            &#8358;{{ number_format($sum_cr - $sum_dr, 2) }}
                                        @endif
                                    </th>
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
                pageLength: 20

            });

            $('#generate').on("click", function() {
                from_date = $('#from_date').val();
                to_date = $('#to_date').val();
                supplier_id = $('#supplier_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.supplier.ledger') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        from_date: from_date,
                        to_date: to_date,
                        supplier_id: supplier_id
                    }
                }).done(function(data) {
                    $("#load").html(data);
                    $('#example1').DataTable({
                        drawCallback: function() {
                            var api = this.api();
                            $(api.table().footer()).html(
                                api.column(4, {
                                    page: 'current'
                                }).data().sum()
                            );
                        }
                    });
                });
            });

        });
    </script>
@endpush
