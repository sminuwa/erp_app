@extends('layouts.backend.app')

@section('title', 'Purchase Additional Invoice')

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
                            <li class="breadcrumb-item active">Additional Invoices</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Additional Invoice List</h3>
                            </div>

                            <div class="card-body table-responsive">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        @can('purchase.additional-invoice.create')
                                            <a href="{{ route('purchase.additional-invoice.create') }}"
                                                class="btn btn-sm btn-secondary" style="margin-left: 2px;"><span
                                                    class="fa fa-plus-circle"> </span> New Invoice</a>
                                        @endcan
                                    </div>
                                </div>
                                <table id="example1"
                                    class="table table-bordered table-striped text-left table-responsive-xl">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>GRN</th>
                                            <th>Account</th>
                                            <th>Amount</th>
                                            <th>Created By</th>
                                            <th>Date Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>GRN</th>
                                            <th>Account</th>
                                            <th>Amount</th>
                                            <th>Created By</th>
                                            <th>Date Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($invoices as $invoice)
                                            <tr class="@if ($invoice->status == 0) bg-warning @endif">

                                                <td>{{ Carbon\Carbon::parse($invoice->date)->toFormattedDateString() }}</td>
                                                <td>{{ $invoice->reference }}</td>
                                                <td>{{ $invoice->purchase->reference }}</td>
                                                <td>{{ $invoice->supplier->code ?? null }} -
                                                    {{ $invoice->supplier->name ?? '' }}</td>

                                                <td align="right">{{ number_format($invoice->amount, 2, '.', ',') }}</td>
                                                <td>{{ $invoice->createdBy->name ?? '' }}</td>
                                                <td>{{ Carbon\Carbon::parse($invoice->created_at)->toFormattedDateString() }}</td>
                                                <td align="center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                            id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                            @if ($invoice->status == 0)
                                                                @can('purchase.additional-invoice.show')
                                                                    <a href="{{ route('purchase.additional-invoice.show', $invoice->id) }}"
                                                                       class="dropdown-item">
                                                                        <i class="fa fa-eye" aria-hidden="true"></i> View
                                                                    </a>
                                                                @endcan
                                                                @can('purchase.additional-invoice.post')
                                                                    <form
                                                                        action="{{ route('purchase.additional-invoice.post', $invoice->id) }}"
                                                                        method="post"
                                                                        onsubmit="return confirm('Are you sure you want post this invoice?')">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fa fa-check" aria-hidden="true"></i> Post
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                                @can('purchase.additional-invoice.edit')
                                                                    <a href="{{ route('purchase.additional-invoice.edit', $invoice->id) }}"
                                                                        class="dropdown-item">
                                                                        <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                                                    </a>
                                                                @endcan
                                                                @can('purchase.additional-invoice.delete')
                                                                    <form
                                                                        action="{{ route('purchase.additional-invoice.delete', $invoice->id) }}"
                                                                        method="post"
                                                                        onsubmit="return confirm('Are you sure you want reverse this invoice?')">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fa fa-reply" aria-hidden="true"></i>
                                                                            Delete
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            @else
                                                                @can('purchase.additional-invoice.reverse')
                                                                    <form
                                                                        action="{{ route('purchase.additional-invoice.reverse', $invoice->id) }}"
                                                                        method="post"
                                                                        onsubmit="return confirm('Are you sure you want reverse this invoice?')">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fa fa-reply" aria-hidden="true"></i>
                                                                            Reverse
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            @endif
                                                            @can('purchase.additional-invoice.print')
                                                                <a href="{{ route('purchase.additional-invoice.print', [$invoice->id,'A4']) }}"
                                                                    target="_blank" class="dropdown-item">
                                                                    <i class="fa fa-print" aria-hidden="true"></i> Print A4
                                                                </a>
                                                                <a href="{{ route('purchase.additional-invoice.print', [$invoice->id,'A5']) }}"
                                                                    target="_blank" class="dropdown-item">
                                                                    <i class="fa fa-print" aria-hidden="true"></i> Print A5
                                                                </a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </td>
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
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>


    <script>
        $(function() {

            $("#example1").DataTable({
                'iDisplayLength': 100
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
            $(document).on('click', '".show"', function() {
                order_id = $(this).attr('data-val');
                $.ajax({
                    type: 'get',
                    url: "{{ route('orders.load') }}",
                    data: {
                        order_id: order_id
                    }
                }).done(function(data) {
                    $('.display').html();
                    $('.display').html(data);
                });
            });

        });
    </script>
@endpush
