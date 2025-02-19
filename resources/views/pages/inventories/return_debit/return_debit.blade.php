@extends('layouts.backend.app')

@section('title', 'Approved Orders')

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
                            <li class="breadcrumb-item active">Return & Debit List</li>
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
                                <h3 class="card-title">Return & Debit List</h3>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    @can('return.debit.create')
                                        <a href="{{ route('return.debit.create') }}" class="btn btn-sm btn-secondary"
                                            style="margin-left: 2px;"><span class="fa fa-plus-circle"> </span> New Return &
                                            Debit</a>
                                    @endcan
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    @can('return.debit.search')
                                        <form action="{{ route('return.debit.search') }}" method="POST">
                                            @csrf
                                            <div class="input-group">
                                                <input type="search" class="form-control rounded" required
                                                    placeholder="Search by Receipt or Cheque number" name="refno"
                                                    aria-label="Search" aria-describedby="search-addon" />
                                                <button type="submit" class="btn btn-outline-primary">search</button>
                                            </div>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1"
                                    class="table table-bordered table-striped text-left table-responsive-xl">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Processed Date</th>
                                            <th>Reference No</th>
                                            <th>Amount</th>
                                            <th>Created By</th>
                                            <th>Date Created</th>
                                            <th>Posted By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Processed Date</th>
                                            <th>Reference No</th>
                                            <th>Amount</th>
                                            <th>Created By</th>
                                            <th>Date Created</th>
                                            <th>Posted By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr class="@if ($payment->status == 0) bg-warning @endif">

                                                <td>{{ $payment->purchase->supplier->code ?? '' }}-{{ $payment->purchase->supplier->name ?? '' }}
                                                </td>
                                                <td>{{ Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $payment->reference }}</td>

                                                <td align="right">{{ number_format($payment->amount, 2, '.', ',') }}</td>
                                                <td>{{ $payment->createdBy->name ?? '' }}</td>

                                                <td>{{ Carbon\Carbon::parse($payment->created_at)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $payment->postedBy->name ?? '' }}</td>
                                                <td align="center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                            id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            @can('return.debit.show')
                                                                <a href="{{ route('return.debit.show', $payment->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i> View
                                                                </a>
                                                            @endcan
                                                            @can('return.debit.print')
                                                                <a href="{{ route('return.debit.print', [$payment->id, 'A4']) }}"
                                                                    target="_BLANK" class="dropdown-item">
                                                                    <i class="fa fa-print" aria-hidden="true"></i> Print A4
                                                                </a>
                                                                <a href="{{ route('return.debit.print', [$payment->id, 'A5']) }}"
                                                                    target="_BLANK" class="dropdown-item">
                                                                    <i class="fa fa-print" aria-hidden="true"></i> Print A5
                                                                </a>
                                                            @endcan
                                                            @if ($payment->status == 0)
                                                                @can('return.debit.edit')
                                                                    <a href="{{ route('return.debit.edit', $payment->id) }}"
                                                                        class="dropdown-item">
                                                                        <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                                                    </a>
                                                                @endcan
                                                                @can('return.debit.post')
                                                                    <form
                                                                        action="{{ route('return.debit.post', $payment->id) }}"
                                                                        method="post"
                                                                        onsubmit="return confirm('Are you sure you want to post this R&D?')">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item ">
                                                                            <i class="fa fa-check" aria-hidden="true"></i> Post
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                                @can('return.debit.destroy')
                                                                    <button class="dropdown-item" type="button"
                                                                        onclick="deleteItem({{ $payment->id }})">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                                    </button>
                                                                    <form id="delete-form-{{ $payment->id }}"
                                                                        action="{{ route('return.debit.destroy', $payment->id) }}"
                                                                        method="post" style="display:none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                @endcan
                                                            @endif


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
