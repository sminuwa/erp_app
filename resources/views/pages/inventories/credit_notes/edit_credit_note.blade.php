@extends('layouts.backend.app')

@section('title', 'Customer')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
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
                        <h4>Credit Note</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.payments') }}">Supplier Payment</a>
                            </li>
                            <li class="breadcrumb-item active">Credit Note</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('customers.credit.note')
                <a class="btn btn-secondary btn-sm" href="{{ route('customers.credit.note') }}">
                    <span class="fa fa-list"> Credit Notes</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Credit Notes
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="order_date">Date</label>
                                            <input type="text" name="date" class="form-control datepicker"
                                                value="{{ $credit_note ? $credit_note->date : date('Y-m-d') }}"
                                                onchange="$('.date').val($(this).val())" required />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Customer Type</label>
                                            <select name="account_type" id="account_type" class="form-control" required>
                                                <option value="" disabled selected>Select...</option>
                                                <option value="Retail" {{ $customer->type == 'Retail' ? 'selected' : '' }}>
                                                    Retail</option>
                                                <option value="Wholesale"
                                                    {{ $customer->type == 'Wholesale' ? 'selected' : '' }}>WholeSale
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <div class="form-group">
                                                <select name="customer_id" id="customer_record"
                                                    class="form-control customer_id ">
                                                    <option value="{{ $customer->id }}"> {{ $customer->code }} -
                                                        {{ $customer->name }} </option>
                                                </select>
                                                <div class="form-group">
                                                    <span class="text text-danger ion-android-alert"
                                                        id="credit_balance"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Invoices</h3>
                            </div>
                            <div class="card-body">
                                <input type="text" id="search" class="form-control" placeholder="Search">
                                <table class="table" id="" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th>Reference No</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body" class="customer-orders">
                                        {{-- @foreach ($orders as $order)
                                        <tr>
                                            <td>
                                                <a href="javascript:void(0)" class="invoice" onclick="load()"
                                                   data-val="{{ $order->invoice_no }}">{{ $order->invoice_no }}</a>
                                            </td>
                                            <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}</td>
                                        </tr>
                                    @endforeach --}}
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-8" id="load">
                        <div class="card card-default">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-info"></i>
                                    Item Lists for Invoice
                                    @if (isset($credit_note))
                                        {{ $credit_note->reference }}
                                    @elseif (Cart::getTotal() > 0)
                                        {{ $reference }}
                                    @endif

                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">

                                @if (Cart::getTotal() < 1)
                                    <div class="alert alert-danger">
                                        No Product Added
                                    </div>
                                @else
                                    <table class="table table-bordered table-striped text-center" style="font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th>S.N</th>
                                                <th>Code</th>
                                                <th>Item</th>
                                                <th>Unit</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                <!--<th><span class="ion-refresh"></span></th> -->
                                                {{-- <th><span class="ion-ios-trash"></span></th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cart_products as $product)
                                                <tr class="item{{ $product->id }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="text-left">{{ $product->code }}</td>
                                                    <td class="text-left">{{ $product->name }}</td>
                                                    <td class="text-left">{{ $product->attributes['unit'] }}</td>
                                                    <td class="text-left">{{ $product->attributes['sold_price'] }}</td>

                                                    <form action="{{ route('credit.note.cart.update') }}" method="post"
                                                        id="p{{ $product->id }}">
                                                        @csrf
                                                        <td>
                                                            <input type="hidden" name="sold_price"
                                                                id="price{{ $product->id }}" class="form-control"
                                                                style="min-width:65px;"
                                                                onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                                                value="{{ $product->price }}"
                                                                data-val="{{ $product->price }}"
                                                                data-value="p{{ $product->id }}">
                                                            <span style="color: red;"
                                                                id="valid_price{{ $product->id }}"></span>
                                                            <input type="text" name="quantity"
                                                                id="quantity{{ $product->id }}"
                                                                class="form-control quantity"
                                                                data-value="p{{ $product->id }}" style="min-width:58px;"
                                                                value="{{ $product->quantity }}" min="1"
                                                                max-qty="{{ $product->quantity }}" required>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
                                                        </td>
                                                        <input type="hidden" name="id" class="form-control"
                                                            value="{{ $product->id }}">
                                                        <input type="hidden" name="selling_price" class="form-control"
                                                            value="{{ $product->attributes['selling_price'] }}">
                                                        <input type="hidden" name="cost_price" class="form-control"
                                                            value="{{ $product->attributes['cost_price'] }}">
                                                        <input type="hidden" name="unit" class="form-control"
                                                            value="{{ $product->attributes['unit'] }}">
                                                    </form>

                                                    <td>
                                                        <form class="deleteForm" id="delete-form-{{ $product->id }}"
                                                            action="{{ route('credit.note.cart.remove', $product->id) }}"
                                                            method="post" data-val="{{ $product->id }}">
                                                            @csrf
                                                            <button class="btn btn-danger btn-sm delete" type="submit">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                                <div class="alert alert-success">
                                    Total : &#8358; <span class="total">{{ number_format(Cart::getTotal()) }}</span>
                                </div>
                                @can('credit.note.store')
                                    <form action="{{ route('credit.note.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="date" class="date"
                                            value="{{ $credit_note->date }}" />
                                        <input type="hidden" name="credit_note_id" id="credit_note_id"
                                            value="{{ $credit_note->id }}" />
                                        <input type="hidden" name="customer_id" id="customer_id"
                                            value="{{ $credit_note->customer_id }}" />
                                        <input name="comment" placeholder="Comment" class="form-control">

                                        <div class="form-group text-right mt-3">
                                            <input type="submit" class=" btn btn-primary" value="Submit" />
                                        </div>

                                    </form>
                                @endcan
                            </div>
                            <!-- /.card-body -->
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
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/backend/plugins/fastclick/fastclick.js') }}"></script>

    <!-- Sweet Alert Js -->
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(function() {
            $("#example1,#store_data").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            $('#account_type').on("change", function() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customers') }}",
                    data: {
                        type: $(this).val()
                    }
                }).done(function(data) {
                    $("#customer_record").html(data);
                });
            });

            $('.customer_id').on('change', function() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.customer-orders') }}",
                    data: {
                        customer_id: $(this).val()
                    }
                }).done(function(data) {
                    console.log(data)
                    $(".customer-orders").html(data);
                });
            });

            $(document).on('keyup', '#search', function() {
                var searchText = $(this).val();
                $.ajax({
                    url: "{{ route('load.order-invoices') }}",
                    method: 'GET',
                    data: {
                        search: searchText
                    },
                    success: function(response) {
                        if (response == null)
                            $('#table-body').html("");
                        $('#table-body').html(response);

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
            var delay = (function() {
                var timer = 0;
                return function(callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();

            // function load() {
            $(document).on('click', '.invoice', function() {
                var reference = $(this).attr('data-val');

                $('#load').html("<h3>Please wait... while it is loading...</h3>");
                $.ajax({
                    url: "{{ route('load.order-cart') }}",
                    method: 'GET',
                    data: {
                        reference: reference
                    },
                    success: function(response) {

                        $('#load').html(response);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            // }

            /*var delay = (function() {
                var timer = 0;
                return function(callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();*/

            $(document).on('keyup', '.quantity', function() {
                let id = $(this).attr('data-value');
                $("#valid_qty" + id.substr(1)).html("");
                if (parseFloat($('#quantity' + id.substr(1)).val()) > parseFloat($('#quantity' + id.substr(
                        1)).attr(
                        'max-qty'))) {
                    $("#valid_qty" + id.substr(1)).html("Selling QTY is more than the available QTY(" + $(
                        '#quantity' +
                        id.substr(1)).attr('max-qty') + ")");
                    $('#quantity' + id.substr(1)).val($('#quantity' + id.substr(1)).attr('max-qty'));
                    return false;
                }
                delay(function() {

                    $.ajax({
                        url: $('#' + id).attr('action'),
                        type: $('#' + id).attr('method'),
                        header: '',
                        data: {
                            quantity: $('#quantity' + (id.substr(1))).val(),
                            store_product_id: id.substr(1),
                            _token: "{{ csrf_token() }}"
                        },
                        //dataType: 'json',
                        success: function(data) {
                            console.log(data)
                            id = id.substr(1);
                            subtotal = $('#price' + id).val() * $('#quantity' + id)
                                .val();
                            $('.subtotal' + id).text(formatMoney(subtotal));
                            $('.total').text(formatMoney(data));
                            $('.testinng').html(data);
                        },
                        error: function(xhr, err) {

                        }
                    });

                }, 500);
            });

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

            $(document).on('submit', '.deleteForm', function(event) {
                event.preventDefault();
                var id = $(this).attr('data-val');
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
                        // $('.item'+id).remove();
                        $.ajax({
                            type: "POST",
                            url: $(this).attr('action'),
                            data: {
                                id: id,
                                _token: '{{ csrf_token() }}'
                            }
                        }).done(function(data) {
                            $('.total').text(formatMoney(data));
                            $('.item' + id).remove();
                        });
                        // return
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
            });
            var delay = (function() {
                var timer = 0;
                return function(callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();


        });
    </script>
@endpush
