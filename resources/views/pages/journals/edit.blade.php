@extends('layouts.backend.app')

@section('title', 'Journal')

@push('css')
@endpush

@section('content')
    <input name="cart_page_type" type="hidden" value="journal">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Edit Journal {{ $journal->reference }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('journal.index') }}">Journals List</a></li>
                            <li class="breadcrumb-item active">New Journal</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('journal.create')
                <a href="{{ route('journal.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;">
                    <span class="fa fa-plus-circle"> </span> New Journal
                </a>
            @endcan
            @can('journal.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('journal.index') }}">
                    <span class="fa fa-list"></span> Journals List
                </a>
            @endcan
            <div class="container-fluid py-4">
                <form action="{{ route('journal.update', $journal->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <input type="text" class="form-control datepicker" name="date" id="date"
                                value="{{ $journal->date }}" required>
                            <label class="floating-label">Date: @error('journal_date')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="col-md-4 mb-3">
                            <textarea name="description" id="description" type="text" class="form-control" placeholder="Description">{{ $journal->description }}</textarea>
                            <label class="floating-label">Description: @error('description')
                                    <span class="text-danger error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="button" data-toggle="modal" data-target="#journal_modal"
                                class="btn btn-primary float-right"><i class="fa fa-cart-plus"></i>
                                Add
                            </button>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-7 text-right">
                            <button type="submit" class="btn btn-primary float-right"><i class="fa fa-cart-plus"></i>
                                Submit
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive cart-container">

                    </div>
                </form>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <div class="modal fade" id="journal_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Journal
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('ajax.cart.add') }}" method="POST" class="addCartItemForm">
                        @csrf
                        <input type="hidden" name="type" id="type" value="{{ 'journal' }}" />
                        <div class="row">
                            <div class="col-md-12">

                                <table>
                                    <tr>
                                        <td>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="account_type" id="account_type"
                                                        class="form-control  type select2-single" required>
                                                        <option value="">Select...</option>
                                                        <option value="Customer">Customer</option>
                                                        <option value="Supplier">Supplier
                                                        <option value="GeneralAccount">General Accounts
                                                        </option>
                                                    </select>
                                                    <label class="floating-label">Account Type: @error('type')
                                                            <span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="payer_id" id="payer_id"
                                                        class="form-control select2-single ajax-general-accounts" required>
                                                        <option value="">Select...</option>
                                                    </select>
                                                    <label class="floating-label">Account: @error('account')
                                                            <span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="Credit"
                                                        name="credit" id="credit">
                                                    <label class="floating-label">Credit: @error('credit')
                                                            <br><span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="Debit"
                                                        name="debit" id="debit">
                                                    <label class="floating-label">Debit: @error('debit')
                                                            <br><span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <textarea class="form-control" placeholder="Description" name="description"></textarea>
                                                    <label class="floating-label">Description: @error('description')
                                                            <br><span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add to Cart</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        function formatNumber(input) {
            // Remove non-numeric and non-decimal characters
            let value = input.value.replace(/[^\d.]/g, '');

            // Split the value into integer and decimal parts
            const parts = value.split('.');
            let integerPart = parts[0] ? parseFloat(parts[0]) : 0;
            let decimalPart = parts[1] !== undefined ? '.' + parts[1] : '';

            // Check if the integer part is not NaN
            if (!isNaN(integerPart)) {
                // Format the integer part with commas and dot as decimal separator
                integerPart = integerPart.toLocaleString('en-US', {
                    maximumFractionDigits: 2,
                    useGrouping: true
                });

                // Set the formatted value back to the input
                input.value = integerPart + decimalPart;
            }
        }

        var creditInput = document.getElementById('credit');
        var debitInput = document.getElementById('debit');

        creditInput.addEventListener('input', function() {
            formatNumber(this);
            if (parseFloat(this.value) !== 0) {
                debitInput.value = '0';
                debitInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }
        });

        debitInput.addEventListener('input', function() {
            formatNumber(this);
            if (parseFloat(this.value) !== 0) {
                creditInput.value = '0';
                creditInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }
        });


        $(function() {
            $('#account_type').on("change", function() {
                $("#payer_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.load.payers') }}",
                    type: 'GET',
                    data: {
                        type: $(this).val()
                    }
                }).done(function(msg) {
                    $("#payer_id").html(msg);
                });
            });
        });
        $(document).on('keyup', '.credit, .debit, .description', function() {
            let formId = $(this).attr('data-value'); //$(this).closest('form').attr('id');
            delayUpdateCart(formId, 1000); // Adjust the delay time (in milliseconds) as needed
        });

        function delayUpdateCart(formId, delay) {
            setTimeout(function() {
                updateCart(formId);
            }, delay);
        }

        function updateCart(formId) {
            let form = $('#' + formId);
            //console.log('Form Action:', form.attr('action'));

            type = 'journal';
            $.ajax({
                url: form.attr('action'),
                type: 'GET',
                data: form.serialize() + '&type=' + type,
            }).done(function(component) {
                console.log(component);
            });
        }
    </script>
@endpush
