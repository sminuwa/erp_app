@extends('layouts.backend.app')
@section('title', 'Manage Expenses')

@push('css')
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
                        <h4>Add Expense</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Expense</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('expenses.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('expenses.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        @include('forms.expense')
                    </div>
                    <div class="col-md-8">
                        <div class="card-body table-responsive">
                            @if (Cart::getTotal() < 1)
                                <div class="alert alert-danger">
                                    No Product Added
                                </div>
                            @else
                                <table class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr style="font-size: 14px;">
                                            <th>S.N</th>
                                            <th style="width:30%">Name</th>
                                            <th style="width:15%">Amount</th>
                                            <th style="width:30%">Reason</th>
                                            <th><span class="ion-refresh"></span></th>
                                            <th><span class="ion-ios-trash"></span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cart_expenses as $expense)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-left">{{ $expense->name }}</td>

                                                <form action="{{ route('expenses.cart.update') }}" method="post">
                                                    @csrf
                                                    @method('PUT')
                                                    <td>
                                                        <input type="number" name="amount"
                                                            id="price{{ $loop->iteration }}" class="form-control"
                                                            style="min-width:65px;" value="{{ $expense->price }}">
                                                    </td>
                                                    <input type="hidden" name="id" class="form-control"
                                                        value="{{ $expense->id }}">
                                                    <td>
                                                        <input type="text" name="reason" class="form-control"
                                                            value="{{ $expense->attributes['reason'] }}">
                                                    </td>

                                                    <td>
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                </form>

                                                <td>
                                                    <button class="btn btn-danger btn-sm" type="button"
                                                        onclick="deleteItem({{ $expense->id }})">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $expense->id }}"
                                                        action="{{ route('expense.cart.remove', $expense->id) }}"
                                                        method="post" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>


                                <div class="alert alert-info">
                                    <p>Quantity : {{ Cart::getTotalQuantity() }}</p>
                                    <p>Sub Total : &#8358; {{ number_format(Cart::getSubTotal(), 2) }}</p>
                                </div>
                                <div class="alert alert-success">
                                    Total : &#8358; {{ number_format(Cart::getTotal()) }}
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-body">
                                                <form action="{{ isset($route) ? $route : route('expenses.store') }}"
                                                    method="POST">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="_method"
                                                        value="{{ isset($method) ? $method : 'POST' }}" />
                                                    <div class="form-group">
                                                        <label for="impress">Impress No</label>
                                                        <input type="text" readonly
                                                            class="form-control {{ $errors->has('impress') ? ' is-invalid' : '' }}"
                                                            name="impress" id="impress"
                                                            value="{{ isset($impress) ? $impress : old('impress', $model->impress) }}"
                                                            placeholder="Impress No" maxlength="191">
                                                        @if ($errors->has('impress'))
                                                            <div class="invalid-feedback">
                                                                <strong>{{ $errors->first('impress') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="payment_mode">Payment Mode</label>
                                                        <select
                                                            class="form-control {{ $errors->has('payment_mode') ? ' is-invalid' : '' }}"
                                                            name="payment_mode" id="payment_mode" required="required">
                                                            <option value="">Select...</option>
                                                            <option value="Cash"
                                                                {{ 'Cash' == $model->payment_mode ? 'selected' : '' }}>
                                                                Cash</option>
                                                            <option value="Cheque"
                                                                {{ 'Cheque' == $model->payment_mode ? 'selected' : '' }}>
                                                                Cheque</option>
                                                        </select>
                                                        @if ($errors->has('payment_mode'))
                                                            <div class="invalid-feedback">
                                                                <strong>{{ $errors->first('payment_mode') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div id="account_number">
                                                        <div class="form-group">
                                                            <label for="bank_account_id">Account Number</label>
                                                            <select
                                                                class="form-control select2-single {{ $errors->has('bank_account_id') ? ' is-invalid' : '' }}"
                                                                name="bank_account_id" id="bank_account_id"
                                                                required="required">
                                                                <option value="">Select...</option>
                                                            </select>
                                                            @if ($errors->has('bank_account_id'))
                                                                <div class="invalid-feedback">
                                                                    <strong>{{ $errors->first('bank_account_id') }}</strong>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if(auth::user()->hasRole('Super-admin'))
                                                    <div class="form-group">
                                                        <label for="date">Date</label>
                                                        <input type="text" autocomplete="off"
                                                            class="form-control datepicker {{ $errors->has('date') ? ' is-invalid' : '' }}"
                                                            name="date" id="date" required
                                                            value="{{ old('date', $model->date) == null?date('Y-m-d'):old('date', $model->date) }}" placeholder=""
                                                            maxlength="191" required="required">
                                                        @if ($errors->has('date'))
                                                            <div class="invalid-feedback">
                                                                <strong>{{ $errors->first('date') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @else
                                                    <input type="hidden" autocomplete="off"
                                                            class="form-control datepicker {{ $errors->has('date') ? ' is-invalid' : '' }}"
                                                            name="date" id="date" required
                                                            value="{{ old('date', $model->date) == null?date('Y-m-d'):old('date', $model->date) }}" placeholder=""
                                                            maxlength="191" required="required">
                                                    @endif
                                                    <div class="form-group text-right ">
                                                        <div class="col-sm-6 text-danger">
                                                            <strong>Total Record is of
                                                                {{ number_format(App\Models\Expense::count('*'), 0, ',', '') }}</strong>
                                                        </div>
                                                        <input type="submit" class="btn btn-primary" value="Save" />

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection
@push('js')
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('#account_number,#account_name').hide();
            $('#payment_mode').on("change", function() {
                if ($(this).val() != "Cash") {
                    $('#bank_account_id,#account_name').removeAttr('disabled');
                    $('#account_number,#account_name').show();
                    $("#bank_account_id").html(" < option value = '' > Loading... < /option>");
                    $.ajax({
                        url: "{{ route('ajax.loadBankAccounts') }}",
                        type: 'GET',
                        data: {
                            payment_mode: $("#payment_mode").val()
                        }
                    }).done(function(msg) {
                        $("#bank_account_id").html("<option value=''>--select--</option>" + msg);
                    });
                } else {
                    $('#bank_account_id,#account_name').attr('disabled', 'disabled');
                    $('#account_number,#account_name').hide();
                }

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
