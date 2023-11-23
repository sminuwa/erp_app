@extends('layouts.backend.app')

@section('title', 'Ledger')

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
                        <h4>Intersite Transfer Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Intersite Transfer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <a class="btn btn-secondary btn-sm"
                   href="{{ route('intersite.index', $record->id) }}">
                    <span class="fa fa-list"></span> Instersites
                </a>

                <a class="btn btn-secondary btn-sm" title="Print" target="_BLANK"
                   href="{{ route('intersite.print', $record->id) }}">
                    <span class="fa fa-print"></span> Print
                </a>
                @if ($record->status == 1 && $record->destination_branch_id == auth()->user()->branch->id)
                    <form action="{{ route('intersite.receive', $record->id) }}"
                          style="display:inline;"
                          method="post" onsubmit="return confirm('Are you sure you want receive this invoice?')">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-check" aria-hidden="true"></i> Receive
                        </button>
                    </form>
                @endif
                @if ($record->status == 0)
                    <form onsubmit="return confirm('Are you sure you want to post this intersite?')"
                          action="{{ route('intersite.post', $record->id) }}"
                          method="post" style="display: inline">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                            <i class="text-white fa fa-check"></i> Post
                        </button>
                    </form>

                    <a class="btn btn-secondary btn-sm"
                       href="{{ route('intersite.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span> Edit
                    </a>

                    <form onsubmit="return confirm('Are you sure you want to delete this intersite?')"
                          action="{{ route('intersite.delete', $record->id) }}"
                          method="post" style="display: inline">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                            <i class="text-danger fa fa-remove"></i> Delete
                        </button>
                    </form>
                @endif
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5>Intersite Transfer
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-block">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th>Reference</th>
                                            <td colspan="3">{{ $record->reference }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ $record->date }}</td>
                                            <th>Truck No</th>
                                            <td>{{ $record->vehicle_no }}</td>
                                        </tr>
                                        <tr>

                                        </tr>
                                        <tr>
                                            <th>Source</th>
                                            <td> {{ $record->source->code ?? '' }}-{{ $record->source->name ?? '' }} </td>
                                            <th>Destination</th>
                                            <td> {{ $record->destination->code ?? '' }}-{{ $record->destination->name ?? '' }} </td>
                                        </tr>
                                        <tr>
                                            <th>Created By</th>
                                            <td colspan="3">{{ $record->createdBy->name ?? ''}}</td>
                                        </tr>
                                        <tr>
                                            <th>Received By</th>
                                            <td colspan="3">{{ $record->receivedBy->name ?? ''}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                Request Products
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered" id="record1">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Code</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Received</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach ($record->products as $product)
                                            <tr>
                                                <th>{{ $loop->index + 1 }}</th>
                                                <td>{{ $product->product->code }}</td>
                                                <td>{{ $product->product->name }}</td>
                                                <td>{{ ($product->quantity - $product->totalReceive()) }}</td>
                                                <td>{{ ($product->totalReceive()) }}</td>
                                                <td class="text-right">
                                                    @if(($product->quantity - $product->totalReceive()) > 0)
                                                    @if($product->intersite->status == 2 && $product->intersite->destination_branch_id == auth()->user()->branch->id)
                                                        <a href="javascript:void(0)" data-toggle="modal"
                                                           data-target="#add_store_product_form"
                                                           class="btn btn-sm btn-success add-product"
                                                           data-product-id="{{ $product->product_id }}"
                                                           data-store-id="{{ $product->store_id }}"
                                                           data-quantity="{{ $product->quantity }}"
                                                           data-cost="{{ $product->cost_price }}"
                                                           ><i class="fa fa-plus"></i> Add Product </a>
                                                    @endif
                                                    @endif
                                                </td>
                                                @php $total += $product->cost_price * $product->quantity_requested; @endphp
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        {{-- <th style="text-align: right">Total</th>
                                        <th style="text-align: right">&#8358;{{ number_format($total, 2) }}</th> --}}
                                        <th></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                Received Products
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered" id="record1">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Product</th>
                                        <th>Store</th>
                                        <th>Quantity</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $total = 0; @endphp
                                    @foreach ($record->receivedProducts as $product)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <td>{{ $product->product->code }} - {{ $product->product->name }}</td>
                                            <td>{{ $product->store->code }} - {{ $product->store->name }}</td>
                                            <td>{{ $product->quantity }}</td>
                                            @php $total += $product->cost_price * $product->quantity_requested; @endphp
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <div class="modal fade" id="add_store_product_form" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add product to store</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('intersite.add-to-store') }}" method="POST" >
                        {{ csrf_field() }}
                        <input type="hidden" name="intersite_transfer_id" value="{{ $record->id }}">
                        <input type="hidden" name="source_store_id" value="">
                        <input type="hidden" name="product_id" value="">
                        <input type="hidden" name="total_quantity" value="">
                        <input type="hidden" name="cost_price" value="">
                        <div class="form-group">
                            <label for="source_store_id">Store</label>
                            <select class="form-control select2-single {{ $errors->has('store_id') ? ' is-invalid' : '' }}"
                                    name="store_id" id="store_id" required="required">
                                <option value="">Select...</option>
                                @if (isset($stores))
                                    @foreach ($stores as $data)
                                        <option value="{{ $data->id }}">
                                            {{ $data->code }}-{{ $data->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control"
                                   name="quantity" id="quantity" placeholder="Quantity"
                                   required="required">
                        </div>
                        <input type="hidden" value="0" name="cost_price" id="cost_price"/>
                        <div class="form-group text-right ">
                            <button type="submit" class="btn btn-primary"><span class="ion-ios-cart-outline"></span> Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- DataTables -->
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('assets/backend/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/backend/plugins/fastclick/fastclick.js') }}"></script>
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript">

        $(document).on('click', '.add-product', function(){
            let item = $(this)
            let s_id = item.data('store-id')
            let p_id = item.data('product-id')
            let qty = item.data('quantity')
            let cost = item.data('cost')
            let destination_store = $('select[name=store_id]')
            let quantity = $('input[name=quantity]')
            let source_store_id = $('input[name=source_store_id]')
            let product_id = $('input[name=product_id]')
            let total_quantity = $('input[name=total_quantity]')
            let cost_price = $('input[name=cost_price]')
            source_store_id.val(s_id)
            product_id.val(p_id)
            total_quantity.val(qty)
            cost_price.val(cost)
            quantity.attr('maxlength',total_quantity)
            quantity.val(qty)
        })

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
