@extends('layouts.backend.app')

@section('title', 'Receipt List')

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
                            <li class="breadcrumb-item active">Receipt List</li>
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
                                <h3 class="card-title">Receipt List</h3>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <a href="{{ route('receipt.payments') }}" class="btn btn-sm btn-secondary"
                                        style="margin-left: 2px;"><span class="fa fa-list"> </span> List</a>
                                    <a href="{{ route('create.payment.reciept') }}" class="btn btn-sm btn-secondary"
                                        style="margin-left: 2px;"><span class="fa fa-plus-circle"> </span> New Reciept</a>
                                    @if (Session::get('prev_id') != null)
                                        <a href="{{ route('receipt.payment.print', Session::get('prev_id')) }}" target="_BLANK"
                                            class="btn btn-sm btn-primary" style="margin-left: 2px;"><span
                                                class="fa fa-print"> Print</span> </a>
                                        <a href="{{ route('receipt.payment.print.pos', Session::get('prev_id')) }}" target="_BLANK"
                                            class="btn btn-secondary btn-sm">
                                            <i class="fa fa-print" aria-hidden="true">PoS</i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('receipt.payment.search') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="search" class="form-control rounded" required
                                                placeholder="Search by Receipt number" name="refno" aria-label="Search"
                                                aria-describedby="search-addon" />
                                            <button type="submit" class="btn btn-outline-primary">search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1"
                                    class="table table-bordered table-striped text-left table-responsive-xl">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Receipt No</th>
                                            <th>Payer</th>
                                            <th>Account</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Received BY</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Date</th>
                                            <th>Receipt No</th>
                                            <th>Payer</th>
                                            <th>Account</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Received BY</th>
                                            <th>Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr class="@if($payment->status == 0) bg-warning @endif">

                                                <td>{{ Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                                                </td>
                                                <td>{{ $payment->receipt_no }}</td>
                                                <td>
                                                    {{--@if ($payment->model_name == 'Customer')
                                                        {{ optional($payment->customer)->code ?? '' }}-{{ optional($payment->customer)->name ?? '' }}
                                                    @elseif($payment->model_name == 'Supplier')
                                                        {{ optional($payment->supplier)->name ?? '' }}{{ optional($payment->supplier)->name ?? '' }}
                                                    @endif--}}
                                                    {{
                                                        $payment->payer()->code ? $payment->payer()->code.' - '.$payment->payer()->name : ($payment->payer()->number.' - '.$payment->payer()->description)
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        $payment->account()->code ? $payment->account()->code.' - '.$payment->account()->name : ($payment->account()->number.' - '.$payment->account()->description)
                                                    }}
                                                </td>

                                                <td align="right">{{ number_format($payment->amount, 2, '.', ',') }}</td>
                                                <td>{{ $payment->description }}</td>
                                                <td>{{ optional($payment->createdBy)->name }}</td>
                                                <td align="center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="{{ route('receipt.payment.print', $payment->id) }}" target="_blank"
                                                               class="dropdown-item">
                                                                <i class="fa fa-print" aria-hidden="true"></i> Print
                                                            </a>
                                                            <a href="{{ route('receipt.payment.print.pos', $payment->id) }}" target="_blank"
                                                               class="dropdown-item">
                                                                <i class="fa fa-print" aria-hidden="true"></i> Print (PoS)
                                                            </a>
                                                            @if($payment->status == 0)
                                                                 <form action="{{ route('receipt.payment.post', $payment->id) }}" method="post">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fa fa-check" aria-hidden="true"></i> Post
                                                                    </button>
                                                                </form>

                                                                <a href="{{ route('create.payment.reciept', ['receipt_id'=>$payment->id]) }}"
                                                                   class="dropdown-item">
                                                                    <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                                                </a>
                                                                <a href="{{ route('create.payment.reciept', ['receipt_id'=>$payment->id]) }}"
                                                                   class="dropdown-item">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                                </a>
                                                            @else
                                                                <a href="{{ route('receipt.payment.reverse',[$payment->id]) }}"
                                                                   class="dropdown-item">
                                                                    <i class="fa fa-reply" aria-hidden="true"></i> Reverse
                                                                </a>
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
                pageLength: 10,
                "ordering": false
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": true,
                "autoWidth": false
            });
            /*$(".show").on('click', function() {
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
            });*/
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
