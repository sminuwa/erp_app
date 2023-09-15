@extends('layouts.backend.app')

@section('title')
    {{ date('F') . 'Expenses' }}
@endsection

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
                            <li class="breadcrumb-item active">{{ date('F') }} Expenses</li>
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
                        <div class="mb-3">
                            <a href="{{ route('expense.month', 'january') }}" class="btn btn-info">January</a>
                            <a href="{{ route('expense.month', 'february') }}" class="btn btn-primary">February</a>
                            <a href="{{ route('expense.month', 'march') }}" class="btn btn-secondary">March</a>
                            <a href="{{ route('expense.month', 'april') }}" class="btn btn-warning">April</a>
                            <a href="{{ route('expense.month', 'may') }}" class="btn btn-info">May</a>
                            <a href="{{ route('expense.month', 'june') }}" class="btn btn-success">June</a>
                            <a href="{{ route('expense.month', 'july') }}" class="btn btn-danger">July</a>
                            <a href="{{ route('expense.month', 'august') }}" class="btn btn-primary">August</a>
                            <a href="{{ route('expense.month', 'september') }}" class="btn btn-info">September</a>
                            <a href="{{ route('expense.month', 'october') }}" class="btn btn-secondary">October</a>
                            <a href="{{ route('expense.month', 'november') }}" class="btn btn-warning">November</a>
                            <a href="{{ route('expense.month', 'december') }}" class="btn btn-danger">December</a>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <strong class="text-danger">{{ strtoupper($month) }}</strong> EXPENSES LISTS
                                    <small class="text-danger pull-right">Total Expenses :&#8358;
                                        {{ number_format($expenses->sum('amount'), 2, '.', ',') }}</small>
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>Serial</th>
                                            <th>Expense Item</th>
                                            <th>Impress</th>
                                            <th>Payment Mode</th>
                                            <th>Amount</th>
                                            <th>Charged Account</th>
                                            <th>Date</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Serial</th>
                                            <th>Expense Item</th>
                                            <th>Impress</th>
                                            <th>Payment Mode</th>
                                            <th>Amount</th>
                                            <th>Charged Account</th>
                                            <th>Month</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                    @foreach ($expenses as $key => $expense)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $expense->item->name }}</td>
                                            <td>{{ $expense->impress }}</td>
                                            <td>{{ $expense->mode->name }}</td>
                                            <td style="text-align: right">{{ number_format($expense->amount, 2) }}</td>
                                            <td>{{ $expense->account->account_name }}</td>
                                            <td>{{ optional($expense->created_at)->toDayDateTimeString() }}</td>
                                            <td>
                                                <a href="{{ route('expenses.edit', $expense->id) }}"
                                                    class="btn
													btn-info">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <button class="btn btn-danger" type="button"
                                                    onclick="deleteItem({{ $expense->id }})">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                                <form id="delete-form-{{ $expense->id }}"
                                                    action="{{ route('expenses.destroy', $expense->id) }}" method="post"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
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
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
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
